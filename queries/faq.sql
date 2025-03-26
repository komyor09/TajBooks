CREATE TABLE faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    total INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO faq (question, answer) VALUES
('Как долго длится доставка книг?', 'Самолётом 3-4 дня, машиной 10+ дней.'),
('Какие способы оплаты доступны?', 'Мы принимаем банковские карты (Visa, MasterCard), электронные кошельки (PayPal, Stripe) и оплату при доставке.'),
('Можно ли вернуть или обменять книгу?', 'Физические книги можно вернуть или обменять в течение 14 дней, если они не повреждены. Электронные книги возврату не подлежат.'),
('Как использовать купоны и скидки?', 'При оформлении заказа введите код купона в специальное поле, и скидка применится автоматически.'),
('Как скачать купленную электронную книгу?', 'После оплаты книга появится в разделе "Мои покупки" в личном кабинете, где её можно скачать в удобном формате (PDF, EPUB, MOBI).');
