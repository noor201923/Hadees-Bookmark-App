<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json'); // Ensure JSON response

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate hadees_id
    $hadees_id = isset($_POST['hadees_id']) ? intval($_POST['hadees_id']) : 0;

    if ($hadees_id > 0) {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE hadees SET is_deleted = 1 WHERE hadees_id = ?");
        $stmt->bind_param("i", $hadees_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
