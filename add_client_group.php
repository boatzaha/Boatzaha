<?php
session_start();
require 'db.php';

// ตรวจสอบสิทธิ์เฉพาะผู้ใช้ที่เป็น manager เท่านั้น
if ($_SESSION['role'] != 'manager') {
    echo "You do not have permission to access this page.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_group_name = trim($_POST['client_group_name']);

    if (empty($client_group_name)) {
        $error_message = "Client group name is required.";
    } else {
        // ตรวจสอบว่ากลุ่มลูกค้าที่ต้องการเพิ่มมีอยู่แล้วหรือไม่
        $sql = "SELECT COUNT(*) FROM client_groups WHERE group_name = :group_name";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':group_name', $client_group_name);
        $stmt->execute();
        $exists = $stmt->fetchColumn();

        if ($exists) {
            $error_message = "This client group already exists.";
        } else {
            // เพิ่มกลุ่มลูกค้าใหม่
            $sql = "INSERT INTO client_groups (group_name) VALUES (:group_name)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':group_name', $client_group_name);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Client group has been added successfully.";
                header("Location: index.php");
                exit();
            } else {
                $error_message = "An error occurred while adding the client group.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Client Group</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Client Group</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <form method="post" action="add_client_group.php">
            <div class="form-group">
                <label for="client_group_name">Client Group Name:</label>
                <input type="text" name="client_group_name" id="client_group_name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Add Group</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.amazonaws.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
