<?php
session_start();
include("includes/db.php");
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check required inputs
if (!isset($_SESSION['user_id']) || !isset($_POST['bookmark_id']) || !isset($_POST['note'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$bookmark_id = (int) $_POST['bookmark_id'];
$note = trim($_POST['note']);

// Prepare update statement
$update_sql = "UPDATE bookmarks SET note = ?, updated_at = NOW() WHERE bookmark_id = ? AND user_id = ? AND is_deleted = 0";
$stmt = $conn->prepare($update_sql);

if (!$stmt) {
    // Prepare failed
    echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("sii", $note, $bookmark_id, $user_id);

if (!$stmt->execute()) {
    // Execution failed
    echo json_encode(['status' => 'error', 'message' => 'Database execute failed: ' . $stmt->error]);
    $stmt->close();
    exit();
}

if ($stmt->affected_rows > 0) {
    // Insert notification
    $notif_sql = "INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'user')";
    $notif_stmt = $conn->prepare($notif_sql);
    if ($notif_stmt) {
        $message = "You updated a note for bookmark ID $bookmark_id.";
        $notif_stmt->bind_param("is", $user_id, $message);
        $notif_stmt->execute();
        $notif_stmt->close();
    }
    echo json_encode(['status' => 'success']);
} else {
    // No rows updated - maybe invalid bookmark or no change
    echo json_encode(['status' => 'error', 'message' => 'Nothing updated or invalid bookmark']);
}

$stmt->close();
?>
