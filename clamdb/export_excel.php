<?php
require '../db.php';
require '../vendor/autoload.php'; // โหลด PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// ตรวจสอบการกรองข้อมูลจากวันที่และข้อมูลที่เป็น New
$export_date = isset($_GET['export_date']) ? $_GET['export_date'] : null;
$show_new_only = isset($_GET['show_new_only']) && $_GET['show_new_only'] === '1';

// สร้าง Spreadsheet และ Worksheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ตั้งชื่อหัวตาราง
$headers = [
    'A1' => 'Item', 'B1' => 'Receive Date', 'C1' => 'Recore Date', 'D1' => 'Company Name', 
    'E1' => 'Insurance', 'F1' => 'Policy', 'G1' => 'Insure Name', 'H1' => 'Date Treatment', 
    'I1' => 'Claim Type', 'J1' => 'Hosp/Clinic', 'K1' => 'Diagnosis', 'L1' => 'Bill Amount', 
    'M1' => 'Remark', 'N1' => 'Status', 'O1' => 'Paid Amount', 'P1' => 'Declined Amount', 
    'Q1' => 'TF Date', 'R1' => 'Final Status', 'S1' => 'Complete Date', 'T1' => 'Duration Date', 
    'U1' => 'Created By'
];

// กำหนดสีและสไตล์ให้หัวตาราง
$headerStyleArray = [
    'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => '0000FF'], // สีน้ำเงิน
    ],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

foreach ($headers as $cell => $header) {
    $sheet->setCellValue($cell, $header);
    $sheet->getStyle($cell)->applyFromArray($headerStyleArray);
}

// ดึงข้อมูลจากฐานข้อมูลพร้อมการกรอง
$sql = "SELECT * FROM claims WHERE 1=1";

if ($export_date) {
    $sql .= " AND DATE(created_at) = :export_date";
}

if ($show_new_only) {
    $sql .= " AND (created_at >= NOW() - INTERVAL 1 DAY)";
}

$stmt = $conn->prepare($sql);

if ($export_date) {
    $stmt->bindParam(':export_date', $export_date);
}

$stmt->execute();
$claims = $stmt->fetchAll(PDO::FETCH_ASSOC);

// เพิ่มข้อมูลลงในแถวถัดไป
$row = 2;
foreach ($claims as $claim) {
    $sheet->setCellValue('A' . $row, $claim['item']);
    $sheet->setCellValue('B' . $row, $claim['receive_date']);
    $sheet->setCellValue('C' . $row, $claim['recore_date']);
    $sheet->setCellValue('D' . $row, $claim['company_name']);
    $sheet->setCellValue('E' . $row, $claim['insurance']);
    $sheet->setCellValue('F' . $row, $claim['policy']);
    $sheet->setCellValue('G' . $row, $claim['insure_name']);
    $sheet->setCellValue('H' . $row, $claim['date_treatment']);
    $sheet->setCellValue('I' . $row, $claim['claim_type']);
    $sheet->setCellValue('J' . $row, $claim['hosp_clinic']);
    $sheet->setCellValue('K' . $row, $claim['diagnosis']);
    $sheet->setCellValue('L' . $row, number_format($claim['bill_amount'], 2));
    $sheet->setCellValue('M' . $row, $claim['remark']);
    $sheet->setCellValue('N' . $row, $claim['status']);
    $sheet->setCellValue('O' . $row, number_format($claim['paid_amount'], 2));
    $sheet->setCellValue('P' . $row, number_format($claim['declined_amount'], 2));
    $sheet->setCellValue('Q' . $row, $claim['tf_date']);
    $sheet->setCellValue('R' . $row, $claim['final_status']);
    $sheet->setCellValue('S' . $row, $claim['complete_date']);
    $sheet->setCellValue('T' . $row, $claim['duration_date']);
    $sheet->setCellValue('U' . $row, $claim['created_by']);
    
    // กำหนดสีพื้นหลังและฟอร์แมตของ Paid Amount และ Declined Amount
    if ($claim['paid_amount'] > 0) {
        $sheet->getStyle('O' . $row)->getFont()->getColor()->setARGB(Color::COLOR_GREEN);
    }

    if ($claim['declined_amount'] > 0) {
        $sheet->getStyle('P' . $row)->getFont()->getColor()->setARGB(Color::COLOR_RED);
    }
    
    $row++;
}

// ปรับความกว้างคอลัมน์อัตโนมัติ
foreach (range('A', 'U') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// ตั้งค่า header สำหรับการดาวน์โหลดไฟล์ Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="claims_report.xlsx"');
header('Cache-Control: max-age=0');

// สร้างไฟล์ Excel และส่งออก
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
