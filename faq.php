<?php
session_start();
require_once 'config/db.php'; // Подключаем БД

// Если форма отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['question'])) {
    $question = trim($_POST['question']);
    if (!empty($_SESSION['user_id'])) {
        if (!empty($question)) {
            $user_id = $_SESSION['user_id'];
            $stmt = $pdo->prepare("INSERT INTO faq (question, asked_user_id) VALUES (:question, :asked_user_id)");
            // Execute once with all parameters
            $stmt->execute([
                'question' => $question,
                'asked_user_id' => $user_id
            ]);
            
            $success = "Ваш вопрос отправлен! Мы ответим на него в ближайшее время.";
        } else {
            $error = "Введите корректный вопрос!";
        }
    } else {
        $error = "Сначала войдите!";
    }
}

// Получаем 5 частозадаваемых вопросов
$query = $pdo->query("SELECT * FROM faq ORDER BY viewed DESC LIMIT 5");
$faqs = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TajBooks - FAQ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/faq.css">
</head>
<body>
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
        <h2 class="text-center mb-4">FAQ - Часто задаваемые вопросы</h2>
        
        <ul class="list-unstyled">
            <?php foreach ($faqs as $faq): ?>
                <li class="faq-item d-flex container py-4 bg-light rounded shadow mb-3">
                    <h3 class="fas fa-plus py-2 toggle-icon"></h3>
                    <div class="px-3">
                        <p class="fw-bold mb-0 question"><?= htmlspecialchars($faq['question']) ?></p>
                        <?php if (!empty($faq['answer'])): ?>
                            <p class="answer text-muted"><?= nl2br(htmlspecialchars($faq['answer'])) ?></p>
                        <?php else: ?>
                            <p class="answer text-muted">Ответ скоро появится.</p>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
<p></p>
        <!-- Форма для отправки вопроса
        <div class="mt-5">
            <h4 class="text-center mb-3">Не нашли ответ? Задайте свой вопрос!</h4>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="faq.php" class="text-center">
                <div class="mb-3">
                    <textarea name="question" class="form-control" rows="3" placeholder="Введите ваш вопрос..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div> -->
    </div>
<p></p>
<footer class="bg-dark text-white py-3">
        <nav class="container d-flex justify-content-between align-items-center">
            <!-- Логотип сайта с именем -->
            <a href="../index.php" class="text-white d-flex align-items-center">
                <img src="pics/logo.jpg" alt="Logo" class="me-1" style="width: 50px;">
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
    <script>
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', function () {
                let answer = this.querySelector('.answer');
                let icon = this.querySelector('.toggle-icon');

                if (answer.classList.contains('show')) {
                    answer.classList.remove('show');
                    icon.classList.replace('fa-minus', 'fa-plus');
                } else {
                    document.querySelectorAll('.answer').forEach(a => a.classList.remove('show'));
                    document.querySelectorAll('.toggle-icon').forEach(i => i.classList.replace('fa-minus', 'fa-plus'));

                    answer.classList.add('show');
                    icon.classList.replace('fa-plus', 'fa-minus');
                }
            });
        });
    </script>
</body>
</html>
