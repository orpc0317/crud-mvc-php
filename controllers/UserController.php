<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';

class UserController {
    private $model;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->model = new User($db);
    }

    public function handleRequest() {
        // Crear usuario
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
            $this->model->create($_POST['name'], $_POST['email']);
            header("Location: index.php?success=created");
            exit;
        }

        // Actualizar usuario
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
            $this->model->update($_POST['id'], $_POST['name'], $_POST['email']);
            header("Location: index.php?success=updated");
            exit;
        }

        // Eliminar usuario
        if (isset($_GET['delete'])) {
            $this->model->delete($_GET['delete']);
            header("Location: index.php?success=deleted");
            exit;
        }

        // Mostrar vista
        $users = $this->model->getAll();
        include __DIR__ . '/../views/users.php';
    }
}