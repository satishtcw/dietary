<?php
namespace App;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $db;
    private $user;
    private $pass;
    private $pdo;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db = $_ENV['DB_NAME'] ?? 'dietary_db';
        $this->user = $_ENV['DB_USER'] ?? 'root';
        $this->pass = $_ENV['DB_PASS'] ?? '';

        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
			\App\Response::json(['success' => false, 'message' => 'Database connection failed'], 1045);
            exit;
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
?>