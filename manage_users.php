<?php
session_start();
include 'includes/db.php';

// Filter Logic
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'joined_desc';

$current_admin_id = $_SESSION['admin_id'] ?? 0;
$where = "role != 'admin' AND user_id != $current_admin_id";

// Filter: active/inactive
if ($filter === 'active') {
    $where .= " AND is_deleted = 0";
} elseif ($filter === 'inactive') {
    $where .= " AND is_deleted = 1";
}

// Search
if (!empty($search)) {
    $escaped_search = mysqli_real_escape_string($conn, $search);
    $where .= " AND (user_name LIKE '%$escaped_search%' OR user_email LIKE '%$escaped_search%')";
}

// Sort
$order = $sort === 'name' ? "user_name ASC" : "user_joined_date DESC";
$query = "SELECT * FROM users WHERE $where ORDER BY $order";
$result = mysqli_query($conn, $query);
?>

<style>
body {
   background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
    animation: fadeIn 0.8s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.main {
    padding: 20px;
}

.user-table {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    animation: fadeIn 0.8s ease-in;
}

.controls-bar {
    display: flex;
    flex-wrap: nowrap;
    gap: 200px;
    margin-bottom: 20px;
    justify-content: flex-start;
    align-items: center;
    animation: fadeIn 1s ease-in;
}

.search-group {
    display: flex;
    gap: 9px;
    height: 40px;
}

.controls-bar select,
.controls-bar input {
    padding: 6px 10px;
    border-radius: 6px;
    min-width: 160px;
    font-size: 0.9rem;
    border: 1.5px solid #1b4d3e;
    color: #155724;
    background-color: #e9f7ef;
    transition: all 0.3s ease;
    appearance: none;
    background-image:
        linear-gradient(45deg, transparent 50%, #155724 50%),
        linear-gradient(135deg, #155724 50%, transparent 50%),
        linear-gradient(to right, #e9f7ef, #e9f7ef);
    background-position:
        calc(100% - 20px) calc(1em + 2px),
        calc(100% - 15px) calc(1em + 2px),
        100% 0;
    background-size: 5px 5px, 5px 5px, 1px 1.5em;
    background-repeat: no-repeat;
    cursor: pointer;
}

.controls-bar select:focus,
.controls-bar input:focus {
    outline: none;
    border-color: #1b4d3e;
    box-shadow: 0 0 6px #1b4d3e88;
    background-color: #d4edda;
    color: #155724;
}

select.form-select option:checked {
    background-color: #d4edda !important;
    color: #155724 !important;
}

select.form-select option:hover {
    background-color: #1b4d3e !important;
    color: white !important;
}

.controls-bar input[type="text"] {
    min-width: 250px;
}

.controls-bar .btn {
    padding: 6px 12px;
    background-color: #1b4d3e;
    border-color: #155724;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.controls-bar .btn:hover {
    background-color: #155724;
    transform: scale(1.05);
}

/* ✅ Table Row Styling */
.table tbody tr:nth-child(even) {
    background-color: #e9f7ef;
}

.table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

.table tbody tr {
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.table tbody tr:hover {
    background-color: #d1f0e2;
    transform: scale(1.005);
}

.table .btn-outline-danger:hover {
    background-color: #f8d7da;
    color: #721c24;
    transform: scale(1.05);
}

.table .btn-outline-success:hover {
    background-color: #d4edda;
    color: #155724;
    transform: scale(1.05);
}

@media (max-width: 576px) {
    .controls-bar {
        flex-direction: column;
        align-items: stretch;
    }

    .controls-bar input[type="text"] {
        min-width: 100%;
    }
}
</style>

<div class="layout-wrapper d-flex">
<?php include 'admin_sidebar.php'; ?>
<div class="main flex-grow-1 p-0">
    <nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
        <span class="navbar-brand text-white fs-4 fw-bold">🧑‍🤝‍🧑 Manage Users</span>
    </nav>

    <div class="container py-5 justify-content-center">
        <div class="user-table table-responsive">

            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filters/Search/Sort -->
            <form class="controls-bar" method="get">
                <select name="filter" onchange="this.form.submit()" class="form-select form-select-sm">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Users</option>
                    <option value="active" <?= $filter === 'active' ? 'selected' : '' ?>>Active Users</option>
                    <option value="inactive" <?= $filter === 'inactive' ? 'selected' : '' ?>>Inactive Users</option>
                </select>

                <div class="search-group">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search name or email..." class="form-control form-control-sm" />
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>

                <select name="sort" onchange="this.form.submit()" class="form-select form-select-sm">
                    <option value="joined_desc" <?= $sort === 'joined_desc' ? 'selected' : '' ?>>Sort by Joined Date</option>
                    <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Sort by Name</option>
                </select>
            </form>

            <!-- User Table -->
            <table class="table table-bordered align-middle text-center">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $user['user_id'] ?></td>
                            <td><?= htmlspecialchars($user['user_name']) ?></td>
                            <td><?= htmlspecialchars($user['user_email']) ?></td>
                            <td>
                                <?= $user['is_deleted'] == 0
                                    ? '<span class="badge bg-success">Active</span>'
                                    : '<span class="badge bg-secondary">Inactive</span>' ?>
                            </td>
                            <td><?= date('d M Y', strtotime($user['user_joined_date'])) ?></td>
                            <td><?= $user['last_login'] ? date('d M Y H:i', strtotime($user['last_login'])) : 'Never' ?></td>
                            <td>
                                <?php if ($user['is_deleted'] == 0): ?>
                                    <a href="soft_delete_user.php?user_id=<?= $user['user_id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Deactivate this user?')">
                                        <i class="bi bi-person-dash"></i> Deactivate
                                    </a>
                                <?php else: ?>
                                    <a href="reactivate_user.php?user_id=<?= $user['user_id'] ?>" class="btn btn-outline-success btn-sm" onclick="return confirm('Reactivate this user?')">
                                        <i class="bi bi-person-check"></i> Reactivate
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    <?php if (mysqli_num_rows($result) == 0): ?>
                        <tr><td colspan="7">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<?php include 'includes/footer.php'; ?>
