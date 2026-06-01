<?php
session_start();
header("Content-Type: application/json");

// SHOW ERRORS
// ini_set("display_errors", 1);
// error_reporting(E_ALL);

include_once "../app/class/databaseConn.php";
include_once "../app/lib/requestHandler.php";

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink;


$action = $_POST['action'] ?? "";

function clean($str) {
    return mysqli_real_escape_string($GLOBALS['db'], trim($str));
}

/* ============================================================
    1. ADD NEW TOPIC
============================================================ */
if ($action == "add_topic") {
    if (empty($_SESSION['user_id'])) {
        echo json_encode(["status" => false, "message" => "Login required"]);
        exit;
    }
    $user_id = intval($_SESSION['user_id']);

    $category_id = intval($_POST['category_id']);
    $title = clean($_POST['title']);
    $content = clean($_POST['content']);
    $slug = strtolower(str_replace(" ", "-", $title));

    if ($title == "" || $content == "") {
        echo json_encode(["status" => false, "message" => "Title & content required"]);
        exit;
    }

    $sql = "INSERT INTO forum_topics (user_id, category_id, topic_title, topic_slug, topic_content)
            VALUES ($user_id, $category_id, '$title', '$slug', '$content')";

    if (mysqli_query($db, $sql)) {
        echo json_encode(["status" => true, "message" => "Topic posted"]);
    } else {
        echo json_encode(["status" => false, "message" => mysqli_error($db)]);
    }
    exit;
}

/* ============================================================
    2. UPDATE TOPIC
============================================================ */
if ($action == "update_topic") {
    if (empty($_SESSION['user_id'])) {
        echo json_encode(["status" => false, "message" => "Login required"]);
        exit;
    }
    $user_id = intval($_SESSION['user_id']);

    $topic_id = intval($_POST['topic_id']);
    $title = clean($_POST['topic_title']);
    $content = clean($_POST['topic_content']);
    $slug = strtolower(str_replace(" ", "-", $title));

    $sql = "UPDATE forum_topics SET 
            topic_title='$title', 
            topic_slug='$slug',
            topic_content='$content'
            WHERE topic_id=$topic_id AND user_id=$user_id";

    if (mysqli_query($db, $sql)) {
        echo json_encode(["status" => true, "message" => "Topic updated"]);
    } else {
        echo json_encode(["status" => false, "message" => mysqli_error($db)]);
    }
    exit;
}

/* ============================================================
    3. DELETE TOPIC
============================================================ */
if ($action == "delete_topic") {
    if (empty($_SESSION['user_id'])) {
        echo json_encode(["status" => false, "message" => "Login required"]);
        exit;
    }
    $user_id = intval($_SESSION['user_id']);
    $topic_id = intval($_POST['topic_id']);

    mysqli_query($db, "DELETE FROM forum_replies WHERE topic_id=$topic_id");
    mysqli_query($db, "DELETE FROM forum_likes WHERE topic_id=$topic_id");

    $sql = "DELETE FROM forum_topics WHERE topic_id=$topic_id AND user_id=$user_id";

    if (mysqli_query($db, $sql)) {
        echo json_encode(["status" => true, "message" => "Topic deleted"]);
    } else {
        echo json_encode(["status" => false, "message" => mysqli_error($db)]);
    }
    exit;
}

/* ============================================================
    4. ADD REPLY
============================================================ */
if ($action == "add_reply") {
    if (empty($_SESSION['user_id'])) {
        echo json_encode(["status" => false, "message" => "Login required"]);
        exit;
    }
    $user_id = intval($_SESSION['user_id']);
    $topic_id = intval($_POST['topic_id']);
    $reply = clean($_POST['reply_text']);

    $sql = "INSERT INTO forum_replies (topic_id, user_id, reply_text)
            VALUES ($topic_id, $user_id, '$reply')";

    if (mysqli_query($db, $sql)) {
        echo json_encode(["status" => true, "message" => "Reply added"]);
    } else {
        echo json_encode(["status" => false, "message" => mysqli_error($db)]);
    }
    exit;
}

/* ============================================================
    5. DELETE REPLY
============================================================ */
if ($action == "delete_reply") {
    if (empty($_SESSION['user_id'])) {
        echo json_encode(["status" => false, "message" => "Login required"]);
        exit;
    }
    $user_id = intval($_SESSION['user_id']);
    $reply_id = intval($_POST['reply_id']);

    $sql = "DELETE FROM forum_replies WHERE reply_id=$reply_id AND user_id=$user_id";

    if (mysqli_query($db, $sql)) {
        echo json_encode(["status" => true, "message" => "Reply deleted"]);
    } else {
        echo json_encode(["status" => false, "message" => mysqli_error($db)]);
    }
    exit;
}

/* ============================================================
    6. LIKE TOPIC
============================================================ */
if ($action == "like_topic") {
    if (empty($_SESSION['user_id'])) {
        echo json_encode(["status" => false, "message" => "Login required"]);
        exit;
    }
    $user_id = intval($_SESSION['user_id']);
    $topic_id = intval($_POST['topic_id']);

    mysqli_query($db, "DELETE FROM forum_likes WHERE topic_id=$topic_id AND user_id=$user_id");

    $sql = "INSERT INTO forum_likes (topic_id, user_id)
            VALUES ($topic_id, $user_id)";

    mysqli_query($db, $sql);

    echo json_encode(["status" => true, "message" => "Liked"]);
    exit;
}

/* ============================================================
    7. LIKE REPLY
============================================================ */
if ($action == "like_reply") {
    if (empty($_SESSION['user_id'])) {
        echo json_encode(["status" => false, "message" => "Login required"]);
        exit;
    }
    $user_id = intval($_SESSION['user_id']);
    $reply_id = intval($_POST['reply_id']);

    mysqli_query($db, "DELETE FROM forum_likes WHERE reply_id=$reply_id AND user_id=$user_id");

    $sql = "INSERT INTO forum_likes (reply_id, user_id)
            VALUES ($reply_id, $user_id)";

    mysqli_query($db, $sql);

    echo json_encode(["status" => true, "message" => "Liked"]);
    exit;
}

/* ============================================================
    8. LOAD ALL TOPICS
============================================================ */
if ($action == "list_topics") {

    $result = mysqli_query($db, "
        SELECT topic_id, topic_title, topic_slug, views, created_at 
        FROM forum_topics 
        WHERE status='ACTIVE' 
        ORDER BY topic_id DESC
    ");

    $topics = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $topics[] = $row;
    }

    echo json_encode(["status" => true, "topics" => $topics]);
    exit;
}

/* ============================================================
    9. LOAD SINGLE TOPIC + REPLIES
============================================================ */
if ($action == "view_topic") {

    $topic_id = intval($_POST['topic_id']);

    $topic = mysqli_fetch_assoc(
        mysqli_query($db, "SELECT * FROM forum_topics WHERE topic_id=$topic_id")
    );

    $reply_result = mysqli_query($db, "
        SELECT * FROM forum_replies 
        WHERE topic_id=$topic_id 
        ORDER BY reply_id ASC
    ");

    $replies = [];
    while ($row = mysqli_fetch_assoc($reply_result)) {
        $replies[] = $row;
    }

    echo json_encode([
        "status" => true,
        "topic" => $topic,
        "replies" => $replies
    ]);
    exit;
}

if ($action == "list_categories") {

    $result = mysqli_query($db, "
        SELECT * 
        FROM forum_categories 
        WHERE status='ACTIVE' 
        ORDER BY category_id DESC
    ");

    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }

    echo json_encode(["status" => true, "categories" => $categories]);
    exit;
}

echo json_encode(["status" => false, "message" => "Invalid Action"]);
exit;
