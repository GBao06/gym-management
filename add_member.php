<?php
$pageTitle = "Thêm Hội Viên";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$newMemberCode = generateMemberCode($conn);
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = sanitize($_POST['fullname'] ?? '');
    $dob = sanitize($_POST['dob'] ?? '');
    $gender = sanitize($_POST['gender'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');

    // Validation
    if (empty($fullname)) $errors[] = "Vui lòng nhập họ tên!";
    if (empty($dob) || !isValidDate($dob)) $errors[] = "Ngày sinh không hợp lệ!";
    if (empty($gender)) $errors[] = "Vui lòng chọn giới tính!";
    if (empty($phone) || !isValidPhone($phone)) $errors[] = "Số điện thoại không hợp lệ (phải là 0XXXXXXXXX)!";
    if (empty($address)) $errors[] = "Vui lòng nhập địa chỉ!";

    // Kiểm tra số điện thoại trùng
    if (!empty($phone) && !empty($errors) == false) {
        $result = $conn->query("SELECT id FROM members WHERE phone='$phone'");
        if ($result->num_rows > 0) {
            $errors[] = "Số điện thoại đã được đăng ký!";
        }
    }

    if (count($errors) == 0) {
        $code = $newMemberCode;
        $sql = "INSERT INTO members (code, fullname, dob, gender, phone, address) 
                VALUES ('$code', '$fullname', '$dob', '$gender', '$phone', '$address')";
        
        if ($conn->query($sql)) {
            $success = true;
            $newMemberCode = generateMemberCode($conn);
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'members.php';
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
                    <h1><i class="fas fa-user-plus"></i> Thêm Hội Viên Mới</h1>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <strong>Thêm hội viên thành công!</strong> Đang chuyển hướng...
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
                        <i class="fas fa-file-alt"></i> Thông Tin Hội Viên
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Mã Hội Viên:</strong></label>
                                    <input type="text" class="form-control" value="<?php echo $newMemberCode; ?>" disabled>
                                    <small class="text-muted">Mã được tạo tự động</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Họ Tên:</strong> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="fullname" required 
                                           value="<?php echo sanitize($_POST['fullname'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Ngày Sinh:</strong> <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="dob" required
                                           value="<?php echo sanitize($_POST['dob'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Giới Tính:</strong> <span class="text-danger">*</span></label>
                                    <select class="form-select" name="gender" required>
                                        <option value="">-- Chọn Giới Tính --</option>
                                        <option value="Nam" <?php echo (sanitize($_POST['gender'] ?? '') == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                                        <option value="Nữ" <?php echo (sanitize($_POST['gender'] ?? '') == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Số Điện Thoại:</strong> <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone" required placeholder="0xxxxxxxxx"
                                           value="<?php echo sanitize($_POST['phone'] ?? ''); ?>">
                                    <small class="text-muted">Ví dụ: 0935123456</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Địa Chỉ:</strong> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="address" required
                                           value="<?php echo sanitize($_POST['address'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="members.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay Lại
                                </a>
                                <button type="reset" class="btn btn-warning">
                                    <i class="fas fa-redo"></i> Xóa Dữ Liệu
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Thêm Hội Viên
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
