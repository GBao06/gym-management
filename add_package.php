<?php
$pageTitle = "Thêm Gói Tập";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = sanitize($_POST['code'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');

    // Validation
    if (empty($code)) $errors[] = "Vui lòng nhập mã gói tập!";
    if (empty($name)) $errors[] = "Vui lòng nhập tên gói tập!";
    if ($duration <= 0) $errors[] = "Thời hạn phải lớn hơn 0!";
    if ($price <= 0) $errors[] = "Giá bán phải lớn hơn 0!";

    // Kiểm tra mã trùng
    if (!empty($code) && count($errors) == 0) {
        $code_safe = $conn->real_escape_string($code);
        $result = $conn->query("SELECT id FROM packages WHERE code='$code_safe'");
        if ($result->num_rows > 0) {
            $errors[] = "Mã gói tập đã tồn tại!";
        }
    }

    if (count($errors) == 0) {
        $code_safe = $conn->real_escape_string($code);
        $name_safe = $conn->real_escape_string($name);
        $desc_safe = $conn->real_escape_string($description);
        
        $sql = "INSERT INTO packages (code, name, duration, price, description, status) 
                VALUES ('$code_safe', '$name_safe', $duration, $price, '$desc_safe', 'active')";
        
        if ($conn->query($sql)) {
            $success = true;
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
                    <h1><i class="fas fa-plus-circle"></i> Thêm Gói Tập Mới</h1>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <strong>Thêm gói tập thành công!</strong> Đang chuyển hướng...
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

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-alt"></i> Thông Tin Gói Tập
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Mã Gói Tập:</strong> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="code" required placeholder="VD: PKG001"
                                           value="<?php echo sanitize($_POST['code'] ?? ''); ?>">
                                    <small class="text-muted">Ví dụ: PKG001, PKG002</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Tên Gói Tập:</strong> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" required placeholder="VD: Gói 3 tháng"
                                           value="<?php echo sanitize($_POST['name'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Thời Hạn (Tháng):</strong> <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="duration" required min="1" 
                                           value="<?php echo sanitize($_POST['duration'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Giá Bán (VNĐ):</strong> <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="price" required min="0" step="0.01"
                                           value="<?php echo sanitize($_POST['price'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Mô Tả:</strong></label>
                                <textarea class="form-control" name="description" rows="4" placeholder="Nhập mô tả chi tiết gói tập..."><?php echo sanitize($_POST['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="packages.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay Lại
                                </a>
                                <button type="reset" class="btn btn-warning">
                                    <i class="fas fa-redo"></i> Xóa Dữ Liệu
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Thêm Gói Tập
                                </button>
                            </div>
                        </form>
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
</body>
</html>
