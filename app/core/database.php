<?php
namespace App\Core;

use Config\Env;

Env::load();

class Database {
  public static function getConnection() {
    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];

    return new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
  }
}
