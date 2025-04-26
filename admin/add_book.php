<?php
session_start();
require_once "../config/db.php";

$errors = [];
$success = "";

try {
    $authors = $pdo->query("SELECT id, name FROM authors")->fetchAll(PDO::FETCH_ASSOC);
    $formats = $pdo->query("SELECT id, name FROM formats")->fetchAll(PDO::FETCH_ASSOC);
    $languages = $pdo->query("SELECT id, name FROM languages")->fetchAll(PDO::FETCH_ASSOC);
    $publishers = $pdo->query("SELECT id, name FROM publishers")->fetchAll(PDO::FETCH_ASSOC);
    $genres = $pdo->query("SELECT id, name FROM genre")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Ошибка загрузки справочников: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($errors)) {
    $title = trim($_POST['title']);
    $author_id = $_POST['author_id'];
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    $genre_ids = $_POST['genres'] ?? [];
    $publisher_id = $_POST['publisher_id'];
    $year = $_POST['year'];
    $language_id = $_POST['language_id'];
    $format_id = $_POST['format_id'];
    $isbn = trim($_POST['isbn'] ?? '');
    $pages = $_POST['pages'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    $availability = isset($_POST['availability']) ? 1 : 0;

    $image_path = null;
    if (!empty($_FILES["image"]["name"])) {
        $image_name = basename($_FILES["image"]["name"]);
        $target_dir = "../pics/";
        $target_file = $target_dir . uniqid() . '_' . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_formats = ["jpg", "png", "jpeg", "gif"];

        if (!in_array($imageFileType, $allowed_formats) || !getimagesize($_FILES["image"]["tmp_name"])) {
            $errors[] = "Неверный формат изображения. Разрешены только JPG, JPEG, PNG и GIF.";
        } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $errors[] = "Ошибка загрузки изображения.";
        } else {
            $image_path = $target_file;
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO book (
                    title, author_id, price, description, publisher_id, 
                    year, language_id, format_id, ISBN, page, 
                    image_path, quantity, availability, created_at
                ) VALUES (
                    :title, :author_id, :price, :description, :publisher_id, 
                    :year, :language_id, :format_id, :isbn, :pages, 
                    :image_path, :quantity, :availability, NOW()
                )
            ");
            
            $stmt->execute([
                ':title' => $title,
                ':author_id' => $author_id,
                ':price' => $price,
                ':description' => $description,
                ':publisher_id' => $publisher_id,
                ':year' => $year,
                ':language_id' => $language_id,
                ':format_id' => $format_id,
                ':isbn' => $isbn,
                ':pages' => $pages,
                ':image_path' => $image_path,
                ':quantity' => $quantity,
                ':availability' => $availability
            ]);
            
            $book_id = $pdo->lastInsertId();

            if (!empty($genre_ids)) {
                $stmt = $pdo->prepare("INSERT INTO book_genre (book_id, genre_id) VALUES (:book_id, :genre_id)");
                foreach ($genre_ids as $genre_id) {
                    $stmt->execute([':book_id' => $book_id, ':genre_id' => $genre_id]);
                }
            }

            $pdo->commit();
            $success = "Книга успешно добавлена!";
            
            $_POST = [];
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Ошибка добавления книги: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить книгу</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/admin.css">
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .alert-box {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 1000;
            animation: fadeIn 0.5s, fadeOut 0.5s 2.5s;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        @keyframes fadeOut {
            from {opacity: 1;}
            to {opacity: 0;}
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-section {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
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

    <div class="container mt-4">
        <h2 class="mb-4">Добавить новую книгу</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mb-4">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="add_book.php" method="post" enctype="multipart/form-data" class="form-container">
            <div class="form-section">
                <h4>Основная информация</h4>
                <div class="mb-3">
                    <label for="title" class="form-label">Название книги*</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="author_id" class="form-label">Автор*</label>
                    <select class="form-select" id="author_id" name="author_id" required>
                        <option value="">-- Выберите автора --</option>
                        <?php foreach ($authors as $author): ?>
                            <option value="<?= $author['id'] ?>" <?= ($_POST['author_id'] ?? '') == $author['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($author['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Описание*</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h4>Детали издания</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="publisher_id" class="form-label">Издательство</label>
                        <select class="form-select" id="publisher_id" name="publisher_id">
                            <option value="">-- Выберите издательство --</option>
                            <?php foreach ($publishers as $publisher): ?>
                                <option value="<?= $publisher['id'] ?>" <?= ($_POST['publisher_id'] ?? '') == $publisher['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($publisher['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="year" class="form-label">Год выпуска*</label>
                        <input type="number" class="form-control" id="year" name="year" 
                               min="1000" max="<?= date('Y') ?>" 
                               value="<?= htmlspecialchars($_POST['year'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="language_id" class="form-label">Язык</label>
                        <select class="form-select" id="language_id" name="language_id">
                            <option value="">-- Выберите язык --</option>
                            <?php foreach ($languages as $language): ?>
                                <option value="<?= $language['id'] ?>" <?= ($_POST['language_id'] ?? '') == $language['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($language['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="format_id" class="form-label">Формат*</label>
                        <select class="form-select" id="format_id" name="format_id" required>
                            <option value="">-- Выберите формат --</option>
                            <?php foreach ($formats as $format): ?>
                                <option value="<?= $format['id'] ?>" <?= ($_POST['format_id'] ?? '') == $format['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($format['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" value="<?= htmlspecialchars($_POST['isbn'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pages" class="form-label">Количество страниц</label>
                        <input type="number" class="form-control" id="pages" name="pages" min="1" value="<?= htmlspecialchars($_POST['pages'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4>Категории и наличие</h4>
                <div class="mb-3">
                    <label class="form-label">Жанры</label>
                    <div class="row">
                        <?php foreach ($genres as $genre): ?>
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="genres[]" 
                                           id="genre_<?= $genre['id'] ?>" value="<?= $genre['id'] ?>"
                                           <?= in_array($genre['id'], $_POST['genres'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="genre_<?= $genre['id'] ?>">
                                        <?= htmlspecialchars($genre['name']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Цена*</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" 
                               min="0" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label">Количество*</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               min="0" value="<?= htmlspecialchars($_POST['quantity'] ?? 1) ?>" required>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="availability" name="availability" 
                           <?= ($_POST['availability'] ?? 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="availability">Доступна для заказа</label>
                </div>
            </div>

            <div class="form-section">
                <h4>Изображение</h4>
                <div class="mb-3">
                    <label for="image" class="form-label">Обложка книги</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <small class="text-muted">Разрешены форматы: JPG, JPEG, PNG, GIF</small>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="reset" class="btn btn-secondary me-md-2">Очистить</button>
                <button type="submit" class="btn btn-primary">Добавить книгу</button>
            </div>
        </form>
    </div>

    <?php if (!empty($success)): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let alertBox = document.createElement("div");
                alertBox.className = "alert-box";
                alertBox.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2" style="font-size: 1.5rem;"></i>
                        <div>
                            <strong>Успешно!</strong><br>
                            <?= $success ?>
                        </div>
                    </div>
                `;
                document.body.appendChild(alertBox);
                
                setTimeout(() => {
                    alertBox.style.animation = "fadeOut 0.5s";
                    setTimeout(() => alertBox.remove(), 500);
                }, 3000);
            });
        </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>