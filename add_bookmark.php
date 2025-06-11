<?php
session_start();
include('includes/db.php');


// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate and sanitize input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hadees_id'])) {
    $user_id = $_SESSION['user_id'];
    $hadees_id = intval($_POST['hadees_id']); // secure conversion
    $bookmark_note = isset($_POST['bookmark_note']) ? mysqli_real_escape_string($conn, $_POST['bookmark_note']) : null;

    // Check if already bookmarked and not soft-deleted
    $check_query = "SELECT * FROM bookmarks WHERE user_id = $user_id AND hadees_id = $hadees_id AND is_deleted = 0";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['bookmark_msg'] = "<div class='alert alert-warning'>⚠️ This Hadees is already bookmarked.</div>";
    } else {
        // Insert new bookmark
        $insert_query = "INSERT INTO bookmarks (user_id, hadees_id, note) 
                         VALUES ($user_id, $hadees_id, '$note')";
        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['bookmark_msg'] = "<div class='alert alert-success'>✅ Hadees bookmarked successfully!</div>";
        } else {
            $_SESSION['bookmark_msg'] = "<div class='alert alert-danger'>❌ Failed to bookmark. Please try again.</div>";
        }
    }
} else {
    $_SESSION['bookmark_msg'] = "<div class='alert alert-danger'>❌ Invalid request.</div>";
}

// Redirect back to Hadees list or wherever appropriate
header("Location: add_bookmark.php");
exit();
?>
