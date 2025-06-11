<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

include 'includes/db.php';

// Fetch notifications for the logged-in user
$sql = "SELECT message, created_at FROM notifications WHERE user_id = ? AND type = 'user' ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Your Notifications - Hadees App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f9fafb;
    }
    .notif-container {
      max-width: 700px;
width: 50%;
      margin: 50px auto;
      background: white;
      padding: 35px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
animation: fadeInUp 0.7s ease forwards;
position: relative;
      overflow: hidden;
    }
main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .notif-title {
      font-size: 1.8rem;
      font-weight: bold;
      color: #2d4739;
      margin-bottom: 25px;
    }
    .notif-item {
      background-color: #f1f5f9;
      border-left: 5px solid #2d4739;
      padding: 15px 20px;
      margin-bottom: 15px;
      border-radius: 10px;
    }
    .notif-item small {
      color: #6c757d;
    }
@keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(25px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>
<main class="content">
<div class="notif-container">
  <h2 class="notif-title"><i class="bi bi-bell-fill"></i> Your Notifications</h2>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="notif-item">
        <div><?= htmlspecialchars($row['message']) ?></div>
        <small><i class="bi bi-clock"></i> <?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></small>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-muted">No notifications found.</p>
  <?php endif; ?>
</div>
</main>
<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
