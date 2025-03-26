<?php
// Подключаем файл для работы с базой данных
require_once '../config/db.php';
session_start();  // Start the session to access the logged-in user

// Проверка, если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];  // Get the logged-in user's ID from the session
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Хешируем новый пароль (если он был введен)
    if (!empty($password)) {
        $hashed_password = md5($password);
    } else {
        // Если пароль не изменен, оставляем старый (можно сделать запрос на старый пароль)
        $hashed_password = null;
    }

    // Подготовка запроса для обновления данных пользователя
    if ($hashed_password !== null) {
        // Если пароль был изменен
        $stmt = $pdo->prepare("UPDATE users SET name = :username, password = :password WHERE id = :user_id");
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    } else {
        // Если пароль не изменен
        $stmt = $pdo->prepare("UPDATE users SET name = :username WHERE id = :user_id");
    }
    
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Если обновление прошло успешно
        $message = "Данные успешно обновлены!";
        $message_type = "success";
        $_SESSION['name'] = $username;
    } else {
        // Если возникла ошибка при обновлении
        $message = "Ошибка при обновлении данных.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки профиля</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
</head>
<style>
    .message {
    padding: 15px;
    margin: 20px;
    border-radius: 5px;
    font-size: 1.2rem;
    text-align: center;
}

.message.success {
    background-color: #4CAF50;
    color: white;
}

.message.error {
    background-color: #f44336;
    color: white;
}
    .signin {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 15px;
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

    <main class="mt-5 mb-5 mx-5 card">
        <h1 class="text-center mt-4 mb-4">Настройки профиля</h1>

        <!-- Вывод сообщения об ошибке или успехе -->
        <?php if (!empty($message)): ?>
            <div class="message <?= $message_type ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form action="#" method="POST">
            <div class="signin">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" placeholder="Имя пользователя" required>
                
                <label for="password">Новый пароль:</label>
                <input type="password" id="password" name="password" placeholder="Пароль">
                <button type="submit" class="btn-primary px-4 py-2 rounded-pill">Сохранить</button>
            </div>
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

    <script>
        function toggleTheme(theme) {
            document.body.setAttribute('data-theme', theme);
        }
    </script>
</body>
</html>
