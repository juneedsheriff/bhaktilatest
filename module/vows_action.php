<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();

include_once '../app/class/databaseConn.php';
include_once '../app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink;

header("Content-Type: application/json");

/* ================= CSRF CHECK ================= */
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid CSRF token!"
    ]);
    exit;
}

/* ================= USER SESSION CHECK ================= */
if (empty($_SESSION['user_id'])) {
    echo json_encode([
        "status" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$action  = $_POST['action'] ?? '';

/* =====================================================
   1. SAVE VOW ENTRY
===================================================== */
if ($action === "save") {

    $vows = trim($_POST['vows'] ?? "");
    $vows_date = trim($_POST['date'] ?? "");
    $created_at = date("Y-m-d H:i:s");

    if ($vows === "") {
        echo json_encode([
            "status" => false,
            "message" => "Vows cannot be empty"
        ]);
        exit;
    }

    if ($vows_date === "") {
        echo json_encode([
            "status" => false,
            "message" => "Please select a date"
        ]);
        exit;
    }

    $stmt = $db->prepare("
        INSERT INTO user_vows (user_id, vows_text, vows_date, created_at)
        VALUES (?, ?, ?, ?)
    ");

    if (!$stmt) {
        echo json_encode([
            "status" => false,
            "message" => "SQL Error: " . $db->error
        ]);
        exit;
    }

    $stmt->bind_param("isss", $user_id, $vows, $vows_date, $created_at);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => true,
            "message" => "Vows saved successfully"
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Database error: " . $stmt->error
        ]);
    }

    $stmt->close();
    exit;
}


/* =====================================================
   2. UPDATE VOW ENTRY
===================================================== */
if ($action === "update") {

    $vows_id = intval($_POST['vows_id']);
    $vows = trim($_POST['vows']);
    $vows_date = trim($_POST['vows_date'] ?? "");

    if ($vows === "") {
        echo json_encode([
            "status" => false,
            "message" => "Vows cannot be empty"
        ]);
        exit;
    }

    $stmt = $db->prepare("
        UPDATE user_vows
        SET vows_text = ?, vows_date = ?
        WHERE id = ? AND user_id = ?
    ");

    if (!$stmt) {
        echo json_encode([
            "status" => false,
            "message" => "SQL Error: " . $db->error
        ]);
        exit;
    }

    $stmt->bind_param("ssii", $vows, $vows_date, $vows_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => true,
            "message" => "Vows updated successfully"
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Update failed"
        ]);
    }

    $stmt->close();
    exit;
}


/* =====================================================
   3. DELETE VOW ENTRY
===================================================== */
if ($action === "delete") {

    $vows_id = intval($_POST['vows_id']);

    $stmt = $db->prepare("
        DELETE FROM user_vows
        WHERE id = ? AND user_id = ?
    ");

    if (!$stmt) {
        echo json_encode([
            "status" => false,
            "message" => "SQL Error: " . $db->error
        ]);
        exit;
    }

    $stmt->bind_param("ii", $vows_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => true,
            "message" => "Vows deleted successfully"
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Delete failed"
        ]);
    }

    $stmt->close();
    exit;
}


/* =====================================================
   4. GET ALL VOWS
===================================================== */
if ($action === "list") {

    $query = "
        SELECT id, vows_text, vows_date, created_at
        FROM user_vows
        WHERE user_id = ?
        ORDER BY id DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $entries = [];

    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }

    echo json_encode([
        "status" => true,
        "vows" => $entries
    ]);

    $stmt->close();
    exit;
}


/* =====================================================
   INVALID ACTION
===================================================== */
echo json_encode([
    "status" => false,
    "message" => "Invalid action"
]);
exit;
?>
