<?php
session_start();
require 'db.php';

// ตรวจสอบการล็อกอิน
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
                if ($user['role'] !== 'officer' && $user['role'] !== 'manager') {
                    $error_message = "Your account is not approved yet. Please wait for approval.";
                } else {
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
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

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
    <link rel="stylesheet" href="css/styleslogin.css"> <!-- แยกไฟล์ CSS -->
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
        <div class="login-logo">
            <img src="image/HOWDENLOGO.png" alt="Logo">
        </div>
            <div class="login-header">
                <h2>Sign In PRM </h2>
            </div>
            <div class="login-body">
                <div class="login-avatar">
                    <img src="image/circle-user.png" alt="Avatar">
                </div>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <form method="post" action="login.php">
                    <div class="form-group">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
            <div class="login-footer">
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
