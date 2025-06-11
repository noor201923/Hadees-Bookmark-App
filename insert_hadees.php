<?php
session_start();
include '../includes/db.php'; // 🔁 Make sure DB connection path is correct

if (isset($_POST['add_hadees'])) {
    $book_name = trim($_POST['book_name']);
    $hadees_text = trim($_POST['hadees_text']);

    $stmt = $conn->prepare("INSERT INTO ahadees (hadees_book_name, hadees_text, hadees_date_added) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $book_name, $hadees_text);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "✅ Hadees added successfully!";
    } else {
        $_SESSION['error_msg'] = "❌ Failed to add hadees. Please try again.";
    }

    $stmt->close();
    $conn->close();

    header("Location: add_hadees.php"); // Redirect back to form
    exit();
}
?>
