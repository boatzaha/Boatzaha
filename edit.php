<?php
session_start();
require 'db.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $inception_date_raw = $_POST['inception_date'];
    $inception_date = DateTime::createFromFormat('Y-m-d', $inception_date_raw);
    $class = $_POST['class'];
    $revenue = str_replace(',', '', $_POST['revenue']);
    $premium = str_replace(',', '', $_POST['premium']);
    $sum_insured = str_replace(',', '', $_POST['sum_insured']);
    $department = $_POST['department'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $policy_type = $_POST['policy_type'];

    if ($status == 'Booked' || $status == 'Unsuccessful') {
        $close_date_raw = $_POST['close_date'];
        if (!empty($close_date_raw)) {
            $close_date = DateTime::createFromFormat('Y-m-d', $close_date_raw);
            if ($close_date) {
                $close_date_formatted = $close_date->format('Y-m-d');
            } else {
                $close_date_formatted = date('Y-m-d');
            }
        } else {
            $close_date_formatted = date('Y-m-d');
        }
    } else {
        $close_date_formatted = '0000-00-00';
    }

    if ($inception_date === false) {
        echo "Invalid inception date.";
        exit();
    }

    $sql = "UPDATE customers SET 
                name = :name, 
                inception_date = :inception_date, 
                class = :class, 
                revenue = :revenue, 
                premium = :premium, 
                sum_insured = :sum_insured, 
                close_date = :close_date, 
                department = :department, 
                status = :status, 
                description = :description, 
                policy_type = :policy_type 
            WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':inception_date', $inception_date->format('Y-m-d'));
    $stmt->bindParam(':class', $class);
    $stmt->bindParam(':revenue', $revenue);
    $stmt->bindParam(':premium', $premium);
    $stmt->bindParam(':sum_insured', $sum_insured);
    $stmt->bindParam(':close_date', $close_date_formatted);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':policy_type', $policy_type);
    $stmt->bindParam(':id', $id);

    try {
        $stmt->execute();
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    $sql = "SELECT * FROM customers WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        echo "Customer not found";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
    <link rel="icon" href="image/HOWDENLOGO.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 900px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h1 {
            font-size: 2.5rem;
            color: #343a40;
            margin-bottom: 30px;
        }
        .form-group label {
            font-weight: 600;
            color: #495057;
        }
        .form-control {
            border-radius: 5px;
            padding: 10px;
            font-size: 1rem;
        }
        .btn-primary, .btn-secondary {
            padding: 5px 15px;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .form-group.col-md-6 {
            flex: 0 0 48%;
            max-width: 48%;
        }
        .form-group.col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edit Record</h1>
            <div class="action-buttons">
                <a href="index.php" class="btn btn-secondary">Back</a>
                <button type="submit" form="editForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
        <form id="editForm" method="post" action="edit.php?id=<?= $id ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Client Company Name:</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="inception_date">Inception Date:</label>
                    <input type="date" class="form-control" id="inception_date" name="inception_date" value="<?= htmlspecialchars($customer['inception_date']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="class">Income Class:</label>
                    <select class="form-control" id="class" name="class" required>
                        <!-- Populate class options dynamically here -->
                        <option value="AV" <?= $customer['class'] == 'AV' ? 'selected' : '' ?>>AV-Aviation Insurance</option>
                        <!-- other options... -->
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="revenue">Revenue:</label>
                    <input type="text" class="form-control" id="revenue" name="revenue" value="<?= htmlspecialchars($customer['revenue']) ?>" onblur="formatCurrency(this)" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="premium">Premium:</label>
                    <input type="text" class="form-control" id="premium" name="premium" value="<?= htmlspecialchars($customer['premium']) ?>" onblur="formatCurrency(this)" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="sum_insured">Sum Insured:</label>
                    <input type="text" class="form-control" id="sum_insured" name="sum_insured" value="<?= htmlspecialchars($customer['sum_insured']) ?>" onblur="formatCurrency(this)" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="department">Department:</label>
                    <select class="form-control" id="department" name="department" required>
                        <option value="EB" <?= $customer['department'] == 'EB' ? 'selected' : '' ?>>EB</option>
                        <option value="Property&Castalty" <?= $customer['department'] == 'Property&Castalty' ? 'selected' : '' ?>>Property&Castalty</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="status">Funnel stage:</label>
                    <select class="form-control" id="status" name="status" required onchange="checkStatus(this.value)">
                        <option value="Approached" <?= $customer['status'] == 'Approached' ? 'selected' : '' ?>>Approached</option>
                        <option value="Booked" <?= $customer['status'] == 'Booked' ? 'selected' : '' ?>>Booked</option>
                        <option value="Identify" <?= $customer['status'] == 'Identify' ? 'selected' : '' ?>>Identify</option>
                        <option value="Quoting" <?= $customer['status'] == 'Quoting' ? 'selected' : '' ?>>Quoting</option>
                        <option value="Unsuccessful" <?= $customer['status'] == 'Unsuccessful' ? 'selected' : '' ?>>Unsuccessful</option>
                    </select>
                </div>
            </div>
            <div class="form-row" id="close_date_group" <?= ($customer['status'] == 'Booked' || $customer['status'] == 'Unsuccessful') ? '' : 'style="display: none;"' ?>>
                <div class="form-group col-md-6">
                    <label for="close_date">Close Date:</label>
                    <input type="date" class="form-control" id="close_date" name="close_date" value="<?= htmlspecialchars($customer['close_date']) ?>">
                </div>
            </div>
            <div class="form-group col-md-12">
                <label for="policy_type">Policy Type:</label>
                <select class="form-control" id="policy_type" name="policy_type">
                    <option value="">Select Policy Type</option>
                    <option value="NewRecurring" <?= $customer['policy_type'] == 'NewRecurring' ? 'selected' : '' ?>>New-Recurring</option>
                    <!-- other options... -->
                </select>
            </div>
            <div class="form-group col-md-12">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="2"><?= htmlspecialchars($customer['description']) ?></textarea>
            </div>
        </form>
    </div>
    <div class="footer">
        <p>Copyright © Boat Patthanapong.URU Verion 1.0.0</p>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.amazonaws.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    function checkStatus(value) {
        var closeDateGroup = document.getElementById('close_date_group');
        var closeDateInput = document.getElementById('close_date');
        var closeDateLabel = document.querySelector('label[for="close_date"]');
        
        if (value === 'Booked' || value === 'Unsuccessful') {
            closeDateGroup.style.display = 'block';
            closeDateInput.required = true;
            closeDateInput.style.borderColor = 'red';
            closeDateLabel.style.color = 'red';
        } else {
            closeDateGroup.style.display = 'none';
            closeDateInput.required = false;
            closeDateInput.value = '';
            closeDateInput.style.borderColor = '';
            closeDateLabel.style.color = '';
        }
    }

    function formatCurrency(input) {
        let value = input.value.replace(/,/g, '');
        
        // ตรวจสอบว่าค่าที่พิมพ์เป็นตัวเลขที่ถูกต้องหรือไม่
        if (!isNaN(value) && value !== "") {
            // แปลงค่าเป็นจำนวนทศนิยม และแสดงด้วยลูกน้ำและจุดทศนิยม
            input.value = parseFloat(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } else {
            // ถ้าไม่ใช่ตัวเลข ให้ตั้งค่า input ให้เป็นค่าว่าง
            input.value = "";
        }
    }
    </script>
</body>
</html>
