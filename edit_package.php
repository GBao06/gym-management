<?php
$pageTitle = "Chỉnh Sửa Gói Tập";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$package = null;
$errors = [];
$success = false;

if ($id > 0) {
    $result = $conn->query("SELECT * FROM packages WHERE id = $id");
    if ($result->num_rows > 0) {
        $package = $result->fetch_assoc();
    } else {
        redirect('packages.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');

    if (empty($name)) $errors[] = "Vui lòng nhập tên gói tập!";
    if ($duration <= 0) $errors[] = "Thời hạn phải lớn hơn 0!";
    if ($price <= 0) $errors[] = "Giá bán phải lớn hơn 0!";

    if (count($errors) == 0) {
        $name_safe = $conn->real_escape_string($name);
        $desc_safe = $conn->real_escape_string($description);
        
        $sql = "UPDATE packages SET 
                name = '$name_safe', 
                duration = $duration, 
                price = $price, 
                description = '$desc_safe',
                status = '$status'
                WHERE id = $id";
        
        if ($conn->query($sql)) {
            $success = true;
            $result = $conn->query("SELECT * FROM packages WHERE id = $id");
            $package = $result->fetch_assoc();
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'packages.php';
                }, 1500);
            </script>";
        } else {
            $errors[] = "Lỗi: " . $conn->error;
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
                    <h1><i class="fas fa-edit"></i> Chỉnh Sửa Gói Tập</h1>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <strong>Cập nhật gói tập thành công!</strong> Đang chuyển hướng...
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

                <?php if ($package): ?>
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-file-alt"></i> Thông Tin Gói Tập
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Mã Gói Tập:</strong></label>
                                        <input type="text" class="form-control" value="<?php echo $package['code']; ?>" disabled>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Tên Gói Tập:</strong> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" required
                                               value="<?php echo htmlspecialchars($package['name']); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label"><strong>Thời Hạn (Tháng):</strong> <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="duration" required min="1"
                                               value="<?php echo $package['duration']; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label"><strong>Giá Bán (VNĐ):</strong> <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="price" required min="0" step="0.01"
                                               value="<?php echo $package['price']; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label"><strong>Trạng Thái:</strong></label>
                                        <select class="form-select" name="status">
                                            <option value="active" <?php echo ($package['status'] == 'active') ? 'selected' : ''; ?>>Đang bán</option>
                                            <option value="inactive" <?php echo ($package['status'] == 'inactive') ? 'selected' : ''; ?>>Ngừng bán</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Mô Tả:</strong></label>
                                    <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($package['description']); ?></textarea>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="packages.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay Lại
                                    </a>
                                    <button type="reset" class="btn btn-warning">
                                        <i class="fas fa-redo"></i> Đặt Lại
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Cập Nhật
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Không tìm thấy gói tập
                    </div>
                    <a href="packages.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Quay Lại
                    </a>
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
