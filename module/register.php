<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();

include_once '../app/class/databaseConn.php';

include_once '../app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$conn = $DatabaseCo->dbLink; // mysqli link

header("Content-Type: application/json");

function jsonResponse($status, $message, $data = []) {
    echo json_encode(["status"=>$status, "message"=>$message, "data"=>$data]);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';


/* ======================================================
   REGISTER
======================================================= */
if (isset($_POST['action']) && $_POST['action'] === "register") {

    // Clean input
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    // Validate mandatory fields
    if ($firstName === "" || $lastName === "" || $email === "" || $password === "") {
        jsonResponse(false, "First Name, Last Name, Email, and Password are required");
    }

    // Check if email exists
    $q = $conn->prepare("SELECT user_id FROM users WHERE email=? LIMIT 1");
    $q->bind_param("s", $email);
    $q->execute();
    $q->store_result();

    if ($q->num_rows > 0) {
        $q->close();
        jsonResponse(false, "Email already registered");
    }
    $q->close();

    // Encrypt password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user (correct bind_param → 4 values)
    $insert = $conn->prepare("
        INSERT INTO users 
        (email, password_hash, first_name, last_name, status) 
        VALUES (?, ?, ?, ?, 'ACTIVE')
    ");

    // FIX: Correct number of params → 4 strings ("ssss")
    $insert->bind_param("ssss", $email, $hash, $firstName, $lastName);

    if ($insert->execute()) {
        jsonResponse(true, "Registration successful");
    } else {
        jsonResponse(false, "Failed to register. Error: " . $insert->error);
    }
}




/* ======================================================
   LOGIN
======================================================= */
/* ======================================================
   LOGIN
======================================================= */
if (isset($_POST['action']) && $_POST['action'] === "login") {

    $loginInput = trim($_POST['email']); 
    $password   = trim($_POST['password']);

    if ($loginInput === "" || $password === "") {
        jsonResponse(false, "All fields are required");
    }

    // Get client IP
    $ip = $_SERVER['REMOTE_ADDR'];

    // Fetch user by email
    $q = $conn->prepare("
        SELECT user_id, email, first_name, last_name, password_hash, 
               account_locked, login_attempts, status
        FROM users 
        WHERE email = ? LIMIT 1
    ");
    $q->bind_param("s", $loginInput);
    $q->execute();
    $result = $q->get_result();

    if ($result->num_rows === 0) {
        jsonResponse(false, "Invalid email or password");
    }

    $user = $result->fetch_assoc();

    // Check account lock
    if ($user['account_locked'] == 1) {
        jsonResponse(false, "Your account is locked due to multiple failed login attempts");
    }

    // Verify password
    if (password_verify($password, $user['password_hash'])) {

        // Reset login attempts
        $reset = $conn->prepare("
            UPDATE users 
            SET login_attempts = 0, last_login = NOW(), last_ip=? 
            WHERE user_id=?
        ");
        $reset->bind_param("si", $ip, $user['user_id']);
        $reset->execute();

        // Set session
        $_SESSION["user_id"]  = $user['user_id'];
        $_SESSION["username"] = $user['first_name'];
        $_SESSION["email"]    = $user['email'];

        jsonResponse(true, "Login successful", ["redirect" => "dashboard.php"]);
    } 
    else {

        // Increase login attempts
        $attempts = $user['login_attempts'] + 1;
        $locked   = ($attempts >= 5) ? 1 : 0;

        $update = $conn->prepare("
            UPDATE users 
            SET login_attempts=?, account_locked=? 
            WHERE user_id=?
        ");
        $update->bind_param("iii", $attempts, $locked, $user['user_id']);
        $update->execute();

        if ($locked) {
            jsonResponse(false, "Too many failed attempts. Account locked.");
        }

        jsonResponse(false, "Invalid email or password");
    }
}



/* ======================================================
   LOGOUT
======================================================= */
if(isset($_POST['action']) && $_POST['action'] === "logout"){
    session_destroy();
    jsonResponse(true, "Logged out successfully");
}


jsonResponse(false, "Invalid request");
