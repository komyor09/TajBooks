<div class="row">
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
        </div>