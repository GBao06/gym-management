<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Gym Management' : 'Gym Management System'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1f3a7a;
            --secondary-color: #ff6b6b;
            --success-color: #51cf66;
            --warning-color: #ffd43b;
            --danger-color: #ff6b6b;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2d5aa8 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: white !important;
        }

        .sidebar {
            background: white;
            min-height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 4px rgba(0,0,0,0.05);
        }

        .sidebar .nav-link {
            color: #333;
            margin: 10px 0;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .main-content {
            padding: 30px;
            background-color: #f5f5f5;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2d5aa8 100%);
            color: white;
            border-radius: 8px 8px 0 0 !important;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: #1a2e5c;
        }

        .btn-success {
            background-color: var(--success-color);
            border: none;
        }

        .btn-success:hover {
            background-color: #40c057;
        }

        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .badge-active {
            background-color: var(--success-color);
        }

        .badge-inactive {
            background-color: var(--danger-color);
        }

        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(31, 58, 122, 0.25);
        }

        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: var(--primary-color);
            font-weight: bold;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            text-align: center;
            margin-bottom: 20px;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .stat-card .stat-label {
            color: #666;
            margin-top: 10px;
        }

        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main-content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">s
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-dumbbell"> </i> GYM Thịnh GM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link" style="cursor: default;">
                            <i class="fas fa-user"></i> Admin
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#aboutModal">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Giới thiệu Modal -->
    <div class="modal fade" id="aboutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Về Hệ Thống</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Hệ Thống Quản Lý Hội Viên Phòng Gym</h6>
                    <p>Phiên bản: 1.0</p>
                    <p>Giúp quản lý thông tin hội viên, gói tập, check-in/out và thống kê hoạt động phòng gym một cách hiệu quả.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
