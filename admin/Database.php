<?php
 class Database
{
    private $servername = "localhost";
    private $username = "root"; 
    private $password = "";
    private $dbname = "TajBooks";
    private $conn;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Ошибка подключения: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn; 
    }

    public function closeConnection()
    {
        $this->conn->close();
    }
}
?>