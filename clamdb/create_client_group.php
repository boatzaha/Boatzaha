    <?php
    session_start();
    require '../db.php';

    // ตรวจสอบการล็อกอิน
    if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
        header("Location: ../login.php");
        exit();
    }

    $search_query = '';
    if (isset($_GET['search'])) {
        $search_query = trim($_GET['search']);
    }

    // ตรวจสอบว่ากำลังแก้ไขข้อมูลหรือไม่
    $edit_mode = false;
    $success_message = '';
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $edit_mode = true;

        // ดึงข้อมูลที่ต้องการแก้ไขมาแสดง
        $edit_sql = "SELECT * FROM add_client_groups WHERE id = :id";
        $stmt = $conn->prepare($edit_sql);
        $stmt->bindParam(':id', $edit_id, PDO::PARAM_INT);
        $stmt->execute();
        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ถ้ามีการส่งข้อมูลจากฟอร์ม POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // รับค่าจากฟอร์ม
        $company_name = $_POST['company_name'];
        $insurance = $_POST['insurance'];
        $policy = $_POST['policy'];

        // ถ้าไม่มีการตรวจสอบข้อมูลซ้ำ ให้บันทึกข้อมูลตามปกติ
        if ($edit_mode) {
            // แก้ไขข้อมูลที่มีอยู่
            $sql = "UPDATE add_client_groups SET company_name = :company_name, insurance = :insurance, policy = :policy WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $edit_id, PDO::PARAM_INT);
        } else {
            // บันทึกข้อมูลใหม่
            $sql = "INSERT INTO add_client_groups (company_name, insurance, policy) VALUES (:company_name, :insurance, :policy)";
            $stmt = $conn->prepare($sql);
        }

        $stmt->bindParam(':company_name', $company_name, PDO::PARAM_STR);
        $stmt->bindParam(':insurance', $insurance, PDO::PARAM_STR);
        $stmt->bindParam(':policy', $policy, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $success_message = $edit_mode ? "ข้อมูลอัปเดตเรียบร้อยแล้ว" : "บันทึกข้อมูลเรียบร้อยแล้ว";
            header("Location: create_client_group.php?success_message=" . urlencode($success_message));
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // ลบข้อมูลเมื่อมีการร้องขอ
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $delete_sql = "DELETE FROM add_client_groups WHERE id = :id";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            header("Location: create_client_group.php?deleted=1");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // ดึงข้อมูลที่เคยกรอกมาแสดง
    $sql = "SELECT * FROM add_client_groups";
    if (!empty($search_query)) {
        $sql .= " WHERE company_name LIKE :search_query OR insurance LIKE :search_query OR policy LIKE :search_query";
    }
    $stmt = $conn->prepare($sql);
    if (!empty($search_query)) {
        $search_param = '%' . $search_query . '%';
        $stmt->bindParam(':search_query', $search_param, PDO::PARAM_STR);
    }
    $stmt->execute();
    $client_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // แสดงข้อความสำเร็จหลังจากอัปเดตหรือสร้างข้อมูล
    if (isset($_GET['success_message'])) {
        $success_message = htmlspecialchars($_GET['success_message']);
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Client Group</title>
        <link rel="icon" href="../image/HOWDENLOGO.png" type="image/png">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <style>
            body {
                background-color: #f8f9fa;
                font-family: Arial, sans-serif;
            }
            .container {
                max-width: 900px;
                background-color: #ffffff;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-top: 50px;
            }
            h1 {
                font-size: 2rem;
                color: #343a40;
                margin-bottom: 30px;
            }
            .form-group label {
                font-weight: 600;
                color: #495057;
            }
            .form-control {
                border-radius: 5px;
                padding: 10px;
                font-size: 1rem;
            }
            .btn-primary, .btn-secondary {
                padding: 10px 20px;
                font-size: 1rem;
                border-radius: 5px;
                transition: background-color 0.3s ease;
                margin-top: 10px;
            }
            .btn-primary:hover {
                background-color: #0056b3;
            }
            .btn-secondary:hover {
                background-color: #5a6268;
            }
            .form-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 20px;
            }
            .search-box, #hidden-data {
                display: none;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                margin-top: 20px;
            }
            table th, table td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            table th {
                background-color: #f1f1f1;
            }
            .delete-icon {
                color: red;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1><?= $edit_mode ? 'Edit' : 'Create' ?> Client Group</h1>

            <!-- ข้อความแจ้งเตือนเมื่อบันทึกหรืออัปเดตสำเร็จ -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <form id="client-form" method="post" action="create_client_group.php<?= $edit_mode ? '?edit_id=' . $edit_id : '' ?>">
                <div class="form-group">
                    <label for="company_name">Company Name:</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="<?= $edit_mode ? htmlspecialchars($edit_data['company_name']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="insurance">Insurance:</label>
                    <input type="text" class="form-control" id="insurance" name="insurance" value="<?= $edit_mode ? htmlspecialchars($edit_data['insurance']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="policy">Policy:</label>
                    <input type="text" class="form-control" id="policy" name="policy" value="<?= $edit_mode ? htmlspecialchars($edit_data['policy']) : '' ?>" required>
                </div>
                <div class="form-actions">
                    <a href="indexclam.php" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary"><?= $edit_mode ? 'Update' : 'Create' ?></button>
                </div>
            </form>

            <!-- Checkbox to toggle visibility -->
            <div class="form-group mt-4">
                <input type="checkbox" id="toggle-visibility">
                <label for="toggle-visibility">Show Previous Entries</label>
            </div>

            <!-- Hidden Data Table -->
            <div id="hidden-data">
                <?php if (!empty($client_groups)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Insurance</th>
                                <th>Policy</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($client_groups as $group): ?>
                                <tr>
                                    <td><?= htmlspecialchars($group['company_name']) ?></td>
                                    <td><?= htmlspecialchars($group['insurance']) ?></td>
                                    <td><?= htmlspecialchars($group['policy']) ?></td>
                                    <td>
                                        <a href="create_client_group.php?edit_id=<?= $group['id'] ?>"><i class="fas fa-edit"></i></a>
                                        <a href="create_client_group.php?delete_id=<?= $group['id'] ?>" onclick="return confirm('Are you sure you want to delete this entry?')"><i class="fas fa-trash delete-icon"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <script>
            // Script to toggle the visibility of the hidden data and search box
            document.getElementById('toggle-visibility').addEventListener('change', function() {
                const hiddenData = document.getElementById('hidden-data');
                const searchBox = document.getElementById('search-box');
                if (this.checked) {
                    hiddenData.style.display = 'block';
                    searchBox.style.display = 'block';
                } else {
                    hiddenData.style.display = 'none';
                    searchBox.style.display = 'none';
                }
            });
        </script>
    </body>
    </html>
