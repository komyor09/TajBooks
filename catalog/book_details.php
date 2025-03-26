<?php
session_start();
require_once '../config/db.php'; // Подключение к БД

// Получаем ID книги из URL
if (isset($_POST['book_id'])) {
    $book_id = (int)$_POST['book_id'];
    $stmt = $pdo->prepare("SELECT * FROM Books WHERE id = :id");
    $stmt->bindParam(':id', $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$book) {
        echo "Книга не найдена";
        exit;
    }
} else {
    echo "Некорректный запрос";
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="bg-dark text-white py-3">
        <nav class="container d-flex justify-content-between align-items-center">
            <!-- Логотип сайта с именем -->
            <div class="text-center mt-4">
                <a href="../catalog/catalog.php?page=<?= $_SESSION['catalog_page'] ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i></a>
            </div>
            <a href="../index.php" class="text-white d-flex align-items-center mx-5">
                <img src="../pics/logo.jpg" alt="Logo" class="me-1 text-center" style="width: 50px;">
                <div class="row">
                <span class="h4 text-center">TajBooks</span>
                <span class="h6 text-center">Read Learn Grow</span>
                </div>
            </a>

            <!-- Меню с иконками -->
            <ul class="nav ms-auto">
                <li class="nav-item ms-3">
                    <a href="../catalog/catalog.php" class="nav-link text-white">
                        <i class="fas fa-book me-2"></i>Каталог
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a href="../order/cart.php" class="nav-link text-white">
                        <i class="fas fa-shopping-cart me-2"></i>Корзина
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a href="../auth/profile.php" class="nav-link text-white">
                        <i class="fas fa-user me-2"></i>Личный кабинет
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="../auth/logout.php" class="nav-link text-white">
                            <i class="fas fa-sign-out-alt me-2"></i>Выйти
                        </a>
                    <?php else: ?>
                        <a href="../auth/login.php" class="nav-link text-white">
                            <i class="fas fa-sign-in-alt me-2"></i>Войти / Регистрация
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    </header>
        <!-- Сообщение при выходе -->
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-custom text-center shadow" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['message']; ?>
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
                    <p class="card-text"><strong>Автор:</strong> <?= htmlspecialchars($book['author']) ?></p>
                    <p class="card-text"><?= htmlspecialchars($book['description']) ?></p>
                    <h4 class="text-primary">Цена: <?= htmlspecialchars($book['price']) ?> TJS</h4>
                    <form action="../order/add_to_cart.php" method="POST">
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <div class="d-flex align-items-center">
                            <div class="input-group w-25">
                                <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">-</button>
                                <input type="number" name="quantity" id="quantity" class="form-control text-center" value="1" min="1" max="100" style="width: 60px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">+</button>
                            </div>
                            <button type="submit" class="btn btn-primary ms-3">Добавить в корзину</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white py-3">
        <nav class="container d-flex justify-content-between align-items-center">
            <!-- Логотип сайта с именем -->
            <a href="../index.php" class="text-white d-flex align-items-center">
                <img src="../pics/logo.jpg" alt="Logo" class="me-1" style="width: 50px;">
                <div class="row">
                <span class="h4 text-center">TajBooks</span>
                <span class="h6 text-center">Read Learn Grow</span>
                </div>
            </a>

            <!-- Меню с иконками -->
            <ul class="nav ms-auto">
                <li class="nav-item ms-3">
                    <a href="faq.php" class="nav-link text-white">
                        <i class="fas fa-question me-2"></i>FAQ
                    </a>
                    <ul>
                        <li><a href="../faq.php/#q1" class="nav-link text-white">Question 1</a></li>
                        <li><a href="../faq.php/#q2" class="nav-link text-white">Question 2</a></li>
                        <li><a href="../faq.php/#q3" class="nav-link text-white">Question 3</a></li>
                    </ul>
                </li>
                <li class="nav-item ms-3">
                <a href="https://t.me/" class="nav-link text-white">
                        <i class="fas fa-telegram me-2"></i>Телеграм    
                    </a>
                    <ul>
                        <li><a href="https://t.me/taj_books" class="nav-link text-white">Канал</a></li>
                        <li><a href="https://t.me/komyor_06" class="nav-link text-white">Аккаунт для заказа</a></li>
                    </ul>
                </li>
                <li class="nav-item ms-3">
                <a href="https://instagram.com/" class="nav-link text-white">
                <i class="fas fa-instagram me-2"></i>Инстаграм       
                    </a>
                    <ul>
                        <li><a href="https://instagram.com/taj.books/" class="nav-link text-white">Публикации</a></li>
                        <li><a href="https://instagram.com/" class="nav-link text-white">Аккаунт для заказа</a></li>
                    </ul>
                </li>
        </nav>
        <p class="text-center mb-4"></p>
        <p class="text-center mb-2 py-2">&copy; 2025 TajBooks. Все права защищены.</p>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function incrementQuantity() {
        let quantity = document.getElementById("quantity");
        if (parseInt(quantity.value) < 100) {
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
