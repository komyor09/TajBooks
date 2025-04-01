<?php
session_start();
require_once '../order/cartClass.php';
require_once '../config/db.php';  // Здесь вы уже используете PDO

$order_success = false;  // Флаг для успешности заказа

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
        
        // Считаем общую стоимость
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        try {
            $sql = "INSERT INTO Orders (user_id, status, price) VALUES (:user_id, 'new', :price)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':price', $total, PDO::PARAM_STR);
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

            // Устанавливаем флаг успешного оформления заказа
            $order_success = true;
            $_SESSION['message'] = "Ваш заказ успешно оформлен!";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Ошибка оформления заказа: " . $e->getMessage();
        }
    } else {
        $_SESSION['message'] = "Пожалуйста, заполните все поля!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа</title>
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
        .alert-custom {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<header class="bg-dark text-white py-3">
    <nav class="container d-flex justify-content-between align-items-center">
        <a href="../index.php" class="text-white d-flex align-items-center">
            <img src="../pics/logo.jpg" alt="Logo" class="me-1" style="width: 50px;">
            <div class="row">
                <span class="h4 text-center">TajBooks</span>
                <span class="h6 text-center">Read Learn Grow</span>
            </div>
        </a>
        <ul class="nav ms-auto">
            <li class="nav-item ms-3">
                <a href="../catalog/catalog.php" class="nav-link text-white">Каталог</a>
            </li>
            <li class="nav-item ms-3">
                <a href="../order/cart.php" class="nav-link text-white">Корзина</a>
            </li>
            <li class="nav-item ms-3">
                <a href="../auth/profile.php" class="nav-link text-white">Личный кабинет</a>
            </li>
        </ul>
    </nav>
</header>

<main class="container py-5">
    <h1 class="text-center mb-4">Оформление заказа</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-custom text-center shadow" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (!$order_success): ?>
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
    <?php else: ?>
        <a href="../order/orders.php" class="btn btn-primary w-100 mt-4">Перейти в меню заказов</a>
    <?php endif; ?>
</main>

<?= require_once "../footer.php"; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
