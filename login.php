<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error_message = "Both fields are required";
    } else {
        try {
            // ตรวจสอบว่ามี username อยู่ในระบบหรือไม่
            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // ตรวจสอบ role เพื่อให้สิทธิ์เฉพาะผู้ใช้ที่ได้รับการอนุมัติ
                if ($user['role'] !== 'officer' && $user['role'] !== 'manager') {
                    $error_message = "Your account is not approved yet. Please wait for approval.";
                } else {
                    // บันทึกข้อมูลใน session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['success_message'] = "Login สำเร็จ";

                    header("Location: index2.php");
                    exit();
                }
            } else {
                $error_message = "Invalid username or password";
            }
        } catch (PDOException $e) {
            // จัดการข้อผิดพลาดเมื่อเชื่อมต่อฐานข้อมูลไม่ได้
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

// ตรวจสอบการเข้าสู่ระบบโดยใช้ข้อมูลจาก session
if (isset($_SESSION['username'])) {
    header("Location: index2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="image/HOWDENLOGO.png" type="image/png">
    <title>Login HOWDENMAXI-LIST</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .logo {
            display: block;
            margin: 0 auto 10px;
        }
        .form-group {
            margin-bottom: 1.5rem;
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
        <div class="login-container">
            <img src="image/HOWDENLOGO.png" alt="Logo" class="logo" width="150">
            <h2 class="text-center">LOGIN-PRM</h2>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <a href="register.php" class="btn btn-link d-block text-center mt-3">Register</a>
        </div>
    <div class="footer">
        <p>Copyright © Boat Patthanapong.URU Version 1.0.0</p>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.amazonaws.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
