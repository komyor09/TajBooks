<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['book_id'])) {
    $book_id = (int)$_POST['book_id'];
    $stmt = $pdo->prepare("
        SELECT b.*, 
               a.name AS author_name, 
               l.name AS language_name, 
               p.name AS publisher_name, 
               f.name AS format_name
        FROM book b
        LEFT JOIN authors a ON b.author_id = a.id
        LEFT JOIN languages l ON b.language_id = l.id
        LEFT JOIN publishers p ON b.publisher_id = p.id
        LEFT JOIN formats f ON b.format_id = f.id
        WHERE b.id = :id
    ");
    $stmt->bindParam(':id', $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        echo "Книга не найдена.";
        exit;
    }
    $genre_stmt = $pdo->prepare("
        SELECT g.name 
        FROM genre g
        INNER JOIN book_genre bg ON g.id = bg.genre_id
        WHERE bg.book_id = :book_id
    ");
    $genre_stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $genre_stmt->execute();
    $genres = $genre_stmt->fetchAll(PDO::FETCH_COLUMN);
} else {
    echo "Некорректный запрос.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
$role = $_SESSION['role'] ?? '';
if ($role === 'admin') {
    require_once('../navbars/admin.php');
} else {
    require_once('../navbars/client.php');
}
?>

<!-- Сообщение при выходе -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success alert-custom text-center shadow" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="container py-5">
    <div class="card shadow-lg">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="../pics/<?= htmlspecialchars($book['image_path']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($book['title']) ?>">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h2 class="card-title"><?= htmlspecialchars($book['title']) ?></h2>
                    <p class="card-text"><strong>Автор:</strong> <?= htmlspecialchars($book['author_name']) ?></p>
                    <?php if (!empty($book['ISBN'])): ?>
                        <p class="card-text"><strong>ISBN:</strong> <?= htmlspecialchars($book['ISBN']) ?></p>
                    <?php endif; ?>
                    <p class="card-text"><strong>Количество страниц:</strong> <?= htmlspecialchars($book['page']) ?></p>
                    <?php if (!empty($book['year'])): ?>
                        <p class="card-text"><strong>Год издания:</strong> <?= htmlspecialchars($book['year']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($book['language_name'])): ?>
                        <p class="card-text"><strong>Язык:</strong> <?= htmlspecialchars($book['language_name']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($book['publisher_name'])): ?>
                        <p class="card-text"><strong>Издатель:</strong> <?= htmlspecialchars($book['publisher_name']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($book['format_name'])): ?>
                        <p class="card-text"><strong>Формат:</strong> <?= htmlspecialchars($book['format_name']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($genres)): ?>
                        <p class="card-text"><strong>Жанры:</strong> <?= htmlspecialchars(implode(', ', $genres)) ?></p>
                    <?php endif; ?>

                    <p class="card-text"><?= nl2br(htmlspecialchars($book['description'])) ?></p>

                    <?php if ($book['availability']): ?>
                        <h4 class="text-primary">Цена: <?= htmlspecialchars($book['price']) ?> TJS</h4>

                        <form action="../order/add_to_cart.php" method="POST">
                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                            <div class="d-flex align-items-center">
                                <div class="input-group w-25">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">-</button>
                                    <input type="number" name="quantity" id="quantity" class="form-control text-center" value="1" min="1" max="<?= $book['quantity'] ?>" style="width: 60px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">+</button>
                                </div>
                                <button type="submit" class="btn btn-primary ms-3">Добавить в корзину</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i> Товар временно недоступен для покупки.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
        <a href="../index.php" class="text-white d-flex align-items-center mb-3 mb-md-0">
            <img src="../pics/logo.jpg" alt="Logo" class="me-2" style="width: 50px;">
            <div>
                <span class="h5 d-block">TajBooks</span>
                <small>Read Learn Grow</small>
            </div>
        </a>

        <ul class="nav">
            <li class="nav-item"><a href="../faq.php" class="nav-link px-2 text-white">FAQ</a></li>
            <li class="nav-item"><a href="https://t.me/taj_books" class="nav-link px-2 text-white">Telegram</a></li>
            <li class="nav-item"><a href="https://instagram.com/taj.books" class="nav-link px-2 text-white">Instagram</a></li>
        </ul>
    </div>
    <p class="text-center mt-3">&copy; 2025 TajBooks. Все права защищены.</p>
</footer>

<script>
    function incrementQuantity() {
        let quantity = document.getElementById("quantity");
        let max = parseInt(quantity.getAttribute('max'));
        if (parseInt(quantity.value) < max) {
            quantity.value = parseInt(quantity.value) + 1;
        }
    }
    function decrementQuantity() {
        let quantity = document.getElementById("quantity");
        if (parseInt(quantity.value) > 1) {
            quantity.value = parseInt(quantity.value) - 1;
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
