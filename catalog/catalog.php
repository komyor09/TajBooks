<?php
session_start();
require_once '../config/db.php';
require_once '../order/cartClass.php';



$cart = new Cart($pdo, $_SESSION['user_id']);
$cartItems = $cart->get_cart_items();

$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$total_stmt = $pdo->query("SELECT COUNT(*) as total FROM book");
$total_books = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];;
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
$total_books = count($books);
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

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Каталог книг</h1>
            <div class="position-relative">
                <button class="btn btn-outline-primary position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge badge bg-danger rounded-pill"><?= count($cartItems) ?></span>
                </button>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form id="filter-form" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Название книги</label>
                            <input type="text" name="title" class="form-control" placeholder="Поиск..." value="<?= $_GET['title'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Автор</label>
                            <input type="text" name="author" class="form-control" placeholder="Автор" value="<?= $_GET['author'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Издательство</label>
                            <input type="text" name="publisher" class="form-control" placeholder="Издательство" value="<?= $_GET['publisher'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Формат</label>
                            <select name="format" class="form-select">
                                <option value="">Все</option>
                                <option value="1" <?= isset($_GET['format']) && $_GET['format'] == '1' ? 'selected' : '' ?>>Бумажная</option>
                                <option value="2" <?= isset($_GET['format']) && $_GET['format'] == '2' ? 'selected' : '' ?>>Электронная</option>
                                <option value="3" <?= isset($_GET['format']) && $_GET['format'] == '3' ? 'selected' : '' ?>>Аудиокнига</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Цена: <span id="priceRange"><?= $_GET['min_price'] ?? 50 ?> - <?= $_GET['max_price'] ?? 1000 ?> сомони</span></label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="range" name="min_price" class="form-range" min="50" max="1000" 
                                    value="<?= $_GET['min_price'] ?? 50 ?>" id="minPrice">
                                <input type="range" name="max_price" class="form-range" min="50" max="1000" 
                                    value="<?= $_GET['max_price'] ?? 1000 ?>" id="maxPrice">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Год выпуска: <span id="yearRange"><?= $_GET['min_year'] ?? 2010 ?> - <?= $_GET['max_year'] ?? 2025 ?></span></label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="range" name="min_year" class="form-range" min="2010" max="2025" 
                                    value="<?= $_GET['min_year'] ?? 2010 ?>" id="minYear">
                                <input type="range" name="max_year" class="form-range" min="2010" max="2025" 
                                    value="<?= $_GET['max_year'] ?? 2025 ?>" id="maxYear">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Применить фильтры
                        </button>
                        <a href="?" class="btn btn-outline-secondary">Сбросить</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Результаты поиска -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Найдено книг: <span class="badge bg-primary"><?= $total_books ?></span></h5>
            </div>
        </div>

        <!-- Список книг -->
        <div class="row g-4">
            <?php foreach ($books as $book): ?>
                <?php 
                $inCart = false;
                $cartQuantity = 0;
                foreach ($cartItems as $item) {
                    if ($item['book_id'] == $book['id']) {
                        $inCart = true;
                        $cartQuantity = $item['quantity'];
                        break;
                    }
                }
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 book-card border-0 shadow-sm">
                        <div class="position-relative" style="padding-top: 150%; overflow: hidden;">
                            <img src="../pics/<?= htmlspecialchars($book['image_path']) ?>" 
                                class="card-img-top position-absolute top-0 start-0 w-100 h-100 object-fit-cover" 
                                alt="<?= htmlspecialchars($book['title']) ?>">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                            <p class="card-text text-muted">Автор: <?= htmlspecialchars($book['author_name']) ?></p>
                            <p class="card-text">Издательство: <?= htmlspecialchars($book['publisher_name']) ?></p>
                            <p class="card-text price-tag"><?= number_format($book['price'], 2) ?> сомони</p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <form action="book_details.php" method="POST" class="me-2">
                                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-info-circle me-2"></i> Подробнее
                                        </button>
                                    </form>
                                    
                                    <?php if ($inCart): ?>
                                        <div class="d-flex align-items-center">
                                            <form action="../order/update_cart.php" method="POST" class="d-flex">
                                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                                <input type="hidden" name="action" value="decrease">
                                                <button type="submit" class="btn btn-outline-secondary quantity-control">-</button>
                                            </form>
                                            
                                            <span class="mx-2"><?= $cartQuantity ?></span>
                                            
                                            <form action="../order/update_cart.php" method="POST" class="d-flex">
                                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                                <input type="hidden" name="action" value="increase">
                                                <button type="submit" class="btn btn-outline-secondary quantity-control">+</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <form action="../order/update_cart.php" method="POST">
                                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                            <input type="hidden" name="action" value="add">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-cart-plus me-2"></i> В корзину
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Пагинация -->
        <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php 
                $clean_params = array_filter($_GET, function($value) {
                    return $value !== '' && $value !== null;
                });
                unset($clean_params['page']);
                
                // Previous page
                if ($page > 1): 
                    $prev_page = $page - 1;
                ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $prev_page ?><?= !empty($clean_params) ? '&' . http_build_query($clean_params) : '' ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php 
                // Show page numbers
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                if ($start > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=1<?= !empty($clean_params) ? '&' . http_build_query($clean_params) : '' ?>">1</a></li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= !empty($clean_params) ? '&' . http_build_query($clean_params) : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($end < $total_pages): ?>
                    <?php if ($end < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $total_pages ?><?= !empty($clean_params) ? '&' . http_build_query($clean_params) : '' ?>"><?= $total_pages ?></a></li>
                <?php endif; ?>
                
                <?php // Next page
                if ($page < $total_pages): 
                    $next_page = $page + 1;
                ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $next_page ?><?= !empty($clean_params) ? '&' . http_build_query($clean_params) : '' ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <!-- Модальное окно корзины -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="cartModalLabel">Ваша корзина</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (empty($cartItems)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                            <h5>Ваша корзина пуста</h5>
                            <p class="text-muted">Добавьте книги из каталога</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Книга</th>
                                        <th>Цена</th>
                                        <th>Количество</th>
                                        <th>Сумма</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total = 0;
                                    foreach ($cartItems as $item): 
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal;
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="../pics/<?= htmlspecialchars($item['image_path']) ?>" 
                                                        class="me-3" style="width: 50px; height: 70px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($item['title']) ?></h6>
                                                        <small class="text-muted"><?= htmlspecialchars($item['author_name']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= number_format($item['price'], 2) ?> сомони</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <form action="../order/update_cart.php" method="POST" class="me-1">
                                                        <input type="hidden" name="book_id" value="<?= $item['id'] ?>">
                                                        <input type="hidden" name="action" value="decrease">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">-</button>
                                                    </form>
                                                    <span class="mx-2"><?= $item['quantity'] ?></span>
                                                    <form action="../order/update_cart.php" method="POST" class="ms-1">
                                                        <input type="hidden" name="book_id" value="<?= $item['id'] ?>">
                                                        <input type="hidden" name="action" value="increase">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">+</button>
                                                    </form>
                                                </div>
                                            </td>
                                            <td><?= number_format($subtotal, 2) ?> сомони</td>
                                            <td>
                                                <form action="../order/update_cart.php" method="POST">
                                                    <input type="hidden" name="book_id" value="<?= $item['id'] ?>">
                                                    <input type="hidden" name="action" value="remove">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Итого:</td>
                                        <td colspan="2" class="fw-bold"><?= number_format($total, 2) ?> сомони</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Продолжить покупки</button>
                    <?php if (!empty($cartItems)): ?>
                        <a href="../order/checkout.php" class="btn btn-primary">Оформить заказ</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="../index.php" class="d-flex align-items-center text-white text-decoration-none">
                        <img src="../pics/logo.jpg" alt="Logo" class="me-2" style="width: 50px;">
                        <div>
                            <span class="h4">TajBooks</span>
                            <p class="mb-0 small">Read Learn Grow</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Контакты</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i> +992 91 139 06 12</li>
                        <li><i class="fas fa-envelope me-2"></i> info@tajbooks.tj</li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Мы в соцсетях</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-telegram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <p class="mb-0">&copy; 2025 TajBooks. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Обновление отображения диапазонов цен и годов
        document.addEventListener('DOMContentLoaded', function() {
            const minPrice = document.getElementById('minPrice');
            const maxPrice = document.getElementById('maxPrice');
            const priceRange = document.getElementById('priceRange');
            
            const minYear = document.getElementById('minYear');
            const maxYear = document.getElementById('maxYear');
            const yearRange = document.getElementById('yearRange');
            
            function updatePriceRange() {
                priceRange.textContent = `${minPrice.value} - ${maxPrice.value} сомони`;
            }
            
            function updateYearRange() {
                yearRange.textContent = `${minYear.value} - ${maxYear.value}`;
            }
            
            minPrice.addEventListener('input', updatePriceRange);
            maxPrice.addEventListener('input', updatePriceRange);
            minYear.addEventListener('input', updateYearRange);
            maxYear.addEventListener('input', updateYearRange);
        });
    </script>
</body>
</html>