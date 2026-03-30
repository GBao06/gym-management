<?php
$pageTitle = "Chỉnh Sửa Hội Viên";
include 'views/header.php';
require 'config/database.php';
require 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$member = null;
$errors = [];
$success = false;

// Lấy thông tin hội viên
if ($id > 0) {
    $result = $conn->query("SELECT * FROM members WHERE id = $id");
    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
    } else {
        redirect('members.php');
    }
}

// Xử lý cập nhật
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
    if (empty($phone) || !isValidPhone($phone)) $errors[] = "Số điện thoại không hợp lệ!";
    if (empty($address)) $errors[] = "Vui lòng nhập địa chỉ!";

    // Kiểm tra trùng số điện thoại
    if (!empty($phone) && count($errors) == 0) {
        $result = $conn->query("SELECT id FROM members WHERE phone='$phone' AND id != $id");
        if ($result->num_rows > 0) {
            $errors[] = "Số điện thoại đã được đăng ký!";
        }
    }

    if (count($errors) == 0) {
        $sql = "UPDATE members SET 
                fullname = '$fullname', 
                dob = '$dob', 
                gender = '$gender', 
                phone = '$phone', 
                address = '$address'
                WHERE id = $id";
        
        if ($conn->query($sql)) {
            $success = true;
            // Reload data
            $result = $conn->query("SELECT * FROM members WHERE id = $id");
            $member = $result->fetch_assoc();
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
                    <h1><i class="fas fa-edit"></i> Chỉnh Sửa Thông Tin Hội Viên</h1>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <strong>Cập nhật thông tin thành công!</strong> Đang chuyển hướng...
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

                <?php if ($member): ?>
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-file-alt"></i> Thông Tin Hội Viên
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Mã Hội Viên:</strong></label>
                                        <input type="text" class="form-control" value="<?php echo $member['code']; ?>" disabled>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Họ Tên:</strong> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="fullname" required 
                                               value="<?php echo htmlspecialchars($member['fullname']); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Ngày Sinh:</strong> <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="dob" required
                                               value="<?php echo $member['dob']; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Giới Tính:</strong> <span class="text-danger">*</span></label>
                                        <select class="form-select" name="gender" required>
                                            <option value="Nam" <?php echo ($member['gender'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                                            <option value="Nữ" <?php echo ($member['gender'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Số Điện Thoại:</strong> <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="phone" required
                                               value="<?php echo $member['phone']; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Địa Chỉ:</strong> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address" required
                                               value="<?php echo htmlspecialchars($member['address']); ?>">
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="members.php" class="btn btn-secondary">
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
                        <i class="fas fa-exclamation-circle"></i> Không tìm thấy hội viên
                    </div>
                    <a href="members.php" class="btn btn-primary">
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
