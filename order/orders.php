<?php
session_start();
include('../config/db.php');  // Подключаем файл с настройками БД

// Проверяем, есть ли данные сессии для пользователя
if (!isset($_SESSION['user_id'])) {
    // Если пользователь не авторизован
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];  // Идентификатор пользователя из сессии

// Проверяем, был ли отправлен POST-запрос с id заказа
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    // Сохраняем id заказа в сессию
    $_SESSION['order_id'] = $_POST['order_id'];

    // Перенаправляем пользователя на страницу с деталями заказа
    header('Location: order-details.php');
    exit();
}

// Запрос для получения всех заказов пользователя
$sql = "SELECT o.id, o.createdAt AS order_date, o.status, o.price AS total_amount
        FROM Orders o
        WHERE o.user_id = :user_id
        ORDER BY o.createdAt DESC";  // Сортировка по дате заказа

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Закрытие соединения с базой данных
$pdo = null;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы</title>
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
        <h1>Мои заказы</h1>

        <!-- Выводим список заказов -->
        <?php if (empty($orders)): ?>
            <p>У вас нет заказов.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Номер заказа</th>
                        <th>Дата заказа</th>
                        <th>Статус</th>
                        <th>Сумма</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <form action="" method="POST">
                                <td><?= htmlspecialchars($order['id']) ?></td> <!-- Используем id заказа -->
                                <td><?= htmlspecialchars($order['order_date']) ?></td>
                                <td>
                                    <span class="status <?= strtolower($order['status']) ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($order['total_amount']) ?> рублей</td>
                                <td>
                                    <!-- Добавляем скрытое поле с id заказа -->
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                                    <button type="submit" class="btn btn-primary">
                                        Подробнее
                                    </button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        <?php endif; ?>

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

    <style>
        .order {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .status {
            font-weight: bold;
        }
        .status.pending { color: orange; }
        .status.completed { color: green; }
        .status.cancelled { color: red; }
    </style>
</body>
</html>
