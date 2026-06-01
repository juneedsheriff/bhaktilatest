<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();

include_once '../app/class/databaseConn.php';
include_once '../app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink; // mysqli link

header("Content-Type: application/json");

// ---------------- CSRF CHECK ----------------
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid CSRF token!"
    ]);
    exit;
}

// ---------------- USER SESSION CHECK ----------------
if (empty($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "User not logged in"]);
    exit();
}

$user_id = intval($_SESSION['user_id']);
$action = $_POST['action'] ?? '';

/* =====================================================
   1. SAVE GOAL ENTRY
===================================================== */
if ($action === "save") {

    $goal = trim($_POST['goal'] ?? "");
    $target_date = trim($_POST['date'] ?? "");
    $created_at = date("Y-m-d H:i:s");

    if ($goal === "") {
        echo json_encode(["status" => false, "message" => "Goal cannot be empty"]);
        exit();
    }

    if ($target_date === "") {
        echo json_encode(["status" => false, "message" => "Please select a target date"]);
        exit();
    }

    $stmt = $db->prepare("
        INSERT INTO user_goals (user_id, goal_text, target_date, created_at) 
        VALUES (?, ?, ?, ?)
    ");

    if (!$stmt) {
        echo json_encode(["status" => false, "message" => "SQL Error: " . $db->error]);
        exit();
    }

    $stmt->bind_param("isss", $user_id, $goal, $target_date, $created_at);

    if ($stmt->execute()) {
        echo json_encode(["status" => true, "message" => "Goal added successfully"]);
    } else {
        echo json_encode(["status" => false, "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
    exit();
}


/* =====================================================
   2. UPDATE GOAL ENTRY
===================================================== */
if ($action === "update") {

    $goal_id = intval($_POST['goal_id']);
    $goal = trim($_POST['goal']);
    $target_date = trim($_POST['target_date'] ?? "");

    if ($goal === "") {
        echo json_encode(["status" => false, "message" => "Goal cannot be empty"]);
        exit();
    }

    $stmt = $db->prepare("
        UPDATE user_goals 
        SET goal_text = ?, target_date = ?
        WHERE id = ? AND user_id = ?
    ");

    $stmt->bind_param("ssii", $goal, $target_date, $goal_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => true, "message" => "Goal updated"]);
    } else {
        echo json_encode(["status" => false, "message" => "Update failed"]);
    }
    exit();
}


/* =====================================================
   3. DELETE GOAL ENTRY
===================================================== */
if ($action === "delete") {

    $goal_id = intval($_POST['goal_id']);

    $stmt = $db->prepare("
        DELETE FROM user_goals 
        WHERE id = ? AND user_id = ?
    ");

    $stmt->bind_param("ii", $goal_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => true, "message" => "Goal deleted"]);
    } else {
        echo json_encode(["status" => false, "message" => "Delete failed"]);
    }
    exit();
}


/* =====================================================
   4. GET ALL GOALS
===================================================== */
if ($action === "list") {

    $query = "SELECT * FROM user_goals 
              WHERE user_id = $user_id 
              ORDER BY id DESC";

    $result = mysqli_query($db, $query);

    $entries = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $entries[] = $row;
    }

    echo json_encode([
        "status" => true,
        "goals" => $entries
    ]);
    exit();
}


/* =====================================================
   INVALID ACTION
===================================================== */
echo json_encode([
    "status" => false,
    "message" => "Invalid action"
]);
exit();
?>
