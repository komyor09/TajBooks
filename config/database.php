<?php
class Database {
    private $host = "localhost";
    private $db_name = "BookStore";
    private $username = "komyor";
    private $password = "11222033";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Ошибка подключения: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>

