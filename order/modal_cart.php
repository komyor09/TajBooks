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
