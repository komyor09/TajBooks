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

function getPopularBooks($conn, $limit, $offset) {
    $sql = "SELECT * FROM books ORDER BY popularity DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $sql);

    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    return $books;
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TajBooks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <header class="bg-dark text-white py-3">
        <nav class="d-flex justify-content-between align-items-center mx-5 my-1">
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
<div class="hero">
    <div>
        <h1 class="display-1">Добро пожаловать в TajBooks</h1>
        <p class="display-5">Погрузитесь в мир удивительных книг!</p>
        <a href="#carouselExample" class="btn btn-primary">Погнали</a>
    </div>
</div>

    <div class="container my-5 py-5" id='carouselExample'>
    <h1 class="text-center"> Популярние </h1>
        <div class="carousel-container my-5">
            <!-- Кнопка слайдера влево -->
            <button class="btn carousel-btn carousel-btn-left" id="prevBtn">←</button>

            <!-- Слайдер книг -->
            <div class="book-slider" id="bookSlider">
                <?php
                require_once('config/db.php');
                $limit = 5;
                $offset = 0;
                $books = getPopularBooks($pdo, $limit, $offset);
                ?>
            </div>
            <button class="btn carousel-btn carousel-btn-right" id="nextBtn">→</button>
        </div>

        </div>
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

<div class="container py-5 bg-light rounded shadow">
    <h2 class="text-center mb-4">Подпишитесь на наши акции!</h2>
        <?php if (!empty($error)): ?>
            <p class="text-danger text-center"><?= $error ?></p>
        <?php endif; ?>
        <form class="a-flex" action="" method="POST">
            <div class="d-flex justify-content-center align-items-center">
                <input type="email" class="form-control me-3 py-2" name="reg_email" placeholder="Введите ваш email" required value="<?= htmlspecialchars($email) ?>" style="border-radius: 25px; width: 400px;">
            </div>
            <p></p>
            <div class="d-flex justify-content-center align-items-center">
                <button class="btn btn-primary px-4 py-2 rounded-pill">Подписаться</button>
            </div>
        </form>
        <!-- <?php if (!empty($email)): ?>
            <p class="text-success text-center mt-3">Ваш email сохранён в сессии: <?= htmlspecialchars($email) ?></p>
        <?php endif; ?>  -->

    <p class="text-center mt-3 text-muted">Получайте уведомления о новых акциях и скидках на книги!</p>
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
    <script>
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const bookSlider = document.getElementById('bookSlider');

        let slideIndex = 0;
        let totalBooks = 10; // Количество доступных книг (можно сделать динамическим)
        let limit = 4;
        let offset = 0;

        const fetchBooks = (offset, limit) => {
            fetch(`getBooks.php?offset=${offset}&limit=${limit}`)
                .then(response => response.json())
                .then(data => {
                    // Логируем данные для проверки
                    console.log('Books received:', data);

                    // Проверяем на ошибку в ответе
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }

                    // Очистим текущий слайдер
                    bookSlider.innerHTML = '';

                    // Выводим новые книги
                    data.books.forEach(book => {
                            bookSlider.innerHTML += `
                            <div class="book-card text-center" style="width: 300px;">
                                <img src="../pics/${book.image_path}" alt="book-image" style=" width: 200px; height: 400px;">
                                <div class="book-info">
                                    <h5>${book.title}</h5>
                                    <p>${book.author}</p>
                                    <p><strong>${book.price} руб.</strong></p>
                                </div>
                                <p></p>
                                <div class="rows " style=" display: flex; gap: 10px; align-items: center; justify-content: center; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%;">
                                    <form action="../order/add_to_cart.php" method="POST">
                                        <input type="hidden" name="book_id" value="${book.id}">
                                        <button class="btn btn-success"><i class="fa fa-shopping-cart"></i> В корзину</button>
                                    </form>
                                    <form action="../catalog/book_details.php" method="POST">
                                        <input type="hidden" name="book_id" value="${book.id}">
                                        <button type="submit" class="btn btn-success"><i class="fa fa-info"></i> Подробнее</button>
                                    </form>
                                </div>
                                </form>
                            </div>`;
                        });
                })
                .catch(error => {
                    console.error('Error fetching books:', error);
                });
        };

        // Изначально загрузим первые 5 книг
        fetchBooks(offset, limit);

        // Функция для сдвига слайдера влево
        prevBtn.addEventListener('click', () => {
            if (offset > 0) {
                offset -= limit;
                slideIndex--;
                updateSlider();
            }
        });

        // Функция для сдвига слайдера вправо
        nextBtn.addEventListener('click', () => {
            if (offset + limit < totalBooks) {
                offset += limit;
                slideIndex++;
                updateSlider();
            }
        });

        // Функция для обновления слайдера
        function updateSlider() {
            const offsetValue = -slideIndex * 300; // 220px - ширина каждой карточки с отступами
            bookSlider.style.transform = `translateX(${offsetValue}px)`;
            
            // Загружаем новые книги
            fetchBooks(offset, limit);
        }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
</body>
</html>
