<?php
session_start();
require_once "../config/db.php";

if (!isset($_POST['book_id']) || empty($_POST['book_id'])) {
    die("ID книги не передан.");
}

$book_id = $_POST['book_id'];
$book = null;

try {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute([':id' => $book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        die("Книга не найдена.");
    }
} catch (PDOException $e) {
    $error = "Ошибка получения данных: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['editing']) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $genre = $_POST['genre'];
    $year = $_POST['year'];
    $image_path = $_POST['image_path'];

    try {
        $stmt = $pdo->prepare("UPDATE books SET title = :title, author = :author, price = :price, genre = :genre, year = :year, image_path = :image_path WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':price' => $price,
            ':genre' => $genre,
            ':year' => $year,
            ':image_path' => $image_path,
            ':id' => $book_id
        ]);

        header("Location: manage_books.php"); 
        exit();
    } catch (PDOException $e) {
        $error = "Ошибка обновления данных: " . $e->getMessage();
    }
}

$genres = ["Фантастика", "Детектив", "Роман", "Фэнтези", "Научная литература", "Биография", "История", "Саморазвитие", "Приключения", "Триллер", "Другое"];

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование книги</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/admin.css">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>

<header class="bg-dark text-white py-3">
    <nav class="container_1 d-flex justify-content-between align-items-center">
        <!-- Логотип сайта с именем -->
        <a href="../index.php" class="text-white d-flex align-items-center p-3">
            <img src="../pics/logo.jpg" alt="Logo" class="me-1 text-center" style="width: 50px;">
            <div class="row">
                <span class="h4 text-center">TajBooks</span>
                <span class="h6 text-center">Read Learn Grow</span>
            </div>
        </a>
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
            <a href="../admin/manage_books.php" class="nav-link text-white">
                <i class="fas fa-cogs me-2"></i>Управление книгами
            </a>
        </li>
        <li class="nav-item ms-3">
            <a href="../admin/add_book.php" class="nav-link text-white">
                <i class="fas fa-plus me-2"></i>Добавить книгу
            </a>
        </li>
        <li class="nav-item ms-3">
            <a href="../admin/orders.php" class="nav-link text-white">
                <i class="fas fa-box me-2"></i>Заказы
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
    </div>
</nav>
</header>

<div class="container mt-5">
    <h2>Редактирование книги</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="edit_book.php" method="POST">
        <input type="hidden" name="book_id" value="<?= $book['id']; ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Название</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="author" class="form-label">Автор</label>
            <input type="text" class="form-control" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Цена</label>
            <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($book['price']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="genre" class="form-label">Жанр</label>
            <select name="genre" required>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>" <?= $g === $book['genre'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g) ?>
                    </option>
                <?php endforeach; ?>
            </select>    
        </div>

        <div class="mb-3">
            <label for="year" class="form-label">Год</label>
            <input type="number" class="form-control" id="year" name="year" value="<?= htmlspecialchars($book['year']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="image_path" class="form-label">Изображение (если есть)</label>
            <input type="file" class="form-control" id="image_path" name="image_path" value="<?= htmlspecialchars($book['image_path']) ?>">
        </div>
        <button type="submit" name="editing" value="true" class="btn btn-success">Сохранить изменения</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
