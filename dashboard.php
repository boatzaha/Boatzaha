<?php
session_start();
require 'db.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่า role เป็น 'manager' หรือไม่
if ($_SESSION['role'] != 'manager') {
    echo "You do not have permission to access this page.";
    exit();
}

// ตรวจสอบว่ามีการค้นหาหรือไม่
$search = "";
$roleFilter = "";
$branchFilter = "";
if (isset($_GET['search']) || isset($_GET['role_filter']) || isset($_GET['branch_filter'])) {
    $search = $_GET['search'] ?? '';
    $roleFilter = $_GET['role_filter'] ?? '';
    $branchFilter = $_GET['branch_filter'] ?? '';

    $sql = "SELECT * FROM users WHERE (username LIKE :search OR email LIKE :search)";
    if (!empty($roleFilter)) {
        $sql .= " AND role = :roleFilter";
    }
    if (!empty($branchFilter)) {
        $sql .= " AND branch = :branchFilter";
    }

    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $search . '%';
    $stmt->bindParam(':search', $searchTerm);
    if (!empty($roleFilter)) {
        $stmt->bindParam(':roleFilter', $roleFilter);
    }
    if (!empty($branchFilter)) {
        $stmt->bindParam(':branchFilter', $branchFilter);
    }
} else {
    // ดึงข้อมูลผู้ใช้ทั้งหมดถ้าไม่มีการค้นหา
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// หากมีการส่งแบบฟอร์มเพื่ออัปเดต role
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['roles'] as $username => $data) {
        $newRole = $data['role'];
        $newBranch = $data['branch'];

        if (!empty($username) && !empty($newRole) && in_array($newRole, ['member', 'officer', 'manager'])) {
            // อัปเดต role และ branch ของผู้ใช้ในฐานข้อมูล
            $sql = "UPDATE users SET role = :role, branch = :branch WHERE username = :username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':role', $newRole);
            $stmt->bindParam(':branch', $newBranch);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
        }
    }
    header("Location: dashboard.php"); // เปลี่ยนเส้นทางหลังจากอัปเดต
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="image/HOWDENLOGO.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2rem;
            color: #343a40;
        }
        .form-inline {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 5px;
            height: 40px;
        }
        .btn-outline-success {
            border-radius: 5px;
        }
        .table th {
            background: #343a40;
            color: #ffffff;
        }
        .table td, .table th {
            padding: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Permission</h1>
            <div class="d-flex align-items-center">  
                <a href="index.php" class="btn btn-secondary">Back</a>
                <button id="confirm-btn" class="btn btn-primary ml-2" onclick="submitForm()">Change</button>
            </div>
        </div>

        <!-- ฟอร์มค้นหาและกรอง Role และ Branch -->
        <form class="form-inline" method="get" action="dashboard.php">
            <input class="form-control" type="search" name="search" placeholder="Search for Username or Email" value="<?= htmlspecialchars($search) ?>">
            
            <select class="form-control" name="role_filter">
                <option value="">Filter by all Roles</option>
                <option value="member" <?= $roleFilter == 'member' ? 'selected' : '' ?>>Member</option>
                <option value="officer" <?= $roleFilter == 'officer' ? 'selected' : '' ?>>Officer</option>
                <option value="manager" <?= $roleFilter == 'manager' ? 'selected' : '' ?>>Manager</option>
            </select>

            <select class="form-control" name="branch_filter">
                <option value="">Filter by all Branches</option>
                <option value="LP" <?= $branchFilter == 'LP' ? 'selected' : '' ?>>LP</option>
                <option value="CW" <?= $branchFilter == 'CW' ? 'selected' : '' ?>>CW</option>
            </select>

            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <h2>All members</h2>
        <div class="table-responsive">
            <form id="role-form" method="post" action="dashboard.php">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Branch</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $index => $user): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <select name="roles[<?= htmlspecialchars($user['username']) ?>][role]" class="form-control">
                                        <option value="member" <?= $user['role'] == 'member' ? 'selected' : '' ?>>Member</option>
                                        <option value="officer" <?= $user['role'] == 'officer' ? 'selected' : '' ?>>Officer</option>
                                        <option value="manager" <?= $user['role'] == 'manager' ? 'selected' : '' ?>>Manager</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="roles[<?= htmlspecialchars($user['username']) ?>][branch]" class="form-control">
                                        <option value="LP" <?= $user['branch'] == 'LP' ? 'selected' : '' ?>>LP</option>
                                        <option value="CW" <?= $user['branch'] == 'CW' ? 'selected' : '' ?>>CW</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No information found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <script>
        function submitForm() {
            if (confirm("Are you sure you want to change the roles and branches for the selected users?")) {
                document.getElementById('role-form').submit();
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.amazonaws.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
