<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

// Delete hadees if requested
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Start transaction for atomicity
    $conn->begin_transaction();

    try {
        // 1. Delete all bookmarks referencing this hadees_id
        $stmt = $conn->prepare("DELETE FROM bookmarks WHERE hadees_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // 2. Delete the hadees entry itself
        $stmt = $conn->prepare("DELETE FROM hadees WHERE hadees_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        $_SESSION['success_msg'] = "Hadees and related bookmarks deleted successfully.";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['success_msg'] = "Error deleting Hadees: " . $e->getMessage();
    }

    header("Location: admin_dashboard.php");
    exit();
}

$result = $conn->query("SELECT * FROM hadees ORDER BY hadees_date_added DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
        }
        .table-hover tbody tr:hover {
            background-color: #d1f0e2;
            transition: background-color 0.3s ease;
        }
        .fade-in {
            animation: fadeIn 0.6s ease forwards;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<div class="layout-wrapper d-flex fade-in">
    <?php include 'admin_sidebar.php'; ?>

    <div class="main flex-grow-1 p-0">
        <nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
            <span class="navbar-brand text-white fw-bold fs-4">📋 Welcome, Admin <?= htmlspecialchars($_SESSION['user_name']); ?>!</span>
        </nav>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                <?= htmlspecialchars($_SESSION['success_msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-success fw-bold">🕌 All Hadees Entries</h3>
                <a href="add_hadees.php" class="btn btn-success shadow">
                    <i class="bi bi-plus-circle-fill me-1"></i> Add Hadees
                </a>
            </div>

            <div class="table-responsive shadow-sm">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Hadees</th>
                            <th>Book</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <tr class="fade-in">
                                <td><?= $count++; ?></td>
                                <td><?= htmlspecialchars($row['hadees_text']); ?></td>
                                <td><?= htmlspecialchars($row['hadees_book_name']); ?></td>
                                <td><i class="bi bi-calendar-event"></i> <?= $row['hadees_date_added']; ?></td>
                                <td>
                                    <a href="?delete=<?= $row['hadees_id']; ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this Hadees?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
