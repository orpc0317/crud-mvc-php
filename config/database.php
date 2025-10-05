<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "Clave01*";
    private $name = "crud_mvc";

    public function getConnection() {
        $conn = new mysqli($this->host, $this->user, $this->pass, $this->name);
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }
        return $conn;
    }
}
?>