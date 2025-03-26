<?php
header("Content-Type: application/json"); // Указываем, что возвращаем JSON

$host = 'localhost';
$dbname = 'bookstore';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("SELECT * FROM books");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($books); // Возвращаем данные в формате JSON
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
<?php
header("Content-Type: application/json");

$host = 'localhost';
$dbname = 'bookstore';
$username = 'root';
$password = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($book);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "ID книги не указан"]);
}
?>
<?php
header("Content-Type: application/json");

$host = 'localhost';
$dbname = 'bookstore';
$username = 'root';
$password = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $genre = $_POST['genre'];
    $createdAt = $_POST['createdAt'];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("UPDATE books SET title = :title, author = :author, price = :price, description = :description, genre = :genre, created_at = :createdAt WHERE id = :id");
        $stmt->execute([
            'title' => $title,
            'author' => $author,
            'price' => $price,
            'description' => $description,
            'genre' => $genre,
            'createdAt' => $createdAt,
            'id' => $id
        ]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "ID книги не указан"]);
}
?>