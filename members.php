<?php
$pageTitle = "Danh Sách Hội Viên";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$search = sanitize($_GET['search'] ?? '');
$filter = sanitize($_GET['filter'] ?? 'all');
$delete_msg = '';

// Xử lý xóa hội viên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $member_id = (int)$_POST['member_id'];
    
    // Kiểm tra gói tập còn hạn
    $result = $conn->query("
        SELECT r.end_date 
        FROM registrations r 
        WHERE r.member_id = $member_id 
        ORDER BY r.end_date DESC LIMIT 1
    ");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (strtotime(date('Y-m-d')) <= strtotime($row['end_date'])) {
            $delete_msg = "<div class='alert alert-warning'><i class='fas fa-exclamation-triangle'></i> Gói tập của hội viên này còn hiệu lực đến " . formatDate($row['end_date']) . ". Không thể xóa hội viên!!</div>";
        } else {
            if ($conn->query("DELETE FROM members WHERE id = $member_id")) {
                $delete_msg = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> <strong>Xóa hội viên thành công!</strong></div>";
            } else {
                $delete_msg = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Lỗi xóa: " . $conn->error . "</div>";
            }
        }
    }
}

// Xây dựng query
$query = "
    SELECT m.id, m.code, m.fullname, m.dob, m.gender, m.phone, m.address, 
           p.name as package_name, r.end_date, r.status
    FROM members m
    LEFT JOIN registrations r ON m.id = r.member_id AND r.status = 'active'
    LEFT JOIN packages p ON r.package_id = p.id
    WHERE 1=1
";

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $query .= " AND (m.code LIKE '%$search_safe%' OR m.fullname LIKE '%$search_safe%' OR m.phone LIKE '%$search_safe%')";
}

if ($filter == 'active') {
    $query .= " AND r.status = 'active' AND r.end_date >= CURDATE()";
} elseif ($filter == 'expired') {
    $query .= " AND (r.status = 'expired' OR r.end_date < CURDATE())";
}

$query .= " ORDER BY m.code DESC";

$result = $conn->query($query);
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/sidebar.php'; ?>

        <div class="col-md-9">
            <div class="main-content">
                <div class="page-header">
                    <h1><i class="fas fa-users"></i> Danh Sách Hội Viên</h1>
                </div>

                <?php if (!empty($delete_msg)) echo $delete_msg; ?>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="GET" class="d-flex gap-2">
                                    <input type="text" class="form-control" name="search" placeholder="Tìm theo mã, tên hoặc SĐT..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <form method="GET" class="d-flex">
                                    <select class="form-select" name="filter" onchange="this.form.submit()">
                                        <option value="all" <?php echo ($filter == 'all') ? 'selected' : ''; ?>>-- Tất cả hội viên --</option>
                                        <option value="active" <?php echo ($filter == 'active') ? 'selected' : ''; ?>>Còn hạn</option>
                                        <option value="expired" <?php echo ($filter == 'expired') ? 'selected' : ''; ?>>Hết hạn</option>
                                    </select>
                                </form>
                            </div>
                            <div class="col-md-2">
                                <a href="add_member.php" class="btn btn-success w-100">
                                    <i class="fas fa-plus"></i> Thêm Mới
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
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
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
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
                                                <a href="edit_member.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal<?php echo $row['id']; ?>" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Xác Nhận Xóa</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Bạn có chắc chắn muốn xóa hội viên <strong><?php echo $row['fullname']; ?></strong> không?</p>
                                                                <p class="text-muted"><small>Hành động này không thể hoàn tác!</small></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                <form method="POST" style="display:inline;">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="member_id" value="<?php echo $row['id']; ?>">
                                                                    <button type="submit" class="btn btn-danger">Xóa Hội Viên</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br/>
                                            Không có hội viên nào
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($result->num_rows > 0): ?>
                    <div class="mt-3 text-muted">
                        <small>Tổng cộng: <strong><?php echo $result->num_rows; ?></strong> hội viên</small>
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
