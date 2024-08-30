<?php
session_start();
require 'db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM customers WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

function formatCurrency($number) {
    return number_format($number, 2, '.', ',') . ' บาท';
}

function formatDate($date) {
    if ($date === '0000-00-00' || $date === null) {
        return 'N/A';
    } else {
        return htmlspecialchars(date('d/m/Y', strtotime($date)));
    }
}

$is_owner = ($_SESSION['username'] == $customer['created_by']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Record</title>
    <link rel="icon" href="image/HOWDENLOGO.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table th, .table td {
            vertical-align: middle;
            word-wrap: break-word;
            max-width: 200px;
        }
        .container {
            margin-top: 50px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-container {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="header">
            <h1>View Record</h1>
            <div class="btn-container">
                <a href="index.php" class="btn btn-secondary">Back</a>
                <?php if ($is_owner): ?>
                    <a href="edit.php?id=<?= $customer['id'] ?>" class="btn btn-warning">Edit</a>
                <?php endif; ?>
            </div>
        </div>
        <table class="table table-bordered">
            <tr>
                <th>Client Company Name</th>
                <td><?= htmlspecialchars($customer['name']) ?></td>
            </tr>
            <tr>
                <th>Inception Date</th>
                <td><?= formatDate($customer['inception_date']) ?></td>
            </tr>
            <tr>
                <th>income-Class</th>
                <td><?= htmlspecialchars($customer['class']) ?></td>
            </tr>
            <tr>
                <th>Revenue</th>
                <td><?= formatCurrency($customer['revenue']) ?></td>    
            </tr>
            <tr>
                <th>Premium</th>
                <td><?= formatCurrency($customer['premium']) ?></td>
            </tr>
            <tr>
                <th>Sum Insured</th>
                <td><?= formatCurrency($customer['sum_insured']) ?></td>
            </tr>
            <tr>
                <th>Close Date</th>
                <td><?= formatDate($customer['close_date']) ?></td>
            </tr>
            <tr>
                <th>Department</th>
                <td><?= htmlspecialchars($customer['department']) ?></td>
            </tr>
            <tr>
                <th>Funnelstage</th>
                <td><?= htmlspecialchars($customer['status']) ?></td>
            </tr>
            <tr>
                <th>Policy Type</th>
                <td><?= htmlspecialchars($customer['policy_type']) ?></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?= htmlspecialchars($customer['description']) ?></td>
            </tr>
            <tr>
                <th>Created By</th>
                <td><?= htmlspecialchars($customer['created_by']) ?></td>
            </tr>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.amazonaws.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
