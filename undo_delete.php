<?php
session_start();
include("includes/db.php");

$bookmark_id = (int)$_POST['bookmark_id'];
$user_id = $_SESSION['user_id'] ?? 0;

$stmt = $conn->prepare("UPDATE bookmarks SET is_deleted = 0 WHERE bookmark_id = ? AND user_id = ?");
$stmt->bind_param("ii", $bookmark_id, $user_id);
$success = $stmt->execute();

echo json_encode(['success' => $success]);
