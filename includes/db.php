<?php
// Database connection file
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hadees_app";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
