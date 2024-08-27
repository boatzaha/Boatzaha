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

// กำหนดตัวแปรเริ่มต้นสำหรับการกรองข้อมูล
$search_clam = $_GET['search_clam'] ?? '';
$search_insure_name = $_GET['search_insure_name'] ?? '';
$search_policy = $_GET['search_policy'] ?? '';
$search_insurance = $_GET['search_insurance'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$show_new_only = isset($_GET['show_new_only']) && $_GET['show_new_only'] == '1';

try {
    $sql = "SELECT *, (created_at >= NOW() - INTERVAL 1 DAY) AS is_new FROM claims WHERE 1=1";

    // เงื่อนไขการกรองข้อมูล
    if ($show_new_only) {
        $sql .= " AND (created_at >= NOW() - INTERVAL 1 DAY)";
    }
    if (!empty($start_date)) {
        $sql .= " AND (recore_date >= :start_date OR complete_date >= :start_date)";
    }
    if (!empty($end_date)) {
        $sql .= " AND (recore_date <= :end_date OR complete_date <= :end_date)";
    }
    if (!empty($search_clam)) {
        $sql .= " AND (item LIKE :search_clam 
                        OR company_name LIKE :search_clam 
                        OR recore_date LIKE :search_clam 
                        OR complete_date LIKE :search_clam)";
    }
    if (!empty($search_insure_name)) {
        $sql .= " AND insure_name LIKE :search_insure_name";
    }
    if (!empty($search_policy)) {
        $sql .= " AND policy LIKE :search_policy";
    }
    if (!empty($search_insurance)) {
        $sql .= " AND insurance LIKE :search_insurance";
    }

    $sql .= " ORDER BY is_new DESC, created_at DESC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($search_clam)) {
        $searchTerm = '%' . $search_clam . '%';
        $stmt->bindParam(':search_clam', $searchTerm);
    }
    if (!empty($start_date)) {
        $stmt->bindParam(':start_date', $start_date);
    }
    if (!empty($end_date)) {
        $stmt->bindParam(':end_date', $end_date);
    }
    if (!empty($search_insure_name)) {
        $searchInsureName = '%' . $search_insure_name . '%';
        $stmt->bindParam(':search_insure_name', $searchInsureName);
    }
    if (!empty($search_policy)) {
        $searchPolicy = '%' . $search_policy . '%';
        $stmt->bindParam(':search_policy', $searchPolicy);
    }
    if (!empty($search_insurance)) {
        $searchInsurance = '%' . $search_insurance . '%';
        $stmt->bindParam(':search_insurance', $searchInsurance);
    }
    $stmt->execute();
    $clams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ตรวจสอบว่ามีผลลัพธ์ที่เกี่ยวข้องกับการค้นหาสำหรับการแสดงในกรอบสีแดงหรือไม่
    $highlightSearchResult = false;
    if (!empty($search_insure_name) || !empty($search_policy) || !empty($search_insurance)) {
        $highlightSearchResult = true;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Report Management</title>
    <link rel="icon" href="../image/HOWDENLOGO.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index2.php"><img src="../image/HOWDENLOGO.png" alt="Logo" height="40"> Claim Report Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="navbar-text text-white mr-3">User: <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index2.php">Home Page</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../clamdb/indexclam.php">Refresh</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Menu
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php if ($role != 'officer'): ?>
                            <a class="dropdown-item" href="../dashboard.php">Permission</a>
                            <a class="dropdown-item" href="../dashboard2.php">Dashboard</a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="../index.php">Add Customer</a>
                        <a class="dropdown-item" href="../clamdb/indexclam.php">Claim Reports</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h3 class="text-center">Search Claim Data</h3>
        <form method="get" action="indexclam.php" class="mb-3 search-form mx-auto" style="max-width: 700px;">
            <!-- Search Fields -->
            <div class="form-row justify-content-center">
                <div class="form-group col-md-4">
                    <input type="text" id="search_clam" name="search_clam" class="form-control" placeholder="Search by Item or Company" value="<?= htmlspecialchars($search_clam) ?>">
                </div>
                <div class="form-group col-md-4">
                    <input type="text" id="search_insure_name" name="search_insure_name" class="form-control" placeholder="Search by Insure Name" value="<?= htmlspecialchars($search_insure_name) ?>">
                </div>
            </div>
            <!-- Additional Fields and Buttons -->
            <div class="form-row justify-content-center">
                <div class="form-group col-md-4">
                    <input type="text" id="search_policy" name="search_policy" class="form-control" placeholder="Search by Policy" value="<?= htmlspecialchars($search_policy) ?>">
                </div>
                <div class="form-group col-md-4">
                    <input type="text" id="search_insurance" name="search_insurance" class="form-control" placeholder="Search by Insurance" value="<?= htmlspecialchars($search_insurance) ?>">
                </div>
            </div>
            <!-- Buttons for Filtering and Submitting -->
            <div class="form-row justify-content-center">
                <div class="form-group d-flex align-items-center justify-content-center">
                    <button type="button" id="toggle_new_only" class="btn btn-outline-secondary mr-3">Show New Only</button>
                    <input type="hidden" id="show_new_only" name="show_new_only" value="0">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-between mb-3">
            <a href="create_clam.php" class="btn btn-success">Add Data</a>
            <a href="export_excel.php?show_new_only=<?= $show_new_only ? '1' : '0' ?>" class="btn btn-info"><i class="fas fa-file-excel"></i> Export to Excel</a>
        </div>
        <!-- ส่วนแสดงผลการค้นหาในกรอบสีแดง -->
        <?php if ($highlightSearchResult && !empty($clams)) : ?>
            <div class="search-result-box">
                <p><strong>Search Result:</strong></p>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Company Name:</strong> <?= htmlspecialchars($clams[0]['company_name']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Insurance:</strong> <?= htmlspecialchars($clams[0]['insurance']) ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Policy:</strong> <?= htmlspecialchars($clams[0]['policy']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Insure Name:</strong> <?= htmlspecialchars($clams[0]['insure_name']) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Item</th>
                        <th>Receive Date</th>
                        <th>Company Name</th>
                        <th>Insure Name</th>
                        <th>Recore Date</th>
                        <th>Complete Date</th>
                        <th>Duration Date <span id="mark-expire"></span></th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $expireExists = false;

                    if (!empty($clams)):
                        foreach ($clams as $clam):
                            $statusClass = "";
                            $statusText = "";

                            if (!empty($clam['recore_date'])) {
                                $recoreDate = new DateTime($clam['recore_date']);
                                $now = new DateTime();
                                $interval = $recoreDate->diff($now);
                                $durationDays = $interval->days + 1;

                                $formattedRecoreDate = $recoreDate->format('d/m/y');
                                $formattedCompleteDate = !empty($clam['complete_date']) ? (new DateTime($clam['complete_date']))->format('d/m/y') : 'N/A';

                                if ($durationDays > 14) {
                                    $statusClass = "status-red";
                                    $statusText = "Expire";
                                    $expireExists = true;
                                } elseif ($durationDays >= 8) {
                                    $statusClass = "status-yellow";
                                } else {
                                    $statusClass = "status-green";
                                }

                                $durationDate = $durationDays . ' days';
                            } else {
                                $formattedRecoreDate = 'N/A';
                                $formattedCompleteDate = 'N/A';
                                $durationDate = 'N/A';
                            }

                            $isNew = $clam['is_new'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($clam['id']) ?></td> <!-- เพิ่มแสดง ID -->
                        <td><?= htmlspecialchars($clam['item']) ?> <?php if ($isNew) echo "<span class='badge badge-warning'>New</span>"; ?></td>
                        <td><?= htmlspecialchars($clam['receive_date']) ?></td>
                        <td><?= htmlspecialchars($clam['company_name']) ?></td>
                        <td><?= htmlspecialchars($clam['insure_name']) ?></td>
                        <td><?= htmlspecialchars($formattedRecoreDate) ?></td>
                        <td><?= htmlspecialchars($formattedCompleteDate) ?></td>
                        <td><span class="<?= $statusClass ?>"><?= htmlspecialchars($durationDate) ?> <?php if ($statusText) echo "<span class='status-red'>$statusText</span>"; ?></span></td>
                        <td class="btn-group">
                        <a href="view_clam.php?id=<?= $clam['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        <a href="update_clam.php?id=<?= $clam['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <a href="delete_clam.php?id=<?= $clam['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php
                        endforeach;
                    else:
                    ?>
                    <tr>
                        <td colspan="9" class="text-center">No data found</td> <!-- ปรับจำนวนคอลัมน์ให้ตรงกับการเพิ่ม ID -->
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="footer">
        <p>Copyright © Boat Patthanapong.URU Version 1.0.0</p>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Script for handling the Show New Only toggle button
        document.getElementById('toggle_new_only').addEventListener('click', function() {
            const button = this;
            const input = document.getElementById('show_new_only');

            if (input.value === "1") {
                input.value = "0";
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
                button.textContent = "Show New Only";
            } else {
                input.value = "1";
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
                button.textContent = "Showing New Only";
            }
        });

        if (<?= json_encode($expireExists) ?>) {
            document.getElementById('mark-expire').innerText = '(Expire)';
        }
    </script>
</body>
</html>
