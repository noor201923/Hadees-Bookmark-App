<?php
session_start();
include("includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['bookmark_id']) || !isset($_POST['note'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$bookmark_id = (int) $_POST['bookmark_id'];
$note = trim($_POST['note']);

// Update note for the user's bookmark only
$sql = "UPDATE bookmarks SET note = ? WHERE bookmark_id = ? AND user_id = ? AND is_deleted = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $note, $bookmark_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'note' => htmlspecialchars($note)]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not update note']);
}
