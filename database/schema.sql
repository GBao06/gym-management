-- Tạo Database
CREATE DATABASE IF NOT EXISTS gym_management;
USE gym_management;

-- Bảng Gói Tập
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    duration INT NOT NULL COMMENT 'Thời hạn tính bằng tháng',
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Bảng Hội Viên
CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Nam', 'Nữ') NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_phone (phone),
    INDEX idx_fullname (fullname)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Bảng Đăng Ký Gói Tập
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    package_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id),
    INDEX idx_member (member_id),
    INDEX idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Bảng Lịch Sử Tập Luyện (Check-in/Check-out)
CREATE TABLE IF NOT EXISTS check_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    log_date DATE NOT NULL,
    check_in TIME,
    check_out TIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    INDEX idx_member (member_id),
    INDEX idx_date (log_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Chèn dữ liệu mẫu cho Gói Tập
INSERT INTO packages (code, name, duration, price, description, status) VALUES
('PKG001', 'Gói 1 Tháng', 1, 500000, 'Gói tập 1 tháng, tập không giới hạn', 'active'),
('PKG003', 'Gói 3 Tháng', 3, 1200000, 'Gói tập 3 tháng, tập không giới hạn', 'active'),
('PKG006', 'Gói 6 Tháng', 6, 2000000, 'Gói tập 6 tháng, tập không giới hạn', 'active'),
('PKG012', 'Gói 1 Năm', 12, 3500000, 'Gói tập 1 năm, tập không giới hạn', 'active');

-- Chèn dữ liệu mẫu cho Hội Viên
INSERT INTO members (code, fullname, dob, gender, phone, address) VALUES
('HV001', 'Phan Thịnh', '1995-05-15', 'Nam', '0935123456', '123 Đường Lê Lợi, TP HCM'),
('HV002', 'Trần Hương', '1998-03-20', 'Nữ', '0912345678', '456 Đường Nguyễn Huệ, TP HCM'),
('HV003', 'Lê Minh', '2000-07-10', 'Nam', '0938765432', '789 Đường Hàng Dương, TP HCM');

-- Chèn dữ liệu mẫu cho Đăng Ký Gói Tập
INSERT INTO registrations (member_id, package_id, start_date, end_date, status) VALUES
(1, 3, '2025-03-01', '2025-06-01', 'active'),
(2, 1, '2025-03-07', '2025-04-07', 'active'),
(3, 6, '2025-02-01', '2025-08-01', 'active');

-- Chèn dữ liệu mẫu cho Lịch Sử Tập Luyện
INSERT INTO check_logs (member_id, log_date, check_in, check_out) VALUES
(1, '2025-03-07', '18:05:00', '19:30:00'),
(2, '2025-03-07', '17:40:00', '19:10:00'),
(1, '2025-03-08', '18:20:00', '20:00:00');
