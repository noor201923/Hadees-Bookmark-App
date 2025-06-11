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

// Actual Delete Logic
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_delete'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();

    session_destroy();
    header("Location: login.php"); // Optional page after delete
    exit();
}
?>

<!-- HTML START -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Admin Account</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
     
    }
    .card {
background: linear-gradient(135deg, #e8f5e9, #ffffff);
      animation: fadeIn 0.7s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .btn-danger {
      background-color: #c82333;
      border-color: #bd2130;
    }
  </style>
</head>
<body>

<div class="layout-wrapper d-flex">
  <?php include 'admin_sidebar.php'; ?>

  <div class="main flex-grow-1 p-0">
    <nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
      <span class="navbar-brand text-white fs-4 fw-bold">⚠️ Delete Account</span>
    </nav>

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
      <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%;">
        <h4 class="mb-3 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Danger Zone</h4>
        <p class="text-secondary">
          Deleting your admin account is <strong>permanent</strong> and cannot be undone. All data linked to your account will be lost.
        </p>
        
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete your admin account permanently?');">
          <button type="submit" name="confirm_delete" class="btn btn-danger w-100 mt-3">
            <i class="bi bi-trash-fill me-1"></i> Delete My Admin Account
          </button>
        </form>
      </div>
    </div>
</div>
</div>
    <?php include 'includes/footer.php'; ?>
  

</body>
</html>
