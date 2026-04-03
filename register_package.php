<?php
$pageTitle = "Đăng Ký Gói Tập";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$errors = [];
$success = false;
$member_info = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_code = sanitize($_POST['member_code'] ?? '');
    $package_id = (int)($_POST['package_id'] ?? 0);

    // Kiểm tra hội viên
    if (empty($member_code)) {
        $errors[] = "Vui lòng nhập mã hội viên!";
    } else {
        $member_code_safe = $conn->real_escape_string($member_code);
        $result = $conn->query("SELECT id, fullname, code FROM members WHERE code='$member_code_safe'");
        if ($result->num_rows == 0) {
            $errors[] = "Mã hội viên chưa chính xác! Vui lòng đăng ký hội viên trước.";
        } else {
            $member_info = $result->fetch_assoc();
        }
    }

    // Kiểm tra gói tập
    if ($package_id <= 0) {
        $errors[] = "Vui lòng chọn gói tập!";
    }

    if (count($errors) == 0 && $member_info) {
        $start_date = date('Y-m-d');
        
        // Lấy thông tin gói tập
        $pkg_result = $conn->query("SELECT duration FROM packages WHERE id = $package_id");
        $pkg = $pkg_result->fetch_assoc();
        $end_date = date('Y-m-d', strtotime($start_date . ' + ' . $pkg['duration'] . ' months'));

        // Xóa đăng ký cũ nếu có
        $conn->query("DELETE FROM registrations WHERE member_id = {$member_info['id']}");

        // Thêm đăng ký mới
        $sql = "INSERT INTO registrations (member_id, package_id, start_date, end_date, status) 
                VALUES ({$member_info['id']}, $package_id, '$start_date', '$end_date', 'active')";
        
        if ($conn->query($sql)) {
            $success = true;
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'register_package.php';
                }, 2000);
            </script>";
        } else {
            $errors[] = "Lỗi: " . $conn->error;
        }
    }
}

// Lấy danh sách gói tập
$packages = $conn->query("SELECT * FROM packages WHERE status='active' ORDER BY duration");
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/sidebar.php'; ?>

        <div class="col-md-9">
            <div class="main-content">
                <div class="page-header">
                    <h1><i class="fas fa-clipboard-check"></i> Đăng Ký Gói Tập</h1>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <strong>Đăng ký gói tập thành công!</strong> Đang chuyển hướng...
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <strong>Lỗi:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-user"></i> Thông Tin Hội Viên
                            </div>
                            <div class="card-body">
                                <form method="POST" id="registerForm">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Mã Hội Viên:</strong> <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="member_code" id="memberCode" required
                                                   placeholder="VD: HV001" value="<?php echo sanitize($_POST['member_code'] ?? ''); ?>">
                                            <button class="btn btn-outline-primary" type="button" id="searchBtn">
                                                <i class="fas fa-search"></i> Tìm Kiếm
                                            </button>
                                        </div>
                                    </div>
                                    <form method="POST" >
                                        <?php if ($member_info): ?>
                                            <div class="alert alert-info">
                                                <strong><i class="fas fa-check"></i> Thông tin hội viên:</strong><br/>
                                                Tên: <?php echo $member_info['fullname']; ?><br/>
                                                Mã: <?php echo $member_info['code']; ?>
                                            </div>
                                        <?php endif; ?> 
                                    </form>


                                    <div class="mb-3">
                                        <label class="form-label"><strong>Chọn Gói Tập:</strong> <span class="text-danger">*</span></label>
                                        <select class="form-select" name="package_id" id="packageSelect" required onchange="updatePackageInfo()">
                                            <option value="">-- Chọn Gói Tập --</option>
                                            <?php while ($pkg = $packages->fetch_assoc()): ?>
                                                <option value="<?php echo $pkg['id']; ?>" 
                                                        data-duration="<?php echo $pkg['duration']; ?>"
                                                        data-price="<?php echo $pkg['price']; ?>"
                                                        data-name="<?php echo htmlspecialchars($pkg['name']); ?>">
                                                    <?php echo $pkg['name']; ?> - <?php echo formatCurrency($pkg['price']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-check"></i> Đăng Ký Gói Tập
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-info-circle"></i> Thông Tin Gói Tập
                            </div>
                            <div class="card-body" id="packageInfo">
                                <p class="text-muted text-center">Vui lòng chọn gói tập để xem chi tiết</p>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <i class="fas fa-list"></i> Danh Sách Gói Tập Có Sẵn
                            </div>
                            <div class="list-group list-group-flush">
                                <?php 
                                $packages = $conn->query("SELECT * FROM packages WHERE status='active' ORDER BY duration");
                                while ($pkg = $packages->fetch_assoc()): 
                                ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-0"><?php echo $pkg['name']; ?></h6>
                                                <small class="text-muted"><?php echo $pkg['duration']; ?> tháng</small>
                                            </div>
                                            <span class="badge bg-primary"><?php echo formatCurrency($pkg['price']); ?></span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2025 Hệ Thống Quản Lý Phòng Gym</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updatePackageInfo() {
    const select = document.getElementById('packageSelect');
    const option = select.options[select.selectedIndex];
    const packageInfo = document.getElementById('packageInfo');
    
    if (option.value) {
        const today = new Date();
        const duration = parseInt(option.dataset.duration);
        const endDate = new Date(today.getFullYear(), today.getMonth() + duration, today.getDate());
        
        packageInfo.innerHTML = `
            <div class="row">
                <div class="col-12 mb-3">
                    <strong>Tên Gói:</strong> ${option.dataset.name}
                </div>
                <div class="col-12 mb-3">
                    <strong>Thời Hạn:</strong> ${option.dataset.duration} tháng
                </div>
                <div class="col-12 mb-3">
                    <strong>Giá Bán:</strong> <span class="text-primary">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(option.dataset.price)}</span>
                </div>
                <div class="col-12">
                    <strong>Hạn Sử Dụng:</strong> ${endDate.toLocaleDateString('vi-VN')}
                </div>
            </div>
        `;
    } else {
        packageInfo.innerHTML = '<p class="text-muted text-center">Vui lòng chọn gói tập để xem chi tiết</p>';
    }
}

</script>
</body>
</html>
