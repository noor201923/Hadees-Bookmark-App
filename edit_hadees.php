<?php
session_start();
include 'includes/db.php';

if (!isset($_GET['hadees_id'])) {
    $_SESSION['error_msg'] = "Invalid request.";
    header("Location: manage_hadees.php");
    exit();
}

$hadees_id = intval($_GET['hadees_id']);
$query = $conn->prepare("SELECT * FROM hadees WHERE hadees_id = ?");
$query->bind_param("i", $hadees_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_msg'] = "Hadees not found.";
    header("Location: manage_hadees.php");
    exit();
}

$hadees = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_text = trim($_POST['hadees_text']);
    $new_book = trim($_POST['hadees_book_name']);

    $update = $conn->prepare("UPDATE hadees SET hadees_text = ?, hadees_book_name = ? WHERE hadees_id = ?");
    $update->bind_param("ssi", $new_text, $new_book, $hadees_id);
    if ($update->execute()) {
        $_SESSION['success_msg'] = "Hadees updated successfully.";
    } else {
        $_SESSION['error_msg'] = "Update failed.";
    }
    header("Location: manage_hadees.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Hadees</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="style.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #e0f7f4, #f3f8fa);
      font-family: 'Segoe UI', sans-serif;
    }

    .main {
      padding: 20px;
    }

    .form-wrapper {
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
      max-width: 700px;
      margin: auto;
      animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
      from {
        transform: translateY(30px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .form-label {
      font-weight: 600;
      color: #166534;
    }

    textarea.form-control, input.form-control {
      border: 1px solid #cce3dc;
      transition: border-color 0.3s ease;
    }

    textarea.form-control:focus, input.form-control:focus {
      border-color: #14532d;
      box-shadow: 0 0 5px rgba(22, 101, 52, 0.3);
    }

    .btn-success {
      background-color: #166534;
      border: none;
      font-weight: 500;
    }

    .btn-success:hover {
      background-color: #14532d;
    }

    .btn-secondary:hover {
      background-color: #374151;
      color: #fff;
    }

    nav.navbar {
      border-radius: 0 0 12px 12px;
      box-shadow: 0 5px 12px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>

<div class="layout-wrapper d-flex">
  <?php include 'admin_sidebar.php'; ?>
  <div class="main flex-grow-1 p-0">

    <nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
      <span class="navbar-brand text-white fw-bold fs-4">📝 Edit Hadees</span>
    </nav>

    <form method="POST" class="form-wrapper">
      <div class="mb-4">
        <label for="hadees_book_name" class="form-label">📘 Book Name</label>
        <input type="text" name="hadees_book_name" id="hadees_book_name" class="form-control" value="<?= htmlspecialchars($hadees['hadees_book_name']) ?>" required>
      </div>

      <div class="mb-4">
        <label for="hadees_text" class="form-label">📜 Hadees Text</label>
        <textarea name="hadees_text" id="hadees_text" class="form-control" rows="6" required><?= htmlspecialchars($hadees['hadees_text']) ?></textarea>
      </div>

      <div class="d-flex justify-content-start gap-3">
        <button type="submit" class="btn btn-success px-4">
          <i class="bi bi-check-circle me-1"></i> Update
        </button>
        <a href="manage_hadees.php" class="btn btn-secondary px-4">
          <i class="bi bi-x-circle me-1"></i> Cancel
        </a>
      </div>
    </form>

  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
