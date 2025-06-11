<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = '$user_id' LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $admin_data = mysqli_fetch_assoc($result);
} else {
    $admin_data = ['username' => 'Admin', 'email' => 'Not Available', 'user_id' => $user_id];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Profile | Hadees Bookmark App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
      
    }
    .sidebar {
      height: 100vh;
      background: #343a40;
      color: white;
      padding: 20px;
      position: fixed;
      width: 220px;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px 0;
      transition: 0.3s;
    }
    .sidebar a:hover {
      background-color: #495057;
      padding-left: 10px;
    }
    .profile-card {
background: linear-gradient(135deg, #e8f5e9, #ffffff);
      animation: slideIn 0.6s ease-out;
    }
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .profile-img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #198754;
    }
    footer {
      text-align: center;
      padding: 10px;
      margin-top: 40px;
      background-color: #343a40;
      color: white;
    }
  </style>
</head>
<body>
<div class="layout-wrapper d-flex">
<?php include 'admin_sidebar.php'; ?>
<div class="main flex-grow-1 p-0 ">
 <nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
    <span class="navbar-brand text-white fs-4 fw-bold">⚙️ Admin Settings Panel</span>
</nav>

<div class="container py-5">
  <div class="row mt-5 justify-content-center">
    <div class="col-md-8 offset-md-2">
      <div class="card profile-card shadow-lg p-4" style="width:750px" ;>
        <div class="text-center">
          <img src="<?= $admin_data['profile_image'] ? 'uploads/' . $admin_data['profile_image'] : 'includes/default.png' ?>" class="profile-img mb-3" alt="Profile Picture">
          <h3 class="mt-2"><?= htmlspecialchars($admin_data['user_name']) ?></h3>
          <p class="text-muted"><?= htmlspecialchars($admin_data['user_email']) ?></p>
        </div>
        <hr>
        <div class="mt-3">
          <p><strong><i class="bi bi-person-badge me-2"></i>Admin ID:</strong> <?= htmlspecialchars($admin_data['user_id']) ?></p>
          <p><strong><i class="bi bi-envelope me-2"></i>Email:</strong> <?= htmlspecialchars($admin_data['user_email']) ?></p>
          <a href="admin_edit_profile.php" class="btn btn-outline-success mt-3"><i class="bi bi-pencil-square me-1"></i>Edit Profile</a>
        </div>
      </div>
    </div>
  </div>
</div>
</main>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
