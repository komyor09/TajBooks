document.addEventListener('DOMContentLoaded', function () {
    const carouselContainer = document.getElementById('carouselContainer-2');
    const prevBtn = document.getElementById('carouselPrevBtn-2');
    const nextBtn = document.getElementById('carouselNextBtn-2');
    const pagination = document.getElementById('carouselPagination-2');
    const bookCards = document.querySelectorAll('#carouselContainer-2 .book-card');

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

    function goToPage(page) {
        const cardWidth = bookCards[0].offsetWidth + 30;
        carouselContainer.scrollTo({
            left: page * cardWidth * booksPerPage,
            behavior: 'smooth'
        });
        updateIndicators(page);
    }

    function updateIndicators(currentPage) {
        document.querySelectorAll('#carouselPagination-2 .page-indicator').forEach((ind, index) => {
            ind.classList.toggle('active', index === currentPage);
        });
    }

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

    // Обработчик "Купить"
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            const bookId = this.getAttribute('data-book-id');
            const originalContent = this.innerHTML;
            const originalBg = this.style.background;

            // Визуальная обратная связь
            this.innerHTML = '<i class="fas fa-check"></i>';
            this.style.background = '#2ecc71';

            // Отправка данных на сервер
            fetch('/order/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `book_id=${bookId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.style.background = originalBg;
                    }, 2000);
                } else {
                    this.innerHTML = '<i class="fas fa-times"></i>';
                    this.style.background = '#e74c3c';
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.style.background = originalBg;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.innerHTML = '<i class="fas fa-times"></i>';
                setTimeout(() => {
                    this.innerHTML = originalContent;
                    this.style.background = originalBg;
                }, 2000);
            });
        });
    });

    // Быстрый просмотр
    document.querySelectorAll('.quick-view').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const bookCard = this.closest('.book-card');
            const bookId = bookCard.querySelector('.add-to-cart').getAttribute('data-book-id');
            
            fetch(`/catalog/books/quick_view.php?id=${bookId}`)
                .then(response => response.text())
                .then(html => {
                    const modal = document.createElement('div');
                    modal.className = 'modal fade';
                    modal.id = 'quickViewModal';
                    modal.innerHTML = `
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Быстрый просмотр</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ${html}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Закрыть</button>
                                    <a href="/catalog/book_details.php?id=${bookId}" class="btn btn-primary">Подробнее</a>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.body.appendChild(modal);
                    const modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                    
                    modal.addEventListener('hidden.bs.modal', () => {
                        document.body.removeChild(modal);
                    });
                });
        });
    });
});