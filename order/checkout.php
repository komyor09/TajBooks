<?php
session_start();
require_once '../order/cartClass.php';
require_once '../config/db.php';  // Здесь вы уже используете PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    if (empty($user_id)) {
        echo "Вы не авторизованы!";
        exit;
    }

    $name = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $phone = htmlspecialchars($_POST['phone']);

    if (!empty($name) && !empty($address) && !empty($phone)) {
        // Получаем товары из корзины
        $cart = new Cart($pdo, $user_id);
        $items = $cart->get_cart_items();
        // print_r($items);    
        // Считаем общую стоимость
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        // echo $total;
        try {
            $sql = "INSERT INTO Orders (user_id, status, price) VALUES (:user_id, 'new', :price)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':price', $total, PDO::PARAM_STR); // Используем строковый тип для цены
            $stmt->execute();

            $order_id = $pdo->lastInsertId();

            foreach ($items as $item) {
                $sql = "INSERT INTO Order_Items (order_id, book_id, quantity, price) VALUES (:order_id, :book_id, :quantity, :price)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                $stmt->bindParam(':book_id', $item['id'], PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $stmt->bindParam(':price', $item['price'], PDO::PARAM_STR);
                $stmt->execute();
            }

            $cart->clear_cart();

            echo "<div class='alert alert-success text-center'>Ваш заказ успешно оформлен!</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger text-center'>Ошибка оформления заказа: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger text-center'>Пожалуйста, заполните все поля!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f6fefc;
        }
        header, footer {
            background-color: #a8d5ba;
        }
        .btn-primary {
            background-color: #5ca982;
            border: none;
        }
        .btn-primary:hover {
            background-color: #4c8d6d;
        }
    </style>
</head>
<body>
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


    <main class="container py-5">
        <h1 class="text-center mb-4">Оформление заказа</h1>
        <form action="" method="POST" class="mx-auto w-50 border p-4 rounded shadow">
            <div class="mb-3">
                <label for="name" class="form-label">Имя:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Адрес:</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон:</label>
                <input type="tel" id="phone" name="phone" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Подтвердить заказ</button>
        </form>
    </main>

    <!-- Подвал -->
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
                    <a href="../faq.php" class="nav-link text-white">
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
                        <i class="fas fa-telegram me-2"></i>Инстаграм    
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
</body>
</html>
