<?php
session_start();
include("includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['hadees_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$hadees_id = (int) $_POST['hadees_id'];

// Check if bookmark exists and is not deleted
$check_sql = "SELECT bookmark_id FROM bookmarks WHERE user_id = $user_id AND hadees_id = $hadees_id AND is_deleted = 0";
$check_result = mysqli_query($conn, $check_sql);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    // Soft delete (unbookmark)
    $row = mysqli_fetch_assoc($check_result);
    $bookmark_id = $row['bookmark_id'];
    $delete_sql = "UPDATE bookmarks SET is_deleted = 1 WHERE bookmark_id = $bookmark_id";

    if (mysqli_query($conn, $delete_sql)) {
        echo json_encode(['status' => 'unbookmarked']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to unbookmark']);
    }

} else {
    // Insert new bookmark
    $insert_sql = "INSERT INTO bookmarks (user_id, hadees_id, bookmarked_at, is_deleted) VALUES ($user_id, $hadees_id, NOW(), 0)";
    
    if (mysqli_query($conn, $insert_sql)) {
        echo json_encode(['status' => 'bookmarked']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to bookmark']);
    }
}
?>
