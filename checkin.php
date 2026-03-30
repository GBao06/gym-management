<?php
$pageTitle = "Check-in / Check-out";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$result_msg = '';
$member_info = null;
$is_checked_in = false;
$member_code = '';

// Lấy mã hội viên từ GET hoặc POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_code = sanitize($_POST['member_code'] ?? '');
    $action = sanitize($_POST['action'] ?? '');
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['member_code'])) {
    $member_code = sanitize($_GET['member_code']);
    $action = '';
}

// Tìm kiếm hội viên
if (!empty($member_code)) {
    $member_code_safe = $conn->real_escape_string($member_code);
    $result = $conn->query("
        SELECT m.id, m.fullname, m.code, r.end_date, r.status 
        FROM members m 
        LEFT JOIN registrations r ON m.id = r.member_id AND r.status = 'active'
        WHERE m.code = '$member_code_safe'
    ");

    if ($result->num_rows == 0) {
        $result_msg = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Mã hội viên không tồn tại trong hệ thống!</div>';
    } else {
        $member_info = $result->fetch_assoc();

        // Kiểm tra gói tập
        if (!$member_info['end_date']) {
            $result_msg = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Hội viên chưa đăng ký gói tập. Vui lòng đăng ký gói tập trước!</div>';
        } elseif (!isPackageValid($member_info['end_date'])) {
            $result_msg = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Gói tập của hội viên đã hết hạn (' . formatDate($member_info['end_date']) . '). Vui lòng gia hạn gói tập!</div>';
        } else {
            // Gói tập hợp lệ
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($action)) {
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                $today = date('Y-m-d');
                $current_time = date('H:i:s');

                if ($action == 'checkin') {
                    // Kiểm tra đã check-in trong ngày chưa
                    $check = $conn->query("
                        SELECT id FROM check_logs 
                        WHERE member_id = {$member_info['id']} 
                        AND log_date = '$today' 
                        AND check_in IS NOT NULL
                    ");

                    if ($check->num_rows > 0) {
                        $result_msg = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Hội viên đã check-in hôm nay!</div>';
                    } else {
                        // Thêm check-in
                        $sql = "INSERT INTO check_logs (member_id, log_date, check_in) 
                                VALUES ({$member_info['id']}, '$today', '$current_time')";
                        
                        if ($conn->query($sql)) {
                            $is_checked_in = true;
                            $result_msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> <strong>Check-in thành công!</strong> Thời gian: ' . date('H:i:s') . '</div>';
                        } else {
                            $result_msg = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Lỗi: ' . $conn->error . '</div>';
                        }
                    }
                } elseif ($action == 'checkout') {
                    // Kiểm tra check-in để check-out
                    $check = $conn->query("
                        SELECT id FROM check_logs 
                        WHERE member_id = {$member_info['id']} 
                        AND log_date = '$today' 
                        AND check_in IS NOT NULL
                        AND check_out IS NULL
                    ");

                    if ($check->num_rows == 0) {
                        $result_msg = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Hội viên chưa check-in hôm nay!</div>';
                    } else {
                        // Cập nhật check-out
                        $sql = "UPDATE check_logs 
                                SET check_out = '$current_time' 
                                WHERE member_id = {$member_info['id']} 
                                AND log_date = '$today' 
                                AND check_out IS NULL
                                LIMIT 1";
                        
                        if ($conn->query($sql)) {
                            $result_msg = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> <strong>Check-out thành công!</strong> Thời gian: ' . date('H:i:s') . '</div>';
                        } else {
                            $result_msg = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Lỗi: ' . $conn->error . '</div>';
                        }
                    }
                }
            }
        }
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/sidebar.php'; ?>

        <div class="col-md-9">
            <div class="main-content">
                <div class="page-header">
                    <h1><i class="fas fa-sign-in-alt"></i> Check-in / Check-out</h1>
                    <p class="text-muted">Điểm danh hội viên vào và ra khỏi phòng tập</p>
                </div>

                <?php if (!empty($result_msg)): ?>
                    <?php echo $result_msg; ?>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <i class="fas fa-barcode"></i> Quét Mã Hội Viên
                            </div>
                            <div class="card-body">
                                <form method="GET" id="searchForm">
                                    <div class="mb-4">
                                        <label class="form-label"><strong>Nhập Mã Hội Viên:</strong></label>
                                        <input type="text" class="form-control form-control-lg" 
                                               name="member_code" id="memberCode" autofocus
                                               placeholder="VD: HV001" autocomplete="off"
                                               value="<?php echo htmlspecialchars($member_code); ?>">
                                        <small class="text-muted">Quét thẻ hoặc nhập mã hội viên...</small>
                                    </div>
                                </form>

                                <form method="POST" id="actionForm">
                                    <input type="hidden" name="member_code" value="<?php echo htmlspecialchars($member_code); ?>" id="hiddenMemberCode">

                                    <?php if ($member_info): ?>
                                        <div class="alert alert-info mb-3">
                                            <h6><i class="fas fa-user"></i> Thông Tin Hội Viên</h6>
                                            <p class="mb-1"><strong>Tên:</strong> <?php echo $member_info['fullname']; ?></p>
                                            <p class="mb-1"><strong>Mã:</strong> <?php echo $member_info['code']; ?></p>
                                            <p class="mb-0"><strong>Hết Hạn:</strong> <?php echo formatDate($member_info['end_date']); ?></p>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" name="action" value="checkin" class="btn btn-success btn-lg">
                                                <i class="fas fa-sign-in-alt"></i> Check-in Vào
                                            </button>
                                            <button type="submit" name="action" value="checkout" class="btn btn-warning btn-lg">
                                                <i class="fas fa-sign-out-alt"></i> Check-out Ra
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-grid gap-2">
                                            <button type="submit" name="action" value="checkin" class="btn btn-success btn-lg"  >
                                                <i class="fas fa-sign-in-alt"></i> Check-in Vào
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                        <!-- Hướng dẫn -->
                        <div class="card mt-4">
                            <div class="card-header bg-info">
                                <i class="fas fa-lightbulb"></i> Hướng Dẫn Sử Dụng
                            </div>
                            <div class="card-body">
                                <ol class="small mb-0">
                                    <li>Nhập hoặc quét mã hội viên</li>
                                    <li>Kiểm tra thông tin hội viên</li>
                                    <li>Nhấn <strong>Check-in</strong> khi vào tập</li>
                                    <li>Nhấn <strong>Check-out</strong> khi kết thúc buổi tập</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê lượt check-in hôm nay -->
                <div class="row mt-4">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-chart-pie"></i> Thống Kê Hôm Nay
                            </div>
                            <div class="card-body">
                                <?php
                                $today = date('Y-m-d');
                                $today_checkins = $conn->query("
                                    SELECT COUNT(*) as count FROM check_logs 
                                    WHERE log_date = '$today' AND check_in IS NOT NULL
                                ")->fetch_assoc()['count'];
                                ?>
                                <div class="text-center">
                                    <h3 class="text-primary mb-2"><?php echo $today_checkins; ?></h3>
                                    <p class="text-muted">Lượt check-in hôm nay</p>
                                </div>
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
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('memberCode');
    const searchForm = document.getElementById('searchForm');
    const actionForm = document.getElementById('actionForm');
    const hiddenInput = document.getElementById('hiddenMemberCode');
    
    if (input) {
        input.focus();
        
        // Auto submit form khi nhấn Enter
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.submit();
            }
        });
        
        // Update hidden input khi change input
        input.addEventListener('change', function() {
            if (hiddenInput) {
                hiddenInput.value = this.value;
            }
        });
    }
});
</script>
</body>
</html>
