<?php
// Các hàm tiện ích chung

// Tạo mã hội viên tự động
function generateMemberCode($conn) {
    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(code, 3) AS UNSIGNED)) as max_code FROM members WHERE code LIKE 'HV%'");
    $row = $result->fetch_assoc();
    $nextNum = ($row['max_code'] ? $row['max_code'] + 1 : 1);
    return 'HV' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
}

// Kiểm tra email
function isValidPhone($phone) {
    return preg_match('/^0\d{9}$/', $phone);
}

// Kiểm tra ngày hợp lệ
function isValidDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Tính số ngày còn lại
function getDaysRemaining($endDate) {
    $today = new DateTime();
    $end = new DateTime($endDate);
    $remaining = $end->diff($today)->days;
    return $remaining >= 0 ? $remaining : 0;
}

// Kiểm tra gói tập còn hạn
function isPackageValid($endDate) {
    return strtotime(date('Y-m-d')) <= strtotime($endDate);
}

// Định dạng tiền tệ
function formatCurrency($amount) {
    return number_format($amount, 0, '.', '.') . ' VNĐ';
}

// Định dạng ngày
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Alert messages
function showAlert($message, $type = 'success') {
    $class = ($type === 'success') ? 'alert-success' : 'alert-danger';
    echo "<div class='alert $class alert-dismissible fade show' role='alert'>
            $message
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}

// Redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

?>
