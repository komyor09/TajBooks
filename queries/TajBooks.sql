USE `bookstore`;

/*Table structure for table `books` */

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

/*Table structure for table `Carts` */

DROP TABLE IF EXISTS `Carts`;

CREATE TABLE `Carts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT DEFAULT NULL,
  `book_id` INT DEFAULT NULL,
  `quantity` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`),
  CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
) ENGINE=INNODB AUTO_INCREMENT=21;

/*Table structure for table `Order_Items` */

DROP TABLE IF EXISTS `Order_Items`;

CREATE TABLE `Order_Items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT DEFAULT NULL,
  `book_id` INT DEFAULT NULL,
  `quantity` INT DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
) ENGINE=INNODB AUTO_INCREMENT=11;

/*Table structure for table `Orders` */

DROP TABLE IF EXISTS `Orders`;

CREATE TABLE `Orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT DEFAULT NULL,
  `status` ENUM('new','processed','shipped','delivered','cancelled') DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT NULL,
  `createdAt` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`)
) ENGINE=INNODB AUTO_INCREMENT=20;

/*Table structure for table `Reviews` */

DROP TABLE IF EXISTS `Reviews`;

CREATE TABLE `Reviews` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `book_id` INT DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `rating` INT DEFAULT NULL,
  `comment` TEXT,
  `createdAt` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`)
) ENGINE=INNODB AUTO_INCREMENT=11;

/*Table structure for table `Users` */

DROP TABLE IF EXISTS `Users`;

CREATE TABLE `Users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) DEFAULT NULL,
  `email` VARCHAR(45) DEFAULT NULL,
  `password` VARCHAR(45) DEFAULT NULL,
  `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
  `createdAt` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=INNODB AUTO_INCREMENT=16;

ALTER TABLE `Carts`
ADD UNIQUE KEY `unique_cart` (`user_id`, `book_id`);