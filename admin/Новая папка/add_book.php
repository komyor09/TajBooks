<?php
session_start();
require_once "../config/db.php"; // Подключение к БД

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    $genre = trim($_POST['genre']);
    $publisher = trim($_POST['publisher']);
    $year = $_POST['year'];
    $language = trim($_POST['language']);
    $format = $_POST['format'];
    $rating = $_POST['rating'];
    $availability = $_POST['availability'];

    $target_file = NULL;
    if (!empty($_FILES["image"]["name"])) {
        $image_name = basename($_FILES["image"]["name"]);
        $target_dir = "../pics/";
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_formats = ["jpg", "png", "jpeg", "gif"];

        if (!in_array($imageFileType, $allowed_formats) || !getimagesize($_FILES["image"]["tmp_name"])) {
            $errors[] = "Неверный формат изображения.";
        } else {
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }
    }

    if (empty($errors)) {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("INSERT INTO books (title, author, price, description, genre, publisher, year, language, format, rating, availability, image_path) 
                                   VALUES (:title, :author, :price, :description, :genre, :publisher, :year, :language, :format, :rating, :availability, :image_path)");
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':price' => $price,
                ':description' => $description,
                ':genre' => $genre,
                ':publisher' => $publisher,
                ':year' => $year,
                ':language' => $language,
                ':format' => $format,
                ':rating' => $rating,
                ':availability' => $availability,
                ':image_path' => $target_file
            ]);

            $success = "Книга успешно добавлена!";
        } catch (PDOException $e) {
            $errors[] = "Ошибка: " . $e->getMessage();
        }
    }
}

$genres = ["Фантастика", "Детектив", "Роман", "Фэнтези", "Научная литература", "Биография", "История", "Саморазвитие", "Приключения", "Триллер", "Другое"];
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить книгу</title>
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

    <div class="admin-container_admin">
        <h2>Добавить новую книгу</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <!-- <div class="success-box"> <?= htmlspecialchars($success) ?> </div> -->
        <?php endif; ?>

        <form action="add_book.php" method="post" enctype="multipart/form-data" class="admin-form">
            <label>Название книги:</label>
            <input type="text" name="title" required>

            <label>Автор:</label>
            <input type="text" name="author" required>

            <label>Цена:</label>
            <input type="number" step="1.00" name="price" required>

            <label>Описание:</label>
            <textarea name="description" required></textarea>

            <label>Жанр:</label>
            <select name="genre" required>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Издатель:</label>
            <input type="text" name="publisher">

            <label>Год выпуска:</label>
            <input type="number" name="year" min="1000" max="<?= date('Y') ?>" required>

            <label>Язык:</label>
            <select name="language" required>
                <option value="Русский">Русский</option>
                <option value="Английский">Английский</option>
                <option value="Таджикский">Таджикский</option>
            </select>

            <label>Формат:</label>
            <select name="format" required>
                <option value="бумажная">Бумажная</option>
                <option value="электронная">Электронная</option>
                <option value="аудиокнига">Аудиокнига</option>
            </select>

            <label>Рейтинг (0-5):</label>
            <input type="number" step="0.1" name="rating" min="0" max="5" value="0">

            <label>Наличие:</label>
            <select name="availability" required>
                <option value="в наличии">В наличии</option>
                <option value="нет в наличии">Нет в наличии</option>
            </select>

            <label>Изображение:</label>
            <input type="file" name="image">

            <button type="submit" class="admin-button">Добавить книгу</button>
        </form>
    </div>
    <?php if (!empty($success)): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let alertBox = document.createElement("div");
                alertBox.innerText = "📚 <?= $success ?>";
                alertBox.className = "alert-box";
                document.body.appendChild(alertBox);
                setTimeout(() => alertBox.remove(), 1000); // 5000 миллисекунд = 5 секунд
            });
        </script>
    <?php endif; ?>

</body>

</html>