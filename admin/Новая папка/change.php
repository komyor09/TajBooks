<?php

include 'db_connection.php';

if(isset($_GET['id']))
{
    $bookId = (int) $_GET['id'];
    $query = "SELECT * FROM Books WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $bookId, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        // echo "Название книги: " . $book['Title'] . "<br>";
        // echo "Автор: " . $book['Author'] . "<br>";
        // echo "Год выпуска: " . $book['created_at'] . "<br>";
    


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="change.css">
    <title>Редактировать книгу</title>
</head>
<body>

<main>
    <h1>Редактировать книгу</h1>
    <form action="BookServise.php" method="POST"  enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?=  htmlspecialchars($_GET['id'] ) ?>">
        <input type="hidden" name="action" value="update">
        
        <div class="form-group">
            <label for="title">Название книги:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['Title'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="author">Автор:</label>
            <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['Author'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="price">Цена:</label>
            <input type="number" id="price" name="price" value="<?= htmlspecialchars($book['Price'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Описание:</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="genre">Жанр:</label>
            <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($book['Genre'] ?? '') ?>" required>
        </div>
        
        <!-- <div class="form-group">
            <label for="created_at">Дата добавления:</label>
            <input type="datetime-local" id="created_at" name="created_at" value="<?= htmlspecialchars($book['created_at'] ?? '') ?>" required>
        </div> -->
        
        <div class="form-group">
            <label for="image">Изображение книги:</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if (!empty($book['Image'])): ?>
                <p>Текущее изображение: <img src="<?= htmlspecialchars($book['Image']) ?>" alt="Обложка книги" width="100"></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Сохранить изменения</button>
        </div>
    </form>
    
    <a href="../index.php">Вернуться на главную</a>
</main>
</body>
</html>

<?php

    } else {
        echo "Книга не найдена.";
    }   
} else {
    echo "ID книги не передан.";

}
