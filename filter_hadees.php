<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger text-center">Please login to view Hadees.</div>';
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$book = isset($_GET['book']) ? trim($_GET['book']) : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) && $_GET['sort'] === 'oldest' ? 'ASC' : 'DESC';

// Get bookmarked hadees
$bookmarkedHadees = [];
$bmQuery = "SELECT hadees_id FROM bookmarks WHERE user_id = $user_id AND is_deleted = 0";
$bmResult = $conn->query($bmQuery);
if ($bmResult) {
    while ($bmRow = $bmResult->fetch_assoc()) {
        $bookmarkedHadees[] = $bmRow['hadees_id'];
    }
}

// Build WHERE conditions
$conditions = [];
if ($book !== 'all') {
    $book = $conn->real_escape_string($book);
    $conditions[] = "hadees_book_name = '$book'";
}
if ($search !== '') {
    $search = $conn->real_escape_string($search);
    $conditions[] = "(hadees_text LIKE '%$search%' OR hadees_book_name LIKE '%$search%')";
}
$whereSql = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

// Final Query with Sort
$sql = "SELECT * FROM hadees $whereSql ORDER BY hadees_date_added $sort";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()):
        $isBookmarked = in_array($row['hadees_id'], $bookmarkedHadees);
        ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo htmlspecialchars($row['hadees_book_name']); ?>
                    <a href="#"
                       class="bookmark-btn"
                       data-hadees-id="<?php echo $row['hadees_id']; ?>"
                       title="<?php echo $isBookmarked ? 'Remove Bookmark' : 'Bookmark this Hadees'; ?>">
                        <i class="bi <?php echo $isBookmarked ? 'bi-bookmark-fill bookmarked' : 'bi-bookmark'; ?>"></i>
                    </a>
                </h5>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['hadees_text'])); ?></p>
                <div class="hadees-meta">
                    <i class="bi bi-calendar-check"></i>
                    <?php echo date("F d, Y", strtotime($row['hadees_date_added'])); ?>
                </div>
            </div>
        </div>
    <?php endwhile;
} else {
    echo '<div class="alert alert-info text-center">No Hadees found for this filter.</div>';
}
?>
