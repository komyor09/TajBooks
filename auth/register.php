<?php
// Подключаем файл для работы с базой данных
require_once '../config/db.php';

// Переменные для сообщений
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty(trim($_POST['reg_email']))) {
        $_POST['email'] = $_POST['reg_email'];
    }
};
// Проверка, если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Получаем данные из формы и убираем пробелы
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // Проверка на пустые значения
    if (empty($username) || empty($password) || empty($email)) {
        $message = "Все поля должны быть заполнены!";
        $message_type = "error";
    }
    // Проверка корректности email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Неверный формат email!";
        $message_type = "error";
    }
    else {
        // Хешируем пароль с использованием password_hash
        $hashed_password = md5($password);
        // Проверяем, есть ли уже такой пользователь
        $query = "SELECT * FROM users WHERE username = :username OR email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['username' => $username, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            $message = "Пользователь с таким именем или email уже существует!";
            $message_type = "error";  // Тип ошибки
        }
        else {
            // Вставляем нового пользователя в базу данных
            $query = "INSERT INTO users (username, password, email, role, createdAt) 
                    VALUES (:username, :password, :email, 'user', CURRENT_TIMESTAMP)";
            $stmt = $pdo->prepare($query);
            // Добавляем параметры для всех переменных в запросе
            $stmt->execute([
                'username' => $username,
                'password' => $hashed_password,
                'email' => $email
            ]);
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $message = "Вы успешно зарегистрировались!";
            $message_type = "success";  // Тип успеха
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta username="viewport" content="width=device-width, initial-scale=1.0">
    <title>TajBooks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">

    <style>
        /* Стили для контейнера с сообщением */
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

        /* Стили для формы */
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
                            <i class="fas fa-sign-in-alt me-2"></i>Войти
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

        <!-- Форма регистрации -->
        <form action="register.php" method="POST">
            <div class="signin">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" 
                        value="<?php echo isset($_POST['email']) ? explode('@', $_POST['email'])[0] : ''; ?>" 
                        required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                        value="<?= isset($_POST['email']) ? $_POST['email'] : ''; ?>" 
                        required>

                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn-primary px-4 py-2 rounded-pill">Зарегистрироваться</button>
            </div>
        </form>

    <p></p>

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
</body>
</html>
