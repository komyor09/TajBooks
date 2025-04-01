<?php
session_start();
require_once '../config/db.php'; 

if (isset($_POST['book_id'])) {
    $book_id = (int)$_POST['book_id'];
    $stmt = $pdo->prepare("SELECT * FROM Books WHERE id = :id");
    $stmt->bindParam(':id', $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$book) {
        echo "Книга не найдена";
        exit;
    }
} else {
    echo "Некорректный запрос";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php
    $role = $_SESSION['role'] ?? '';
    if ($role === 'admin') {
        require_once('navbars\admin.php');
    } else {
        require_once('../navbars/client.php');
    }

    ?>
        <!-- Сообщение при выходе -->
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-custom text-center shadow" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

<div class="container py-5">
    <div class="card shadow-lg">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="../pics/<?= htmlspecialchars($book['image_path']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($book['title']) ?>">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h2 class="card-title"><?= htmlspecialchars($book['title']) ?></h2>
                    <p class="card-text"><strong>Автор:</strong> <?= htmlspecialchars($book['author']) ?></p>
                    <p class="card-text"><?= htmlspecialchars($book['description']) ?></p>
                    <h4 class="text-primary">Цена: <?= htmlspecialchars($book['price']) ?> TJS</h4>
                    <form action="../order/add_to_cart.php" method="POST">
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <div class="d-flex align-items-center">
                            <div class="input-group w-25">
                                <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">-</button>
                                <input type="number" name="quantity" id="quantity" class="form-control text-center" value="1" min="1" max="100" style="width: 60px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">+</button>
                            </div>
                            <button type="submit" class="btn btn-primary ms-3">Добавить в корзину</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= require_once "../footer.php"; ?>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function incrementQuantity() {
        let quantity = document.getElementById("quantity");
        if (parseInt(quantity.value) < 100) {
            quantity.value = parseInt(quantity.value) + 1;
        }
    }
    function decrementQuantity() {
        let quantity = document.getElementById("quantity");
        if (parseInt(quantity.value) > 1) {
            quantity.value = parseInt(quantity.value) - 1;
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
