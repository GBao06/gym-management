# 📋 Hệ Thống Quản Lý Hội Viên Phòng Gym

Một hệ thống quản lý hội viên phòng gym hoàn chỉnh được xây dựng bằng **PHP** và **MySQL**, cung cấp các tính năng quản lý toàn diện cho phòng tập.

## ✨ Tính Năng Chính

### 1. **Quản Lý Hội Viên**
- ✅ Thêm hội viên mới (tự động tạo mã hội viên)
- ✅ Cập nhật thông tin hội viên
- ✅ Xóa hội viên (kiểm tra gói tập còn hạn)
- ✅ Danh sách hội viên với phân trang
- ✅ Lọc theo trạng thái gói tập

### 2. **Quản Lý Gói Tập**
- ✅ Thêm gói tập mới
- ✅ Cập nhật gói tập
- ✅ Xóa gói tập (kiểm tra ràng buộc)
- ✅ Danh sách gói tập
- ✅ Lọc theo trạng thái (Đang bán/Ngừng bán)

### 3. **Đăng Ký & Gia Hạn Gói**
- ✅ Đăng ký gói tập cho hội viên
- ✅ Gia hạn gói tập (thay thế gói cũ)
- ✅ Kiểm tra thời hạn gói tập
- ✅ Hiển thị ngày hết hạn và ngày còn lại

### 4. **Check-in / Check-out**
- ✅ Check-in khi hội viên vào
- ✅ Check-out khi hội viên ra
- ✅ Ghi nhận chính xác thời gian
- ✅ Kiểm tra gói tập còn hạn
- ✅ Thống kê lượt tập hôm nay

### 5. **Lịch Sử Tập Luyện**
- ✅ Xem lịch sử tập theo hội viên
- ✅ Lọc theo ngày
- ✅ Hiển thị thời lượng tập (giờ:phút)
- ✅ Danh sách tất cả lượt tập

### 6. **Tìm Kiếm & Lọc**
- ✅ Tìm hội viên theo mã
- ✅ Tìm hội viên theo tên
- ✅ Tìm hội viên theo số điện thoại
- ✅ Lọc theo trạng thái gói tập

### 7. **Thống Kê & Báo Cáo**
- ✅ Tổng số hội viên
- ✅ Hội viên còn hạn / hết hạn
- ✅ Lượt tập hôm nay / tháng
- ✅ Gói tập phổ biến
- ✅ Thống kê 7 ngày gần đây

## 📋 Yêu Cầu Hệ Thống

- **PHP**: 7.4 trở lên
- **MySQL**: 5.7 trở lên hoặc MariaDB 10.3+
- **Web Server**: Apache, Nginx hoặc IIS
- **Trình duyệt**: Chrome, Firefox, Safari, Edge (phiên bản mới nhất)

## 📥 Hướng Dẫn Cài Đặt

### Bước 1: Tải Dự Án
```bash
# Clone hoặc tải dự án
git clone <repo-url>
cd gym_management_system
```

### Bước 2: Tạo Database

#### Cách 1: Sử dụng phpMyAdmin
1. Mở phpMyAdmin (http://localhost/phpmyadmin)
2. Tạo database mới tên `gym_management`
3. Chọn database và vào tab "SQL"
4. Copy toàn bộ nội dung file `database/schema.sql`
5. Paste vào và chạy (Execute)

#### Cách 2: Sử dụng Command Line
```bash
mysql -u root -p < database/schema.sql
```

#### Cách 3: Sử dụng MySQL Workbench
1. Mở MySQL Workbench
2. Tạo connection đến MySQL server
3. File → Open SQL Script → Chọn `database/schema.sql`
4. Execute All

### Bước 3: Cấu Hình Kết Nối Database

Mở file `config/database.php` và cập nhật:

```php
define('DB_HOST', 'localhost');     // Địa chỉ MySQL
define('DB_USER', 'root');          // Tên đăng nhập MySQL
define('DB_PASS', '');              // Mật khẩu MySQL
define('DB_NAME', 'gym_management'); // Tên database
```

### Bước 4: Cấu Hình Web Server

#### Với Apache
1. Copy thư mục `gym_management_system` vào `htdocs`
2. Truy cập: `http://localhost/gym_management_system/`

#### Với XAMPP
1. Copy vào `C:\xampp\htdocs\`
2. Truy cập: `http://localhost/gym_management_system/`

#### Với WAMP
1. Copy vào `C:\wamp\www\`
2. Truy cập: `http://localhost/gym_management_system/`

### Bước 5: Kiểm Tra Cài Đặt
- Truy cập trang chủ: `http://localhost/gym_management_system/index.php`
- Nếu không có lỗi, hệ thống đã sẵn sàng sử dụng

## 🔐 Dữ Liệu Mẫu

Hệ thống đi kèm dữ liệu mẫu để kiểm tra:

### Hội Viên
- **HV001** - Phan Thịnh (Gói 3 tháng)
- **HV002** - Trần Hương (Gói 1 tháng)
- **HV003** - Lê Minh (Gói 6 tháng)

### Gói Tập
- **PKG001** - Gói 1 Tháng (500.000 VNĐ)
- **PKG003** - Gói 3 Tháng (1.200.000 VNĐ)
- **PKG006** - Gói 6 Tháng (2.000.000 VNĐ)
- **PKG012** - Gói 1 Năm (3.500.000 VNĐ)

## 🎯 Hướng Dẫn Sử Dụng

### 1. Thêm Hội Viên Mới
1. Vào menu → **Quản Lý Hội Viên** → **Thêm Hội Viên**
2. Nhập thông tin:
   - Họ tên
   - Ngày sinh
   - Giới tính
   - Số điện thoại (format: 0XXXXXXXXX)
   - Địa chỉ
3. Nhấn **Thêm Hội Viên**

### 2. Đăng Ký Gói Tập
1. Vào menu → **Đăng Ký & Check-in** → **Đăng Ký Gói Tập**
2. Nhập mã hội viên
3. Chọn gói tập phù hợp
4. Nhấn **Đăng Ký Gói Tập**

### 3. Check-in Vào Phòng Tập
1. Vào menu → **Check-in / Check-out**
2. Nhập mã hội viên
3. Nhấn **Check-in Vào** hoặc **Check-out Ra**

### 4. Xem Lịch Sử Tập
1. Vào menu → **Lịch Sử Tập Luyện**
2. Tìm kiếm theo mã hội viên (nếu cần)
3. Lọc theo ngày (nếu cần)

### 5. Thống Kê Hoạt Động
1. Vào menu → **Thống Kê**
2. Xem các chỉ số chính:
   - Tổng hội viên
   - Hội viên còn hạn / hết hạn
   - Lượt tập hôm nay
   - Gói tập phổ biến

## 📁 Cấu Trúc Dự Án

```
gym_management_system/
├── index.php                 # Trang chủ
├── config/
│   └── database.php         # Cấu hình database
├── includes/
│   └── functions.php        # Các hàm tiện ích
├── views/
│   ├── header.php           # Phần header chung
│   └── sidebar.php          # Sidebar menu
├── database/
│   └── schema.sql           # Cấu trúc database
├── members.php              # Danh sách hội viên
├── add_member.php           # Thêm hội viên
├── edit_member.php          # Chỉnh sửa hội viên
├── packages.php             # Danh sách gói tập
├── add_package.php          # Thêm gói tập
├── edit_package.php         # Chỉnh sửa gói tập
├── register_package.php     # Đăng ký gói tập
├── checkin.php              # Check-in/check-out
├── history.php              # Lịch sử tập luyện
├── statistics.php           # Thống kê
├── search.php               # Tìm kiếm
└── README.md                # Tài liệu này
```

## 🗄️ Cấu Trúc Database

### Bảng: members (Hội Viên)
```
- id (INT, Primary Key)
- code (VARCHAR(20), UNIQUE) - Mã hội viên
- fullname (VARCHAR(100))
- dob (DATE)
- gender (ENUM: Nam/Nữ)
- phone (VARCHAR(15))
- address (TEXT)
```

### Bảng: packages (Gói Tập)
```
- id (INT, Primary Key)
- code (VARCHAR(20), UNIQUE)
- name (VARCHAR(100))
- duration (INT) - Tháng
- price (DECIMAL(10,2))
- description (TEXT)
- status (ENUM: active/inactive)
```

### Bảng: registrations (Đăng Ký)
```
- id (INT, Primary Key)
- member_id (INT, FK)
- package_id (INT, FK)
- start_date (DATE)
- end_date (DATE)
- status (ENUM: active/expired)
```

### Bảng: check_logs (Lịch Sử Check-in)
```
- id (INT, Primary Key)
- member_id (INT, FK)
- log_date (DATE)
- check_in (TIME)
- check_out (TIME)
```

## 🔧 Cách Sao Lưu & Khôi Phục

### Sao Lưu Database
```bash
mysqldump -u root -p gym_management > backup.sql
```

### Khôi Phục Database
```bash
mysql -u root -p gym_management < backup.sql
```

## ⚙️ Các Thông Báo Lỗi Phổ Biến

| Lỗi | Nguyên Nhân | Cách Khắc Phục |
|-----|-----------|----------------|
| "Lỗi kết nối" | Database chưa cấu hình | Kiểm tra file `config/database.php` |
| "Bảng không tồn tại" | Database chưa được tạo | Chạy file `database/schema.sql` |
| "Số điện thoại không hợp lệ" | Format sai | Nhập theo format 0XXXXXXXXX |
| "Mã đã tồn tại" | Trùng mã | Hệ thống sẽ tự tạo mã duy nhất |

## 📞 Hỗ Trợ & Phát Triển

### Để Liên Hệ
- Email: support@gymmanagement.vn
- Website: https://gymmanagement.vn

### Các Tính Năng Sắp Có
- 🔜 Quản lý tài khoản & đăng nhập
- 🔜 Tích hợp QR code check-in
- 🔜 Ứng dụng mobile
- 🔜 Thông báo qua email/SMS
- 🔜 Xuất báo cáo PDF/Excel
- 🔜 Quản lý HLV & lịch tập

## 📄 License

Dự án này được cấp phép theo MIT License.

## 🙏 Cảm Ơn

Cảm ơn bạn đã sử dụng Hệ Thống Quản Lý Phòng Gym!

---

**Phiên Bản:** 1.0  
**Cập Nhật Lần Cuối:** 2025-03-09
