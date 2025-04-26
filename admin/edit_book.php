<?php
session_start();
require_once "../config/db.php";

if (!isset($_POST['book_id']) || empty($_POST['book_id'])) {
    die("ID книги не передан.");
}

$book_id = $_POST['book_id'];
$book = null;
$error = null;

try {
    $stmt = $pdo->prepare("
        SELECT b.*, 
               a.name as author_name,
               f.name as format_name,
               l.name as language_name,
               p.name as publisher_name
        FROM book b
        LEFT JOIN authors a ON b.author_id = a.id
        LEFT JOIN formats f ON b.format_id = f.id
        LEFT JOIN languages l ON b.language_id = l.id
        LEFT JOIN publishers p ON b.publisher_id = p.id
        WHERE b.id = :id
    ");
    $stmt->execute([':id' => $book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        die("Книга не найдена.");
    }

    $authors = $pdo->query("SELECT id, name FROM authors")->fetchAll(PDO::FETCH_ASSOC);
    $formats = $pdo->query("SELECT id, name FROM formats")->fetchAll(PDO::FETCH_ASSOC);
    $languages = $pdo->query("SELECT id, name FROM languages")->fetchAll(PDO::FETCH_ASSOC);
    $publishers = $pdo->query("SELECT id, name FROM publishers")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Ошибка получения данных: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editing'])) {
    $title = $_POST['title'];
    $author_id = $_POST['author_id'];
    $price = $_POST['price'];
    $year = $_POST['year'];
    $description = $_POST['description'];
    $isbn = $_POST['isbn'];
    $pages = $_POST['pages'];
    $language_id = $_POST['language_id'];
    $publisher_id = $_POST['publisher_id'];
    $format_id = $_POST['format_id'];
    $quantity = $_POST['quantity'];
    $availability = isset($_POST['availability']) ? 1 : 0;
    
    $image_path = $book['image_path'];
    if (!empty($_FILES['image_path']['name'])) {
        $target_dir = "../pics/";
        $target_file = $target_dir . basename($_FILES["image_path"]["name"]);
        if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            $error = "Ошибка загрузки изображения.";
        }
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE book SET 
                title = :title,
                author_id = :author_id,
                price = :price,
                year = :year,
                description = :description,
                ISBN = :isbn,
                page = :pages,
                language_id = :language_id,
                publisher_id = :publisher_id,
                format_id = :format_id,
                image_path = :image_path,
                quantity = :quantity,
                availability = :availability
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':title' => $title,
            ':author_id' => $author_id,
            ':price' => $price,
            ':year' => $year,
            ':description' => $description,
            ':isbn' => $isbn,
            ':pages' => $pages,
            ':language_id' => $language_id,
            ':publisher_id' => $publisher_id,
            ':format_id' => $format_id,
            ':image_path' => $image_path,
            ':quantity' => $quantity,
            ':availability' => $availability,
            ':id' => $book_id
        ]);

        header("Location: manage_books.php"); 
        exit();
    } catch (PDOException $e) {
        $error = "Ошибка обновления данных: " . $e->getMessage();
    }
}
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
    </nav>
</header>

<div class="container mt-5">
    <h2>Редактирование книги</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="edit_book.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="book_id" value="<?= $book['id']; ?>">

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="title" class="form-label">Название*</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="author_id" class="form-label">Автор*</label>
                    <select class="form-select" id="author_id" name="author_id" required>
                        <?php foreach ($authors as $author): ?>
                            <option value="<?= $author['id'] ?>" <?= $author['id'] == $book['author_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($author['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Описание*</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($book['description']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" class="form-control" id="isbn" name="isbn" value="<?= htmlspecialchars($book['ISBN']) ?>">
                </div>

                <div class="mb-3">
                    <label for="pages" class="form-label">Количество страниц*</label>
                    <input type="number" class="form-control" id="pages" name="pages" value="<?= htmlspecialchars($book['page']) ?>" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="year" class="form-label">Год издания</label>
                    <input type="number" class="form-control" id="year" name="year" value="<?= htmlspecialchars($book['year']) ?>">
                </div>

                <div class="mb-3">
                    <label for="language_id" class="form-label">Язык</label>
                    <select class="form-select" id="language_id" name="language_id">
                        <option value="">-- Выберите язык --</option>
                        <?php foreach ($languages as $language): ?>
                            <option value="<?= $language['id'] ?>" <?= $language['id'] == $book['language_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($language['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="publisher_id" class="form-label">Издательство</label>
                    <select class="form-select" id="publisher_id" name="publisher_id">
                        <option value="">-- Выберите издательство --</option>
                        <?php foreach ($publishers as $publisher): ?>
                            <option value="<?= $publisher['id'] ?>" <?= $publisher['id'] == $book['publisher_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($publisher['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="format_id" class="form-label">Формат*</label>
                    <select class="form-select" id="format_id" name="format_id" required>
                        <?php foreach ($formats as $format): ?>
                            <option value="<?= $format['id'] ?>" <?= $format['id'] == $book['format_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($format['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Цена*</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($book['price']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Количество*</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($book['quantity']) ?>" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="availability" name="availability" <?= $book['availability'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="availability">Доступна для заказа</label>
                </div>

                <div class="mb-3">
                    <label for="image_path" class="form-label">Изображение</label>
                    <input type="file" class="form-control" id="image_path" name="image_path">
                    <?php if ($book['image_path']): ?>
                        <div class="mt-2">
                            <small>Текущее изображение:</small><br>
                            <img src="<?= htmlspecialchars($book['image_path']) ?>" alt="Обложка книги" style="max-height: 100px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <button type="submit" name="editing" value="1" class="btn btn-success">Сохранить изменения</button>
        <a href="manage_books.php" class="btn btn-secondary">Отмена</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>