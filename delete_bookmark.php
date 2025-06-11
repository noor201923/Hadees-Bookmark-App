<?php
session_start();
include("includes/db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['bookmark_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$bookmark_id = filter_input(INPUT_POST, 'bookmark_id', FILTER_VALIDATE_INT);

if (!$bookmark_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Bookmark ID']);
    exit();
}

// Soft delete: set is_deleted = 1, update updated_at
$sql = "UPDATE bookmarks SET is_deleted = 1, updated_at = NOW() WHERE bookmark_id = ? AND user_id = ? AND is_deleted = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookmark_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    // Add deletion notification
    $notif_sql = "INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'user')";
    $notif_stmt = $conn->prepare($notif_sql);
    $message = "Your Bookmark was successfully deleted.";
    $notif_stmt->bind_param("is", $user_id, $message);
    $notif_stmt->execute();
    $notif_stmt->close();
    
    echo json_encode(['status' => 'success', 'bookmark_id' => $bookmark_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Bookmark not found or already deleted']);
}

$stmt->close();
?>
