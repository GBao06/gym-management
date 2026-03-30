<?php
$pageTitle = "Trang Chủ";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

// Lấy thống kê cơ bản
$totalMembers = $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count'];
$activeMembers = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE status='active'")->fetch_assoc()['count'];
$totalPackages = $conn->query("SELECT COUNT(*) as count FROM packages WHERE status='active'")->fetch_assoc()['count'];

// Lấy số lượt tập hôm nay
$today = date('Y-m-d');
$todayCheckins = $conn->query("SELECT COUNT(*) as count FROM check_logs WHERE log_date='$today' AND check_in IS NOT NULL")->fetch_assoc()['count'];

$conn->close();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include 'views/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1><i class="fas fa-home"></i> Dashboard - Trang Chủ</h1>
                    <p class="text-muted">Chào mừng đến với Hệ Thống Quản Lý Phòng Gym</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <div class="stat-number"><?php echo $totalMembers; ?></div>
                            <div class="stat-label">Tổng Hội Viên</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <div class="stat-number"><?php echo $activeMembers; ?></div>
                            <div class="stat-label">Hội Viên Còn Hạn</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <i class="fas fa-box fa-2x text-info mb-2"></i>
                            <div class="stat-number"><?php echo $totalPackages; ?></div>
                            <div class="stat-label">Gói Tập Đang Bán</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <i class="fas fa-dumbbell fa-2x text-warning mb-2"></i>
                            <div class="stat-number"><?php echo $todayCheckins; ?></div>
                            <div class="stat-label">Lượt Tập Hôm Nay</div>
                        </div>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-info-circle"></i> Hướng Dẫn Sử Dụng
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>1. Quản Lý Hội Viên</strong><br/>
                                        <small>Thêm, cập nhật, xóa và quản lý thông tin hội viên</small>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>2. Quản Lý Gói Tập</strong><br/>
                                        <small>Tạo, chỉnh sửa gói tập phù hợp với nhu cầu khách hàng</small>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>3. Đăng Ký & Gia Hạn</strong><br/>
                                        <small>Đăng ký gói tập mới hoặc gia hạn gói hiện tại cho hội viên</small>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>4. Check-in/Check-out</strong><br/>
                                        <small>Ghi nhận thời gian hội viên vào và ra phòng tập</small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-lightbulb"></i> Tính Năng Chính
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success"></i> Quản lý dữ liệu tập trung
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success"></i> Tìm kiếm nhanh theo mã hoặc tên
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success"></i> Theo dõi thời hạn gói tập
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success"></i> Thống kê hoạt động chi tiết
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success"></i> Lịch sử tập luyện đầy đủ
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success"></i> Giao diện dễ sử dụng
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2025 Hệ Thống Quản Lý Phòng Gym - Phiên Bản 1.0</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
