<?php
namespace App\Models;

use App\Core\Database;

class UserModel
{
  private $db;

  public function __construct()
  {
    $this->db = (new Database())->getConnection();
  }

  // 🔍 Obtener usuarios filtrados, ordenados y paginados
  public function getFilteredSortedUsers($query, $limit, $offset, $sort, $order)
  {
    $allowedSort = ['id', 'name', 'email'];
    $sort = in_array($sort, $allowedSort) ? $sort : 'id';
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM users WHERE name LIKE :q OR email LIKE :q ORDER BY $sort $order LIMIT :limit OFFSET :offset";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':q', "%$query%", \PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  // 🔢 Contar usuarios filtrados
  public function getFilteredUserCount($query)
  {
    $sql = "SELECT COUNT(*) FROM users WHERE name LIKE :q OR email LIKE :q";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':q', "%$query%", \PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  public function getFilteredUsers($query = '')
  {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE name LIKE :query OR email LIKE :query");
    $stmt->execute(['query' => "%$query%"]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}
