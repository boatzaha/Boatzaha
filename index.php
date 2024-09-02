<?php
session_start();
require 'db.php';

// ตรวจสอบว่ามีการเข้าสู่ระบบหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลจาก session
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// กำหนดการเรียงลำดับ
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$show_my_data = isset($_GET['show_my_data']) && $_GET['show_my_data'] == '1';

// Pagination settings
$limits = [10, 20, 30, 'All'];
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($limit == 'All') ? 0 : ($page - 1) * (int)$limit;

// Search customers
$search_result_customers = [];
$search_query_customers = "";
$customers = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_customers'])) {
    $search_query_customers = trim($_POST['search_customers']);
    $sql = "SELECT * FROM customers WHERE (name LIKE :search OR class LIKE :search OR department LIKE :search OR status LIKE :search OR client_group LIKE :search)";
    if ($show_my_data) {
        $sql .= " AND created_by = :created_by";
    }
    $sql .= " ORDER BY created_at $order";
    if ($limit != 'All') {
        $sql .= " LIMIT :limit OFFSET :offset";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':search', "%$search_query_customers%");
    if ($show_my_data) {
        $stmt->bindValue(':created_by', $username);
    }
    if ($limit != 'All') {
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    $stmt->execute();
    $search_result_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT * FROM customers";
    if ($show_my_data) {
        $sql .= " WHERE created_by = :created_by";
    }
    $sql .= " ORDER BY created_at $order";
    if ($limit != 'All') {
        $sql .= " LIMIT :limit OFFSET :offset";
    }
    $stmt = $conn->prepare($sql);
    if ($show_my_data) {
        $stmt->bindValue(':created_by', $username);
    }
    if ($limit != 'All') {
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get total number of customers
$sql = "SELECT COUNT(*) FROM customers";
if ($show_my_data) {
    $sql .= " WHERE created_by = :created_by";
}
$stmt = $conn->prepare($sql);
if ($show_my_data) {
    $stmt->bindValue(':created_by', $username);
}
$stmt->execute();
$total_customers = $stmt->fetchColumn();
$total_pages = ($limit == 'All') ? 1 : ceil($total_customers / $limit);

function formatCurrency($number) {
    return number_format($number, 2, '.', ',');
}

function formatDate($date) {
    if ($date === '0000-00-00') {
        return 'N/A';
    } else {
        return date('d/m/Y', strtotime($date));
    }
}

function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'booked':
            return 'status-booked';
        case 'unsuccessful':
            return 'status-unsuccessful';
        case 'approached':
        case 'identify':
        case 'quoting':
            return 'status-yellow';
        default:
            return '';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Customer Management</title>
    <link rel="icon" href="image/HOWDENLOGO.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stleyesindex.css"> <!-- ลิงก์ไปยังไฟล์ CSS -->
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
                        <!-- <a class="dropdown-item" href="dashboard2.php">Dashboard</a> -->
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

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Search Customer Data</h3>
        </div>
        <div class="card-body">
            <form method="post" action="index.php" class="form-inline mb-3" onsubmit="return validateForm()">
                <div class="form-group mr-2">
                    <input type="text" id="search_customers" name="search_customers" class="form-control" placeholder="Search Data" value="<?= htmlspecialchars($search_query_customers) ?>">
                </div>
                <button type="submit" class="btn btn-primary mr-2">Search</button>
                <a href="create.php" class="btn btn-success mr-2">Add Data</a>
                <?php if ($role == 'manager'): ?>
                    <a href="add_client_group.php" class="btn btn-success mr-2">Add Client Group</a>
                <?php endif; ?>
                <a href="export.php" class="btn btn-info mr-2">Export All</a>
                <div class="form-group ml-2">
                    <label for="limit" class="mr-2">Show:</label>
                    <select id="limit" name="limit" class="form-control" onchange="window.location.href='index.php?limit='+this.value+'&order=<?= $order ?>&show_my_data=<?= $show_my_data ? '1' : '0' ?>';">
                        <?php foreach ($limits as $l): ?>
                            <option value="<?= $l ?>" <?= $l == $limit ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group ml-2">
                    <label for="order" class="mr-2">Order:</label>
                    <select id="order" name="order" class="form-control" onchange="window.location.href='index.php?limit=<?= $limit ?>&order='+this.value+'&show_my_data=<?= $show_my_data ? '1' : '0' ?>';">
                        <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Newest First</option>
                        <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Oldest First</option>
                    </select>
                </div>
                <div class="form-group ml-2">
                    <input type="checkbox" id="show_my_data" name="show_my_data" <?= $show_my_data ? 'checked' : '' ?> onchange="window.location.href='index.php?limit=<?= $limit ?>&order=<?= $order ?>&show_my_data='+ (this.checked ? '1' : '0');">
                    <label for="show_my_data"> My data</label>
                </div>
                <div class="form-group ml-2" id="export_my_data_container" style="display: <?= $show_my_data ? 'block' : 'none' ?>;">
                    <a href="export.php?created_by=<?= htmlspecialchars($username) ?>" id="export_my_data" class="btn btn-info">Export</a>
                </div>
            </form>
            <h2>Customer List</h2>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Client Group</th>
                            <th>Client Company Name</th> 
                            <th>Inception Date</th>
                            <th>Income Class</th>
                            <th>Revenue</th>
                            <th>Premium</th>
                            <th>Close Date</th>
                            <th>Department</th>
                            <th>Funnel stage</th>
                            <th class="actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $order_num = $offset + 1;
                        if (!empty($search_result_customers)): ?>
                            <?php foreach ($search_result_customers as $customer): ?>
                            <tr>
                                <td><?= $order_num++ ?></td>
                                <td><?= isset($customer['client_group']) ? htmlspecialchars($customer['client_group']) : 'N/A' ?></td>
                                <td><?= htmlspecialchars($customer['name']) ?></td>
                                <td><?= formatDate($customer['inception_date']) ?></td>
                                <td><?= htmlspecialchars($customer['class']) ?></td>
                                <td><?= formatCurrency($customer['revenue']) ?></td>
                                <td><?= formatCurrency($customer['premium']) ?></td>
                                <td><?= formatDate($customer['close_date']) ?></td>
                                <td><?= htmlspecialchars($customer['department']) ?></td>
                                <td><span class="status-badge <?= getStatusClass($customer['status']) ?>"><?= htmlspecialchars($customer['status']) ?></span></td>
                                <td class="btn-group">
                                    <a href="view.php?id=<?= $customer['id'] ?>" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                    <?php if ($_SESSION['username'] == $customer['created_by']): ?>
                                        <a href="edit.php?id=<?= $customer['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="delete.php?id=<?= $customer['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger btn-sm" title="Delete"><i class="fas fa-trash-alt"></i></a>
                                    <?php else: ?>
                                        <a href="#" class="btn btn-grey btn-sm" title="Edit" onclick="return false;"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="btn btn-grey btn-sm" title="Delete" onclick="return false;"><i class="fas fa-trash-alt"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php if (!empty($customers)): ?>
                                <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?= $order_num++ ?></td>
                                    <td><?= isset($customer['client_group']) ? htmlspecialchars($customer['client_group']) : 'N/A' ?></td>
                                    <td><?= htmlspecialchars($customer['name']) ?></td>
                                    <td><?= formatDate($customer['inception_date']) ?></td>
                                    <td><?= htmlspecialchars($customer['class']) ?></td>
                                    <td><?= formatCurrency($customer['revenue']) ?></td>
                                    <td><?= formatCurrency($customer['premium']) ?></td>
                                    <td><?= formatDate($customer['close_date']) ?></td>
                                    <td><?= htmlspecialchars($customer['department']) ?></td>
                                    <td><span class="status-badge <?= getStatusClass($customer['status']) ?>"><?= htmlspecialchars($customer['status']) ?></span></td>
                                    <td class="btn-group">
                                        <a href="view.php?id=<?= $customer['id'] ?>" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                        <?php if ($_SESSION['username'] == $customer['created_by']): ?>
                                            <a href="edit.php?id=<?= $customer['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="delete.php?id=<?= $customer['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger btn-sm" title="Delete"><i class="fas fa-trash-alt"></i></a>
                                        <?php else: ?>
                                            <a href="#" class="btn btn-grey btn-sm" title="Edit" onclick="return false;"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="btn btn-grey btn-sm" title="Delete" onclick="return false;"><i class="fas fa-trash-alt"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">No data found in the system</td>
                                </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?page=<?= $page - 1 ?>&limit=<?= $limit ?>&order=<?= $order ?>&show_my_data=<?= $show_my_data ? '1' : '0' ?>"><i class="fas fa-arrow-left"></i> Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?page=<?= $i ?>&limit=<?= $limit ?>&order=<?= $order ?>&show_my_data=<?= $show_my_data ? '1' : '0' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?page=<?= $page + 1 ?>&limit=<?= $limit ?>&order=<?= $order ?>&show_my_data=<?= $show_my_data ? '1' : '0' ?>">Next <i class="fas fa-arrow-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="footer">
    <p>Copyright © Boat Patthanapong.URU Version 1.0.0</p>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function validateForm() {
        const searchInput = document.getElementById('search_customers').value;
        const alertBox = document.getElementById('alert');
        if (!searchInput) {
            alertBox.style.display = 'block';
            return false;
        }
        alertBox.style.display = 'none';
        return true;
    }

    function toggleExportButton() {
        const checkbox = document.getElementById('show_my_data');
        const exportButtonContainer = document.getElementById('export_my_data_container');
        if (checkbox.checked) {
            exportButtonContainer.style.display = 'block';
        } else {
            exportButtonContainer.style.display = 'none';
        }
    }
</script>
</body>
</html>
