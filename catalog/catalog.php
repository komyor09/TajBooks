<?php
    session_start();
    require_once '../config/db.php'; // Подключение к БД

    $limit = 6; // Количество книг на одной странице
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Получаем общее количество книг
    $total_stmt = $pdo->query("SELECT COUNT(*) as total FROM books");
    $total_books = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_books / $limit);

    // Получаем книги для текущей страницы
    $result = $pdo->query("SELECT * FROM Books LIMIT $limit OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог книг</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <!-- Шапка -->
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

<p class="text-center mt-2"></p>
<h1 class="text-center mb-4">Каталог книг</h1>

<main class="container py-2">
    <div class="row">
    <form id="filter-form" class="p-3 border rounded shadow">
        <div class="row p-3">
            <div class="col-md-3">
                <label class="form-label">Книга:</label>
                <input type="text" name="title" class="form-control" placeholder="Название книги">
            </div>
            <div class="col-md-3">
                <label class="form-label">Автор:</label>
                <input type="text" name="author" class="form-control" placeholder="Имя автора">
            </div>
            <div class="col-md-3">
                <label class="form-label">Издательство:</label>
                <input type="text" name="publisher" class="form-control" placeholder="Издательство"">
            </div>
            <div class="col-md-3">
                    <label class="form-label">Формат:</label>
                    <select name="format" class="form-select">
                        <option value="">Все</option>
                        <option value="paper" <?= isset($_POST['format']) && $_POST['format'] == 'paper' ? 'selected' : '' ?>>бумажная</option>
                        <option value="ebook" <?= isset($_POST['format']) && $_POST['format'] == 'ebook' ? 'selected' : '' ?>>электронная</option>
                    </select>
                </div>
        </div>
        <div class="row p-3">
            <div class="col-md-3">
                <label class="form-label">Цена (сомони): <span id="priceValue">100</span></label>
                <input type="range" name="min_price" min="50" max="1000" value="<?= isset($_POST['min_price']) ? $_POST['min_price'] : 60 ?>" class="form-range" oninput="document.getElementById('priceValue').textContent = this.value">
                <input type="range" name="max_price" min="50" max="1000" value="<?= isset($_POST['max_price']) ? $_POST['max_price'] : 500 ?>" class="form-range" oninput="document.getElementById('priceValue').textContent = this.value">
            </div>

            <div class="col-md-3">
                <label class="form-label">Год выпуска: <span id="yearValue"><?= isset($_POST['min_year']) ? $_POST['min_year'] : 2005 ?> - <?= isset($_POST['max_year']) ? $_POST['max_year'] : 2025 ?></span></label>
                <input type="range" name="min_year" min="2010" max="2025" value="<?= isset($_POST['min_year']) ? $_POST['min_year'] : 2005 ?>" class="form-range" oninput="document.getElementById('yearValue').textContent = this.value + ' - ' + document.getElementById('max_year').value">
                <input type="range" name="max_year" min="2010" max="2025" value="<?= isset($_POST['max_year']) ? $_POST['max_year'] : 2025 ?>" class="form-range" id="max_year" oninput="document.getElementById('yearValue').textContent = document.getElementById('min_year').value + ' - ' + this.value">
            </div>
            <div class="col-md-3 text-center">
                <label class="form-label">Рейтинг (от 1 до 5):</label>
                <input type="range" name="rating" min="1" max="5" value="<?= isset($_POST['rating']) ? $_POST['rating'] : 3 ?>" class="form-range" oninput="document.getElementById('ratingValue').textContent = this.value">
                <span id="ratingValue"><?= isset($_POST['rating']) ? $_POST['rating'] : 3 ?></span>
            </div>
            <!-- <div class="col-md-3">
                <label class="form-label">Наличие:</label>
                <select name="availability" class="form-select">
                    <option value="">Все</option>
                    <option value="in_stock" <?= isset($_POST['availability']) && $_POST['availability'] == 'in_stock' ? 'selected' : '' ?>>В наличии</option>
                    <option value="out_of_stock" <?= isset($_POST['availability']) && $_POST['availability'] == 'out_of_stock' ? 'selected' : '' ?>>Нет в наличии</option>
                </select>
            </div>
        </div>
        <div class="row p-3">
            <div class="col-md-3">
                <label class="form-label">Жанр:</label>
                <select name="genre[]" class="form-select" multiple>
                    <option value="fiction" <?= in_array('fiction', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Саморазвитие</option>
                    <option value="detective" <?= in_array('detective', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Триллер</option>
                    <option value="romance" <?= in_array('romance', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Финансы</option>
                    <option value="adventure" <?= in_array('adventure', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Фэнтези</option>
                    <option value="history" <?= in_array('history', $_POST['genre'] ?? []) ? 'selected' : '' ?>>История</option>
                    <option value="science" <?= in_array('science', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Научная</option>
                    <option value="biography" <?= in_array('biography', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Биография</option>
                    <option value="children" <?= in_array('children', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Детская</option>
                    <option value="horror" <?= in_array('horror', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Ужасы</option>
                    <option value="thriller" <?= in_array('thriller', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Триллер</option>
                    <option value="mystery" <?= in_array('mystery', $_POST['genre'] ?? []) ? 'selected' : '' ?>>Мистика</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Язык:</label>
                <select name="language[]" class="form-select" multiple>
                    <option value="ru" <?= in_array('ru', $_POST['language'] ?? []) ? 'selected' : '' ?>>Русский</option>
                    <option value="tj" <?= in_array('tj', $_POST['language'] ?? []) ? 'selected' : '' ?>>Таджикский</option>
                    <option value="en" <?= in_array('en', $_POST['language'] ?? []) ? 'selected' : '' ?>>Английский</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Сортировать по:</label>
                <select name="sort" class="form-select">
                    <option value="new" <?= isset($_POST['sort']) && $_POST['sort'] == 'new' ? 'selected' : '' ?>>Сначала новые</option>
                    <option value="cheap" <?= isset($_POST['sort']) && $_POST['sort'] == 'cheap' ? 'selected' : '' ?>>Сначала дешёвые</option>
                    <option value="expensive" <?= isset($_POST['sort']) && $_POST['sort'] == 'expensive' ? 'selected' : '' ?>>Сначала дорогие</option>
                    <option value="popular" <?= isset($_POST['sort']) && $_POST['sort'] == 'popular' ? 'selected' : '' ?>>Популярные</option>
                </select>
            </div> -->
        </div>
        <button type="submit" class="btn btn-success w-100 mt-3"><i class="fa fa-filter"></i> Фильтровать</button>
    </form>
    <div id="books-container"></div>
    </div>
    </div>
    </div>
</main>


<script>
    document.getElementById("filter-form").addEventListener("submit", function(event) {
        event.preventDefault();
        let formData = new FormData(this);

        fetch("filter.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById("books-container").innerHTML = data;
        });
    });
</script>

<main class="container py-3">
    <div class="row">
        <?php while ($book = $result->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="text-center">
                        <img src="../pics/<?= htmlspecialchars($book['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>" style="height: 50%; width: 50%;">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                        <p class="card-text">Автор: <?= htmlspecialchars($book['author']) ?></p>
                        <form action="book_details.php" method="POST">
                            <input type="hidden" name="book_id" value="<?=$book['id']?>">
                            <button type="submit" class="btn btn-success"><i class="fas fa-info"></i> Подробнее</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Пагинация -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="../catalog/catalog.php?page=<?= $i ?>"><?= $i ?><?php $_SESSION['catalog_page'] = $page; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</main>

    <!-- Подвал -->
    <footer class="bg-dark text-white py-3">
        <nav class="container d-flex justify-content-between align-items-center">
            <!-- Логотип сайта с именем -->
            <a href="../index.php" class="text-white d-flex align-items-center">
                <img src="../pics/logo.jpg" alt="Logo" class="me-1" style="width: 50px;">
                <div class="row">
                <span class="h4 text-center">TajBooks</span>
                <span class="h6 text-center">Read Learn Grow</span>
                </div>
            </a>

            <!-- Меню с иконками -->
            <ul class="nav ms-auto">
                <li class="nav-item ms-3">
                    <a href="../faq.php" class="nav-link text-white">
                        <i class="fas fa-question me-2"></i>FAQ
                    </a>
                    <ul>
                        <li><a href="../faq.php/#q1" class="nav-link text-white">Question 1</a></li>
                        <li><a href="../faq.php/#q2" class="nav-link text-white">Question 2</a></li>
                        <li><a href="../faq.php/#q3" class="nav-link text-white">Question 3</a></li>
                    </ul>
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
                        <i class="fas fa-telegram me-2"></i>Инстаграм    
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
