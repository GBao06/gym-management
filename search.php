<?php
$pageTitle = "Tìm Kiếm Hội Viên";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$search_type = sanitize($_GET['search_type'] ?? 'code');
$search_value = sanitize($_GET['search_value'] ?? '');
$results = null;

if (!empty($search_value)) {
    $search_safe = $conn->real_escape_string($search_value);

    if ($search_type == 'code') {
        $results = $conn->query("
            SELECT m.*, p.name as package_name, r.end_date, r.status
            FROM members m
            LEFT JOIN registrations r ON m.id = r.member_id AND r.status = 'active'
            LEFT JOIN packages p ON r.package_id = p.id
            WHERE m.code LIKE '%$search_safe%'
            ORDER BY m.code DESC
        ");
    } elseif ($search_type == 'name') {
        $results = $conn->query("
            SELECT m.*, p.name as package_name, r.end_date, r.status
            FROM members m
            LEFT JOIN registrations r ON m.id = r.member_id AND r.status = 'active'
            LEFT JOIN packages p ON r.package_id = p.id
            WHERE m.fullname LIKE '%$search_safe%'
            ORDER BY m.fullname ASC
        ");
    } elseif ($search_type == 'phone') {
        $results = $conn->query("
            SELECT m.*, p.name as package_name, r.end_date, r.status
            FROM members m
            LEFT JOIN registrations r ON m.id = r.member_id AND r.status = 'active'
            LEFT JOIN packages p ON r.package_id = p.id
            WHERE m.phone LIKE '%$search_safe%'
            ORDER BY m.phone ASC
        ");
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/sidebar.php'; ?>

        <div class="col-md-9">
            <div class="main-content">
                <div class="page-header">
                    <h1><i class="fas fa-search"></i> Tìm Kiếm Hội Viên</h1>
                    <p class="text-muted">Tìm kiếm thông tin hội viên theo mã, tên hoặc số điện thoại</p>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-filter"></i> Tiêu Chí Tìm Kiếm
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><strong>Tìm Kiếm Theo:</strong></label>
                                <select class="form-select" name="search_type" onchange="this.form.submit()">
                                    <option value="code" <?php echo ($search_type == 'code') ? 'selected' : ''; ?>>Mã Hội Viên</option>
                                    <option value="name" <?php echo ($search_type == 'name') ? 'selected' : ''; ?>>Họ Tên</option>
                                    <option value="phone" <?php echo ($search_type == 'phone') ? 'selected' : ''; ?>>Số Điện Thoại</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Nhập Từ Khóa:</strong></label>
                                <input type="text" class="form-control" name="search_value" 
                                       placeholder="<?php 
                                           if ($search_type == 'code') echo 'VD: HV001';
                                           elseif ($search_type == 'name') echo 'VD: Phan Thịnh';
                                           else echo 'VD: 0935123456';
                                       ?>"
                                       value="<?php echo htmlspecialchars($search_value); ?>">
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Tìm Kiếm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Kết quả tìm kiếm -->
                <?php if (!empty($search_value)): ?>
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-list"></i> Kết Quả Tìm Kiếm
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Mã HV</th>
                                        <th>Họ Tên</th>
                                        <th>Ngày Sinh</th>
                                        <th>Giới Tính</th>
                                        <th>Điện Thoại</th>
                                        <th>Gói Tập</th>
                                        <th>Hết Hạn</th>
                                        <th>Trạng Thái</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($results && $results->num_rows > 0): ?>
                                        <?php while ($row = $results->fetch_assoc()): ?>
                                            <?php 
                                                $is_valid = $row['end_date'] && strtotime(date('Y-m-d')) <= strtotime($row['end_date']);
                                                $status_badge = $is_valid ? '<span class="badge bg-success">Còn hạn</span>' : '<span class="badge bg-danger">Hết hạn</span>';
                                            ?>
                                            <tr>
                                                <td><strong><?php echo $row['code']; ?></strong></td>
                                                <td><?php echo $row['fullname']; ?></td>
                                                <td><?php echo formatDate($row['dob']); ?></td>
                                                <td><?php echo $row['gender']; ?></td>
                                                <td><?php echo $row['phone']; ?></td>
                                                <td><?php echo $row['package_name'] ?? '<span class="text-muted">-</span>'; ?></td>
                                                <td><?php echo $row['end_date'] ? formatDate($row['end_date']) : '<span class="text-muted">-</span>'; ?></td>
                                                <td><?php echo $status_badge; ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="edit_member.php?id=<?php echo $row['id']; ?>" 
                                                           class="btn btn-sm btn-primary" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="history.php?member_code=<?php echo $row['code']; ?>" 
                                                           class="btn btn-sm btn-info" title="Lịch sử">
                                                            <i class="fas fa-history"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                <i class="fas fa-search fa-2x mb-2"></i><br/>
                                                Không tìm thấy hội viên nào khớp với từ khóa "<strong><?php echo htmlspecialchars($search_value); ?></strong>"
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php if ($results && $results->num_rows > 0): ?>
                        <div class="mt-3 text-muted">
                            <small>Tìm thấy: <strong><?php echo $results->num_rows; ?></strong> hội viên</small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Hướng dẫn:</strong> Chọn loại tìm kiếm và nhập từ khóa để tìm kiếm hội viên
                    </div>

                    <!-- Gợi ý hỗ trợ -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-id-card fa-3x text-primary mb-3"></i>
                                    <h6>Tìm Theo Mã</h6>
                                    <p class="text-muted small">Nhanh nhất - Mã định danh duy nhất</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-user fa-3x text-success mb-3"></i>
                                    <h6>Tìm Theo Tên</h6>
                                    <p class="text-muted small">Có thể tìm phần tên</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-phone fa-3x text-info mb-3"></i>
                                    <h6>Tìm Theo SĐT</h6>
                                    <p class="text-muted small">Số điện thoại liên hệ</p>
                                </div>
                            </div>
                        </div>
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
