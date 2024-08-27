<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $confirm_password = trim($_POST['confirm_password']); // ฟิลด์ยืนยันรหัสผ่าน

    if (empty($username) || empty($password) || empty($email) || empty($confirm_password)) {
        $error_message = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } elseif ($password !== $confirm_password) { // ตรวจสอบว่ารหัสผ่านและการยืนยันตรงกันหรือไม่
        $error_message = "Passwords do not match";
    } else {
        // ตรวจสอบว่ามี username หรือ email ซ้ำหรือไม่
        $check_sql = "SELECT * FROM users WHERE username = :username OR email = :email";
        $stmt = $conn->prepare($check_sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $error_message = "Username or email already exists";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'member';

            try {
                $sql = "INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password_hash);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':role', $role);

                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Registration successful! Please wait for approval.";
                    header("Location: login.php");
                    exit();
                } else {
                    $error_message = "Error: " . $stmt->errorInfo()[2];
                }
            } catch (PDOException $e) {
                $error_message = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="icon" href="image/HOWDENLOGO.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .register-container {
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
        <div class="register-container">
            <img src="image/HOWDENLOGO.png" alt="Logo" class="logo" width="150">
            <h2 class="text-center">Register</h2>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="post" action="register.php">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            <a href="login.php" class="btn btn-link d-block text-center mt-3">Login</a>
        </div>
    <div class="footer">
        <p>Copyright © Boat Patthanapong.URU Version 1.0.0</p>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
