<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php'); // Если не авторизован, перенаправляем на страницу входа
    exit();
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
</head>
<style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .main-container {
            max-width: 800px;
            margin: 100px auto;
        }
        .card-custom {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #28a745;
            color: white;
            font-size: 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        .btn-custom {
            background-color: #28a745;
            color: white;
            border-radius: 12px;
            text-decoration: none;
            padding: 10px 20px;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
        .profile-info {
            font-size: 1.2rem;
            margin-top: 20px;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border-radius: 12px;
            padding: 10px 20px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            display: inline-block;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
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


    <!-- Центральная часть -->
    <div class="container main-container">
        <!-- Карточка с приветствием -->
        <div class="card card-custom mb-4">
            <div class="card-header bg-dark">
                Привет, <strong><?php echo $_SESSION['name']; ?></strong>!
            </div>
            <div class="card-body">
                <p class="profile-info">Добро пожаловать в ваш личный кабинет! Здесь вы можете управлять своим аккаунтом, просматривать заказы и редактировать профиль.</p>
                <a href="../order/orders.php" class="btn btn-primary"><i class="fas fa-box-open me-2"></i> Мои заказы</a>
                <a href="../auth/settings.php" class="btn btn-primary"><i class="fas fa-cogs me-2"></i> Настройки профиля</a>
            </div>
        </div>

        <!-- Кнопка выхода -->
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt me-2"></i> Выйти
        </a>
    </div>

    <!-- Подключаем Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
</body>
</html>
