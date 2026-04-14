<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\UserModel;

class UserController
{
    private $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $rol = $_POST['rol'] ?? 'viewer';
            $estado = $_POST['estado'] ?? 'activo';

            $this->model->create($name, $email, $rol, $estado);
            header("Location: index.php?success=created");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
            $this->model->update($_POST['id'], $_POST['name'], $_POST['email'], $_POST['rol'], $_POST['estado']);
            header("Location: index.php?success=updated");
            exit;
        }

        if (isset($_GET['delete'])) {
            $this->model->delete($_GET['delete']);
            header("Location: index.php?success=deleted");
            exit;
        }

        $users = $this->model->getAll();
        include __DIR__ . '/../views/users.php';
    }

    public function ajaxList()
    {
        $query = $_GET['query'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $sort = $_GET['sort'] ?? 'id';
        $order = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc') ? 'DESC' : 'ASC';

        $limit = 10;
        $offset = ($page - 1) * $limit;

        $users = $this->model->getFilteredSortedUsers($query, $limit, $offset, $sort, $order);
        $totalUsers = $this->model->getFilteredUserCount($query);
        $totalPages = ceil($totalUsers / $limit);

        echo json_encode([
            'data' => $users, // ✅ DataTables requiere esta clave
            'recordsTotal' => $totalUsers,
            'recordsFiltered' => $totalUsers,
            'pages' => $totalPages,
            'currentPage' => $page,
            'nextOrder' => ($order === 'ASC') ? 'desc' : 'asc'
        ]);
    }

    public function create()
    {
        $nombre = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $estado = $_POST['estado'] ?? '';

        if ($nombre && $email && $rol && $estado) {
            $this->model->create($nombre, $email, $rol, $estado);
            header('Location: /crud-mvc-php/public/?success=created');
            exit;
        }

        echo "Error: datos incompletos.";
    }

    public function update()
    {
        header('Content-Type: application/json');

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode([
                'success' => false,
                'message' => 'JSON mal formado: ' . json_last_error_msg()
            ]);
            return;
        }

        if (!isset($input['id'], $input['name'], $input['email'], $input['rol'], $input['estado'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $updated = $this->model->update($input['id'], [
            'name' => $input['name'],
            'email' => $input['email'],
            'rol' => $input['rol'],
            'estado' => $input['estado']
        ]);

        echo json_encode([
            'success' => $updated,
            'message' => $updated ? 'Usuario actualizado correctamente' : 'No se pudo actualizar el usuario'
        ]);
    }

    public function delete($id)
    {
        $this->model->delete($id);
        header('Location: /crud-mvc-php/public/?success=deleted');
        exit;
    }
}