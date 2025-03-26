CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL CHECK (price >= 0),
    description TEXT NOT NULL,
    genre VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    image_path VARCHAR(255),
    popularity INT DEFAULT 0,
    publisher VARCHAR(255),
    YEAR INT CHECK (YEAR >= 1000 AND YEAR <= YEAR(NOW())),
    LANGUAGE VARCHAR(50) NOT NULL,
    FORMAT ENUM('бумажная', 'электронная', 'аудиокнига') NOT NULL,
    rating DECIMAL(2,1) CHECK (rating >= 0 AND rating <= 5),
    availability ENUM('в наличии', 'нет в наличии') NOT NULL,
    views INT DEFAULT 0
);
