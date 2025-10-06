<?php
namespace App\Models;

$toastMessage = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $toastMessage = 'Registro creado exitosamente';
            break;
        case 'updated':
            $toastMessage = 'Registro actualizado correctamente';
            break;
        case 'deleted':
            $toastMessage = 'Registro eliminado';
            break;
    }
} 

// Clase que representa el modelo de Usuario
class User
{
    private $conn;       // Conexión a la base de datos
    private $table = "users"; // Nombre de la tabla

    // Constructor: recibe la conexión activa
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Obtiene todos los usuarios de la base de datos
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        return $this->conn->query($query);
    }

    // Crea un nuevo usuario con nombre y correo
    public function create($name, $email)
    {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        return $stmt->execute();
    }

    // Actualiza los datos de un usuario existente
    public function update($id, $name, $email)
    {
        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);
        return $stmt->execute();
    }

    // Elimina un usuario por su ID
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
