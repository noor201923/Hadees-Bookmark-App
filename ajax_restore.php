<?php
session_start();
include("includes/db.php");

if (isset($_POST['bookmark_id'], $_SESSION['user_id'])) {
    $bookmark_id = (int)$_POST['bookmark_id'];
    $user_id = (int)$_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE bookmarks SET is_deleted = 0 WHERE bookmark_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $bookmark_id, $user_id);
    $stmt->execute();

    echo json_encode(['success' => true]);
}
?>
