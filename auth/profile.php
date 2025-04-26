<?php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header('Location: ../auth/login.php');
    exit();
}

$userName = htmlspecialchars($_SESSION['name'] ?? 'Пользователь');
$userId = $_SESSION['user_id'] ?? null;

// Пример для тестирования (сохраните сообщение и тип на 3 секунды)
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = "Добро пожаловать, $userName!";
    $_SESSION['message_type'] = 'success'; // Тип сообщения: 'success', 'warning', 'error'
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="TajBooks, книги, личный кабинет, настройки, профиль">
    <meta name="author" content="TajBooks">
    <meta name="description" content="Личный кабинет пользователя TajBooks">
    <title>Личный кабинет | TajBooks</title>

    <!-- Стили -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
            --success-color: #28a745;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .navbar-custom {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: none;
            transition: transform 0.3s;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header-custom {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .btn-primary-custom {
            background-color: var(--secondary-color);
            border: none;
            transition: all 0.3s;
        }
        
        .btn-primary-custom:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .btn-success-custom {
            background-color: var(--success-color);
            border: none;
        }
        
        .quick-action-card {
            border-radius: 8px;
            transition: all 0.3s;
            height: 100%;
        }
        
        .quick-action-card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .logout-btn {
            background-color: var(--accent-color);
            color: white;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        
        footer {
            background-color: var(--dark-bg);
            color: white;
            margin-top: auto;
        }
        
        .avatar-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .social-icon {
            color: white;
            transition: all 0.3s;
            margin: 0 10px;
        }
        
        .social-icon:hover {
            color: var(--secondary-color);
            transform: scale(1.2);
        }
    </style>
</head>
<body>

<!-- Навигационная панель -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <img src="../pics/logo.jpg" alt="TajBooks" height="40" class="me-2 rounded-circle">
            <span class="fw-bold">TajBooks</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../order/cart.php"><i class="fas fa-shopping-cart me-1"></i> Корзина</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i> Профиль
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item active" href="../auth/profile.php"><i class="fas fa-user-circle me-2"></i>Мой профиль</a></li>
                        <li><a class="dropdown-item" href="../order/orders.php"><i class="fas fa-box-open me-2"></i>Мои заказы</a></li>
                        <li><a class="dropdown-item" href="../auth/settings.php"><i class="fas fa-cog me-2"></i>Настройки</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Выйти</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Основное содержимое -->
<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Приветствие -->
            <div class="card profile-card mb-4">
                <div class="card-header card-header-custom">
                    <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>Личный кабинет</h4>
                </div>
                <div class="card-body text-center py-4">
                    <img src="../pics/default-avatar.jpg" alt="Аватар" class="avatar-img rounded-circle mb-3">
                    <h3>Добро пожаловать, <?= $userName ?>!</h3>
                    <p class="text-muted mb-4">
                        <i class="fas fa-calendar-alt me-2"></i>Участник с <?= date('d.m.Y', strtotime($_SESSION['created_at'] ?? 'now')) ?>
                    </p>
                    </div>
            </div>

            <!-- Быстрые действия -->
            <div class="card profile-card mb-4">
                <div class="card-header card-header-custom">
                    <h4 class="mb-0"><i class="fas fa-bolt me-2"></i>Быстрые действия</h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card quick-action-card h-100 border-0 shadow-sm">
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-history fa-3x mb-3 text-primary"></i>
                                    <h5>История заказов</h5>
                                    <p class="text-muted">Просмотрите ваши предыдущие заказы</p>
                                    <a href="../order/orders.php" class="btn btn-outline-primary">Перейти</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card quick-action-card h-100 border-0 shadow-sm">
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-heart fa-3x mb-3 text-danger"></i>
                                    <h5>Избранное</h5>
                                    <p class="text-muted">Ваши сохраненные книги</p>
                                    <a href="../catalog/favorites.php" class="btn btn-outline-danger">Перейти</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card quick-action-card h-100 border-0 shadow-sm">
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-book fa-3x mb-3 text-success"></i>
                                    <h5>Каталог книг</h5>
                                    <p class="text-muted">Найдите свою следующую книгу</p>
                                    <a href="../catalog/catalog.php" class="btn btn-outline-success">Перейти</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card quick-action-card h-100 border-0 shadow-sm">
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-edit fa-3x mb-3 text-info"></i>
                                    <h5>Редактировать профиль</h5>
                                    <p class="text-muted">Обновите ваши данные</p>
                                    <a href="../auth/settings.php" class="btn btn-outline-info">Перейти</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Кнопка выхода -->
            <div class="text-center mt-4">
                <a href="../auth/logout.php" class="btn logout-btn px-4 py-2">
                    <i class="fas fa-sign-out-alt me-2"></i> Выйти из аккаунта
                </a>
            </div>
        </div>
    </div>
</main>

<!-- Модальное окно для смены пароля -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="../auth/change_password.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-key me-2"></i>Изменить пароль</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="old_password" class="form-label">Текущий пароль</label>
          <input type="password" class="form-control" name="old_password" required>
        </div>
        <div class="mb-3">
          <label for="new_password" class="form-label">Новый пароль</label>
          <input type="password" class="form-control" name="new_password" required minlength="8">
          <small class="text-muted">Минимум 8 символов</small>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Подтвердите пароль</label>
          <input type="password" class="form-control" name="confirm_password" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
        <button type="submit" class="btn btn-success-custom">Сохранить изменения</button>
      </div>
    </form>
  </div>
</div>

<!-- Подвал -->
<footer class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">TajBooks</h5>
                <p>Лучшие книги для вашего развития и удовольствия.</p>
                <div class="mt-3">
                    <a href="#" class="social-icon"><i class="fab fa-telegram fa-lg"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-facebook fa-lg"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4">
                <h5 class="mb-3">Меню</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="../index.php" class="nav-link p-0 text-white">Главная</a></li>
                    <li class="nav-item mb-2"><a href="../catalog/catalog.php" class="nav-link p-0 text-white">Каталог</a></li>
                    <li class="nav-item mb-2"><a href="../order/cart.php" class="nav-link p-0 text-white">Корзина</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">Контакты</h5>
                <ul class="nav flex-column">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> г. Худжанд, ул. И. Сомони 256</li>
                    <li class="mb-2"><i class="fas fa-phone me-2"></i> +992 91 139 06 12</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@tajbooks.tj</li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">О нас</h5>
                <p>Мы предлагаем широкий выбор книг на любой вкус. Читайте, учитесь, растите вместе с TajBooks.</p>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center">
            <p class="mb-0">&copy; 2025 TajBooks. Все права защищены.</p>
        </div>
    </div>
</footer>

<!-- Скрипты -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($_SESSION['message'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Создаем toast уведомление
            const toastHtml = `
                <div class="position-fixed top-20 end-0 p-3" style="z-index: 11">
                    <div class="toast show align-items-center text-white bg-<?= $_SESSION['message_type'] === 'success' ? 'success' : $_SESSION['message_type'] === 'warning' ? 'warning' : 'danger' ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : $_SESSION['message_type'] === 'warning' ? 'exclamation-triangle' : 'times-circle' ?> me-2"></i>
                                <?= addslashes($_SESSION['message']) ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', toastHtml);
            
            // Автоматическое закрытие через 5 секунд
            setTimeout(() => {
                const toast = document.querySelector('.toast');
                if (toast) {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 5000);
        });
    </script>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

</body>
</html>