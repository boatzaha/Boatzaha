<?php
require 'db.php';
session_start();

// ตรวจสอบค่า ID ที่รับเข้ามาว่าเป็นตัวเลขและมีค่า
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

// ตรวจสอบว่าผู้ใช้มีสิทธิ์ในการลบหรือไม่
$sql = "SELECT created_by FROM customers WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// ถ้าผู้ใช้ไม่มีสิทธิ์ให้แจ้งเตือนและกลับไปที่หน้าหลัก
if (!$customer || $customer['created_by'] != $_SESSION['username']) {
    echo "<script>
            alert('คุณไม่มีสิทธิ์ลบข้อมูลนี้');
            window.location.href = 'index.php';
          </script>";
    exit();
}

// ดำเนินการลบข้อมูล
$sql = "DELETE FROM customers WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo "<script>
            alert('Data has been successfully deleted.');
            window.location.href = 'index.php';
          </script>";
} else {
    echo "<script>
            alert('เกิดข้อผิดพลาดในการลบข้อมูล');
            window.location.href = 'index.php';
          </script>";
}
?>
