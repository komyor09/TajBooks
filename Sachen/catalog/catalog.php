<?php
session_start();
require_once '../config/db.php';

$limit = 6; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Запрос для фильтрации
$filters = [];
$query = "SELECT * FROM Books";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $title = isset($_GET['title']) ? "%" . $_GET['title'] . "%" : null;
    $author = isset($_GET['author']) ? "%" . $_GET['author'] . "%" : null;
    $publisher = isset($_GET['publisher']) ? "%" . $_GET['publisher'] . "%" : null;
    $format = isset($_GET['format']) ? $_GET['format'] : null;
    $min_price = isset($_GET['min_price']) ? $_GET['min_price'] : 50;
    $max_price = isset($_GET['max_price']) ? $_GET['max_price'] : 1000;
    $min_year = isset($_GET['min_year']) ? $_GET['min_year'] : 2010;
    $max_year = isset($_GET['max_year']) ? $_GET['max_year'] : 2025;
    $rating = isset($_GET['rating']) ? $_GET['rating'] : 3;

    $whereClauses = ["1=1"];

    if ($title) {
        $whereClauses[] = "title LIKE :title";
        $filters['title'] = $title;
    }

    if ($author) {
        $whereClauses[] = "author LIKE :author";
        $filters['author'] = $author;
    }

    if ($publisher) {
        $whereClauses[] = "publisher LIKE :publisher";
        $filters['publisher'] = $publisher;
    }

    if ($format) {
        $whereClauses[] = "format = :format";
        $filters['format'] = $format;
    }

    $whereClauses[] = "price BETWEEN :min_price AND :max_price";
    $filters['min_price'] = $min_price;
    $filters['max_price'] = $max_price;

    $whereClauses[] = "year BETWEEN :min_year AND :max_year";
    $filters['min_year'] = $min_year;
    $filters['max_year'] = $max_year;

    $whereClauses[] = "rating >= :rating";
    $filters['rating'] = $rating;

    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$query .= " LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($filters);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Запрос для всех книг из каталога (без фильтрации)
$allBooksQuery = "SELECT * FROM Books LIMIT 6";
$allBooksStmt = $pdo->query($allBooksQuery);
$allBooks = $allBooksStmt->fetchAll(PDO::FETCH_ASSOC);

// Запрос для подсчета фильтрованных книг
$total_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM Books WHERE " . implode(" AND ", $whereClauses));
$total_stmt->execute($filters);
$total_books = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_books / $limit);
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
    <?= require_once "search.php"; ?>

    <!-- Отфильтрованные книги -->
    <h2>Результаты поиска</h2>
    <div class="row" id="books-list">
        <?php if (count($books) > 0): ?>
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
        <?php else: ?>
            <p>Книги, соответствующие вашему запросу, не найдены.</p>
        <?php endif; ?>
    </div>

    <!-- Пагинация для отфильтрованных книг -->
    <nav>
        <ul class="pagination justify-content-center" id="pagination">
            <?php 
            $queryParams = $_GET;
            unset($queryParams['page']);
            for ($i = 1; $i <= $total_pages; $i++): 
            ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query($queryParams) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

    <!-- Все книги из каталога -->
    <h2>Все книги из каталога</h2>
    <div class="row">
        <?php foreach ($allBooks as $book): ?>
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
</main>

<?= require_once "../footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
