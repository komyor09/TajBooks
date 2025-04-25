<?php
session_start();

if (isset($_SESSION['message'])): ?>
        <div class="iphone-notification">
            <div class="notification-content">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['message']) ?></span>
            </div>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>