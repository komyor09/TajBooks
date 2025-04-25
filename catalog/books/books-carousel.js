document.addEventListener('DOMContentLoaded', function() {
    const carouselContainer = document.getElementById('carouselContainer');
    const prevBtn = document.getElementById('carouselPrevBtn');
    const nextBtn = document.getElementById('carouselNextBtn');
    const pagination = document.getElementById('carouselPagination');
    const bookCards = document.querySelectorAll('.book-card');
    
    // Количество книг на странице (адаптивно)
    let booksPerPage = 4;
    if (window.innerWidth < 768) booksPerPage = 1;
    else if (window.innerWidth < 992) booksPerPage = 2;
    else if (window.innerWidth < 1200) booksPerPage = 3;
    
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
        const cardWidth = bookCards[0].offsetWidth + 24; // Ширина карточки + отступ
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
        const cardWidth = bookCards[0].offsetWidth + 24;
        const currentPage = Math.round(scrollPos / (cardWidth * booksPerPage));
        
        if (currentPage > 0) {
            goToPage(currentPage - 1);
        } else {
            goToPage(pageCount - 1); // Циклическая навигация
        }
    });
    
    nextBtn.addEventListener('click', () => {
        const scrollPos = carouselContainer.scrollLeft;
        const cardWidth = bookCards[0].offsetWidth + 24;
        const currentPage = Math.round(scrollPos / (cardWidth * booksPerPage));
        
        if (currentPage < pageCount - 1) {
            goToPage(currentPage + 1);
        } else {
            goToPage(0); // Циклическая навигация
        }
    });
    
    // Автопрокрутка
    let autoScroll = setInterval(() => {
        const scrollPos = carouselContainer.scrollLeft;
        const cardWidth = bookCards[0].offsetWidth + 24;
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
            const cardWidth = bookCards[0].offsetWidth + 24;
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
            
            // Анимация добавления
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> Добавлено';
            this.style.backgroundColor = '#00b894';
            this.classList.add('animate__animated', 'animate__pulse');
            
            // Отправка AJAX запроса
            fetch('/cart/add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ book_id: bookId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем счетчик корзины в шапке
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                        cartCount.classList.add('animate__bounce');
                        setTimeout(() => {
                            cartCount.classList.remove('animate__bounce');
                        }, 1000);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
            
            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.style.backgroundColor = '';
                this.classList.remove('animate__pulse');
            }, 2000);
        });
    });
    
    // Обработчик для быстрого просмотра
    document.querySelectorAll('.quick-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookId = this.getAttribute('data-book-id');
            openQuickViewModal(bookId);
        });
    });
    
    // Адаптация при изменении размера окна
    window.addEventListener('resize', function() {
        // Обновляем количество книг на странице
        if (window.innerWidth < 768) booksPerPage = 1;
        else if (window.innerWidth < 992) booksPerPage = 2;
        else if (window.innerWidth < 1200) booksPerPage = 3;
        else booksPerPage = 4;
        
        // Переходим на первую страницу
        goToPage(0);
    });
});

// Функция для открытия модального окна быстрого просмотра
function openQuickViewModal(bookId) {
    fetch(`/quick_view.php?id=${bookId}`)
        .then(response => response.text())
        .then(html => {
            // Создаем модальное окно с улучшенным дизайном
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'quickViewModal';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
                        <div class="modal-header" style="background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%); color: white; border: none;">
                            <h5 class="modal-title"><i class="fas fa-book-open me-2"></i> Быстрый просмотр</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row">
                                <div class="col-md-5">
                                    <img src="/pics/${bookId}.jpg" class="img-fluid rounded-3 shadow" alt="Обложка книги" style="max-height: 400px; object-fit: contain;">
                                </div>
                                <div class="col-md-7">
                                    ${html}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="background: #f8f9fa; border-top: none;">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Закрыть
                            </button>
                            <a href="/catalog/book_details.php?id=${bookId}" class="btn btn-primary">
                                <i class="fas fa-info-circle me-2"></i>Подробнее
                            </a>
                            <button class="btn btn-success add-to-cart-from-modal" data-book-id="${bookId}">
                                <i class="fas fa-shopping-cart me-2"></i>В корзину
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            
            // Обработчик для кнопки добавления в корзину из модального окна
            modal.querySelector('.add-to-cart-from-modal')?.addEventListener('click', function() {
                const bookId = this.getAttribute('data-book-id');
                // Здесь можно добавить логику добавления в корзину
                this.innerHTML = '<i class="fas fa-check me-2"></i>Добавлено';
                this.classList.add('btn-success');
                setTimeout(() => {
                    modalInstance.hide();
                }, 1500);
            });
            
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