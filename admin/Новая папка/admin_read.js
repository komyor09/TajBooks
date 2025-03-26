document.addEventListener("DOMContentLoaded", function () {
    const booksTable = document.getElementById("books-table").getElementsByTagName("tbody")[0];
    const editModal = document.getElementById("edit-modal");
    const closeModal = document.querySelector(".close");
    const editForm = document.getElementById("edit-form");

    // Загрузка книг при загрузке страницы
    loadBooks();

    // Открытие модального окна для редактирования
    booksTable.addEventListener("click", function (event) {
        if (event.target.classList.contains("edit-btn")) {
            const bookId = event.target.getAttribute("data-id");
            openEditModal(bookId);
        }
    });

    // Закрытие модального окна
    closeModal.addEventListener("click", function () {
        editModal.style.display = "none";
    });

    // Обработка отправки формы редактирования
    editForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const bookId = editForm.getAttribute("data-id");
        updateBook(bookId);
    });

    // Функция для загрузки книг
    async function loadBooks() {
        const response = await fetch("get_books.php");
        const books = await response.json();
        booksTable.innerHTML = books.map(book => `
            <tr>
                <td>${book.title}</td>
                <td>${book.author}</td>
                <td>${book.price}</td>
                <td>${book.genre}</td>
                <td>${book.created_at}</td>
                <td>
                    <button class="edit-btn" data-id="${book.id}">Редактировать</button>
                    <button class="delete-btn" data-id="${book.id}">Удалить</button>
                </td>
            </tr>
        `).join("");
    }

    // Функция для открытия модального окна
    async function openEditModal(bookId) {
        const response = await fetch(`get_book.php?id=${bookId}`);
        const book = await response.json();
        document.getElementById("edit-title").value = book.title;
        document.getElementById("edit-author").value = book.author;
        document.getElementById("edit-price").value = book.price;
        document.getElementById("edit-description").value = book.description;
        document.getElementById("edit-genre").value = book.genre;
        document.getElementById("edit-createdAt").value = book.created_at;
        editForm.setAttribute("data-id", bookId);
        editModal.style.display = "block";
    }

    // Функция для обновления книги
    async function updateBook(bookId) {
        const formData = new FormData(editForm);
        const response = await fetch(`update_book.php?id=${bookId}`, {
            method: "POST",
            body: formData
        });
        if (response.ok) {
            editModal.style.display = "none";
            loadBooks();
        }
    }
});
async function loadBooks() {
    const response = await fetch("get_books.php"); // Отправляем запрос на сервер
    const books = await response.json(); // Получаем ответ в формате JSON
    // Обновляем таблицу с книгами
    const booksTable = document.getElementById("books-table").getElementsByTagName("tbody")[0];
    booksTable.innerHTML = books.map(book => `
        <tr>
            <td>${book.title}</td>
            <td>${book.author}</td>
            <td>${book.price}</td>
            <td>${book.genre}</td>
            <td>${book.created_at}</td>
            <td>
                <button class="edit-btn" data-id="${book.id}">Редактировать</button>
                <button class="delete-btn" data-id="${book.id}">Удалить</button>
            </td>
        </tr>
    `).join("");
}