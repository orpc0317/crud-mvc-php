<?php

namespace App\Models;

use PDO;
use App\Core\Database;

class UserModel
{
  private $db;

  public function __construct()
  {
    $this->db = (new Database())->getConnection();
  }

  // Crea un registro
  public function create($name, $email, $rol = 'viewer', $estado = 'activo')
  {
    $stmt = $this->db->prepare("INSERT INTO users (name, email, rol, estado) 
                                VALUES (:name, :email, :rol, :estado)");
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':rol', $rol);
    $stmt->bindValue(':estado', $estado);
    return $stmt->execute();
  }

  // Actualiza un registro
public function update($id, $data)
{
    $sql = "UPDATE users SET name = :name, email = :email, rol = :rol, estado = :estado WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':name', $data['name']);
    $stmt->bindValue(':email', $data['email']);
    $stmt->bindValue(':rol', $data['rol']);
    $stmt->bindValue(':estado', $data['estado']);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}

// Elimina un registro
  public function delete($id)
  {
    $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  // 🔍 Obtener registros filtrados, ordenados y paginados
  public function getFiltered($search, $orderColumn, $orderDir, $start, $length)
  {
    $sql = "SELECT * FROM users WHERE name LIKE :search OR email LIKE :search OR rol LIKE :search OR estado LIKE :search ORDER BY $orderColumn $orderDir LIMIT :start, :length";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':search', "%$search%");
    $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
    $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getFilteredSortedUsers($query, $limit, $offset, $sort, $order)
  {
    $allowedSort = ['id', 'name', 'email', 'rol', 'estado'];
    $sort = in_array($sort, $allowedSort) ? $sort : 'id';
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

    $sql = "SELECT * FROM users WHERE name LIKE :q OR email LIKE :q OR rol LIKE :q OR estado LIKE :q ORDER BY $sort $order LIMIT :limit OFFSET :offset";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':q', "%$query%", \PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  // 🔢 Contar registros filtrados
  public function getFilteredUserCount($query)
  {
    $sql = "SELECT COUNT(*) FROM users WHERE name LIKE :q OR email LIKE :q OR rol LIKE :q OR estado LIKE :q";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':q', "%$query%", \PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  public function getFilteredUsers($query = '')
  {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE name LIKE :query OR email LIKE :query OR rol LIKE :query OR estado LIKE :query");
    $stmt->execute(['query' => "%$query%"]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function countAll()
  {
    return $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
  }

  public function countFiltered($search)
  {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE name LIKE :search OR email LIKE :search OR rol LIKE :search OR estado LIKE :search");
    $stmt->bindValue(':search', "%$search%");
    $stmt->execute();
    return $stmt->fetchColumn();
  }
  public function getAll()
  {
    $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
