<?php
session_start();
require 'db.php';

// ตรวจสอบว่าผู้ใช้เป็นหัวหน้าหรือไม่
if ($_SESSION['role'] != 'manager') {
    echo "You do not have permission to access this page.";
    exit();
}

$sql = "SELECT * FROM pending_users WHERE approved = 0";
$stmt = $conn->prepare($sql);
$stmt->execute();
$pending_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        // อนุมัติการสมัคร
        $sql = "UPDATE pending_users SET approved = 1 WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // ย้ายข้อมูลไปยังตาราง users
        $sql = "INSERT INTO users (username, password, email, role) 
                SELECT username, password, email, role 
                FROM pending_users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // ลบข้อมูลจากตาราง pending_users
        $sql = "DELETE FROM pending_users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo "User approved successfully.";
    } elseif ($action == 'reject') {
        // ปฏิเสธการสมัคร
        $sql = "DELETE FROM pending_users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo "User rejected successfully.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending User Approvals</title>
</head>
<body>
    <h1>Pending User Approvals</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php foreach ($pending_users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                    <button type="submit" name="action" value="approve">Approve</button>
                    <button type="submit" name="action" value="reject">Reject</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
