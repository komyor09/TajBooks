<?php
session_start();
require_once "../config/db.php"; 

$search = '';
$sort_by = 'created_at DESC'; 

if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

if (isset($_POST['sort'])) {
    switch ($_POST['sort']) {
        case 'title':
            $sort_by = 'title ASC';
            break;
        case 'price':
            $sort_by = 'price ASC';
            break;
        case 'year':
            $sort_by = 'year ASC';
            break;
        case 'author':
            $sort_by = 'author ASC';
            break;
        case 'created_at':
            $sort_by = 'created_at DESC';
            break;
    }
}

$books = [];
try {
    $query = "SELECT * FROM books WHERE title LIKE :search OR author LIKE :search ORDER BY $sort_by";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':search' => "%$search%"]);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Ошибка получения данных: " . $e->getMessage();
}

if (isset($_POST['delete'])) {
    $book_id = $_POST['book_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute([':id' => $book_id]);
        header("Location: manage_books.php");
        exit();
    } catch (PDOException $e) {
        $error = "Ошибка удаления книги: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление книгами</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/admin.css">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>

<header class="bg-dark text-white py-3">
        <nav class="container_1 d-flex justify-content-between align-items-center p-3">
            <!-- Логотип сайта с именем -->
            <a href="../index.php" class="text-white d-flex align-items-center">
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
    <h2>Управление книгами</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Форма поиска и сортировки -->
    <form class="mb-4" method="POST" action="manage_books.php">
        <div class="mb-3 input-group">
            <input type="text" class="form-control" name="search" placeholder="Поиск по названию или автору" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
        </div>

        <label for="sort" class="form-label">Сортировать по:</label>
        <select class="form-select" id="sort" name="sort" onchange="this.form.submit()">
            <option value="created_at" <?= $sort_by == 'created_at DESC' ? 'selected' : '' ?>>Дата добавления</option>
            <option value="title" <?= $sort_by == 'title ASC' ? 'selected' : '' ?>>Название</option>
            <option value="price" <?= $sort_by == 'price ASC' ? 'selected' : '' ?>>Цена</option>
            <option value="year" <?= $sort_by == 'year ASC' ? 'selected' : '' ?>>Год</option>
            <option value="author" <?= $sort_by == 'author ASC' ? 'selected' : '' ?>>Автор</option>
        </select>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Название</th>
                <th>Автор</th>
                <th>Цена</th>
                <th>Жанр</th>
                <th>Год</th>
                <th>Изображение</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['price']) ?> руб.</td>
                    <td><?= htmlspecialchars($book['genre']) ?></td>
                    <td><?= htmlspecialchars($book['year']) ?></td>
                    <td>
                        <?php if ($book['image_path']): ?>
                            <img src="../pics/<?= htmlspecialchars($book['image_path']) ?>" alt="Обложка книги" width="50">
                        <?php else: ?>
                            Нет изображения
                        <?php endif; ?>
                    </td>
                    <td nowrap>
                        <form action="../admin/edit_book.php" method="POST" style="all: unset !important;">
                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="btn btn-success"><i class="fas fa-edit"></i></button>
                        </form>
                        &emsp;
                        <form action="../admin/manage_books.php" method="POST" style="all: unset !important;">
                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                            <button type="submit" name="delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>