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
    <?php
    $role = $_SESSION['role'] ?? '';
    if ($role === 'admin') {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/admin.php');
    } else {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/client.php');
    }

    ?>
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

<?= require_once "footer.php"; ?>


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
            reset: false, 
            distance: '50px', 
            duration: 1500, 
            delay: 200, 
        });

        ScrollReveal().reveal('.hero', { origin: 'top' });
        ScrollReveal().reveal('.carousel', { origin: 'bottom', delay: 300 });
        ScrollReveal().reveal('.counter', { origin: 'left', delay: 400 });
        ScrollReveal().reveal('.container', { origin: 'right', delay: 500 });
        ScrollReveal().reveal('.col-md-4', { origin: 'bottom', interval: 200 }); 
    </script>
    <script type="text/javascript">
       
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
