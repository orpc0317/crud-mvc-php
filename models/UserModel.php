<?php
class UserModel {
  private $db;

  public function __construct() {
    try {
      $this->db = new PDO('mysql:host=localhost;dbname=crud_mvc', 'root', 'Clave01*');
      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      die("Error de conexión: " . $e->getMessage());
    }
  }

  // 🔍 Obtener usuarios filtrados, ordenados y paginados
  public function getFilteredSortedUsers($query, $limit, $offset, $sort, $order) {
    $allowedSort = ['id', 'name', 'email'];
    $sort = in_array($sort, $allowedSort) ? $sort : 'id';
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM users WHERE name LIKE :q OR email LIKE :q ORDER BY $sort $order LIMIT :limit OFFSET :offset";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':q', "%$query%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // 🔢 Contar usuarios filtrados
  public function getFilteredUserCount($query) {
    $sql = "SELECT COUNT(*) FROM users WHERE name LIKE :q OR email LIKE :q";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':q', "%$query%", PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
  }
}