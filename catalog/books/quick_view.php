<?php
// book/quick-view.php
require_once __DIR__ . '/../../config/db.php';

if (!isset($_GET['id'])) {
    die('ID книги не указан');
}

$bookId = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$book) {
        die('Книга не найдена');
    }
    
    // Выводим HTML с информацией о книге
    ?>
    <div class="row">
        <div class="col-md-4">
            <img src="/pics/<?= htmlspecialchars($book['image_path']) ?>" 
                class="img-fluid" 
                alt="<?= htmlspecialchars($book['title']) ?>">
        </div>
        <div class="col-md-8">
            <h3><?= htmlspecialchars($book['title']) ?></h3>
            <p><strong>Автор:</strong> <?= htmlspecialchars($book['author']) ?></p>
            <p><strong>Рейтинг:</strong> <?= number_format($book['rating'], 1) ?> (<?= $book['reviews_count'] ?> отзывов)</p>
            <p><strong>Цена:</strong> <?= number_format($book['price'], 0, '', ' ') ?> сомони</p>
            <p><?= htmlspecialchars($book['description']) ?></p>
        </div>
    </div>
    <?php
    
} catch (PDOException $e) {
    die('Ошибка при получении информации о книге');
}