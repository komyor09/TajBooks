
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel" style="color: black;">Вход в TajBooks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div id="login-message" class="alert d-none"></div>
                <form id="loginForm" action="auth/login.php" method="POST">
                    <label for="identifier" style="color: black;">Email или логин:</label>
                    <input type="text" id="identifier" name="identifier" class="form-control mb-3" required>

                    <label for="password" style="color: black;">Пароль:</label>
                    <input type="password" id="password" name="password" class="form-control mb-3" required>

                    <button type="submit" class="btn btn-primary w-100">Войти</button>
                </form>
            </div>
        </div>
    </div>
</div>
