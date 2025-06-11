<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

// Fetch stats data
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_deleted = 0")->fetch_assoc()['count'];
$activeUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_deleted = 0 AND last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count'];
$inactiveUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_deleted = 0 AND (last_login < DATE_SUB(NOW(), INTERVAL 30 DAY) OR last_login IS NULL)")->fetch_assoc()['count'];
$totalHadees = $conn->query("SELECT COUNT(*) as count FROM hadees WHERE is_deleted = 0")->fetch_assoc()['count'];
$totalBookmarks = $conn->query("SELECT COUNT(*) as count FROM bookmarks")->fetch_assoc()['count'];
$recentHadees = $conn->query("SELECT COUNT(*) as count FROM hadees WHERE is_deleted = 0 AND hadees_date_added >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];
$recentActiveUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_deleted = 0 AND last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Cards style */
    .stat-card {
        border-radius: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: linear-gradient(to bottom right, #e0f7f1, #f8fdfb);
        border: none;
    }

    .stat-card:hover {
        transform: scale(1.03);
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        font-size: 2.5rem;
        color: #1b4d3e;
    }

    .stat-title {
        font-size: 1.25rem;
        color: #555;
        margin-top: 10px;
    }

    .stat-value {
        font-size: 2.2rem;
        font-weight: bold;
        color: #1b4d3e;
    }

    .fade-in {
        animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Spacing between cards and toggle buttons */
    .container > .row.g-4 {
        margin-bottom: 2rem;
    }

    .toggle-buttons {
        margin-bottom: 2rem;
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .toggle-buttons button {
        background-color: #1b4d3e;
        border: none;
        padding: 10px 20px;
        color: white;
        border-radius: 30px;
        cursor: pointer;
        font-weight: 600;
        box-shadow: 0 0 10px rgba(27, 77, 62, 0.5);
        transition: background-color 0.3s ease;
    }

    .toggle-buttons button.active,
    .toggle-buttons button:hover {
        background-color: #145230;
        box-shadow: 0 0 15px #145230;
    }

    /* Chart containers with fixed height for better sizing */
    #userChartContainer,
    #hadeesChartContainer {
        display: none;
        max-width: 440px;
        margin: 0 auto 3rem auto;
        height: 340px;
    }

    /* Show active chart container */
    #userChartContainer.active,
    #hadeesChartContainer.active {
        display: block;
    }

    /* Canvas full size */
    canvas {
        width: 100% !important;
        height: 100% !important;
    }
</style>

<div class="layout-wrapper d-flex">
    <?php include 'admin_sidebar.php'; ?>

    <div class="main flex-grow-1 p-0">
        <nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
            <span class="navbar-brand text-white fs-4 fw-bold">📊 System Statistics Dashboard</span>
        </nav>

        <div class="container">
            <div class="row g-4 fade-in">
                <?php
                $cards = [
                    ['icon' => 'bi-people-fill', 'value' => $totalUsers, 'label' => 'Total Registered Users'],
                    ['icon' => 'bi-person-check-fill', 'value' => $activeUsers, 'label' => 'Active Users (30 Days)'],
                    ['icon' => 'bi-person-x-fill', 'value' => $inactiveUsers, 'label' => 'Inactive Users (30 Days)'],
                    ['icon' => 'bi-book-fill', 'value' => $totalHadees, 'label' => 'Total Hadees'],
                    ['icon' => 'bi-bookmark-star-fill', 'value' => $totalBookmarks, 'label' => 'Total Bookmarks'],
                    ['icon' => 'bi-clock-history', 'value' => $recentHadees, 'label' => 'Recently Added Hadees (7 Days)'],
                    ['icon' => 'bi-person-heart', 'value' => $recentActiveUsers, 'label' => 'Recently Active Users (7 Days)'],
                ];

                foreach ($cards as $card) {
                    echo '
                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm text-center p-3">
                            <div class="stat-icon mb-2"><i class="bi ' . $card['icon'] . '"></i></div>
                            <div class="stat-value">' . $card['value'] . '</div>
                            <div class="stat-title">' . $card['label'] . '</div>
                        </div>
                    </div>';
                }
                ?>
            </div>

            <!-- Toggle buttons for charts -->
            <div class="toggle-buttons">
                <button id="btnUserStatus" class="active">User Activity Chart</button>
                <button id="btnHadeesStatus">Hadees Distribution Chart</button>
            </div>

            <!-- Chart containers -->
            <div id="userChartContainer" class="active">
                <canvas id="userStatusChart"></canvas>
            </div>
            <div id="hadeesChartContainer">
                <canvas id="hadeesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // Chart instances global
    let userStatusChart, hadeesChart;

    function createCharts() {
        const userStatusCtx = document.getElementById('userStatusChart').getContext('2d');
        const hadeesCtx = document.getElementById('hadeesChart').getContext('2d');

        userStatusChart = new Chart(userStatusCtx, {
            type: 'bar',
            data: {
                labels: ['Active Users', 'Inactive Users'],
                datasets: [{
                    label: 'User Activity',
                    data: [<?php echo $activeUsers; ?>, <?php echo $inactiveUsers; ?>],
                    backgroundColor: ['#1b4d3e', '#a3c9a8']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        hadeesChart = new Chart(hadeesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Recently Added', 'Older Hadees'],
                datasets: [{
                    label: 'Hadees Distribution',
                    data: [<?php echo $recentHadees; ?>, <?php echo ($totalHadees - $recentHadees); ?>],
                    backgroundColor: ['#1b4d3e', '#d4f4e0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    // Animate chart redraw on toggle button click
    function animateChart(chart) {
        chart.reset();
        chart.update();
    }

    // Toggle logic
    const btnUserStatus = document.getElementById('btnUserStatus');
    const btnHadeesStatus = document.getElementById('btnHadeesStatus');
    const userChartContainer = document.getElementById('userChartContainer');
    const hadeesChartContainer = document.getElementById('hadeesChartContainer');

    btnUserStatus.addEventListener('click', () => {
        if (!btnUserStatus.classList.contains('active')) {
            btnUserStatus.classList.add('active');
            btnHadeesStatus.classList.remove('active');

            userChartContainer.classList.add('active');
            hadeesChartContainer.classList.remove('active');

            animateChart(userStatusChart);
        }
    });

    btnHadeesStatus.addEventListener('click', () => {
        if (!btnHadeesStatus.classList.contains('active')) {
            btnHadeesStatus.classList.add('active');
            btnUserStatus.classList.remove('active');

            hadeesChartContainer.classList.add('active');
            userChartContainer.classList.remove('active');

            animateChart(hadeesChart);
        }
    });

    // Initialize charts on page load
    window.onload = () => {
        createCharts();
    };
</script>

<?php include 'includes/footer.php'; ?>
