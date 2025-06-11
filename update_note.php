<?php
session_start();
include("includes/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $bookmark_id = (int)$_POST['bookmark_id'];
    $note = trim($_POST['note']);

    $stmt = $conn->prepare("UPDATE bookmarks SET note = ? WHERE bookmark_id = ? AND user_id = ?");
    $stmt->bind_param("sii", $note, $bookmark_id, $user_id);
    $success = $stmt->execute();

    echo json_encode(['success' => $success]);
}
