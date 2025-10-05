<?php
require 'vendor/autoload.php';
require_once 'models/UserModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$model = new UserModel();

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$format = isset($_GET['format']) ? strtolower($_GET['format']) : 'xlsx';

$users = $model->getFilteredSortedUsers($query, 10000, 0, 'id', 'asc'); // Exporta hasta 10,000 registros
$filename = "usuarios_exportados_" . date('Ymd_His');

switch ($format) {
  case 'csv':
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=$filename.csv");
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Nombre', 'Email']);
    foreach ($users as $user) {
      fputcsv($output, [$user['id'], $user['name'], $user['email']]);
    }
    fclose($output);
    break;

  case 'txt':
    header('Content-Type: text/plain');
    header("Content-Disposition: attachment; filename=$filename.txt");
    foreach ($users as $user) {
      echo "ID: {$user['id']} | Nombre: {$user['name']} | Email: {$user['email']}\n";
    }
    break;

  case 'xlsx':
  default:
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Nombre');
    $sheet->setCellValue('C1', 'Email');

    $row = 2;
    foreach ($users as $user) {
      $sheet->setCellValue("A$row", $user['id']);
      $sheet->setCellValue("B$row", $user['name']);
      $sheet->setCellValue("C$row", $user['email']);
      $row++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=$filename.xlsx");
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    break;
}
exit;
