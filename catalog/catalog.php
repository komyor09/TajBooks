<?php
session_start();
require_once '../config/db.php';

$limit = 60;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_stmt = $pdo->query("SELECT COUNT(*) as total FROM book");
$total_books = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_books / $limit);

$query = "SELECT b.*, 
                a.name as author_name,
                p.name as publisher_name,
                f.name as format_name,
                l.name as language_name
        FROM book b
        LEFT JOIN authors a ON b.author_id = a.id
        LEFT JOIN publishers p ON b.publisher_id = p.id
        LEFT JOIN formats f ON b.format_id = f.id
        LEFT JOIN languages l ON b.language_id = l.id";


$filters = [];
$whereClauses = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty(trim($_GET['title'] ?? ''))) {
        $title = "%" . trim($_GET['title']) . "%";
        $whereClauses[] = "b.title LIKE :title";
        $filters['title'] = $title;
    }

    if (!empty(trim($_GET['author'] ?? ''))) {
        $author = "%" . trim($_GET['author']) . "%";
        $whereClauses[] = "a.name LIKE :author";
        $filters['author'] = $author;
    }

    if (!empty(trim($_GET['publisher'] ?? ''))) {
        $publisher = "%" . trim($_GET['publisher']) . "%";
        $whereClauses[] = "p.name LIKE :publisher";
        $filters['publisher'] = $publisher;
    }

    $valid_formats = ['0', '1', '2', '3'];
    if (!empty($_GET['format']) && in_array($_GET['format'], $valid_formats)) {
        $whereClauses[] = "f.id = :format";
        $filters['format'] = $_GET['format'];
    }


    $default_min_price = 50;
    $default_max_price = 1000;
    
    if ($_GET['min_price'] != $default_min_price || $_GET['max_price'] != $default_max_price) {
        $min_price = max(0, (int)$_GET['min_price']);
        $max_price = min(999999, (int)$_GET['max_price']);
        $whereClauses[] = "b.price BETWEEN :min_price AND :max_price";
        $filters['min_price'] = $min_price;
        $filters['max_price'] = $max_price;
    }

    $default_min_year = 2010;
    $default_max_year = 2025;
    
    if ($_GET['min_year'] != $default_min_year || $_GET['max_year'] != $default_max_year) {
        $min_year = max(0, (int)$_GET['min_year']);
        $max_year = min(9999, (int)$_GET['max_year']);
        $whereClauses[] = "b.year BETWEEN :min_year AND :max_year";
        $filters['min_year'] = $min_year;
        $filters['max_year'] = $max_year;
    }


    if (!empty($whereClauses)) {
        $query .= " WHERE " . implode(" AND ", $whereClauses);
    }
}


$query .= " LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($filters);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог книг</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
    $role = $_SESSION['role'] ?? '';
    if ($role === 'admin') {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/admin.php');
    } else {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/client.php');
    }

    ?>

    <h1 class="text-center mb-4">Каталог книг</h1>

    <main class="container py-2">
        <div class="row">
            <!-- Форма фильтрации -->
            <form id="filter-form" class="p-3 border rounded shadow" method="GET">
                <div class="row p-3">
                    <div class="col-md-3">
                        <label class="form-label">Книга:</label>
                        <input type="text" name="title" class="form-control" placeholder="Название книги" value="<?= $_GET['title'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Автор:</label>
                        <input type="text" name="author" class="form-control" placeholder="Имя автора" value="<?= $_GET['author'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Издательство:</label>
                        <input type="text" name="publisher" class="form-control" placeholder="Издательство" value="<?= $_GET['publisher'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Формат:</label>
                        <select name="format" class="form-select">
                            <option value="">Все</option>
                            <option value="1" <?= isset($_GET['format']) && $_GET['format'] == '1' ? 'selected' : '' ?>>Бумажная</option>
                            <option value="2" <?= isset($_GET['format']) && $_GET['format'] == '2' ? 'selected' : '' ?>>Электронная</option>
                            <option value="3" <?= isset($_GET['format']) && $_GET['format'] == '3' ? 'selected' : '' ?>>Аудиокнига</option>
                        </select>
                    </div>
                </div>

                <div class="row p-3">
                    <div class="col-md-3">
                        <label class="form-label">Цена (сомони):</label>
                        <input type="range" name="min_price" min="50" max="1000" value="<?= $_GET['min_price'] ?? 50 ?>" class="form-range">
                        <input type="range" name="max_price" min="50" max="1000" value="<?= $_GET['max_price'] ?? 1000 ?>" class="form-range">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Год выпуска:</label>
                        <input type="range" name="min_year" min="2010" max="2025" value="<?= $_GET['min_year'] ?? 2010 ?>" class="form-range">
                        <input type="range" name="max_year" min="2010" max="2025" value="<?= $_GET['max_year'] ?? 2025 ?>" class="form-range">
                    </div>
                    <!-- <div class="col-md-3">
                        <label class="form-label">Рейтинг:</label>
                        <input type="range" name="rating" min="1" max="5" value="<?= $_GET['rating'] ?? 3 ?>" class="form-range">
                    </div> -->
                </div>

                <button type="submit" class="btn btn-success w-100 mt-3"><i class="fa fa-filter"></i> Фильтровать</button>
            </form>
        </div>

        <div class="row">
            <?php foreach ($books as $book): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="../pics/<?= htmlspecialchars($book['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                            <p class="card-text">Автор: <?= htmlspecialchars($book['author']) ?></p>
                            <form action="book_details.php" method="POST">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit" class="btn btn-success"><i class="fas fa-info"></i> Подробнее</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Пагинация -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <?php
$clean_params = array_filter($_GET, function($value) {
    return $value !== '' && $value !== null;
});
unset($clean_params['page']);
?>
<a class="page-link" href="?page=<?= $i ?><?= !empty($clean_params) ? '&' . http_build_query($clean_params) : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </main>

    <footer class="bg-dark text-white py-3">
        <nav class="container d-flex justify-content-between align-items-center">
            <a href="../index.php" class="text-white d-flex align-items-center">
                <img src="../pics/logo.jpg" alt="Logo" class="me-1" style="width: 50px;">
                <div class="row">
                    <span class="h4 text-center">TajBooks</span>
                    <span class="h6 text-center">Read Learn Grow</span>
                </div>
            </a>
        </nav>
        <p class="text-center mb-2 py-2">&copy; 2025 TajBooks. Все права защищены.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
