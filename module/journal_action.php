<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();

include_once '../app/class/databaseConn.php';

include_once '../app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink; // mysqli link

// Force JSON output
header("Content-Type: application/json");

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid CSRF token!"
    ]);
    exit;
}

// Check user login
if (empty($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "User not logged in"]);
    exit();
}

$user_id = intval($_SESSION['user_id']);
$action = $_POST['action'] ?? '';

/* -------------------------
   1. SAVE JOURNAL ENTRY
------------------------- */
if ($action === "save") {

    // CSRF CHECK
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(["status" => false, "message" => "Invalid request (CSRF failed)."]);
        exit();
    }

    $note  = trim($_POST['note'] ?? "");
    $date  = trim($_POST['date'] ?? "");
    $created_at = date("Y-m-d H:i:s");

    if ($date == "") {
        echo json_encode(["status" => false, "message" => "Please select a date"]);
        exit();
    }

    if ($note == "") {
        echo json_encode(["status" => false, "message" => "Note cannot be empty"]);
        exit();
    }

    // Prepare statement
    $stmt = $db->prepare("
        INSERT INTO user_journals (user_id, action_date, note, created_at) 
        VALUES (?, ?, ?, ?)
    ");

    if (!$stmt) {
        echo json_encode(["status" => false, "message" => "SQL Error: " . $db->error]);
        exit();
    }

    // Correct bind_param → 1 int + 3 strings = "isss"
    $stmt->bind_param("isss", $user_id, $date, $note, $created_at);

    if ($stmt->execute()) {
        echo json_encode(["status" => true, "message" => "Journal saved successfully"]);
    } else {
        echo json_encode(["status" => false, "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
    exit();
}


/* -------------------------
   2. UPDATE JOURNAL ENTRY
------------------------- */
if ($action === "update") {

    $journal_id = intval($_POST['journal_id']);
    $note = trim($_POST['note']);

    if ($note == "") {
        echo json_encode(["status" => false, "message" => "Note cannot be empty"]);
        exit();
    }

    $stmt = $db->prepare("UPDATE user_journals SET note = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $note, $journal_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => true, "message" => "Journal updated"]);
    } else {
        echo json_encode(["status" => false, "message" => "Update failed"]);
    }
    exit();
}


/* -------------------------
   3. DELETE JOURNAL ENTRY
------------------------- */
if ($action === "delete") {

    $journal_id = intval($_POST['journal_id']);

    $stmt = $db->prepare("DELETE FROM user_journals WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $journal_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => true, "message" => "Journal deleted"]);
    } else {
        echo json_encode(["status" => false, "message" => "Delete failed"]);
    }
    exit();
}


/* -------------------------
   4. GET ALL JOURNALS
------------------------- */
if ($action === "list") {

    $query = "SELECT * FROM user_journals 
              WHERE user_id = $user_id 
              ORDER BY id DESC";

    $result = mysqli_query($db, $query);

    $entries = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $entries[] = $row;
    }

    echo json_encode([
        "status" => true,
        "journals" => $entries
    ]);
    exit();
}


/* -------------------------
   INVALID ACTION
------------------------- */
echo json_encode(["status" => false, "message" => "Invalid action"]);
exit();
?>
