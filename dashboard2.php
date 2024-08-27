<?php
session_start();
require 'db.php';

// ตรวจสอบว่า user มีสิทธิ์เป็น manager หรือไม่
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'manager') {
    echo "You do not have permission to access this page.";
    exit();
}

// ดึงข้อมูลจาก session
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// ดึงข้อมูลจำนวนผู้ใช้ทั้งหมด
$sql = "SELECT COUNT(*) as user_count FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$user_count = $stmt->fetch(PDO::FETCH_ASSOC)['user_count'];

// ดึงข้อมูลจำนวนลูกค้าทั้งหมด
$sql = "SELECT COUNT(*) as customer_count FROM customers";
$stmt = $conn->prepare($sql);
$stmt->execute();
$customer_count = $stmt->fetch(PDO::FETCH_ASSOC)['customer_count'];

// ดึงข้อมูลจำนวนลูกค้าที่แต่ละ user กรอกข้อมูล
$sql = "SELECT created_by, COUNT(*) as customer_count FROM customers GROUP BY created_by";
$stmt = $conn->prepare($sql);
$stmt->execute();
$user_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลยอดขายรวม
$sql = "SELECT SUM(revenue) as total_revenue, SUM(premium) as total_premium, SUM(sum_insured) as total_sum_insured FROM customers";
$stmt = $conn->prepare($sql);
$stmt->execute();
$sales_data = $stmt->fetch(PDO::FETCH_ASSOC);

// ดึงข้อมูลสถานะลูกค้า
$sql = "SELECT status, COUNT(*) as status_count FROM customers GROUP BY status";
$stmt = $conn->prepare($sql);
$stmt->execute();
$status_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลแผนกลูกค้า
$sql = "SELECT department, COUNT(*) as department_count FROM customers GROUP BY department";
$stmt = $conn->prepare($sql);
$stmt->execute();
$department_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatCurrency($number) {
    return number_format($number, 2, '.', ',');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <link rel="icon" href="image/HOWDENLOGO.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40; /* สีเทาเข้ม */
            padding: 15px;
        }
        .navbar-brand img {
            height: 40px;
            margin-right: 15px;
        }
        .navbar-nav .nav-item .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            margin-right: 15px;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-item .nav-link:hover {
            color: #adb5bd !important; /* สีเทาอ่อน */
        }
        .navbar-text {
            font-weight: 600;
            color: #adb5bd !important; /* สีเทาอ่อน */
            margin-right: 15px;
        }
        .container {
            margin-top: 20px;
        }
        h1 {
            font-size: 2rem; /* ลดขนาดฟอนต์ */
            font-weight: 700;
            color: #343a40; /* สีเทาเข้ม */
            margin-bottom: 20px; /* ลดระยะห่างด้านล่าง */
        }
        .feature-box {
            text-align: center;
            padding: 15px; /* ลดขนาด Padding */
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px; /* ลด Margin */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #495057; /* สีเทาเข้มกลาง */
            color: #ffffff;
        }
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .feature-box i {
            font-size: 3em; /* ลดขนาดไอคอน */
            margin-bottom: 10px; /* ลด Margin */
        }
        .feature-box h3 {
            font-size: 1.2em; /* ลดขนาดฟอนต์ */
            margin-bottom: 5px; /* ลด Margin */
        }
        .feature-box p {
            font-size: 1em; /* ลดขนาดฟอนต์ */
            margin-bottom: 5px; /* ลด Margin */
        }
        .chart-container {
            width: 100%;
            height: 250px; /* ลดความสูงของแผนภูมิ */
            margin-top: 20px;
        }
        .chart-row {
            display: flex;
            justify-content: space-around;
        }
        .chart-item {
            width: 48%; /* ให้แผนภูมิอยู่ข้างกันและแบ่งเป็นสองส่วน */
        }
        .navbar-dark .navbar-nav .nav-item .dropdown-menu {
            background-color: #95a5a6;
            border-radius: 5px;
            border: 1px solid #007bff;
        }

        .dropdown-item.dropdown-custom {
            color: #ffffff;
            transition: background-color 0.3s, padding-left 0.3s;
        }

        .dropdown-item.dropdown-custom:hover {
            background-color: #007bff;
            padding-left: 20px;
        }

        .dropdown-item.dropdown-custom:focus {
            background-color:#8395a7;
            color: #ffffff;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index2.php">Customer Management</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="navbar-text text-white mr-3">User: <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index2.php">Home Page</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Refresh</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Menu
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php if ($role != 'officer'): ?>
                        <a class="dropdown-item" href="dashboard.php">Permission</a>
                        <a class="dropdown-item" href="dashboard2.php">Dashboard</a>
                    <?php endif; ?>
                    <a class="dropdown-item" href="index.php">Add Customer</a>
                    <a class="dropdown-item" href="clamdb/indexclam.php">Claim Reports</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
    <div class="container">
        <h1>Manager Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="feature-box bg-primary text-white">
                    <i class="fas fa-users"></i>
                    <h3>Total Users</h3>
                    <p><?= $user_count ?> users</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box bg-success text-white">
                    <i class="fas fa-database"></i>
                    <h3>Total Customers</h3>
                    <p><?= $customer_count ?> customers</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box bg-warning text-white">
                    <i class="fas fa-coins"></i>
                    <h3>Total Revenue</h3>
                    <p><?= formatCurrency($sales_data['total_revenue']) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box bg-danger text-white">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Total Premium</h3>
                    <p><?= formatCurrency($sales_data['total_premium']) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box bg-info text-white">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h3>Total Sum Insured</h3>
                    <p><?= formatCurrency($sales_data['total_sum_insured']) ?></p>
                </div>
            </div>
        </div>
        <div class="chart-row">
            <div class="chart-item">
                <canvas id="statusChart" width="300" height="300"></canvas>
        </div>
            <div class="chart-item">
                <canvas id="departmentChart" width="300" height="300"></canvas>
        </div>
            </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    var statusCtx = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: [<?php foreach($status_data as $status) { echo '"' . $status['status'] . '",'; } ?>],
            datasets: [{
                data: [<?php foreach($status_data as $status) { echo $status['status_count'] . ','; } ?>],
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'],
                borderColor: '#ffffff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: false, 
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    var departmentCtx = document.getElementById('departmentChart').getContext('2d');
    var departmentChart = new Chart(departmentCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php foreach($department_data as $dept) { echo '"' . $dept['department'] . '",'; } ?>],
            datasets: [{
                data: [<?php foreach($department_data as $dept) { echo $dept['department_count'] . ','; } ?>],
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'],
                borderColor: '#ffffff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: false, // เปลี่ยนจาก true เป็น false
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
</body>
</html>
