<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hadees_app");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_id = $_SESSION['user_id'];

// Mark as read
if (isset($_GET['mark_read'])) {
    $notif_id = intval($_GET['mark_read']);
    $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $notif_id ");
    header("Location: admin_notifications.php");
    exit();
}

// Delete notification
if (isset($_GET['delete'])) {
    $notif_id = intval($_GET['delete']);
    $conn->query("DELETE FROM notifications WHERE id = $notif_id");
    header("Location: admin_notifications.php");
    exit();
}

$admin_id = $_SESSION['user_id']; // Admin's own ID
$sql = "SELECT n.*, u.user_name 
        FROM notifications n 
        JOIN users u ON n.user_id = u.user_id 
        WHERE n.user_id != ? 
        ORDER BY n.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();



?>
<div class="layout-wrapper d-flex">
<?php include 'admin_sidebar.php'; ?>
<div class="main flex-grow-1 p-0 ">
 <nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
    <span class="navbar-brand text-white fs-4 fw-bold">⚙️ Admin Settings Panel</span>
</nav>

<div class="container py-5 justify-content-center">
  <div class="card shadow-lg">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">🔔 Notifications</h5>
    </div>
    <div class="card-body">
      <?php if ($result->num_rows > 0): ?>
        <ul class="list-group list-group-flush">
          <?php while ($row = $result->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between align-items-start <?php echo $row['is_read'] ? 'text-muted' : ''; ?>">
              <div class="ms-2 me-auto">
                <div class="fw-bold">
                  <i class="bi bi-info-circle text-primary"></i> <?php echo htmlspecialchars($row['title']); ?>
                </div>
                <?php echo htmlspecialchars($row['message']); ?>
                <br>
                <small class="text-secondary">
                  <i class="bi bi-clock"></i> <?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?>
                </small>
              </div>
              <div class="btn-group btn-group-sm">
                <?php if (!$row['is_read']): ?>
                  <a class="btn btn-outline-success" title="Mark as Read" href="?mark_read=<?= $row['id'] ?>">
<i class="bi bi-check2-circle"></i>
  </a>

                <?php endif; ?>
                <a class="btn btn-outline-danger" title="Delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this notification?');">
    <i class="bi bi-trash"></i>
</a>

              </div>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <div class="alert alert-info text-center">
          <i class="bi bi-bell-slash"></i> No notifications yet.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</div>
<?php include("includes/footer.php"); ?>

<script>
  const items = document.querySelectorAll('.list-group-item');
  items.forEach((item, index) => {
    item.style.animation = `fadeIn 0.5s ease ${index * 0.1}s forwards`;
    item.style.opacity = 0;
  });

  // Fade-in animation
  const style = document.createElement('style');
  style.innerHTML = `
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  `;
  document.head.appendChild(style);
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
