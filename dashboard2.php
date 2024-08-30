<?php
session_start();
require 'db.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'manager') {
    echo "You do not have permission to access this page.";
    exit();
}

// ดึงข้อมูลจาก session
$username = htmlspecialchars($_SESSION['username']);
$role = htmlspecialchars($_SESSION['role']);

// ฟังก์ชันการจัดรูปแบบตัวเลขให้เป็นสกุลเงิน
function formatCurrency($number) {
    return number_format($number, 2, '.', ',');
}

// ดึงข้อมูลจากฐานข้อมูล
try {
    // จำนวนผู้ใช้ทั้งหมด
    $stmt = $conn->prepare("SELECT COUNT(*) as user_count FROM users");
    $stmt->execute();
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['user_count'];

    // จำนวนลูกค้าทั้งหมด
    $stmt = $conn->prepare("SELECT COUNT(*) as customer_count FROM customers");
    $stmt->execute();
    $customer_count = $stmt->fetch(PDO::FETCH_ASSOC)['customer_count'];

    // จำนวนลูกค้าตามผู้ใช้ที่กรอกข้อมูล
    $stmt = $conn->prepare("SELECT created_by, COUNT(*) as customer_count FROM customers GROUP BY created_by");
    $stmt->execute();
    $user_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ยอดขายรวม
    $stmt = $conn->prepare("SELECT SUM(revenue) as total_revenue, SUM(premium) as total_premium, SUM(sum_insured) as total_sum_insured FROM customers");
    $stmt->execute();
    $sales_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // สถานะลูกค้า
    $stmt = $conn->prepare("SELECT status, COUNT(*) as status_count FROM customers GROUP BY status");
    $stmt->execute();
    $status_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // แผนกลูกค้า
    $stmt = $conn->prepare("SELECT department, COUNT(*) as department_count FROM customers GROUP BY department");
    $stmt->execute();
    $department_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
    <link rel="stylesheet" href="css/stleyesdashboard2.css">
    <style>
    .dashboard-container {
        max-width: 90%; /* ปรับขนาด container */
        padding: 20px; /* เพิ่ม padding */
        background-color: #ffffff; /* เพิ่มพื้นหลัง */
        border-radius: 10px; /* เพิ่มขอบมุมมน */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* เพิ่มเงา */
        margin: auto; /* จัดกึ่งกลางหน้า */
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around; /* จัดการ์ดให้อยู่ตรงกลาง */
        margin-bottom: 10px; 
    }

    .col-md-3 {
        flex: 1 1 22%; /* ขนาดการ์ดจะเท่ากันในทุกคอลัมน์ */
        margin: 10px;
        text-align: center;
    }

    .feature-box {
        padding: 15px;
        font-size: 12px;
        background-color: #f8f9fa;
        border-radius: 5px;
        box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
    }

    .chart-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around; /* จัดกราฟให้อยู่ตรงกลาง */
        margin-top: 20px;
        background-color: #ffffff; /* เพิ่มพื้นหลังสีขาว */
        padding: 20px; /* เพิ่ม padding */
        border-radius: 10px; /* เพิ่มขอบมุมมน */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* เพิ่มเงา */
    }

    .chart-item {
        flex: 1 1 45%; 
        min-width: 250px;
        max-width: 300px; /* จำกัดขนาดความกว้างสูงสุด */
        margin-bottom: 20px;
        padding: 15px; 
        background-color: #f8f9fa;
        border-radius: 5px;
        box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
    }

    canvas {
        width: 100% !important;
        height: auto !important;
    }

    h1 {
        font-size: 18px;
        text-align: center;
        margin-bottom: 20px; 
    }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="index2.php">
        <img src="image/HOWDEN2.png" alt="Logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <span class="navbar-text">Hi User: <span><?= htmlspecialchars($username) ?></span> (<?= htmlspecialchars($role) ?>)</span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="index2.php">Home Page</a>
        </li>
        <!-- Dropdown Menu -->
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

<!-- ใช้ <div> เพื่อคลุมเนื้อหา -->
<div class="dashboard-container">
    <h1>Manager Dashboard</h1>
    <div class="row">
        <div class="col-md-3">
            <div class="feature-box bg-primary text-white">
                <h3>Total Users</h3>
                <p><?= $user_count ?> users</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="feature-box bg-success text-white">
                <h3>Total Customers</h3>
                <p><?= $customer_count ?> customers</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="feature-box bg-warning text-white">
                <h3>Total Revenue</h3>
                <p><?= formatCurrency($sales_data['total_revenue']) ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="feature-box bg-danger text-white">
                <h3>Total Premium</h3>
                <p><?= formatCurrency($sales_data['total_premium']) ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="feature-box bg-info text-white">
                <h3>Total Sum Insured</h3>
                <p><?= formatCurrency($sales_data['total_sum_insured']) ?></p>
            </div>
        </div>
    </div>
    <div class="chart-container">
        <div class="chart-item">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="chart-item">
            <canvas id="departmentChart"></canvas>
        </div>
        <div class="chart-item">
            <canvas id="userCustomersChart"></canvas>
        </div>
        <div class="chart-item">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Status Chart
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
            responsive: true, 
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Department Chart
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
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // User Customers Chart
    var userCustomersCtx = document.getElementById('userCustomersChart').getContext('2d');
    var userCustomersChart = new Chart(userCustomersCtx, {
        type: 'bar',
        data: {
            labels: [<?php foreach($user_customers as $user) { echo '"' . $user['created_by'] . '",'; } ?>],
            datasets: [{
                label: 'Number of Customers',
                data: [<?php foreach($user_customers as $user) { echo $user['customer_count'] . ','; } ?>],
                backgroundColor: '#28a745',
                borderColor: '#ffffff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Revenue Chart
    var revenueCtx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: [<?php foreach($user_customers as $user) { echo '"' . $user['created_by'] . '",'; } ?>],
            datasets: [{
                label: 'Total Revenue',
                data: [<?php foreach($user_customers as $user) { echo $sales_data['total_revenue'] . ','; } ?>],
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: '#28a745',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
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