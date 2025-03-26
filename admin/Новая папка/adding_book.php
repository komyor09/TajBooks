<?php
require_once 'Database.php';
require_once 'Book.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    if (isset($_POST['title'], $_POST['author'], $_POST['price'], $_POST['description'], $_FILES['image'], $_POST['genre'], $_POST['created_at'])) {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $genre = $_POST['genre'];
        $created_at = $_POST['created_at'];
        $image = $_FILES['image'];

        $book = new Book();
        $result = $book->addBook($title, $author, $price, $description, $image, $genre, $created_at);
        echo $result;
    } else {
        echo "Ошибка: Не все данные были переданы.";
    }
}
?>
  
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Document</title>
  </head>
  <body>
    <!-- Форма для добавления книги -->
    <section>
            <h2>Добавить новую книгу</h2>
            <form action="read_book.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="title">Название книги:</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="author">Автор:</label>
                    <input type="text" id="author" name="author" required>
                </div>

                <div class="form-group">
                    <label for="price">Цена:</label>
                    <input type="number" id="price" name="price" required>
                </div>

                <div class="form-group">
                    <label for="description">Описание:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="genre">Жанр:</label>
                    <input type="text" id="genre" name="genre" required>
                </div>

                <div class="form-group">
                    <label for="created_at">Дата добавления:</label>
                    <input type="datetime-local" id="created_at" name="created_at" required>
                </div>

                <div class="form-group">
                    <label for="image">Изображение книги:</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Добавить книгу</button>
                </div>
            </form>
        </section>

  </body>
  </html>
  