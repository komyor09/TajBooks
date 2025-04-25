<?php
session_start();

$email = $_SESSION['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['reg_email']) && filter_var($_POST['reg_email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['email'] = $_POST['reg_email'];
        header("Location: ../auth/register.php");
        exit();
    } else {
        $error = "Введите корректный email!";
    }
}

// Подключение к базе данных (PDO)
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';

function getPopularBooks($pdo, $limit, $offset) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM books ORDER BY popularity DESC LIMIT :limit OFFSET :offset");
        
        // Явное приведение типов для безопасности
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        // Логирование ошибки
        error_log("Ошибка при получении книг: " . $e->getMessage());
        return [];
    }
}

// Получаем книги из БД
$books = getPopularBooks($pdo, 8, 0); // Используем $pdo вместо $conn
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/../../css/carusel.css">

<!-- Карусель книг -->
<div class="book-carousel" id="book-carousel">
    <div class="carousel-header">
        <h2 class="carousel-title">Популярные книги</h2>
        <div class="carousel-controls">
            <button class="carousel-btn" id="carouselPrevBtn"><i class="fas fa-chevron-left"></i></button>
            <button class="carousel-btn" id="carouselNextBtn"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <div class="carousel-container" id="carouselContainer">
        <?php foreach ($books as $book): ?>
        <div class="book-card">
            <div class="book-badges">
                <span class="book-badge badge-popular">Только для вас!</span>
            </div>
            <div class="book-image-container">
                <img src="<?= '/../../pics/' . htmlspecialchars($book['image_path']) ?>" 
                    alt="<?= htmlspecialchars($book['title']) ?>" 
                    class="book-image">
                <div class="quick-view"><i class="fas fa-eye"></i> Быстрый просмотр</div>
            </div>
            <div class="book-details">
                <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
                <p class="book-author">Автор: <?= htmlspecialchars($book['author']) ?></p>
                <div class="book-meta">
                    <span class="rating"><i class="fas fa-star"></i> <?= number_format($book['rating'], 1) ?></span>
                    <span>(<?= $book['reviews_count'] ?> отзывов)</span>
                </div>
                <div class="price-container">
                    <div>
                        <?php if ($book['old_price']): ?>
                            <span class="old-price"><?= number_format($book['old_price'], 0, '', ' ') ?> сомони</span>
                        <?php endif; ?>
                        <span class="book-price"><?= number_format($book['price'], 0, '', ' ') ?> сомони</span>
                    </div>
                    <button class="add-to-cart" data-book-id="<?= $book['id'] ?>">
                        <i class="fas fa-shopping-cart"></i> Купить
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination" id="carouselPagination">
        <!-- Индикаторы будут добавлены через JS -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carouselContainer = document.getElementById('carouselContainer');
    const prevBtn = document.getElementById('carouselPrevBtn');
    const nextBtn = document.getElementById('carouselNextBtn');
    const pagination = document.getElementById('carouselPagination');
    const bookCards = document.querySelectorAll('.book-card');
    
    // Количество книг на странице (адаптивно)
    const booksPerPage = window.innerWidth < 768 ? 1 : 
                        window.innerWidth < 992 ? 2 : 
                        window.innerWidth < 1200 ? 3 : 4;
    
    const pageCount = Math.ceil(bookCards.length / booksPerPage);
    
    // Создаем индикаторы
    for (let i = 0; i < pageCount; i++) {
        const indicator = document.createElement('div');
        indicator.className = `page-indicator ${i === 0 ? 'active' : ''}`;
        indicator.addEventListener('click', () => goToPage(i));
        pagination.appendChild(indicator);
    }
    
    // Функция перехода к странице
    function goToPage(page) {
        const cardWidth = bookCards[0].offsetWidth + 30; // Ширина карточки + отступ
        carouselContainer.scrollTo({
            left: page * cardWidth * booksPerPage,
            behavior: 'smooth'
        });
        updateIndicators(page);
    }
    
    // Обновление индикаторов
    function updateIndicators(currentPage) {
        document.querySelectorAll('.page-indicator').forEach((ind, index) => {
            ind.classList.toggle('active', index === currentPage);
        });
    }
    
    // Обработчики кнопок
    prevBtn.addEventListener('click', () => {
        const scrollPos = carouselContainer.scrollLeft;
        const cardWidth = bookCards[0].offsetWidth + 30;
        const currentPage = Math.round(scrollPos / (cardWidth * booksPerPage));
        
        if (currentPage > 0) {
            goToPage(currentPage - 1);
        }
    });
    
    nextBtn.addEventListener('click', () => {
        const scrollPos = carouselContainer.scrollLeft;
        const cardWidth = bookCards[0].offsetWidth + 30;
        const currentPage = Math.round(scrollPos / (cardWidth * booksPerPage));
        
        if (currentPage < pageCount - 1) {
            goToPage(currentPage + 1);
        }
    });
    
    // Автопрокрутка
    let autoScroll = setInterval(() => {
        const scrollPos = carouselContainer.scrollLeft;
        const cardWidth = bookCards[0].offsetWidth + 30;
        const currentPage = Math.round(scrollPos / (cardWidth * booksPerPage));
        
        if (currentPage < pageCount - 1) {
            goToPage(currentPage + 1);
        } else {
            goToPage(0);
        }
    }, 5000);
    
    // Пауза при наведении
    carouselContainer.addEventListener('mouseenter', () => clearInterval(autoScroll));
    carouselContainer.addEventListener('mouseleave', () => {
        autoScroll = setInterval(() => {
            const scrollPos = carouselContainer.scrollLeft;
            const cardWidth = bookCards[0].offsetWidth + 30;
            const currentPage = Math.round(scrollPos / (cardWidth * booksPerPage));
            
            if (currentPage < pageCount - 1) {
                goToPage(currentPage + 1);
            } else {
                goToPage(0);
            }
        }, 5000);
    });
    
    // Обработчик для кнопки "Купить"
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookId = this.getAttribute('data-book-id');
            // Здесь можно добавить AJAX-запрос для добавления в корзину
            console.log('Добавить в корзину книгу с ID:', bookId);
            
            // Анимация добавления
            this.innerHTML = '<i class="fas fa-check"></i> Добавлено';
            this.style.backgroundColor = '#28a745';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-shopping-cart"></i> Купить';
                this.style.backgroundColor = '';
            }, 2000);
        });
    });
});

// В разделе <script> добавьте этот код
document.querySelectorAll('.quick-view').forEach(btn => {
    btn.addEventListener('click', function() {
        // Находим ID книги из ближайшей карточки
        const bookCard = this.closest('.book-card');
        const bookId = bookCard.querySelector('.add-to-cart').getAttribute('data-book-id');
        
        // Здесь можно:
        // 1. Открыть модальное окно с информацией о книге
        openQuickViewModal(bookId);
        
        // ИЛИ 2. Перейти на страницу книги
        // window.location.href = `/book/details.php?id=${bookId}`;
    });
});

// Функция для открытия модального окна
function openQuickViewModal(bookId) {
    // Пример с использованием Bootstrap модального окна
    fetch(`<?php $_SERVER['DOCUMENT_ROOT'] . '/quick_view.php?id=${bookId}?>)
        .then(response => response.text())
        .then(html => {
            // Создаем модальное окно
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'quickViewModal';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Быстрый просмотр</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ${html}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                            <form action="<?php $_SERVER['DOCUMENT_ROOT'] . '/catalog/book_details.php' ?>" method="POST" class="d-inline">
                                <input type="hidden" name="book_id" value="${bookId}">
                                <button type="submit" class="btn btn-primary">Подробнее</button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            
            // Удаляем модальное окно после закрытия
            modal.addEventListener('hidden.bs.modal', () => {
                document.body.removeChild(modal);
            });
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Не удалось загрузить информацию о книге');
        });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>