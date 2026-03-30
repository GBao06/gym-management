<div class="col-md-3">
    <div class="sidebar">
        <div class="mb-3 pb-3 border-bottom">
            <h5 class="text-primary"><i class="fas fa-bars"></i> Menu Quản Lý</h5>
        </div>

        <div class="nav flex-column">
            <h6 class="text-secondary small text-uppercase mb-2">Hội Viên</h6>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'members.php') ? 'active' : ''; ?>" 
               href="members.php">
                <i class="fas fa-users"></i> Danh Sách Hội Viên
            </a>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_member.php') ? 'active' : ''; ?>" 
               href="add_member.php">
                <i class="fas fa-user-plus"></i> Thêm Hội Viên
            </a>

            <h6 class="text-secondary small text-uppercase mt-3 mb-2">Gói Tập</h6>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'packages.php') ? 'active' : ''; ?>" 
               href="packages.php">
                <i class="fas fa-box"></i> Danh Sách Gói Tập
            </a>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_package.php') ? 'active' : ''; ?>" 
               href="add_package.php">
                <i class="fas fa-plus-circle"></i> Thêm Gói Tập
            </a>

            <h6 class="text-secondary small text-uppercase mt-3 mb-2">Đăng Ký & Check-in</h6>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'register_package.php') ? 'active' : ''; ?>" 
               href="register_package.php">
                <i class="fas fa-clipboard-check"></i> Đăng Ký Gói Tập
            </a>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'checkin.php') ? 'active' : ''; ?>" 
               href="checkin.php">
                <i class="fas fa-sign-in-alt"></i> Check-in / Check-out
            </a>

            <h6 class="text-secondary small text-uppercase mt-3 mb-2">Thống Kê & Báo Cáo</h6>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'history.php') ? 'active' : ''; ?>" 
               href="history.php">
                <i class="fas fa-history"></i> Lịch Sử Tập Luyện
            </a>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'statistics.php') ? 'active' : ''; ?>" 
               href="statistics.php">
                <i class="fas fa-chart-bar"></i> Thống Kê
            </a>

            <h6 class="text-secondary small text-uppercase mt-3 mb-2">Tìm Kiếm</h6>
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'search.php') ? 'active' : ''; ?>" 
               href="search.php">
                <i class="fas fa-search"></i> Tìm Hội Viên
            </a>
        </div>
    </div>
</div>
