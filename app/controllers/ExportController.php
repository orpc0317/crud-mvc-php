<?php
namespace App\Controllers;

use App\Core\Database;

class ExportController {
  public function exportCsv() {
    // Conexión a la base de datos
    $db = (new Database())->getConnection();

    // Consulta de usuarios
    $stmt = $db->query("SELECT id, name, email FROM users");
    $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Encabezados para descarga
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="usuarios.csv"');

    // Generar CSV
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Nombre', 'Email']); // encabezado

    foreach ($users as $user) {
      fputcsv($output, [$user['id'], $user['name'], $user['email']]);
    }

    fclose($output);
  }
}