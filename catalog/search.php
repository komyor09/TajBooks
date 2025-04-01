<div class="row">
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
                <div class="col-md-3">
                    <label class="form-label">Рейтинг:</label>
                    <input type="range" name="rating" min="1" max="5" value="<?= $_GET['rating'] ?? 3 ?>" class="form-range">
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 mt-3"><i class="fa fa-filter"></i> Фильтровать</button>
        </form>
    </div>