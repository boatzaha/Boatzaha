<?php
require '../db.php'; // เชื่อมต่อกับฐานข้อมูล

<<<<<<< HEAD
if (isset($_GET['id'])) { // ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
    $id = $_GET['id'];

    // ลบข้อมูล Clam ตาม ID ที่ระบุ
    $sql = "DELETE FROM claims WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
=======
if (isset($_GET['item'])) { // ตรวจสอบว่ามีการส่งค่า item มาหรือไม่
    $item = $_GET['item'];

    // ลบข้อมูล Clam ตาม Item ที่ระบุ
    $sql = "DELETE FROM claims WHERE item = :item";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':item', $item);
>>>>>>> origin/main

    if ($stmt->execute()) {
        // เปลี่ยนเส้นทางกลับไปยังหน้า indexclam.php หลังจากลบข้อมูลเสร็จ
        header("Location: indexclam.php");
        exit();
    } else {
        echo "Error deleting clam report.";
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
