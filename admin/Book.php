<?php
require '../config/db.php';
class Book
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    private function uploadImage($image)
    {
        if ($image && isset($image['error']) && $image['error'] === 0) {
            $image_name = $image['name'];
            $image_tmp_name = $image['tmp_name'];
            $image_new_name = uniqid('', true) . '.' . pathinfo($image_name, PATHINFO_EXTENSION);
            $image_upload_path = 'uploads/' . $image_new_name;

            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                return $image_upload_path;
            } else {
                return false;
            }
        }
        return false;
    }

    public function addBook($title, $author, $price, $description, $image, $genre, $created_at)
    {
        $image_path = $this->uploadImage($image);

        if ($image_path) {
            $sql = "INSERT INTO Books (Title, Author, Price, description, Image, Genre, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)";

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
        } else {
            return "Ошибка при загрузке изображения.";
        }
    }

    public function updateBook($id, $title, $author, $price, $description, $genre, $image = null)
    {
        $image_path = null;

        if ($image && isset($image['error']) && $image['error'] === 0) {
            $image_path = $this->uploadImage($image);
            if (!$image_path) {
                return "Ошибка при загрузке изображения.";
            }
        }

        if ($image_path) {
            $sql = "UPDATE Books SET Title = ?, Author = ?, Price = ?, description = ?, Image = ?, Genre = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssdsssi", $title, $author, $price, $description, $image_path, $genre,  $id);
        } else {
            $sql = "UPDATE Books SET Title = ?, Author = ?, Price = ?, description = ?, Genre = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssdssi", $title, $author, $price, $description, $genre, $id);
        }

        if ($stmt->execute()) {
            return "Книга успешно обновлена!";
        } else {
            return "Ошибка при обновлении книги: " . $stmt->error;
        }
    }

    public function delete($id)
    {
        // Start a transaction
        $this->conn->begin_transaction();
    
        try {
            // Delete from Carts table where book_id matches
            $sql1 = "DELETE FROM Carts WHERE book_id = ?";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->bind_param("i", $id);
    
            if (!$stmt1->execute()) {
                throw new Exception("Ошибка при удалении книги из корзины: " . $stmt1->error);
            }
    
            // Delete from Reviews table where book_id matches
            $sql2 = "DELETE FROM Reviews WHERE book_id = ?";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bind_param("i", $id);
    
            if (!$stmt2->execute()) {
                throw new Exception("Ошибка при удалении отзыва: " . $stmt2->error);
            }
    
            // Delete from Order_Items table where book_id matches
            $sql3 = "DELETE FROM Order_Items WHERE book_id = ?";
            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->bind_param("i", $id);
    
            if (!$stmt3->execute()) {
                throw new Exception("Ошибка при удалении книги из заказов: " . $stmt3->error);
            }
    
            // Delete from Books table where id matches
            $sql = "DELETE FROM Books WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
    
            if (!$stmt->execute()) {
                throw new Exception("Ошибка при удалении книги: " . $stmt->error);
            }
    
            // Commit the transaction if all queries succeed
            $this->conn->commit();
    
            return "Книга успешно удалена!";
    
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $this->conn->rollback();
            return $e->getMessage();
        }
    }
    
    public function deleteBook($id)
{
    // Начинаем транзакцию с использованием MySQLi
    $this->conn->begin_transaction();  // Используем маленькие буквы для MySQLi

    try {
        // Удаляем книгу из корзины
        $sql = "DELETE FROM Carts WHERE book_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);  // Привязываем параметр id
        if (!$stmt->execute()) {
            throw new Exception("Ошибка при удалении книги из корзины");
        }

        // Удаляем книгу из таблицы Books
        $sql = "DELETE FROM Books WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);  // Привязываем параметр id
        if (!$stmt->execute()) {
            throw new Exception("Ошибка при удалении книги из таблицы Books");
        }

        // Завершаем транзакцию
        $this->conn->commit();

        return "Книга успешно удалена!";
    } catch (Exception $e) {
        // Если произошла ошибка, откатываем транзакцию
        $this->conn->rollback();
        error_log("Ошибка при удалении книги: " . $e->getMessage());
        return "Ошибка при удалении книги: " . $e->getMessage();
    }
}


    

    public function getAllBooks()
    {
        $sql = "SELECT * FROM Books";
        $result = $this->conn->query($sql);

        $books = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }
        return $books;
    }

    public function getBookById($id)
    {
        $sql = "SELECT * FROM Books WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function __destruct()
    {
        $this->db->closeConnection();
    }
}
?>