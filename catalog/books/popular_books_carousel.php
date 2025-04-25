<?php
session_start();

$email = $_SESSION['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['reg_email']) && filter_var($_POST['reg_email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['email'] = $_POST['reg_email'];
        header("Location: ../auth/register.php");
        exit();
    } else {
        $error = "Введите корректный email!";
    }
}

// Подключение к базе данных (PDO)
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';

function getPopularBooks($pdo, $limit, $offset) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM book ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка при получении книг: " . $e->getMessage());
        return [];
    }
}

$books = getPopularBooks($pdo, 8, 0);
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="catalog/books/books-carousel.css">

<!-- Карусель книг -->
<div class="book-carousel animate__animated animate__fadeIn">
    <div class="carousel-header">
        <h2 class="carousel-title">Популярные книги</h2>
        <div class="carousel-controls">
            <button class="carousel-btn" id="carouselPrevBtn"><i class="fas fa-chevron-left"></i></button>
            <button class="carousel-btn" id="carouselNextBtn"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <div class="carousel-container" id="carouselContainer">
        <?php foreach ($books as $index => $book): ?>
        <div class="book-card">
            <div class="book-badges">
                <span class="book-badge badge-popular">Только для вас!</span>
            </div>
            <div class="book-image-container">
                <img src="<?= '/../../pics/' . htmlspecialchars($book['image_path']) ?>" 
                    alt="<?= htmlspecialchars($book['title']) ?>" 
                    class="book-image">
                <div class="quick-view" data-book-id="<?= $book['id'] ?>">
                    <i class="fas fa-eye"></i> Быстрый просмотр
                </div>
            </div>
            <div class="book-details">
                <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
                <p class="book-author">Автор: <?= htmlspecialchars($book['author']) ?></p>
                <div class="book-meta">
                    <span class="rating"><i class="fas fa-star"></i> <?= number_format($book['rating'], 1) ?></span>
                    <span class="text-muted">(<?= $book['reviews_count'] ?> отзывов)</span>
                </div>
                <div class="price-container">
                    <div>
                        <?php if ($book['old_price']): ?>
                            <span class="old-price"><?= number_format($book['old_price'], 0, '', ' ') ?> сомони</span>
                        <?php endif; ?>
                        <span class="book-price"><?= number_format($book['price'], 0, '', ' ') ?> сомони</span>
                    </div>
                    <button class="add-to-cart" data-book-id="<?= $book['id'] ?>">
                        <i class="fas fa-shopping-cart"></i> Купить
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination" id="carouselPagination">
        <!-- Индикаторы будут добавлены через JS -->
    </div>
</div>

<script src="catalog/books/books-carousel.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>