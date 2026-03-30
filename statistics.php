<?php
$pageTitle = "Thống Kê";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

// Thống kê cơ bản
$total_members = $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count'];

$today = date('Y-m-d');
$active_members = $conn->query("
    SELECT COUNT(DISTINCT m.id) as count 
    FROM members m 
    JOIN registrations r ON m.id = r.member_id 
    WHERE r.status = 'active' AND r.end_date >= '$today'
")->fetch_assoc()['count'];

$expired_members = $conn->query("
    SELECT COUNT(DISTINCT m.id) as count 
    FROM members m 
    JOIN registrations r ON m.id = r.member_id 
    WHERE r.end_date < '$today'
")->fetch_assoc()['count'];

$today_checkins = $conn->query("
    SELECT COUNT(*) as count FROM check_logs 
    WHERE log_date = '$today' AND check_in IS NOT NULL
")->fetch_assoc()['count'];

$today_checkouts = $conn->query("
    SELECT COUNT(*) as count FROM check_logs 
    WHERE log_date = '$today' AND check_out IS NOT NULL
")->fetch_assoc()['count'];

// Thống kê chi tiết
$month_start = date('Y-m-01');
$month_checkins = $conn->query("
    SELECT COUNT(*) as count FROM check_logs 
    WHERE log_date >= '$month_start' AND check_in IS NOT NULL
")->fetch_assoc()['count'];

// Top gói tập phổ biến
$popular_packages = $conn->query("
    SELECT p.name, COUNT(r.id) as count
    FROM packages p
    LEFT JOIN registrations r ON p.id = r.package_id AND r.status = 'active'
    GROUP BY p.id
    ORDER BY count DESC
    LIMIT 5
");

// Thống kê theo ngày trong tuần
$week_stats = $conn->query("
    SELECT 
        DATE_FORMAT(log_date, '%d/%m') as date,
        COUNT(*) as count
    FROM check_logs
    WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    AND check_in IS NOT NULL
    GROUP BY log_date
    ORDER BY log_date ASC
");
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/sidebar.php'; ?>

        <div class="col-md-9">
            <div class="main-content">
                <div class="page-header">
                    <h1><i class="fas fa-chart-bar"></i> Thống Kê</h1>
                    <p class="text-muted">Theo dõi hoạt động phòng gym</p>
                </div>

                <!-- Main Statistics -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <div class="stat-number"><?php echo $total_members; ?></div>
                            <div class="stat-label">Tổng Hội Viên</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <div class="stat-number"><?php echo $active_members; ?></div>
                            <div class="stat-label">Hội Viên Còn Hạn</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                            <div class="stat-number"><?php echo $expired_members; ?></div>
                            <div class="stat-label">Hội Viên Hết Hạn</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <i class="fas fa-dumbbell fa-2x text-warning mb-2"></i>
                            <div class="stat-number"><?php echo $today_checkins; ?></div>
                            <div class="stat-label">Check-in Hôm Nay</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Chi tiết ngày hôm nay -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-calendar-day"></i> Hôm Nay - <?php echo formatDate($today); ?>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Check-in</p>
                                        <h3 class="text-success"><?php echo $today_checkins; ?></h3>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Check-out</p>
                                        <h3 class="text-info"><?php echo $today_checkouts; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê tháng -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-calendar-alt"></i> Tháng Hiện Tại
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="text-muted mb-1">Tổng Lượt Check-in</p>
                                        <h3 class="text-primary"><?php echo $month_checkins; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Gói tập phổ biến -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-star"></i> Gói Tập Phổ Biến
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tên Gói</th>
                                            <th class="text-end">Số Hội Viên</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($popular_packages->num_rows > 0): ?>
                                            <?php while ($pkg = $popular_packages->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $pkg['name']; ?></td>
                                                    <td class="text-end">
                                                        <span class="badge bg-primary"><?php echo $pkg['count']; ?></span>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-3">
                                                    Không có dữ liệu
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Check-in 7 ngày gần đây -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-chart-line"></i> Check-in 7 Ngày Gần Đây
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ngày</th>
                                            <th class="text-end">Lượt Check-in</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($week_stats->num_rows > 0): ?>
                                            <?php while ($stat = $week_stats->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $stat['date']; ?></td>
                                                    <td class="text-end">
                                                        <span class="badge bg-info"><?php echo $stat['count']; ?></span>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-3">
                                                    Không có dữ liệu
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ghi chú -->
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle"></i>
                    <strong>Ghi chú:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Hội viên còn hạn: Hội viên có gói tập còn hiệu lực</li>
                        <li>Hội viên hết hạn: Hội viên có gói tập đã vượt quá ngày hết hạn</li>
                        <li>Check-in: Số lần hội viên vào phòng gym</li>
                        <li>Dữ liệu được cập nhật theo thời gian thực</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2025 Hệ Thống Quản Lý Phòng Gym</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
