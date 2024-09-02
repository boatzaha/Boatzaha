<?php
session_start();
require 'db.php';

// ดึงข้อมูล Client Groups
$sql = "SELECT * FROM client_groups ORDER BY group_name ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$client_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $name = $_POST['name'];
    $inception_date_raw = $_POST['inception_date'];
    $inception_date = DateTime::createFromFormat('Y-m-d', $inception_date_raw);
    $client_group = $_POST['client_group']; // Client Group ที่เลือกเข้ามา
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
            $close_date_formatted = $close_date ? $close_date->format('Y-m-d') : date('Y-m-d');
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

    $sql = "INSERT INTO customers (name, inception_date, client_group, class, revenue, premium, sum_insured, close_date, department, status, description, policy_type, created_by)
        VALUES (:name, :inception_date, :client_group, :class, :revenue, :premium, :sum_insured, :close_date, :department, :status, :description, :policy_type, :created_by)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':inception_date', $inception_date->format('Y-m-d'));
    $stmt->bindParam(':client_group', $client_group); // ใช้ client_group ที่ถูกต้อง
    $stmt->bindParam(':class', $class);
    $stmt->bindParam(':revenue', $revenue);
    $stmt->bindParam(':premium', $premium);
    $stmt->bindParam(':sum_insured', $sum_insured);
    $stmt->bindParam(':close_date', $close_date_formatted);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':policy_type', $policy_type);
    $stmt->bindParam(':created_by', $_SESSION['username']);
    try {
        $stmt->execute();
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Record</title>
    <link rel="icon" href="image/HOWDENLOGO.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
<<<<<<< HEAD
=======
        /* ใช้ CSS แบบเดียวกับที่ใช้ในฟอร์ม edit.php */
>>>>>>> origin/main
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
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Create Record</h1>
            <div class="action-buttons">
                <a href="index.php" class="btn btn-secondary">Back</a>
                <button type="submit" form="createForm" class="btn btn-primary">Create</button>
            </div>
        </div>
        <form id="createForm" method="post" action="create.php">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Client Company Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <!-- เพิ่มการเลือก Client Group -->
                <div class="form-group col-md-6">
                    <label for="client_group">Client Group:</label>
                    <select class="form-control" id="client_group" name="client_group" required>
                        <option value="">Select Client Group</option>
                        <?php foreach ($client_groups as $group): ?>
                            <option value="<?= htmlspecialchars($group['group_name']) ?>"><?= htmlspecialchars($group['group_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="inception_date">Inception Date:</label>
                    <input type="date" class="form-control" id="inception_date" name="inception_date" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="class">Income Class:</label>
<<<<<<< HEAD
                    <select class="form-control" id="class" name="class" required>
                    <option value="AV">AV-Aviation Insurance</option>
=======
                    <select class="form-control" id="class" name="class" required>""
                        <option value="AV">AV-Aviation Insurance</option>
>>>>>>> origin/main
                        <option value="BBB">BBB-Bankers Blanket Bond Insurance</option>
                        <option value="BPV">BPV-Boiler & Pressure Vessel Insurance</option>
                        <option value="BI">BI-Business Interruption Insurance</option>
                        <option value="CL">CL-Carrier's Liability Insurance</option>
                        <option value="CMV">CMV-Commercial Motor Voluntary Insurance</option>
                        <option value="CMCV">CMCV-Commercial Motorcycle Voluntary Insurance</option>
                        <option value="CGL">CGL-Comprehensive General Liability Insurance</option>
                        <option value="CAR">CAR-Construction All Risks Insurance</option>
                        <option value="CPI">CPI-Contaminated Product Insurance Insurance</option>
                        <option value="CPM">CPM-Contractor's Plant & Machinery Insurance</option>
                        <option value="CYBER">CYBER-Cyber Risk Insurance</option>
                        <option value="D&O">D&O-Directors & Officers Liability Insurance</option>
                        <option value="EEI">EEI-Electronic Equipment Insurance</option>
                        <option value="BOND">BOND-Employee Bond Insurance</option>
                        <option value="EL">EL-Employer's Liability Insurance</option>
                        <option value="ENV">ENV-Environmental Liability Insurance</option>
                        <option value="EXCESS">EXCESS-Erection All Risks Insurance</option>
                        <option value="FG">FG-Fidelity Guarantee Insurance</option>
                        <option value="FINE">FINE-Fine Art Insurance</option>
                        <option value="FIRE">FIRE-Fire Insurance</option>
                        <option value="FFL">FFL-Freight Forwarder Liability Insurance</option>
                        <option value="MRC">MRC-General Marine Cargo Insurance</option>
                        <option value="PL">PL-General Public Liability Insurance</option>
                        <option value="GH">GH-Group Health Insurance</option>
                        <option value="GLH">GLH-Group Life and Health Insurance</option>
                        <option value="GPA">GPA-Group Personal Accident Insurance</option>
                        <option value="HPP">HPP-Hired Purchase Protection Insurance</option>
                        <option value="IH">IH-Individual Health Insurance</option>
                        <option value="IMV1">IMV1-Individual Motor Voluntary Insurance 1</option>
                        <option value="IMC1">IMC1-Industrial All Risks and Business Interruption Insurance</option>
                        <option value="IARBI">IARBI-Industrial All Risks and Business Interruption Insurance</option>
                        <option value="IAR">IAR-Industrial All Risks Insurance</option>
                        <option value="INLAND">INLAND-Inland Transit Insurance</option>
                        <option value="JW">JW-Jewelers Block Insurance</option>
                        <option value="MB">MB-Machinery Breakdown Insurance</option>
                        <option value="HULL">HULL-Marine Hull Insurance</option>
                        <option value="MEDMAL">MEDMAL-Medical Malpractice Insurance</option>
                        <option value="MN">MN-Money Insurance</option>
                        <option value="MPI">MPI-Motor Compulsory Insurance</option>
                        <option value="OGI">OGI-OIL & GAS INSURANCE (LIABILITY INSURANCE)</option>
                        <option value="PA">PA-Personal Accident Insurance</option>
                        <option value="PRI">PRI-Political Risk Insurance</option>
                        <option value="PVIBI">PVIBI-Political Violence and Business Interruption Insurance</option>
                        <option value="PVI">PVI-Political Violence Insurance</option>
                        <option value="PDL">PDL-Product Liability Insurance</option>
                        <option value="RECALL">RECALL-Product Recall Insurance</option>
                        <option value="PI">PI-Professional Indemnity Insurance</option>
                        <option value="P&I">P&I-Protection & Indemnity</option>
                        <option value="PLPLI">PLPLI-Public and Product Liability Insurance</option>
                        <option value="TL">TL-Terminal Liability Insurance</option>
                        <option value="TERRO">TERRO-Terrorism Insurance</option>
                        <option value="TRADE">TRADE-Trade Credit Insurance</option>
                        <option value="WHL">WHL-Warehousemen Liability Insurance</option>
                        <option value="W&I">W&I-Warranty & Indemnity</option>
                        <option value="WCEL">WCEL-Workmen's Compensation and Employer's Liability Insurance</option>
                        <option value="WC">WC-Workmen's Compensation Insurance</option>
<<<<<<< HEAD
                        <option value="GL">GL-Group Life insurance</option>

=======
>>>>>>> origin/main
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="revenue">Revenue:</label>
                    <input type="text" class="form-control" id="revenue" name="revenue" oninput="formatCurrency(this)" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="premium">Premium:</label>
                    <input type="text" class="form-control" id="premium" name="premium" oninput="formatCurrency(this)" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="sum_insured">Sum Insured:</label>
                    <input type="text" class="form-control" id="sum_insured" name="sum_insured" oninput="formatCurrency(this)" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="department">Department:</label>
                    <select class="form-control" id="department" name="department" required>
                        <option value="EB">EB</option>
                        <option value="Property&Castalty">Property&Castalty</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="status">Funnel stage:</label>
                    <select class="form-control" id="status" name="status" required onchange="checkStatus(this.value)">
                        <option value="Approached">Approached</option>
                        <option value="Booked">Booked</option>
                        <option value="Identify">Identify</option>
                        <option value="Quoting">Quoting</option>
                        <option value="Unsuccessful">Unsuccessful</option>
                    </select>
                </div>
            </div>
            <div class="form-row" id="close_date_group" style="display: none;">
                <div class="form-group col-md-6">
                    <label for="close_date">*Close Date*:</label>
                    <input type="date" class="form-control" id="close_date" name="close_date">
                </div>
            </div>
            <div class="form-group">
                <label for="policy_type">Policy Type:</label>
                <select class="form-control" id="policy_type" name="policy_type">
                    <option value="">Select Policy Type</option>
                    <option value="NewRecurring">New-Recurring</option>
                    <option value="NewNonRecurring">New-Non-Recurring</option>
                    <option value="Renewal">Renewal</option>
                    <option value="ExpandNonRecurring">Expand Non Recurring</option>
                    <option value="ExpandRecurring">Expand Recurring</option>
                    <option value="Extend">Extend</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<<<<<<< HEAD
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
=======
    <script src="https://stackpath.amazonaws.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
>>>>>>> origin/main
    <script>
        function checkStatus(value) {
            var closeDateGroup = document.getElementById('close_date_group');
            var closeDateInput = document.getElementById('close_date');

            if (value === 'Booked' || value === 'Unsuccessful') {
                closeDateGroup.style.display = '';
                closeDateInput.required = true;
            } else {
                closeDateGroup.style.display = 'none';
                closeDateInput.required = false;
                closeDateInput.value = '0000-00-00';
            }
        }

        function formatCurrency(input) {
            var value = input.value.replace(/,/g, '');
            if (!isNaN(value) && value !== "") {
<<<<<<< HEAD
                var parts = value.split('.');
                parts[0] = parseInt(parts[0]).toLocaleString('en-US');
                if (parts.length > 1) {
                    input.value = parts[0] + '.' + parts[1].substring(0, 2); // จัดการทศนิยมให้อยู่ในรูปแบบ x.xx
                } else {
                    input.value = parts[0];
                }
=======
                input.value = parseFloat(value).toLocaleString('en-US', {maximumFractionDigits: 2});
>>>>>>> origin/main
            } else {
                input.value = "";
            }
        }
    </script>
</body>
</html>
