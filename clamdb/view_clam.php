<?php
session_start();
require '../db.php'; // ตรวจสอบว่ามีการเรียกใช้ db.php ที่ถูกต้อง

if (isset($_GET['id'])) { // เปลี่ยนจาก 'item' เป็น 'id' ตามฐานข้อมูลของคุณ
    $id = $_GET['id'];

    // Fetch the claim record based on the id
    $sql = "SELECT * FROM claims WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ใช้ PARAM_INT สำหรับ id ที่เป็นตัวเลข
    $stmt->execute();
    $claim = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$claim) {
        echo "Claim not found!";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            max-width: 800px;
        }
        .table th {
            width: 30%;
            background-color: #f5f5f5;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-container">
            <h2>View Record</h2>
            <a href="indexclam.php" class="btn btn-secondary">Back</a>
        </div>
        <table class="table table-bordered">
        <tr>
                <th>ID</th>
                <td><?= htmlspecialchars($claim['id']) ?></td>
            </tr>
            <tr>
                <th>Item</th>
                <td><?= htmlspecialchars($claim['item']) ?></td>
            </tr>
            <tr>
                <th>Receive Date</th>
                <td><?= htmlspecialchars($claim['receive_date']) ?></td>
            </tr>
            <tr>
                <th>Recore Date</th>
                <td><?= htmlspecialchars($claim['recore_date']) ?></td>
            </tr>
            <tr>
                <th>Company Name</th>
                <td><?= htmlspecialchars($claim['company_name']) ?></td>
            </tr>
            <tr>
                <th>Insurance</th>
                <td><?= htmlspecialchars($claim['insurance']) ?></td>
            </tr>
            <tr>
                <th>Policy</th>
                <td><?= htmlspecialchars($claim['policy']) ?></td>
            </tr>
            <tr>
                <th>Insure Name</th>
                <td><?= htmlspecialchars($claim['insure_name']) ?></td>
            </tr>
            <tr>
                <th>Date Treatment</th>
                <td><?= htmlspecialchars($claim['date_treatment']) ?></td>
            </tr>
            <tr>
                <th>Claim Type</th>
                <td><?= htmlspecialchars($claim['claim_type']) ?></td>
            </tr>
            <tr>
                <th>Hosp/Clinic</th>
                <td><?= htmlspecialchars($claim['hosp_clinic']) ?></td>
            </tr>
            <tr>
                <th>Diagnosis</th>
                <td><?= htmlspecialchars($claim['diagnosis']) ?></td>
            </tr>
            <tr>
                <th>Bill Amount</th>
                <td><?= number_format($claim['bill_amount'], 2) ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?= htmlspecialchars($claim['status']) ?></td>
            </tr>
            <tr>
                <th>Paid Amount</th>
                <td><?= number_format($claim['paid_amount'], 2) ?></td>
            </tr>
            <tr>
                <th>Declined Amount</th>
                <td><?= number_format($claim['declined_amount'], 2) ?></td>
            </tr>
            <tr>
                <th>TF Date</th>
                <td><?= htmlspecialchars($claim['tf_date']) ?></td>
            </tr>
            <tr>
                <th>Final Status</th>
                <td><?= htmlspecialchars($claim['final_status']) ?></td>
            </tr>
            <tr>
                <th>Complete Date</th>
                <td><?= htmlspecialchars($claim['complete_date']) ?></td>
            </tr>
            <tr>
                <th>Duration Date</th>
                <td><?= htmlspecialchars($claim['duration_date']) ?></td>
            </tr>
            <tr>
                <th>Created By</th>
                <td><?= htmlspecialchars($claim['created_by']) ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
