<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hadees_text = trim($_POST['hadees_text'] ?? '');
    $hadees_book_name = trim($_POST['hadees_book_name'] ?? '');

    if (empty($hadees_text) || empty($hadees_book_name)) {
        $_SESSION['error_msg'] = "Both Hadees text and Book name are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO hadees (hadees_text, hadees_book_name, hadees_date_added) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $hadees_text, $hadees_book_name);
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Hadees added successfully.";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['error_msg'] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add New Hadees - Admin</title>

    <!-- Bootstrap CSS (ensure you have this in your project) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            margin: 0;
        }

        .navbar-custom {
            background-color: #1b4d3e;
        }

        .toast-container {
            z-index: 1050;
            min-width: 300px;
        }

        .hadees-form {
background: linear-gradient(135deg, #e8f5e9, #ffffff);
            max-width: 600px;
            margin: 30px auto;
            animation: fadeIn 1s ease-in-out;
        }

        .hadees-form .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn i {
            vertical-align: middle;
        }

        /* Make sidebar and main content full height */
        
    </style>
</head>
<body>



<div class="layout-wrapper d-flex fade-in">
    <!-- Sidebar -->
    <?php include 'admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-0">

        <!-- Navbar -->
        <nav class="navbar navbar-dark px-3 mb-4  d-flex justify-content-center" style="background-color: #1b4d3e;">
            <span class="navbar-brand text-white fw-bold fs-4">
                <i class="bi bi-journal-plus me-2"></i> Add New Hadees
            </span>
        </nav>

        <!-- Toasts -->
        <?php if (isset($_SESSION['success_msg']) || isset($_SESSION['error_msg'])): ?>
            <div class="position-fixed top-0 end-0 p-3 toast-container">
                <div class="toast show align-items-center text-white <?= isset($_SESSION['success_msg']) ? 'bg-success' : 'bg-danger' ?>" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?= htmlspecialchars($_SESSION['success_msg'] ?? $_SESSION['error_msg']) ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['success_msg'], $_SESSION['error_msg']); ?>
        <?php endif; ?>

        <!-- Add Hadees Form -->
        <form action="" method="POST" class="shadow p-4 bg-white rounded hadees-form">
            <div class="mb-3">
                <label for="hadees_text" class="form-label">📜 Hadees Text</label>
                <textarea id="hadees_text" name="hadees_text" class="form-control" rows="5" placeholder="Enter the Hadees here..." required></textarea>
            </div>

            <div class="mb-3">
                <label for="hadees_book_name" class="form-label">📘 Book Name</label>
                <input type="text" id="hadees_book_name" name="hadees_book_name" class="form-control" placeholder="Name of the Book" required>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle-fill me-1"></i> Add Hadees
            </button>
            <a href="admin_dashboard.php" class="btn btn-outline-secondary ms-2">
                <i class="bi bi-arrow-left-circle me-1"></i> Cancel
            </a>
        </form>

    </div>
</div>
<?php include 'includes/footer.php'; ?>
<!-- Bootstrap JS (for toast dismiss and other features) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
