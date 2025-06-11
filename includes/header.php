<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

$current_page = basename($_SERVER['PHP_SELF']); // To highlight current page
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hadees App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="style.css" rel="stylesheet" />
  <style>

    body {
      background-color: #e3f2fd;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
    }

    .navbar-custom {
      background: #1b4d3e;
    }

    .navbar-custom .navbar-brand, 
    .navbar-custom .nav-link {
      color: #c6f2d6;
    }

    .navbar-custom .nav-link:hover {
      color: #a8d5ba;
    }

    /* Sidebar styles and animation */
    .sidebar {
      background: #1b4d3e;
      color: #c6f2d6;
      min-height: 100vh;
      width: 250px;
      position: fixed;
      top: 56px; /* height of navbar */
      left: 0;
      overflow-y: auto;
      padding: 20px;
      box-sizing: border-box;
      transition: transform 0.3s ease-in-out;
      z-index: 1050;
    }

    /* Hide sidebar on small screens by default */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
        box-shadow: 2px 0 10px rgba(0,0,0,0.3);
      }
    }

    /* Sidebar links */
    .sidebar a {
      color: #a8d5ba;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 10px 15px;
      border-radius: 6px;
      margin-bottom: 8px;
      transition: background-color 0.3s, color 0.3s transform 0.3s ease;
      font-weight: 500;
    }

    .sidebar a:hover {
      background-color: #2e7d5a;
      color: #fff;
transform: scale(1.05);
z-index:1;
    }

    .sidebar .nav-link.active {
      background-color: #2e7d5a;
      color: #fff !important;
      font-weight: 700;
    }

    /* Icon margin */
    .sidebar a i {
      margin-right: 12px;
      font-size: 1.2rem;
    }

    /* Toggle button */
    #sidebarToggle {
      display: none;
      position: fixed;
      top: 10px;
      left: 10px;
      background: #1b4d3e;
      border: none;
      color: #c6f2d6;
      font-size: 1.5rem;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      z-index: 1100;
      transition: background-color 0.3s;
    }

    #sidebarToggle:hover {
      background-color: #2e7d5a;
      color: #fff;
    }

    /* Show toggle button only on small screens */
    @media (max-width: 768px) {
      #sidebarToggle {
        display: block;
      }

      /* Add margin to content when sidebar is visible */
      .content {
        padding-left: 20px;
      }
    }

    /* Content area styling */
    .content {
      margin-left: 250px;
      padding: 20px;
     background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
      border-radius: 8px;
      margin-top: 56px; /* navbar height */
      min-height: calc(100vh - 56px);
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      transition: margin-left 0.3s ease-in-out;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0;
      }
    }

  </style>
</head>
<!-- Aapka page ka content yahan -->

<script>
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('show');
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<body>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
  <div class="container-fluid">
    <button id="sidebarToggle" aria-label="Toggle Sidebar"><i class="bi bi-list"></i></button>
    <a class="navbar-brand" href="index.php">Hadees App</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <?php if ($userName): ?>
          <li class="nav-item">
            <a class="nav-link" href="#">Welcome, <?php echo htmlspecialchars($userName); ?></a>
          </li>
          <?php if ($role === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="user_dashboard.php">User Dashboard</a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">Logout <i class="bi bi-box-arrow-right"></i></a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login <i class="bi bi-box-arrow-in-right"></i></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Register <i class="bi bi-pencil-square"></i></a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="d-flex">
  <?php if ($userName): ?>
  <div class="sidebar" id="sidebar">
    <h5 class="text-white mb-4">Menu</h5>
    <ul class="nav nav-pills flex-column mb-auto">
      <?php if ($role === 'admin'): ?>
        <li class="nav-item mb-2">
          <a href="admin_dashboard.php" class="nav-link <?php echo ($current_page == 'admin_dashboard.php') ? 'active' : 'text-light'; ?>">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="manage_users.php" class="nav-link <?php echo ($current_page == 'manage_users.php') ? 'active' : 'text-light'; ?>">
            <i class="bi bi-people me-2"></i>Manage Users
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="manage_hadees.php" class="nav-link <?php echo ($current_page == 'manage_hadees.php') ? 'active' : 'text-light'; ?>">
            <i class="bi bi-journal-bookmark me-2"></i>Manage Hadees
          </a>
        </li>
      <?php else: ?>
        <li class="nav-item mb-2">
          <a href="user_dashboard.php" class="nav-link <?php echo ($current_page == 'user_dashboard.php') ? 'active' : 'text-light'; ?>">
            <i class="bi bi-house-door me-2"></i>Dashboard
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="bookmarks.php" class="nav-link <?php echo ($current_page == 'bookmarks.php') ? 'active' : 'text-light'; ?>">
            <i class="bi bi-bookmark-star me-2"></i>My Bookmarks
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="hadees_list.php" class="nav-link <?php echo ($current_page == 'hadees_list.php') ? 'active' : 'text-light'; ?>">
            <i class="bi bi-book me-2"></i>Browse Hadees
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="profile.php" class="nav-link <?php echo ($current_page == 'profile.php') ? 'active' : 'text-light'; ?>">
            <i class="bi bi-person-circle me-2"></i>Profile
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="settings.php" class="nav-link <?php echo ($current_page == 'settings.php') ? 'active' : 'text-light'; ?>">
            <i class="bi bi-gear me-2"></i>Settings
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
  <?php endif; ?>

    