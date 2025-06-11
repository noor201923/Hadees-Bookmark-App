<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

include 'includes/db.php';

header('Content-Type: application/json');

$data = [
    'totalUsers' => $conn->query("SELECT COUNT(*) as count FROM users WHERE is_deleted = 0")->fetch_assoc()['count'],
    'activeUsers' => $conn->query("SELECT COUNT(*) as count FROM users WHERE is_deleted = 0 AND last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count'],
    'inactiveUsers' => $conn->query("SELECT COUNT(*) as count FROM users WHERE is_deleted = 0 AND (last_login < DATE_SUB(NOW(), INTERVAL 30 DAY) OR last_login IS NULL)")->fetch_assoc()['count'],
    'totalHadees' => $conn->query("SELECT COUNT(*) as count FROM hadees WHERE is_deleted = 0")->fetch_assoc()['count'],
    'totalBookmarks' => $conn->query("SELECT COUNT(*) as count FROM bookmarks")->fetch_assoc()['count'],
    'recentHadees' => $conn->query("SELECT COUNT(*) as count FROM hadees WHERE is_deleted = 0 AND hadees_date_added >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'],
    'recentActiveUsers' => $conn->query("SELECT COUNT(*) as count FROM users WHERE is_deleted = 0 AND last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count']
];

echo json_encode($data);
?>
