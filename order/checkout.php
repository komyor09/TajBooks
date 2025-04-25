<?php
session_start();
require_once '../order/cartClass.php';
require_once '../config/db.php';

$order_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;
    if (empty($user_id)) {
        $_SESSION['message'] = "Вы не авторизованы! Пожалуйста, войдите в систему.";
        header("Location: ../auth/login.php");
        exit;
    }

    $name = htmlspecialchars(trim($_POST['name']));
    $address = htmlspecialchars(trim($_POST['address']));
    $phone = htmlspecialchars(trim($_POST['phone']));

    if (empty($name) || empty($address) || empty($phone)) {
        $_SESSION['message'] = "Пожалуйста, заполните все обязательные поля!";
        header("Location: checkout.php");
        exit;
    }


    if (!preg_match('/^\+?[0-9\s\-\(\)]{10,20}$/', $phone)) {
        $_SESSION['message'] = "Пожалуйста, введите корректный номер телефона";
        header("Location: checkout.php");
        exit;
    }

    try {
        $cart = new Cart($pdo, $user_id);
        $items = $cart->get_cart_items();
        
        if (empty($items)) {
            $_SESSION['message'] = "Ваша корзина пуста!";
            header("Location: ../order/cart.php");
            exit;
        }


        $total = array_reduce($items, function($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        // Начинаем транзакцию
        $pdo->beginTransaction();

        // Создаем заказ
        $sql = "INSERT INTO Orders (user_id, status, price, customer_name, delivery_address, customer_phone) 
                VALUES (:user_id, 'НОВЫЙ', :price, :name, :address, :phone)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':price' => $total,
            ':name' => $name,
            ':address' => $address,
            ':phone' => $phone
        ]);

        $order_id = $pdo->lastInsertId();

        // Добавляем товары заказа
        foreach ($items as $item) {
            $sql = "INSERT INTO order_items (order_id, book_id, quantity, unit_price) 
                    VALUES (:order_id, :book_id, :quantity, :price)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':order_id' => $order_id,
                ':book_id' => $item['id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
        }

        $cart->clear_cart();
        $pdo->commit();

        $order_success = true;
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $order_id;
        $_SESSION['message'] = "Ваш заказ #$order_id успешно оформлен! Мы свяжемся с вами для подтверждения.";

        // Отправка уведомления (можно реализовать через email или телеграм бота)
        // sendOrderNotification($order_id, $name, $phone, $total);

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Ошибка оформления заказа: " . $e->getMessage();
        header("Location: checkout.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа | TajBooks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --accent-color: #e74c3c;
        --light-bg: #f8f9fa;
        --dark-bg: #343a40;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-custom {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        .checkout-card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: none;
            transition: transform 0.3s;
        }

        .checkout-card:hover {
            transform: translateY(-5px);
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .success-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-left: 5px solid var(--secondary-color);
        }

        footer {
            background-color: var(--dark-bg);
            color: white;
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

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #dee2e6;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
            font-weight: bold;
            color: #6c757d;
        }

        .step.active .step-number {
            background-color: var(--secondary-color);
            color: white;
        }

        .step.completed .step-number {
            background-color: #28a745;
            color: white;
        }

        .step-title {
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }

        .step.active .step-title {
            color: var(--secondary-color);
            font-weight: bold;
        }

        .step.completed .step-title {
            color: #28a745;
        }

        .progress-line {
            position: absolute;
            height: 4px;
            background-color: #dee2e6;
            top: 20px;
            left: 0;
            right: 0;
            z-index: 1;
        }
    .progress {
        height: 100%;
        background-color: var(--secondary-color);
        transition: width 0.3s;
        width: <?= $order_success ? '100%' : '50%' ?>;
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
                    <a class="nav-link" href="../catalog/catalog.php"><i class="fas fa-book me-1"></i> Каталог</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../order/cart.php"><i class="fas fa-shopping-cart me-1"></i> Корзина</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../auth/profile.php"><i class="fas fa-user me-1"></i> Личный кабинет</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Основное содержимое -->
<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="text-center mb-4 fw-bold">Оформление заказа</h1>
            
            <!-- Индикатор шагов -->
            <div class="step-indicator mb-5">
                <div class="progress-line">
                    <div class="progress"></div>
                </div>
                <div class="step <?= $order_success ? 'completed' : 'active' ?>">
                    <div class="step-number">1</div>
                    <div class="step-title">Данные покупателя</div>
                </div>
                <div class="step <?= $order_success ? 'active' : '' ?>">
                    <div class="step-number">2</div>
                    <div class="step-title">Подтверждение</div>
                </div>
            </div>
            
            <!-- Сообщения -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= isset($_SESSION['order_success']) && $_SESSION['order_success'] ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                unset($_SESSION['message']);
                unset($_SESSION['order_success']);
                ?>
            <?php endif; ?>
            
            <?php if (!$order_success): ?>
                <!-- Форма оформления заказа -->
                <div class="card checkout-card mb-4">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4"><i class="fas fa-user-circle me-2"></i>Контактная информация</h3>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">ФИО <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                        value="<?= $_SESSION['user_name'] ?? '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Адрес доставки <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" required>
                                <small class="text-muted">Укажите полный адрес с индексом</small>
                            </div>
                            <div class="mb-4">
                                <label for="phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required
                                        value="<?= $_SESSION['user_phone'] ?? '' ?>">
                                <small class="text-muted">Формат: +992 XX XXX XX XX</small>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary-custom btn-lg py-3">
                                    <i class="fas fa-check-circle me-2"></i>Подтвердить заказ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Успешное оформление заказа -->
                <div class="card success-card border-0 mb-4">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="mb-3">Спасибо за ваш заказ!</h2>
                        <p class="lead mb-4">Ваш заказ #<?= $_SESSION['order_id'] ?> успешно оформлен.</p>
                        <p>Мы свяжемся с вами в ближайшее время для подтверждения заказа.</p>
                        
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="../catalog/catalog.php" class="btn btn-outline-secondary">
                                <i class="fas fa-book me-2"></i>Вернуться в каталог
                            </a>
                            <a href="../order/orders.php" class="btn btn-primary-custom">
                                <i class="fas fa-clipboard-list me-2"></i>Мои заказы
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Подвал -->
<footer class="py-5">
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
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> г. Хучанд, ул. И.Сомони 256</li>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Маска для телефона
    document.getElementById('phone').addEventListener('input', function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
        e.target.value = !x[2] ? x[1] : '+' + x[1] + ' ' + x[2] + (x[3] ? ' ' + x[3] : '') + (x[4] ? ' ' + x[4] : '') + (x[5] ? ' ' + x[5] : '');
    });
</script>
</body>
</html>