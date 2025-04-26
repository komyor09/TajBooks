<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TajBooks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/iphone-notification.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php
    $role = $_SESSION['role'] ?? '';
    if ($role === 'admin') {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/admin.php');
    } else {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/client.php');
    }
    require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/iphone-notification.php');
    ?>
<div class="hero">
    <div>
        <h1 class="display-1">Добро пожаловать в TajBooks</h1>
        <p class="display-5">Погрузитесь в мир удивительных книг!</p>
        <a href="catalog/catalog.php" class="btn btn-primary">Погнали</a>
    </div>
</div>

    <div>
        <?php  include __DIR__ . '/catalog/books/popular_books_carousel.php'; ?>
    </div>
    <div class="counter my-4 py-6">
        <h2>Более <span id="clients">500</span> довольных клиентов — мы гордимся довериями!</h2>
        <h3><span id="books">1500</span> книг в наличии, с каждым днем выбираем только лучшие!</h3>
        <p>Присоединяйтесь к нам и почувствуйте разницу в качестве обслуживания и большом выборе!</p>
    </div>


<div class="container text-center my-5">
    <h2 class="display-4 mb-4">Почему выбирают нас?</h2>
    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="feature-box p-5 shadow rounded">
                <h4 class="text-primary">📚 Огромный ассортимент</h4>
                <p>Мы предлагаем более 1500 книг в различных жанрах, от классики до новинок!</p>
                <img src="pics/assortment.jpg" alt="Assortment" class="img-fluid mt-4">
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-box p-5 shadow rounded">
                <h4 class="text-success">🚚 Быстрая доставка</h4>
                <p>Мы гарантируем быструю доставку по всему Таджикистану, прямо до вашей двери!</p>
                <img src="pics/Delivery.jpg" alt="Delivery" class="img-fluid mt-4">
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-box p-5 shadow rounded">
                <h4 class="text-warning">💸 Выгодные акции</h4>
                <p>Каждый месяц мы предлагаем скидки до 30%! Не пропустите наши предложения и радуйте родных!</p>
                <img src="pics/discount.jpg" alt="Discount" class="img-fluid mt-4">
            </div>
        </div>
    </div>
</div>

<p class="text-center mt-5"></p>

<footer class="bg-dark text-white py-3">
        <nav class="d-flex justify-content-between align-items-center mx-5 ">
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
                <i class="fas fa-instagram me-2"></i>Инстаграм     
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

    <div id="toTop" onclick="scrollToTop()">⬆️</div>

    <script>
        window.onscroll = function() {
            let toTop = document.getElementById("toTop");
            if (window.pageYOffset > 300) {
                toTop.style.display = "block";
            } else {
                toTop.style.display = "none";
            }
        };
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
    <script>
        ScrollReveal({
            reset: false, // Если true — анимация повторяется при каждом скролле
            distance: '50px', // Расстояние появления
            duration: 1500, // Длительность анимации (в миллисекундах)
            delay: 200, // Задержка
        });

        ScrollReveal().reveal('.hero', { origin: 'top' }); // Анимация сверху
        ScrollReveal().reveal('.carousel', { origin: 'bottom', delay: 300 });
        ScrollReveal().reveal('.counter', { origin: 'left', delay: 400 });
        ScrollReveal().reveal('.container', { origin: 'right', delay: 500 });
        ScrollReveal().reveal('.col-md-4', { origin: 'bottom', interval: 200 }); // Для фишек магазина
    </script>
    <script type="text/javascript">
        // Анимация чисел
        const clientsElement = document.getElementById("clients");
        const booksElement = document.getElementById("books");

        let clientsCount = 0;
        let booksCount = 0;

        function updateCounter() { 
            if (clientsCount < 500) {
                clientsCount = clientsCount + 5;
                clientsElement.textContent = clientsCount;
            }
            if (booksCount < 1500) {
                booksCount = booksCount + 10;
                booksElement.textContent = booksCount;
            }
        }

        let interval = setInterval(updateCounter, 100);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>


</body>
</html>
