<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/config/db.php');  // Подключаем файл с настройками БД

// Проверяем, есть ли данные сессии для пользователя
if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_id'])) {
    // Если пользователь не авторизован или нет номера заказа
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];  // Идентификатор пользователя из сессии
$order_id = $_SESSION['order_id'];  // Номер заказа из сессии

// Запрос для получения данных о заказе с добавлением пути к изображению
$sql = "SELECT o.createdAt AS order_date, o.status, o.price * oi.quantity AS total_amount, o.delivery_address,
                oi.book_id, b.title AS book_title, b.price AS book_price, oi.quantity, b.image_path
        FROM Orders o
        JOIN Order_Items oi ON o.id = oi.order_id
        JOIN books b ON oi.book_id = b.id
        WHERE o.user_id = :user_id AND o.id = :order_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id, 'order_id' => $order_id]);

$order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Проверка на наличие данных
if (empty($order_details)) {
    echo "Заказ не найден.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детали заказа</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
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


    <main class="mt-5 mb-5 mx-5">
        <!-- Отображение данных на странице -->
        <h1>Детали заказа №<?= htmlspecialchars($order_id) ?></h1>

        <div class="order-info">
            <p><strong>Дата заказа:</strong> <?= htmlspecialchars($order_info['order_date']) ?></p>
            <p><strong>Статус:</strong> <span class="status <?= strtolower($order_info['status']) ?>"><?= htmlspecialchars($order_info['status']) ?></s></p>
            <p><strong>Сумма заказа:</strong> <?= htmlspecialchars($order_info['total_amount']) ?> рублей</p>
            <p><strong>Адрес доставки:</strong> <?= htmlspecialchars($order_info['delivery_address']) ?></p>
        </div>

        <h2>Состав заказа</h2>
        <div class="order-items">
            <?php foreach ($order_details as $item): ?>
                <div class="order-item">
                    <?php
                        if ($item['image_path']) {
                            $image_url = "../pics/" . $item['image_path']; // Путь к изображению
                        } else {
                            $image_url = "../pics/default.jpg"; // Путь к изображению по умолчанию
                        }?>
                <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($item['book_title']) ?>">                    <p><?= htmlspecialchars($item['book_title']) ?></p>
                    <p>Цена: <?= htmlspecialchars($item['book_price']) ?> рублей</p>
                    <p>Количество: <?= htmlspecialchars($item['quantity']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($order_info['status'] !== 'cancelled') { ?>
            <button class="btn cancel-order" onclick="cancelOrder()">Отменить заказ</button>
        <?php } else { ?>
            <p>Ваш заказ был отменен.</p>
        <?php } ?>
    <script>
    function cancelOrder() {
        if (confirm('Вы уверены, что хотите отменить заказ?')) {
            // Отправка данных через форму
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'cancel_order.php'; // Страница, где будет обрабатываться отмена
            var orderIdInput = document.createElement('input');
            orderIdInput.type = 'hidden';
            orderIdInput.name = 'order_id';  // Имя переменной для передачи
            orderIdInput.value = '<?= htmlspecialchars($order_id) ?>';  // Значение ID заказа
            
            form.appendChild(orderIdInput);
            document.body.appendChild(form);
            form.submit();  // Отправляем форму
        }
    }
    </script>


        <?php
        // Закрытие соединения с базой данных
        $pdo = null;
        ?>
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

    <style>
        .order-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .order-items {
            display: flex;
            gap: 15px;
        }
        .order-item {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background: #fff;
            text-align: center;
        }
        .order-item img {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        .btn.cancel-order {
            background: red;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .status.pending { color: orange; font-weight: bold; }
    </style>
</body>
</html>
