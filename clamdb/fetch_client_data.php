<?php
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['insurance'])) {
    $insurance = $_POST['insurance'];

    // SQL statement to fetch the client group data
    $sql = "SELECT * FROM add_client_groups WHERE insurance = :insurance LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':insurance', $insurance);
    $stmt->execute();
    $clientGroup = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($clientGroup) {
        // If data found, return it as JSON
        echo json_encode([
            'company_name' => $clientGroup['company_name'],
            'insurance' => $clientGroup['insurance'],
            'policy' => $clientGroup['policy'],
        ]);
    } else {
        // If no data found, return an empty array
        echo json_encode(null);
    }
}
?>
