<?php

require '../config/db.php';
class Catalog 
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function FilterByName($title)
    {
            $sql = "SELECT b.*, 
            a.name as author_name,
            p.name as publisher_name,
            f.name as format_name,
            l.name as language_name
    FROM book b
    LEFT JOIN authors a ON b.author_id = a.id
    LEFT JOIN publishers p ON b.publisher_id = p.id
    LEFT JOIN formats f ON b.format_id = f.id
    LEFT JOIN languages l ON b.language_id = l.id";

            if ($stmt = $this->conn->prepare($sql)) {
                $stmt->bind_param("ssdssss", $title, $author, $price, $description, $image_path, $genre, $created_at);

                if ($stmt->execute()) {
                    return "Книга успешно добавлена!";
                } else {
                    return "Ошибка при добавлении книги: " . $stmt->error;
                }
                $stmt->close();
            } else {
                return "Ошибка при подготовке запроса: " . $this->conn->error;
            }
        }


    public function __destruct()
    {
        $this->db->closeConnection();
    }
}
?>