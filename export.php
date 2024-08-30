<?php
require 'db.php';
require 'vendor/autoload.php';  

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// รับค่า username จาก session
$username = $_SESSION['username'];

// ตรวจสอบว่ามีการส่งค่าพารามิเตอร์ created_by หรือไม่ และตรวจสอบความถูกต้องของค่า
$created_by = isset($_GET['created_by']) && !empty($_GET['created_by']) ? $_GET['created_by'] : null;

try {
    // Query เพื่อดึงข้อมูลลูกค้า
    $sql = "SELECT * FROM customers";
    if ($created_by) {
        $sql .= " WHERE created_by = :created_by";
    }
    $stmt = $conn->prepare($sql);
    if ($created_by) {
        $stmt->bindParam(':created_by', $created_by, PDO::PARAM_STR);
    }
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // สร้างไฟล์ Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // กำหนดหัวตารางให้เป็นตัวหนาและมีพื้นหลังสีเหลือง
    $headerStyleArray = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFFF00']
        ],
    ];

    $sheet->getStyle('A1:M1')->applyFromArray($headerStyleArray);

    // กำหนดหัวข้อของตาราง
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Client Group');
    $sheet->setCellValue('C1', 'Client Company Name');
    $sheet->setCellValue('D1', 'Inception Date');
    $sheet->setCellValue('E1', 'Income Class');
    $sheet->setCellValue('F1', 'Revenue');
    $sheet->setCellValue('G1', 'Premium');
    $sheet->setCellValue('H1', 'Close Date');
    $sheet->setCellValue('I1', 'Department');
    $sheet->setCellValue('J1', 'Funnel Stage');
    $sheet->setCellValue('K1', 'Description');
    $sheet->setCellValue('L1', 'Sum Insured');
    $sheet->setCellValue('M1', 'Created by');

    // เพิ่มข้อมูลลงในตาราง
    $row = 2;
    foreach ($customers as $customer) {
        $sheet->setCellValue('A' . $row, $row - 1);
        $sheet->setCellValue('B' . $row, $customer['client_group']);
        $sheet->setCellValue('C' . $row, $customer['name']);
        $sheet->setCellValue('D' . $row, date('d-M-Y', strtotime($customer['inception_date'])));
        $sheet->setCellValue('E' . $row, $customer['class']);
        $sheet->setCellValue('F' . $row, number_format($customer['revenue']));
        $sheet->setCellValue('G' . $row, number_format($customer['premium']));
        $sheet->setCellValue('H' . $row, $customer['close_date'] != '0000-00-00' ? date('d-M-Y', strtotime($customer['close_date'])) : 'N/A');
        $sheet->setCellValue('I' . $row, $customer['department']);
        $sheet->setCellValue('J' . $row, $customer['status']);
        $sheet->setCellValue('K' . $row, $customer['description']);
        $sheet->setCellValueExplicit('L' . $row, $customer['sum_insured'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('M' . $row, $customer['created_by']);
        $row++;
    }

    // ปรับขนาดความกว้างของคอลัมน์อัตโนมัติ
    foreach (range('A', 'M') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // ตั้งชื่อไฟล์
    $filename = 'customers_export_' . date('Y-m-d') . '.xlsx';

    // ตั้งค่า header เพื่อลงให้เป็นไฟล์ Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // ส่งไฟล์ไปยัง output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    exit;
}
