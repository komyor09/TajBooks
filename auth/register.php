<?php
session_start();
require_once '../config/db.php';

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Вы уже авторизованы!";
    $_SESSION['message_type'] = "warning";
    header("Location: profile.php");
    exit();
}

// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    if (empty($username) || empty($password) || empty($email)) {
        $_SESSION['message'] = "Все поля должны быть заполнены!";
        $_SESSION['message_type'] = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Неверный формат email!";
        $_SESSION['message_type'] = "danger";
    } else {
        $hashed_password = md5($password);
        
        try {
            $check_query = "SELECT id FROM users WHERE username = :username OR email = :email";
            $stmt = $pdo->prepare($check_query);
            $stmt->execute(['username' => $username, 'email' => $email]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "Пользователь с таким именем или email уже существует!";
                $_SESSION['message_type'] = "danger";
            } else {
                $insert_query = "INSERT INTO users (username, password, email, role, createdAt) 
                                VALUES (:username, :password, :email, 'user', CURRENT_TIMESTAMP)";
                $stmt = $pdo->prepare($insert_query);
                $stmt->execute([
                    'username' => $username,
                    'password' => $hashed_password,
                    'email' => $email
                ]);
                
                $user_id = $pdo->lastInsertId();
                
                $_SESSION['user_id'] = $user_id;
                $_SESSION['name'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'user';
                
                $_SESSION['message'] = "Регистрация прошла успешно! Добро пожаловать, $username!";
                $_SESSION['message_type'] = "success";
                
                header("Location: profile.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Ошибка при регистрации: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }
    }
    
    header("Location: register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | TajBooks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .registration-form {
            max-width: 500px;
            margin: 30px auto;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-footer {
            margin-top: 20px;
            text-align: center;
        }
        .alert-custom {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            min-width: 300px;
            animation: fadeIn 0.5s, fadeOut 0.5s 2.5s forwards;
        }
        @keyframes fadeIn {
            from {opacity: 0; top: 0;}
            to {opacity: 1; top: 20px;}
        }
        @keyframes fadeOut {
            from {opacity: 1; top: 20px;}
            to {opacity: 0; top: 0;}
        }
        .login-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.7);
            z-index: 1050;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-prompt {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
    </style>
</head>
<body>
<?php
    $role = $_SESSION['role'] ?? '';
    if ($role === 'admin') {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/admin.php');
    } else {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/client.php');
    }
    require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/iphone-notification.php');
?>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show alert-custom" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>
                <?php if ($_SESSION['message_type'] == 'success'): ?>
                    <i class="fas fa-check-circle me-2"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle me-2"></i>
                <?php endif; ?>
            </strong>
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>

    <main class="container">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="registration-form">
                <h2 class="text-center mb-4">Регистрация</h2>
                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Имя пользователя</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Зарегистрироваться</button>
                </form>
                <div class="form-footer">
                    Уже есть аккаунт? <a href="login.php">Войдите</a>
                </div>
            </div>
        <?php else: ?>
            <div class="login-overlay">
                <div class="login-prompt">
                    <h3>Вы уже авторизованы</h3>
                    <p>Вы не можете зарегистрироваться, так как уже вошли в систему как <?= htmlspecialchars($_SESSION['name']) ?></p>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="profile.php" class="btn btn-primary">Перейти в профиль</a>
                        <a href="logout.php" class="btn btn-outline-secondary">Выйти</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white py-4 mt-5">
        <!-- ... (как в предыдущем коде) ... -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 3000);
            });
        });
    </script>
</body>
</html>