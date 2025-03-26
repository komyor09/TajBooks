<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">

    <!-- Шапка -->
    <header class="bg-dark text-white py-3">
        <nav class="container d-flex justify-content-between align-items-center">
            <!-- Логотип сайта с именем -->
            <a href="../index.php" class="text-white d-flex align-items-center">
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

<div class="container mt-5">
    <h1 class="text-center mb-4">Ваша корзина</h1>
    <div class="row">
        <?php
        require_once '../order/cartClass.php';
        require_once '../config/db.php';
        session_start();

        $cart = new Cart($pdo, $_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['remove_book_id'])) {
                $book_id = intval($_POST['remove_book_id']);
                $cart->remove_from_cart($book_id);
            }
            if (isset($_POST['decrease_book_id'])) {
                $book_id = intval($_POST['decrease_book_id']);
                $cart->decrease_quantity($book_id);
            }
            if (isset($_POST['increase_book_id'])) {
                $book_id = intval($_POST['increase_book_id']);
                $cart->increase_quantity($book_id);
            }
        }

        $items = $cart->get_cart_items();
        $total = 0;
        $quantity = 0;
        if (count($items) > 0): ?>
            <?php foreach ($items as $item): ?>
                <div class="col-12 col-md-4">
                    <div class="card mb-4 shadow-lg">
                        <img src="<?php echo htmlspecialchars("../pics/" . $item['image_path']); ?>" class="card-img" alt="Book Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                            <p class="card-text">Цена: <?php echo htmlspecialchars($item['price']); ?> сомони</p>
                            <p class="card-text">Количество: <?php echo htmlspecialchars($item['quantity']); ?> шт.</p>

                            <!-- Управление количеством -->
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="decrease_book_id" value="<?php echo htmlspecialchars($item['book_id']); ?>">
                                <button type="submit" class="btn btn-warning btn-sm">-</button>
                            </form>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="increase_book_id" value="<?php echo htmlspecialchars($item['book_id']); ?>">
                                <button type="submit" class="btn btn-success btn-sm">+</button>
                            </form>

                            <!-- Удаление книги -->
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="remove_book_id" value="<?php echo htmlspecialchars($item['book_id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                    $total += $item['price'] * $item['quantity'];
                    $quantity += $item['quantity'];
                ?>
            <?php endforeach; ?>
            <div class="d-flex justify-content-between mt-4">
                <h4>Итого:</h4>
                <h4><?php echo htmlspecialchars($quantity); ?> шт. книг</h4>
                <h4><?php echo htmlspecialchars($total); ?> сомони</h4>
            </div>
            <div class="text-center mt-4">
                <a href="../order/checkout.php" class="btn btn-success">Перейти к оформлению заказа</a>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">Ваша корзина пуста.</p>
        <?php endif; ?>
    </div>
</div>

<p class="mt-5"></p>

<footer class="bg-dark text-white py-3">
    <nav class="container d-flex justify-content-between align-items-center">
        <a href="../index.php" class="text-white d-flex align-items-center">
            <img src="../pics/logo.jpg" alt="Logo" class="me-1" style="width: 50px;">
            <div class="row">
                <span class="h4 text-center">TajBooks</span>
                <span class="h6 text-center">Read Learn Grow</span>
            </div>
        </a>
        <ul class="nav ms-auto">
            <li class="nav-item ms-3">
                <a href="../faq.php" class="nav-link text-white">
                    <i class="fas fa-question me-2"></i>FAQ
                </a>
            </li>
            <li class="nav-item ms-3">
                <a href="https://t.me/" class="nav-link text-white">
                    <i class="fas fa-telegram me-2"></i>Телеграм    
                </a>
            </li>
            <li class="nav-item ms-3">
                <a href="https://instagram.com/" class="nav-link text-white">
                    <i class="fas fa-instagram me-2"></i>Инстаграм       
                </a>
            </li>
        </ul>
    </nav>
    <p class="text-center mb-4"></p>
    <p class="text-center mb-2 py-2">&copy; 2025 TajBooks. Все права защищены.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
