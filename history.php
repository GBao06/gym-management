<?php
$pageTitle = "Lịch Sử Tập Luyện";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$member_code = sanitize($_GET['member_code'] ?? '');
$filter_date = sanitize($_GET['filter_date'] ?? '');
$logs = null;
$member_info = null;

if (!empty($member_code)) {
    $member_code_safe = $conn->real_escape_string($member_code);
    $result = $conn->query("SELECT id, fullname, code FROM members WHERE code='$member_code_safe'");
    
    if ($result->num_rows > 0) {
        $member_info = $result->fetch_assoc();
        
        // Xây dựng query
        $query = "
            SELECT cl.*, m.fullname, m.code
            FROM check_logs cl
            JOIN members m ON cl.member_id = m.id
            WHERE cl.member_id = {$member_info['id']}
        ";
        
        if (!empty($filter_date)) {
            $filter_date_safe = $conn->real_escape_string($filter_date);
            $query .= " AND cl.log_date = '$filter_date_safe'";
        }
        
        $query .= " ORDER BY cl.log_date DESC, cl.check_in DESC";
        
        $logs = $conn->query($query);
    }
}

// Lấy tất cả lịch sử (nếu không lọc theo hội viên)
if (empty($member_code)) {
    $all_logs_query = "
        SELECT cl.*, m.fullname, m.code
        FROM check_logs cl
        JOIN members m ON cl.member_id = m.id
    ";
    
    if (!empty($filter_date)) {
        $filter_date_safe = $conn->real_escape_string($filter_date);
        $all_logs_query .= " WHERE cl.log_date = '$filter_date_safe'";
    }
    
    $all_logs_query .= " ORDER BY cl.log_date DESC, cl.check_in DESC";
    $logs = $conn->query($all_logs_query);
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/sidebar.php'; ?>

        <div class="col-md-9">
            <div class="main-content">
                <div class="page-header">
                    <h1><i class="fas fa-history"></i> Lịch Sử Tập Luyện</h1>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="GET" class="d-flex gap-2">
                                    <input type="text" class="form-control" name="member_code" 
                                           placeholder="Tìm theo mã hội viên..." 
                                           value="<?php echo htmlspecialchars($member_code); ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if (!empty($member_code)): ?>
                                        <a href="history.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Xóa Lọc
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" class="d-flex gap-2">
                                    <input type="hidden" name="member_code" value="<?php echo htmlspecialchars($member_code); ?>">
                                    <input type="date" class="form-control" name="filter_date" 
                                           value="<?php echo htmlspecialchars($filter_date); ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Lọc
                                    </button>
                                    <?php if (!empty($filter_date)): ?>
                                        <a href="history.php<?php echo !empty($member_code) ? '?member_code=' . htmlspecialchars($member_code) : ''; ?>" 
                                           class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($member_info): ?>
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Lịch sử tập của:</strong> <?php echo $member_info['fullname']; ?> (<?php echo $member_info['code']; ?>)
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Mã HV</th>
                                    <th>Họ Tên</th>
                                    <th>Ngày Tập</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Thời Lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($logs && $logs->num_rows > 0): ?>
                                    <?php while ($log = $logs->fetch_assoc()): ?>
                                        <?php 
                                            $duration = '';
                                            if ($log['check_in'] && $log['check_out']) {
                                                $start = strtotime($log['check_in']);
                                                $end = strtotime($log['check_out']);
                                                $minutes = round(($end - $start) / 60);
                                                $hours = floor($minutes / 60);
                                                $mins = $minutes % 60;
                                                $duration = ($hours > 0 ? $hours . 'h ' : '') . $mins . 'p';
                                            }
                                        ?>
                                        <tr>
                                            <td><strong><?php echo $log['code']; ?></strong></td>
                                            <td><?php echo $log['fullname']; ?></td>
                                            <td><?php echo formatDate($log['log_date']); ?></td>
                                            <td>
                                                <?php if ($log['check_in']): ?>
                                                    <span class="badge bg-success"><?php echo $log['check_in']; ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($log['check_out']): ?>
                                                    <span class="badge bg-info"><?php echo $log['check_out']; ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted text-warning">Chưa checkout</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($duration): ?>
                                                    <strong><?php echo $duration; ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br/>
                                            Không có lịch sử tập luyện
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($logs && $logs->num_rows > 0): ?>
                    <div class="mt-3 text-muted">
                        <small>Tổng cộng: <strong><?php echo $logs->num_rows; ?></strong> lần tập</small>
                    </div>
                <?php endif; ?>
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
