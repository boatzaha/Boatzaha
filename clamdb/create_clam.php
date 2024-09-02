<?php
session_start();
require '../db.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

<<<<<<< HEAD
// ดึงข้อมูล company name, insurance และ policy จากตาราง add_client_groups
$sql = "SELECT id, company_name, insurance, policy FROM add_client_groups";
$stmt = $conn->prepare($sql);
$stmt->execute();
$client_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

=======
>>>>>>> origin/main
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์มและลบลูกน้ำออก
    $item = $_POST['item'] ?? '';
    $receive_date = $_POST['receive_date'] ?? '';
    $recore_date = $_POST['recore_date'] ?? '';
    $company_name = $_POST['company_name'] ?? '';
    $insurance = $_POST['insurance'] ?? '';
    $policy = $_POST['policy'] ?? '';
    $insure_name = $_POST['insure_name'] ?? '';
    $date_treatment = $_POST['date_treatment'] ?? '';
    $claim_type = $_POST['claim_type'] ?? '';
    $hosp_clinic = $_POST['hosp_clinic'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $bill_amount = str_replace(',', '', $_POST['bill_amount'] ?? ''); // ลบลูกน้ำออกก่อนเก็บข้อมูล
    $remark = !empty($_POST['remark']) ? $_POST['remark'] : null;
    $status = $_POST['status'] ?? '';
    $paid_amount = str_replace(',', '', $_POST['paid_amount'] ?? ''); // ลบลูกน้ำออกก่อนเก็บข้อมูล
    $declined_amount = str_replace(',', '', $_POST['declined_amount'] ?? ''); // ลบลูกน้ำออกก่อนเก็บข้อมูล
    $tf_date = !empty($_POST['tf_date']) ? $_POST['tf_date'] : null;
    $final_status = $_POST['final_status'] ?? '';
    $complete_date = $_POST['complete_date'] ?? '';

    // ตรวจสอบข้อมูลที่จำเป็นว่าครบถ้วนหรือไม่
    if (empty($item) || empty($company_name) || empty($insurance)) {
        echo "โปรดกรอกข้อมูลที่จำเป็นให้ครบถ้วน";
        exit();
    }

    // คำนวณ Duration Date
    if (!empty($recore_date) && !empty($complete_date)) {
        $recoreDateObj = new DateTime($recore_date);
        $completeDateObj = new DateTime($complete_date);
        $interval = $recoreDateObj->diff($completeDateObj);
        $duration_date = $interval->days; // คำนวณเป็นจำนวนวัน
    } else {
        $duration_date = null; // หากข้อมูลไม่ครบ กำหนดเป็น null
    }

    // กำหนด created_by เป็นชื่อผู้ใช้ปัจจุบัน
    $created_by = $username;

    // SQL สำหรับการเพิ่มข้อมูลใหม่
    $sql = "INSERT INTO claims (item, receive_date, recore_date, company_name, insurance, policy, insure_name, date_treatment, claim_type, hosp_clinic, diagnosis, bill_amount, remark, status, paid_amount, declined_amount, tf_date, final_status, complete_date, duration_date, created_by) 
            VALUES (:item, :receive_date, :recore_date, :company_name, :insurance, :policy, :insure_name, :date_treatment, :claim_type, :hosp_clinic, :diagnosis, :bill_amount, :remark, :status, :paid_amount, :declined_amount, :tf_date, :final_status, :complete_date, :duration_date, :created_by)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':item', $item);
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
    $stmt->bindParam(':created_by', $created_by);

    try {
        if ($stmt->execute()) {
            header("Location: indexclam.php");
            exit();
        } else {
            echo "Error adding clam report.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Claim Report</title>
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
    <form method="post" action="create_clam.php">
        <div class="container mt-3">
=======
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Claim Report</title>
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
            <form method="post" action="create_clam.php">
            <div class="container mt-3">
>>>>>>> origin/main
            <div class="header-container">
                <h2>Create Claim Report</h2>
                <div class="button-group">
                    <a href="indexclam.php" class="btn btn-secondary">Back</a>
<<<<<<< HEAD
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <!-- กลุ่มฟอร์มด้านซ้าย -->
                    <div class="form-group">
                        <label for="item">Item:</label>
                        <input type="text" class="form-control" id="item" name="item" required>
                    </div>
                    <div class="form-group">
                        <label for="receive_date">Receive Date:</label>
                        <input type="date" class="form-control" id="receive_date" name="receive_date" required>
                    </div>
                    <div class="form-group">
                        <label for="recore_date">Recore Date:</label>
                        <input type="date" class="form-control" id="recore_date" name="recore_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="company_name">Company Name:</label>
                        <select class="form-control" id="company_name" name="company_name" required onchange="updateCompanyDetails()">
                            <option value="">Select Company</option>
                            <?php foreach ($client_groups as $group): ?>
                                <option value="<?= htmlspecialchars($group['company_name']) ?>" data-insurance="<?= htmlspecialchars($group['insurance']) ?>" data-policy="<?= htmlspecialchars($group['policy']) ?>"><?= htmlspecialchars($group['company_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="insurance">Insurance:</label>
                        <input type="text" class="form-control" id="insurance" name="insurance">
                    </div>
                    <div class="form-group">
                        <label for="policy">Policy:</label>
                        <input type="text" class="form-control" id="policy" name="policy">
                    </div>
                    <div class="form-group">
                        <label for="insure_name">Insure Name:</label>
                        <input type="text" class="form-control" id="insure_name" name="insure_name" required>
                    </div>
                    <div class="form-group">
                        <label for="date_treatment">Date of Treatment:</label>
                        <input type="date" class="form-control" id="date_treatment" name="date_treatment" required>
                    </div>
                    <div class="form-group">
                        <label for="claim_type">Claim Type:</label>
                        <select class="form-control" id="claim_type" name="claim_type" required>
                            <option value="">Select Claim Type</option>
                            <option>Death Benefit</option>
                            <option>In-Patient</option>
                            <option>Major Medical</option>
                            <option>Out-Patient</option>
                            <option>Dental-Maternity</option>
                            <option>Medical Expense</option>
                            <option>HB-Incentive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hosp_clinic">Hospital/Clinic:</label>
                        <input type="text" class="form-control" id="hosp_clinic" name="hosp_clinic" required>
                    </div>
                    <div class="form-group">
                        <label for="diagnosis">Diagnosis:</label>
                        <input type="text" class="form-control" id="diagnosis" name="diagnosis" required>
                    </div>
                    <div class="form-group">
                        <label for="bill_amount">Bill Amount:</label>
                        <input type="text" class="form-control" id="bill_amount" name="bill_amount" required oninput="formatNumber(this)">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="paid_amount">Paid Amount:</label>
                        <input type="text" class="form-control" id="paid_amount" name="paid_amount" required oninput="formatNumber(this)">
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option>Approve</option>
                            <option>Decline</option>
                            <option>On-Going</option>
                            <option>Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="declined_amount">Declined Amount:</label>
                        <input type="text" class="form-control" id="declined_amount" name="declined_amount" required oninput="formatNumber(this)">
                    </div>
                    <div class="form-group">
                        <label for="tf_date">TF Date:</label>
                        <input type="date" class="form-control" id="tf_date" name="tf_date">
                    </div>
                    <div class="form-group">
                        <label for="final_status">Final Status:</label>
                        <select class="form-control" id="final_status" name="final_status" required>
                            <option value="">Select Status</option>
                            <option>Complete</option>
                            <option>Decline</option>
                            <option>Pending</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="complete_date">Complete Date:</label>
                        <input type="date" class="form-control" id="complete_date" name="complete_date" required>
                    </div>
                    <div class="form-group">
                        <label for="remark">Remark:</label>
                        <textarea class="form-control" id="remark" name="remark"></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // ฟังก์ชันสำหรับอัปเดตรายละเอียดของบริษัทเมื่อมีการเลือกจาก dropdown
        function updateCompanyDetails() {
            const companyDropdown = document.getElementById('company_name');
            const selectedOption = companyDropdown.options[companyDropdown.selectedIndex];
            const insurance = selectedOption.getAttribute('data-insurance');
            const policy = selectedOption.getAttribute('data-policy');

            document.getElementById('insurance').value = insurance;
            document.getElementById('policy').value = policy; // ระบบจะเติมข้อมูลในฟิลด์นี้
        }


        // ฟังก์ชันสำหรับฟอร์แมตตัวเลขโดยเพิ่มลูกน้ำคั่นหลักพัน
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
            let recoreDate = new Date(this.value);
            let daysToAdd = 14 - 1; // ลบ 1 เพราะวันที่เลือกถือเป็นวันที่ 1
            recoreDate = calculateCompleteDate(recoreDate, daysToAdd);

            let dd = String(recoreDate.getDate()).padStart(2, '0');
            let mm = String(recoreDate.getMonth() + 1).padStart(2, '0');
            let yyyy = recoreDate.getFullYear();

            let completeDate = `${yyyy}-${mm}-${dd}`;
            document.getElementById('complete_date').value = completeDate;
            calculateDurationDate();
        });

        function calculateDurationDate() {
            const recoreDate = new Date(document.getElementById('recore_date').value);
            const completeDate = new Date(document.getElementById('complete_date').value);
            if (recoreDate && completeDate) {
                const diffTime = Math.abs(completeDate - recoreDate);
                const durationDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('duration_date').value = durationDays + " days";
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            calculateDurationDate();
        });
    </script>
</body>
</html>
=======
                    <!-- ปรับตำแหน่งปุ่มให้ถูกต้องภายใน form -->
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <!-- กลุ่มฟอร์มด้านซ้าย -->
                        <div class="form-group">
                            <label for="item">Item:</label>
                            <input type="text" class="form-control" id="item" name="item" required>
                        </div>
                        <div class="form-group">
                            <label for="receive_date">Receive Date:</label>
                            <input type="date" class="form-control" id="receive_date" name="receive_date" required>
                        </div>
                        <div class="form-group">
                            <label for="recore_date">Recore Date:</label>
                            <input type="date" class="form-control" id="recore_date" name="recore_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="company_name">Company Name:</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" required>
                        </div>
                        <div class="form-group">
                            <label for="insurance">Insurance:</label>
                            <input type="text" class="form-control" id="insurance" name="insurance" required>
                        </div>
                        <div class="form-group">
                            <label for="policy">Policy:</label>
                            <input type="text" class="form-control" id="policy" name="policy" required>
                        </div>
                        <div class="form-group">
                            <label for="insure_name">Insure Name:</label>
                            <input type="text" class="form-control" id="insure_name" name="insure_name" required>
                        </div>
                        <div class="form-group">
                            <label for="date_treatment">Date of Treatment:</label>
                            <input type="date" class="form-control" id="date_treatment" name="date_treatment" required>
                        </div>
                        <div class="form-group">
                            <label for="claim_type">Claim Type:</label>
                            <select class="form-control" id="claim_type" name="claim_type" required>
                                <option value="">Select ClaimType</option>
                                <option>Death Beuefit</option>
                                <option>Iu-Patieut</option>
                                <option>Mayor Medical</option>
                                <option>Out-Pultient</option>
                                <option>Dental-Maternity</option>
                                <option>Medical-Expent</option>
                                <option>HB-incentier</option>
                            </select>
                        </div>
                        <!-- ย้าย Hospital/Clinic, Diagnosis, และ Bill Amount มาที่นี่ -->
                        <div class="form-group">
                            <label for="hosp_clinic">Hospital/Clinic:</label>
                            <input type="text" class="form-control" id="hosp_clinic" name="hosp_clinic" required>
                        </div>
                        <div class="form-group">
                            <label for="diagnosis">Diagnosis:</label>
                            <input type="text" class="form-control" id="diagnosis" name="diagnosis" required>
                        </div>
                        <div class="form-group">
                            <label for="bill_amount">Bill Amount:</label>
                            <input type="text" class="form-control" id="bill_amount" name="bill_amount" required oninput="formatNumber(this)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="paid_amount">Paid Amount:</label>
                            <input type="text" class="form-control" id="paid_amount" name="paid_amount" required oninput="formatNumber(this)">
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option>Approve</option>
                                <option>Decline</option>
                                <option>On-Going</option>
                                <option>Rejected</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="declined_amount">Declined Amount:</label>
                            <input type="text" class="form-control" id="declined_amount" name="declined_amount" required oninput="formatNumber(this)">
                        </div>
                        <div class="form-group">
                            <label for="tf_date">TF Date:</label>
                            <input type="date" class="form-control" id="tf_date" name="tf_date">
                        </div>
                        <div class="form-group">
                            <label for="final_status">Final Status:</label>
                            <select class="form-control" id="final_status" name="final_status" required>
                                <option value="">Select Status</option>
                                <option>Complete</option>
                                <option>Decline</option>
                                <option>Pending</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="complete_date">Complete Date:</label>
                            <input type="date" class="form-control" id="complete_date" name="complete_date" required>
                        </div>
                        <div class="form-group">
                            <label for="remark">Remark:</label>
                            <textarea class="form-control" id="remark" name="remark"></textarea>
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
        // ตรวจสอบวันหยุดสุดสัปดาห์
        if (date.getDay() === 6 || date.getDay() === 0) { // เสาร์ = 6, อาทิตย์ = 0
            return true;
        }

        // ตรวจสอบวันหยุดเทศกาล
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
        // รับค่า Recore Date ที่เลือกมา
        let recoreDate = new Date(this.value);

        // นับวันที่ 1 จากวันที่เลือก
        let daysToAdd = 14 - 1; // ลบ 1 เพราะวันที่เลือกถือเป็นวันที่ 1

        // คำนวณ Complete Date โดยบวก 14 วัน (ไม่นับวันหยุดสุดสัปดาห์และวันหยุดเทศกาล)
        recoreDate = calculateCompleteDate(recoreDate, daysToAdd);

        // แปลงวันที่กลับเป็นรูปแบบ yyyy-mm-dd
        let dd = String(recoreDate.getDate()).padStart(2, '0');
        let mm = String(recoreDate.getMonth() + 1).padStart(2, '0');
        let yyyy = recoreDate.getFullYear();

        let completeDate = `${yyyy}-${mm}-${dd}`;

        // ตั้งค่าให้กับฟิลด์ Complete Date
        document.getElementById('complete_date').value = completeDate;

        // คำนวณ Duration Date ใหม่
        calculateDurationDate();
    });

    function calculateDurationDate() {
        const recoreDate = new Date(document.getElementById('recore_date').value);
        const completeDate = new Date(document.getElementById('complete_date').value);

        if (recoreDate && completeDate) {
            const diffTime = Math.abs(completeDate - recoreDate);
            const durationDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // บวก 1 เพื่อให้เริ่มนับจากวันที่ 1

            document.getElementById('duration_date').value = durationDays + " days";
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

    </html>
>>>>>>> origin/main
