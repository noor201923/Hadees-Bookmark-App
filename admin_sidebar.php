<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

<style>
  .admin-sidebar {
    width: 240px;
    height: 100vh;
    background-color: #1b4d3e; /* Dark green background */
    color: #c6f2d6;            /* Light green text */
    padding: 20px 15px;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    flex-shrink: 0;
  }

  .admin-sidebar h4 {
    font-weight: bold;
    margin-bottom: 30px;
    text-align: center;
    color: white; /* lighter green heading */
  }

  .admin-sidebar a {
    display: flex;
    align-items: center;
    color: white;          /* lighter green for links */
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
  }

  .admin-sidebar a i {
    margin-right: 10px;
    font-size: 1.2rem;
  }

  .admin-sidebar a:hover,
  .admin-sidebar a.active {
    background-color: #2e7d5a;  /* lighter green background for hover/active */
    color: #ffffff !important;  /* white text on active/hover */
    transform: translateX(5px);
  }

  /* Responsive for smaller screens */
  @media (max-width: 768px) {
    .admin-sidebar {
      width: 100%;
      height: auto;
      position: static;
    }
  }
</style>

<div class="admin-sidebar">
  <h4><i class="bi bi-speedometer2"></i> Admin Panel</h4>

  <a href="admin_profile.php" class="<?= $currentPage == 'admin_profile.php' ? 'active' : '' ?>">
    <i class="bi bi-person-circle"></i> Profile
  </a>
  <a href="admin_dashboard.php" class="<?= $currentPage == 'admin_dashboard.php' ? 'active' : '' ?>">
    <i class="bi bi-house-door-fill"></i> Dashboard
  </a>
  <a href="manage_users.php" class="<?= $currentPage == 'manage_users.php' ? 'active' : '' ?>">
    <i class="bi bi-people-fill"></i> Manage Users
  </a>
  <a href="manage_hadees.php" class="<?= $currentPage == 'manage_hadees.php' ? 'active' : '' ?>">
    <i class="bi bi-journal-text"></i> Manage Ahadees
  </a>
  <a href="system_stats.php" class="<?= $currentPage == 'system_stats.php' ? 'active' : '' ?>">
    <i class="bi bi-bar-chart-line"></i> System Stats
  </a>
  <a href="admin_settings.php" class="<?= $currentPage == 'admin_settings.php' ? 'active' : '' ?>">
    <i class="bi bi-gear-fill"></i> Settings
  </a>
  <a href="logout.php">
    <i class="bi bi-box-arrow-right"></i> Logout
  </a>
</div> 