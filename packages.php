<?php
$pageTitle = "Quản Lý Gói Tập";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$search = sanitize($_GET['search'] ?? '');
$filter = sanitize($_GET['filter'] ?? 'all');

// Xử lý xóa gói tập
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $package_id = (int)$_POST['package_id'];
    
    // Kiểm tra có hội viên nào đang sử dụng gói này
    $check = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE package_id = $package_id");
    $count = $check->fetch_assoc()['count'];
    
    if ($count > 0) {
        $_SESSION['error'] = "Không thể xóa! Có $count hội viên đang sử dụng gói tập này.";
    } else {
        if ($conn->query("DELETE FROM packages WHERE id = $package_id")) {
            $_SESSION['success'] = "Xóa gói tập thành công!";
        }
    }
}

// Xây dựng query
$query = "SELECT * FROM packages WHERE 1=1";

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $query .= " AND (code LIKE '%$search_safe%' OR name LIKE '%$search_safe%')";
}

if ($filter == 'active') {
    $query .= " AND status = 'active'";
} elseif ($filter == 'inactive') {
    $query .= " AND status = 'inactive'";
}

$query .= " ORDER BY created_at DESC";

$result = $conn->query($query);
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/sidebar.php'; ?>

        <div class="col-md-9">
            <div class="main-content">
                <div class="page-header">
                    <h1><i class="fas fa-box"></i> Quản Lý Gói Tập</h1>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="GET" class="d-flex gap-2">
                                    <input type="text" class="form-control" name="search" placeholder="Tìm theo mã hoặc tên gói..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <form method="GET" class="d-flex">
                                    <select class="form-select" name="filter" onchange="this.form.submit()">
                                        <option value="all" <?php echo ($filter == 'all') ? 'selected' : ''; ?>>-- Tất cả gói tập --</option>
                                        <option value="active" <?php echo ($filter == 'active') ? 'selected' : ''; ?>>Đang bán</option>
                                        <option value="inactive" <?php echo ($filter == 'inactive') ? 'selected' : ''; ?>>Ngừng bán</option>
                                    </select>
                                </form>
                            </div>
                            <div class="col-md-2">
                                <a href="add_package.php" class="btn btn-success w-100">
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
                                    <th>STT</th>
                                    <th>Mã Gói</th>
                                    <th>Tên Gói</th>
                                    <th>Thời Hạn</th>
                                    <th>Giá Bán</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $stt = 1; ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $stt++; ?></td>
                                            <td><strong><?php echo $row['code']; ?></strong></td>
                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['duration']; ?> tháng</td>
                                            <td><?php echo formatCurrency($row['price']); ?></td>
                                            <td>
                                                <?php if ($row['status'] == 'active'): ?>
                                                    <span class="badge bg-success">Đang bán</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Ngừng bán</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="edit_package.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Chỉnh sửa">
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
                                                                <p>Bạn có chắc chắn muốn xóa gói tập <strong><?php echo $row['name']; ?></strong> không?</p>
                                                                <p class="text-muted"><small>Hành động này không thể hoàn tác!</small></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                <form method="POST" style="display:inline;">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="package_id" value="<?php echo $row['id']; ?>">
                                                                    <button type="submit" class="btn btn-danger">Xóa Gói Tập</button>
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
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br/>
                                            Không có gói tập nào
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($result->num_rows > 0): ?>
                    <div class="mt-3 text-muted">
                        <small>Tổng cộng: <strong><?php echo $result->num_rows; ?></strong> gói tập</small>
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
