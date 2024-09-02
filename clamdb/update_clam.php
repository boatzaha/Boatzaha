<?php
require '../db.php';

<<<<<<< HEAD
if (isset($_GET['id'])) { // ตรวจสอบพารามิเตอร์ 'id'
    $id = $_GET['id'];

    // Debug: ตรวจสอบว่าตัวแปร id ถูกส่งมาหรือไม่
    echo "Item received: " . htmlspecialchars($id) . "<br>";

    // ดึงข้อมูล Clam ตาม id ที่ระบุ
    $sql = "SELECT * FROM claims WHERE id = :id"; // เปลี่ยนเป็น 'id' แทน 'item'
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
=======
if (isset($_GET['item'])) {
    $item = $_GET['item'];

    // ดึงข้อมูล Clam ตาม Item ที่ระบุ
    $sql = "SELECT * FROM claims WHERE item = :item";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':item', $item);
>>>>>>> origin/main
    $stmt->execute();
    $claim = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$claim) {
        echo "Claim not found!";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // รับค่าจากฟอร์ม
        $receive_date = $_POST['receive_date'];
        $recore_date = $_POST['recore_date'];
        $company_name = $_POST['company_name'];
        $insurance = $_POST['insurance'];
        $policy = $_POST['policy'];
        $insure_name = $_POST['insure_name'];
        $date_treatment = $_POST['date_treatment'];
        $claim_type = $_POST['claim_type'];
        $hosp_clinic = $_POST['hosp_clinic'];
        $diagnosis = $_POST['diagnosis'];
        $bill_amount = str_replace(',', '', $_POST['bill_amount']);
        $remark = !empty($_POST['remark']) ? $_POST['remark'] : null;
        $status = $_POST['status'];
        $paid_amount = str_replace(',', '', $_POST['paid_amount']);
        $declined_amount = str_replace(',', '', $_POST['declined_amount']);
        $tf_date = !empty($_POST['tf_date']) ? $_POST['tf_date'] : null;
        $final_status = $_POST['final_status'];
        $complete_date = $_POST['complete_date'];

        // ตรวจสอบว่ามีการเปลี่ยนแปลง Recore Date หรือไม่
        $previous_recore_date = $claim['recore_date']; // ค่า Recore Date เดิม

        // หาก Recore Date มีการเปลี่ยนแปลง ให้คำนวณ Duration Date ใหม่
        if ($recore_date !== $previous_recore_date) {
            // ตรวจสอบ Recore Date ไม่ให้เป็นวันย้อนหลัง
            $currentDate = new DateTime();
            $recoreDateObj = new DateTime($recore_date);
            if ($recoreDateObj < $currentDate) {
                echo "<script>alert('Recore Date ไม่สามารถเป็นวันย้อนหลังได้! ระบบจะใช้วันที่ปัจจุบันแทน');</script>";
                $recoreDateObj = $currentDate;
                $recore_date = $recoreDateObj->format('Y-m-d');
            }

            // คำนวณ Complete Date และ Duration Date ใหม่
            $completeDateObj = new DateTime($complete_date);
            $interval = $recoreDateObj->diff($completeDateObj);
            $duration_date = $interval->days;
        } else {
            // หาก Recore Date ไม่เปลี่ยนแปลง ให้ใช้ Duration Date เดิม
            $duration_date = $claim['duration_date'];
        }

        // SQL สำหรับการอัปเดตข้อมูล Clam
        $sql = "UPDATE claims SET 
                    receive_date = :receive_date,
                    recore_date = :recore_date,
                    company_name = :company_name,
                    insurance = :insurance,
                    policy = :policy,
                    insure_name = :insure_name,
                    date_treatment = :date_treatment,
                    claim_type = :claim_type,
                    hosp_clinic = :hosp_clinic,
                    diagnosis = :diagnosis,
                    bill_amount = :bill_amount,
                    remark = :remark,
                    status = :status,
                    paid_amount = :paid_amount,
                    declined_amount = :declined_amount,
                    tf_date = :tf_date,
                    final_status = :final_status,
                    complete_date = :complete_date,
                    duration_date = :duration_date
<<<<<<< HEAD
                WHERE id = :id"; // เปลี่ยนเป็น 'id' แทน 'item'
=======
                WHERE item = :item";
>>>>>>> origin/main

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':receive_date', $receive_date);
            $stmt->bindParam(':recore_date', $recore_date);
            $stmt->bindParam(':company_name', $company_name);
            $stmt->bindParam(':insurance', $insurance);
            $stmt->bindParam(':policy', $policy);
            $stmt->bindParam(':insure_name', $insure_name);
            $stmt->bindParam(':date_treatment', $date_treatment);
            $stmt->bindParam(':claim_type', $claim_type);
            $stmt->bindParam(':hosp_clinic', $hosp_clinic);
            $stmt->bindParam(':diagnosis', $diagnosis);
            $stmt->bindParam(':bill_amount', $bill_amount);
            $stmt->bindParam(':remark', $remark);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':paid_amount', $paid_amount);
            $stmt->bindParam(':declined_amount', $declined_amount);
            $stmt->bindParam(':tf_date', $tf_date);
            $stmt->bindParam(':final_status', $final_status);
            $stmt->bindParam(':complete_date', $complete_date);
            $stmt->bindParam(':duration_date', $duration_date);
<<<<<<< HEAD
            $stmt->bindParam(':id', $id);
=======
            $stmt->bindParam(':item', $item);
>>>>>>> origin/main

            if ($stmt->execute()) {
                header("Location: indexclam.php");
                exit();
            } else {
                echo "Error updating clam report.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "Invalid request.";
    exit();
}
?>

<<<<<<< HEAD

=======
>>>>>>> origin/main
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Clam Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-group {
            margin-bottom: 10px;
        }

        .form-row .form-group {
            padding-right: 10px;
            padding-left: 10px;
        }

        .form-control {
            font-size: 14px;
            padding: 5px 10px;
        }

        textarea.form-control {
            height: 70px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .header-container h2 {
            margin: 0;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body>
<<<<<<< HEAD
<form method="POST" action="update_clam.php?id=<?= htmlspecialchars($id) ?>">
    <div class="container mt-3">
        <div class="header-container">
            <h2>Update Claim Report</h2>
            <div class="button-group">
                <a href="indexclam.php" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update</button>
=======
    <form method="post" action="update_clam.php?item=<?= htmlspecialchars($item) ?>">
        <div class="container mt-3">
            <div class="header-container">
                <h2>Update Claim Report</h2>
                <div class="button-group">
                    <a href="indexclam.php" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Update</button>
>>>>>>> origin/main
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="receive_date">Receive Date:</label>
                        <input type="date" class="form-control" id="receive_date" name="receive_date" value="<?= htmlspecialchars($claim['receive_date']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="recore_date">Recore Date:</label>
                        <input type="date" class="form-control" id="recore_date" name="recore_date" value="<?= htmlspecialchars($claim['recore_date']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="company_name">Company Name:</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="<?= htmlspecialchars($claim['company_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="insurance">Insurance:</label>
                        <input type="text" class="form-control" id="insurance" name="insurance" value="<?= htmlspecialchars($claim['insurance']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="policy">Policy:</label>
                        <input type="text" class="form-control" id="policy" name="policy" value="<?= htmlspecialchars($claim['policy']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="insure_name">Insure Name:</label>
                        <input type="text" class="form-control" id="insure_name" name="insure_name" value="<?= htmlspecialchars($claim['insure_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="date_treatment">Date Treatment:</label>
                        <input type="date" class="form-control" id="date_treatment" name="date_treatment" value="<?= htmlspecialchars($claim['date_treatment']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="claim_type">Claim Type:</label>
                        <select class="form-control" id="claim_type" name="claim_type" required>
                            <option value="">Select ClaimType</option>
                            <option <?= $claim['claim_type'] === 'Death Beuefit' ? 'selected' : '' ?>>Death Beuefit</option>
                            <option <?= $claim['claim_type'] === 'Iu-Patieut' ? 'selected' : '' ?>>Iu-Patieut</option>
                            <option <?= $claim['claim_type'] === 'Mayor Medical' ? 'selected' : '' ?>>Mayor Medical</option>
                            <option <?= $claim['claim_type'] === 'Out-Pultient' ? 'selected' : '' ?>>Out-Pultient</option>
                            <option <?= $claim['claim_type'] === 'Dental-Maternity' ? 'selected' : '' ?>>Dental-Maternity</option>
                            <option <?= $claim['claim_type'] === 'Medical-Expent' ? 'selected' : '' ?>>Medical-Expent</option>
                            <option <?= $claim['claim_type'] === 'HB-incentier' ? 'selected' : '' ?>>HB-incentier</option>
                        </select>
                    </div>
                    <!-- ย้าย Hospital/Clinic, Diagnosis, และ Bill Amount มาที่นี่ -->
                    <div class="form-group">
                        <label for="hosp_clinic">Hospital/Clinic:</label>
                        <input type="text" class="form-control" id="hosp_clinic" name="hosp_clinic" value="<?= htmlspecialchars($claim['hosp_clinic']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="diagnosis">Diagnosis:</label>
                        <input type="text" class="form-control" id="diagnosis" name="diagnosis" value="<?= htmlspecialchars($claim['diagnosis']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bill_amount">Bill Amount:</label>
                        <input type="text" class="form-control" id="bill_amount" name="bill_amount" value="<?= number_format($claim['bill_amount'], 2) ?>" required oninput="formatNumber(this)">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="paid_amount">Paid Amount:</label>
                        <input type="text" class="form-control" id="paid_amount" name="paid_amount" value="<?= number_format($claim['paid_amount'], 2) ?>" required oninput="formatNumber(this)">
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option <?= $claim['status'] === 'Approve' ? 'selected' : '' ?>>Approve</option>
                            <option <?= $claim['status'] === 'Decline' ? 'selected' : '' ?>>Decline</option>
                            <option <?= $claim['status'] === 'On-Going' ? 'selected' : '' ?>>On-Going</option>
                            <option <?= $claim['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="declined_amount">Declined Amount:</label>
                        <input type="text" class="form-control" id="declined_amount" name="declined_amount" value="<?= number_format($claim['declined_amount'], 2) ?>" required oninput="formatNumber(this)">
                    </div>
                    <div class="form-group">
                        <label for="tf_date">TF Date:</label>
                        <input type="date" class="form-control" id="tf_date" name="tf_date" value="<?= htmlspecialchars($claim['tf_date']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="final_status">Final Status:</label>
                        <select class="form-control" id="final_status" name="final_status" required>
                            <option value="">Select Status</option>
                            <option <?= $claim['final_status'] === 'Complete' ? 'selected' : '' ?>>Complete</option>
                            <option <?= $claim['final_status'] === 'Decline' ? 'selected' : '' ?>>Decline</option>
                            <option <?= $claim['final_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="complete_date">Complete Date:</label>
                        <input type="date" class="form-control" id="complete_date" name="complete_date" value="<?= htmlspecialchars($claim['complete_date']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="remark">Remark:</label>
                        <textarea class="form-control" id="remark" name="remark"><?= htmlspecialchars($claim['remark']) ?></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
  const holidays = [
        '2024-12-25', // ตัวอย่างวันหยุดคริสต์มาส
        '2024-01-01', // วันปีใหม่
        // เพิ่มวันหยุดอื่น ๆ ที่นี่
    ];

    function isHoliday(date) {
        if (date.getDay() === 6 || date.getDay() === 0) { // เสาร์ = 6, อาทิตย์ = 0
            return true;
        }

        const formattedDate = date.toISOString().split('T')[0];
        return holidays.includes(formattedDate);
    }

    function calculateCompleteDate(recoreDate, daysToAdd) {
        let daysAdded = 0;

        while (daysAdded < daysToAdd) {
            recoreDate.setDate(recoreDate.getDate() + 1);

            if (!isHoliday(recoreDate)) {
                daysAdded++;
            }
        }

        return recoreDate;
    }

    document.getElementById('recore_date').addEventListener('change', function () {
        // รับค่าวันที่จากฟิลด์ Recore Date
        let recoreDate = new Date(this.value);

        // คำนวณ Complete Date ใหม่ โดยเริ่มนับเป็นวันที่ 1 เสมอ
        recoreDate = calculateCompleteDate(recoreDate, 14); // นับเพิ่มไป 14 วัน

        // แปลงวันที่กลับเป็นรูปแบบ yyyy-mm-dd
        let dd = String(recoreDate.getDate()).padStart(2, '0');
        let mm = String(recoreDate.getMonth() + 1).padStart(2, '0');
        let yyyy = recoreDate.getFullYear();

        let completeDate = `${yyyy}-${mm}-${dd}`;
        document.getElementById('complete_date').value = completeDate;

        // เริ่มนับ Duration Date ใหม่
        calculateDurationDate();
    });

    function calculateDurationDate() {
        const recoreDate = new Date(document.getElementById('recore_date').value);
        const completeDate = new Date(document.getElementById('complete_date').value);

        if (recoreDate && completeDate) {
            const diffTime = Math.abs(completeDate - recoreDate);
            const durationDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // บวกเพิ่ม 1 เพื่อให้นับวันแรกด้วย

            document.getElementById('duration_date').value = durationDays + " days";
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        calculateDurationDate();
    });

    function formatNumber(input) {
        let value = input.value.replace(/,/g, '');
        if (value.includes('.')) {
            let parts = value.split('.');
            parts[0] = Number(parts[0]).toLocaleString();
            input.value = parts.join('.');
        } else {
            input.value = Number(value).toLocaleString();
        }
    }
    // เรียกใช้ฟังก์ชันคำนวณ Duration Date เมื่อหน้าโหลดขึ้นมา
    document.addEventListener('DOMContentLoaded', function () {
        calculateDurationDate();
    });
        function formatNumber(input) {
            // แยกส่วนตัวเลขหลักก่อนและหลังจุดทศนิยม
            let value = input.value.replace(/,/g, '');
            if (value.includes('.')) {
                let parts = value.split('.');
                // ฟอร์แมตเฉพาะส่วนก่อนจุดทศนิยม
                parts[0] = Number(parts[0]).toLocaleString();
                input.value = parts.join('.');
            } else {
                // ฟอร์แมตตัวเลขทั้งหมดหากไม่มีจุดทศนิยม
                input.value = Number(value).toLocaleString();
            }
        }
    </script>
</body>

<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> origin/main
