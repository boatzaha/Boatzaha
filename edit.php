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

    // ตรวจสอบสถานะและกำหนดค่า close_date
    if ($status == 'Booked' || $status == 'Unsuccessful') {
        $close_date_raw = $_POST['close_date'];
        if (!empty($close_date_raw)) {
            $close_date = DateTime::createFromFormat('Y-m-d', $close_date_raw);
            if ($close_date) {
                $close_date_formatted = $close_date->format('Y-m-d');
            } else {
                // ตั้งค่าเป็นวันที่ปัจจุบันถ้าแปลงวันที่ไม่สำเร็จ
                $close_date_formatted = date('Y-m-d');
            }
        } else {
            // ถ้าไม่มีการกรอก close_date ให้ตั้งเป็นวันที่ปัจจุบันหรือค่า default
            $close_date_formatted = date('Y-m-d');
        }
    } else {
        // กำหนดค่าเป็น '0000-00-00' หากสถานะไม่ใช่ Booked หรือ Unsuccessful
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
    $stmt->bindParam(':close_date', $close_date_formatted); // บันทึกวันที่จริงลงในฐานข้อมูล
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
            margin-bottom: 5px;
        }
        .form-group {
            margin-bottom: 10px;
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
                    <option value="AV" <?= $customer['class'] == 'AV' ? 'selected' : '' ?>>AV-Aviation Insurance</option>
                        <option value="BBB" <?= $customer['class'] == 'BBB' ? 'selected' : '' ?>>BBB-Bankers Blanket Bond Insurance</option>
                        <option value="BPV" <?= $customer['class'] == 'BPV' ? 'selected' : '' ?>>BPV-Boiler & Pressure Vessel Insurance</option>
                        <option value="BI" <?= $customer['class'] == 'BI' ? 'selected' : '' ?>>BI-Business Interruption Insurance</option>
                        <option value="CL" <?= $customer['class'] == 'CL' ? 'selected' : '' ?>>CL-Carrier's Liability Insurance</option>
                        <option value="CMV" <?= $customer['class'] == 'CMV' ? 'selected' : '' ?>>CMV-Commercial Motor Voluntary Insurance</option>
                        <option value="CMCV" <?= $customer['class'] == 'CMCV' ? 'selected' : '' ?>>CMCV-Commercial Motorcycle Voluntary Insurance</option>
                        <option value="CGL" <?= $customer['class'] == 'CGL' ? 'selected' : '' ?>>CGL-Comprehensive General Liability Insurance</option>
                        <option value="CAR" <?= $customer['class'] == 'CAR' ? 'selected' : '' ?>>CAR-Construction All Risks Insurance</option>
                        <option value="CPI" <?= $customer['class'] == 'CPI' ? 'selected' : '' ?>>CPI-Contaminated Product Insurance Insurance</option>
                        <option value="CPM" <?= $customer['class'] == 'CPM' ? 'selected' : '' ?>>CPM-Contractor's Plant & Machinery Insurance</option>
                        <option value="CYBER" <?= $customer['class'] == 'CYBER' ? 'selected' : '' ?>>CYBER-Cyber Risk Insurance</option>
                        <option value="D&O" <?= $customer['class'] == 'D&O' ? 'selected' : '' ?>>D&O-Directors & Officers Liability Insurance</option>
                        <option value="EEI" <?= $customer['class'] == 'EEI' ? 'selected' : '' ?>>EEI-Electronic Equipment Insurance</option>
                        <option value="BOND" <?= $customer['class'] == 'BOND' ? 'selected' : '' ?>>BOND-Employee Bond Insurance</option>
                        <option value="EL" <?= $customer['class'] == 'EL' ? 'selected' : '' ?>>EL-Employer's Liability Insurance</option>
                        <option value="ENV" <?= $customer['class'] == 'ENV' ? 'selected' : '' ?>>ENV-Environmental Liability Insurance</option>
                        <option value="EXCESS" <?= $customer['class'] == 'EXCESS' ? 'selected' : '' ?>>EXCESS-Erection All Risks Insurance</option>
                        <option value="FG" <?= $customer['class'] == 'FG' ? 'selected' : '' ?>>FG-Fidelity Guarantee Insurance</option>
                        <option value="FINE" <?= $customer['class'] == 'FINE' ? 'selected' : '' ?>>FINE-Fine Art Insurance</option>
                        <option value="FIRE" <?= $customer['class'] == 'FIRE' ? 'selected' : '' ?>>FIRE-Fire Insurance</option>
                        <option value="FFL" <?= $customer['class'] == 'FFL' ? 'selected' : '' ?>>FFL-Freight Forwarder Liability Insurance</option>
                        <option value="MRC" <?= $customer['class'] == 'MRC' ? 'selected' : '' ?>>MRC-General Marine Cargo Insurance</option>
                        <option value="PL" <?= $customer['class'] == 'PL' ? 'selected' : '' ?>>PL-General Public Liability Insurance</option>
                        <option value="GH" <?= $customer['class'] == 'GH' ? 'selected' : '' ?>>GH-Group Health Insurance</option>
                        <option value="GLH" <?= $customer['class'] == 'GLH' ? 'selected' : '' ?>>GLH-Group Life and Health Insurance</option>
                        <option value="GPA" <?= $customer['class'] == 'GPA' ? 'selected' : '' ?>>GPA-Group Personal Accident Insurance</option>
                        <option value="HPP" <?= $customer['class'] == 'HPP' ? 'selected' : '' ?>>HPP-Hired Purchase Protection Insurance</option>
                        <option value="IH" <?= $customer['class'] == 'IH' ? 'selected' : '' ?>>IH-Individual Health Insurance</option>
                        <option value="IMV1" <?= $customer['class'] == 'IMV1' ? 'selected' : '' ?>>IMV1-Individual Motor Voluntary Insurance 1</option>
                        <option value="IMC1" <?= $customer['class'] == 'IMC1' ? 'selected' : '' ?>>IMC1-Industrial All Risks and Business Interruption Insurance</option>
                        <option value="IARBI" <?= $customer['class'] == 'IARBI' ? 'selected' : '' ?>>IARBI-Industrial All Risks and Business Interruption Insurance</option>
                        <option value="IAR" <?= $customer['class'] == 'IAR' ? 'selected' : '' ?>>IAR-Industrial All Risks Insurance</option>
                        <option value="INLAND" <?= $customer['class'] == 'INLAND' ? 'selected' : '' ?>>INLAND-Inland Transit Insurance</option>
                        <option value="JW" <?= $customer['class'] == 'JW' ? 'selected' : '' ?>>JW-Jewelers Block Insurance</option>
                        <option value="MB" <?= $customer['class'] == 'MB' ? 'selected' : '' ?>>MB-Machinery Breakdown Insurance</option>
                        <option value="HULL" <?= $customer['class'] == 'HULL' ? 'selected' : '' ?>>HULL-Marine Hull Insurance</option>
                        <option value="MEDMAL" <?= $customer['class'] == 'MEDMAL' ? 'selected' : '' ?>>MEDMAL-Medical Malpractice Insurance</option>
                        <option value="MN" <?= $customer['class'] == 'MN' ? 'selected' : '' ?>>MN-Money Insurance</option>
                        <option value="MPI" <?= $customer['class'] == 'MPI' ? 'selected' : '' ?>>MPI-Motor Compulsory Insurance</option>
                        <option value="OGI" <?= $customer['class'] == 'OGI' ? 'selected' : '' ?>>OGI-OIL & GAS INSURANCE (LIABILITY INSURANCE)</option>
                        <option value="PA" <?= $customer['class'] == 'PA' ? 'selected' : '' ?>>PA-Personal Accident Insurance</option>
                        <option value="PRI" <?= $customer['class'] == 'PRI' ? 'selected' : '' ?>>PRI-Political Risk Insurance</option>
                        <option value="PVIBI" <?= $customer['class'] == 'PVIBI' ? 'selected' : '' ?>>PVIBI-Political Violence and Business Interruption Insurance</option>
                        <option value="PVI" <?= $customer['class'] == 'PVI' ? 'selected' : '' ?>>PVI-Political Violence Insurance</option>
                        <option value="PDL" <?= $customer['class'] == 'PDL' ? 'selected' : '' ?>>PDL-Product Liability Insurance</option>
                        <option value="RECALL" <?= $customer['class'] == 'RECALL' ? 'selected' : '' ?>>RECALL-Product Recall Insurance</option>
                        <option value="PI" <?= $customer['class'] == 'PI' ? 'selected' : '' ?>>PI-Professional Indemnity Insurance</option>
                        <option value="P&I" <?= $customer['class'] == 'P&I' ? 'selected' : '' ?>>P&I-Protection & Indemnity</option>
                        <option value="PLPLI" <?= $customer['class'] == 'PLPLI' ? 'selected' : '' ?>>PLPLI-Public and Product Liability Insurance</option>
                        <option value="TL" <?= $customer['class'] == 'TL' ? 'selected' : '' ?>>TL-Terminal Liability Insurance</option>
                        <option value="TERRO" <?= $customer['class'] == 'TERRO' ? 'selected' : '' ?>>TERRO-Terrorism Insurance</option>
                        <option value="TRADE" <?= $customer['class'] == 'TRADE' ? 'selected' : '' ?>>TRADE-Trade Credit Insurance</option>
                        <option value="WHL" <?= $customer['class'] == 'WHL' ? 'selected' : '' ?>>WHL-Warehousemen Liability Insurance</option>
                        <option value="W&I" <?= $customer['class'] == 'W&I' ? 'selected' : '' ?>>W&I-Warranty & Indemnity</option>
                        <option value="WCEL" <?= $customer['class'] == 'WCEL' ? 'selected' : '' ?>>WCEL-Workmen's Compensation and Employer's Liability Insurance</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="revenue">Revenue:</label>
                    <input type="text" class="form-control" id="revenue" name="revenue" value="<?= htmlspecialchars($customer['revenue']) ?>" oninput="formatCurrency(this)" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="premium">Premium:</label>
                    <input type="text" class="form-control" id="premium" name="premium" value="<?= htmlspecialchars($customer['premium']) ?>" oninput="formatCurrency(this)" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="sum_insured">Sum Insured:</label>
                    <input type="text" class="form-control" id="sum_insured" name="sum_insured" value="<?= htmlspecialchars($customer['sum_insured']) ?>" oninput="formatCurrency(this)" required>
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
            <div class="form-group">
                <label for="policy_type">Policy Type:</label>
                <select class="form-control" id="policy_type" name="policy_type">
                    <option value="">Select Policy Type</option>
                    <option value="NewRecurring" <?= $customer['policy_type'] == 'NewRecurring' ? 'selected' : '' ?>>New-Recurring</option>
                    <option value="NewNonRecurring" <?= $customer['policy_type'] == 'NewNonRecurring' ? 'selected' : '' ?>>New-Non-Recurring</option>
                    <option value="Renewal" <?= $customer['policy_type'] == 'Renewal' ? 'selected' : '' ?>>Renewal</option>
                    <option value="ExpandNonRecurring" <?= $customer['policy_type'] == 'ExpandNonRecurring' ? 'selected' : '' ?>>Expand Non Recurring</option>
                    <option value="ExpandRecurring" <?= $customer['policy_type'] == 'ExpandRecurring' ? 'selected' : '' ?>>Expand Recurring</option>
                    <option value="Extend" <?= $customer['policy_type'] == 'Extend' ? 'selected' : '' ?>>Extend</option>
                </select>
            </div>
            <div class="form-group">
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
            document.getElementById('close_date_group').style.display = 'block'; 
            document.getElementById('close_date').style.borderColor = 'red'; 
            document.querySelector('label[for="close_date"]').style.color = 'red';

            if (value === 'Booked' || value === 'Unsuccessful') {
                closeDateGroup.style.display = '';
                closeDateInput.required = true;
            } else {
                closeDateGroup.style.display = 'none';
                closeDateInput.required = false;
                closeDateInput.value = '0000-00-00'; // ตั้งค่าเป็น '0000-00-00' เมื่อสถานะไม่ใช่ 'Booked' หรือ 'Unsuccessful'
            }
        }

        function formatCurrency(input) {
            var value = input.value.replace(/,/g, '');
            if (!isNaN(value) && value !== "") {
                input.value = parseFloat(value).toLocaleString('en-US', {maximumFractionDigits: 2});
            } else {
                input.value = "";
            }
        }
    </script>
</body>
</html>
