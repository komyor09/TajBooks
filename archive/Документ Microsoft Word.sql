/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 8.0.30 : Database - TajBooks
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`TajBooks` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `TajBooks`;

/*Table structure for table `Books` */

DROP TABLE IF EXISTS `Books`;

CREATE TABLE `Books` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Title` varchar(35) DEFAULT NULL,
  `Author` varchar(45) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `description` text,
  `Image` varchar(50) DEFAULT NULL,
  `Genre` varchar(35) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `Books` */

insert  into `Books`(`Id`,`Title`,`Author`,`Price`,`description`,`Image`,`Genre`,`createdAt`) values 
(1,'Война и мир','Лев Толстой',29.99,'Эпический роман о войне и жизни в России.','war_and_peace.jpg','Классика','2025-02-22 12:36:52'),
(2,'1984','Джордж Оруэлл',19.99,'Антиутопия о тоталитарном обществе.','1984.jpg','Антиутопия','2025-02-22 12:36:52'),
(3,'Мастер и Маргарита','Михаил Булгаков',24.99,'Мистический роман о дьяволе в Москве.','master_and_margarita.jpg','Мистика','2025-02-22 12:36:52'),
(4,'Преступление и наказание','Федор Достоевский',22.99,'Роман о преступлении и его последствиях.','crime_and_punishment.jpg','Классика','2025-02-22 12:36:52'),
(5,'Гарри Поттер и философский камень','Дж. К. Роулинг',15.99,'Первая книга о приключениях Гарри Поттера.','harry_potter_1.jpg','Фэнтези','2025-02-22 12:36:52'),
(6,'Маленький принц','Антуан де Сент-Экзюпери',12.99,'Философская сказка для детей и взрослых.','little_prince.jpg','Детская литература','2025-02-22 12:36:52'),
(7,'Улисс','Джеймс Джойс',34.99,'Сложный и многогранный роман.','ulysses.jpg','Модернизм','2025-02-22 12:36:52'),
(8,'Анна Каренина','Лев Толстой',27.99,'Трагическая история любви.','anna_karenina.jpg','Классика','2025-02-22 12:36:52'),
(9,'Сто лет одиночества','Габриэль Гарсиа Маркес',21.99,'Магический реализм в истории семьи Буэндиа.','100_years_of_solitude.jpg','Магический реализм','2025-02-22 12:36:52'),
(10,'Властелин колец','Дж. Р. Р. Толкин',39.99,'Эпическая трилогия о Средиземье.','lord_of_the_rings.jpg','Фэнтези','2025-02-22 12:36:52'),
(11,'Гордость и предубеждение','Джейн Остин',18.99,'Роман о любви и социальных предрассудках.','pride_and_prejudice.jpg','Роман','2025-02-22 12:36:52'),
(12,'Мертвые души','Николай Гоголь',16.99,'Сатирический роман о русском обществе.','dead_souls.jpg','Классика','2025-02-22 12:36:52'),
(13,'Братья Карамазовы','Федор Достоевский',29.99,'Философский роман о вере и морали.','brothers_karamazov.jpg','Классика','2025-02-22 12:36:52'),
(14,'Тень горы','Грегори Дэвид Робертс',26.99,'Продолжение романа \"Шантарам\".','shadow_of_the_mountain.jpg','Приключения','2025-02-22 12:36:52'),
(15,'Шантарам','Грегори Дэвид Робертс',25.99,'История беглого преступника в Индии.','shantaram.jpg','Приключения','2025-02-22 12:36:52');

/*Table structure for table `Carts` */

DROP TABLE IF EXISTS `Carts`;

CREATE TABLE `Carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`Id`),
  CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `Books` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `Carts` */

insert  into `Carts`(`id`,`user_id`,`book_id`,`quantity`) values 
(1,1,2,1),
(2,1,5,2),
(3,2,3,1),
(4,2,1,3),
(5,3,6,1),
(6,3,4,2),
(7,4,7,1),
(8,5,9,1),
(9,5,8,3),
(10,6,10,2),
(11,1,5,1),
(12,1,5,1),
(13,1,5,1),
(14,1,5,1),
(15,1,5,1),
(16,1,5,1),
(17,1,5,1),
(18,1,5,1),
(19,1,5,1),
(20,1,5,1);

/*Table structure for table `Order_Items` */

DROP TABLE IF EXISTS `Order_Items`;

CREATE TABLE `Order_Items` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `order_id` (`order_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`Id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `Books` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `Order_Items` */

insert  into `Order_Items`(`Id`,`order_id`,`book_id`,`quantity`,`price`) values 
(1,1,1,2,29.99),
(2,1,2,1,19.99),
(3,2,3,3,24.99),
(4,3,4,1,22.99),
(5,4,5,2,15.99),
(6,5,6,1,12.99),
(7,6,7,4,34.99),
(8,7,8,1,27.99),
(9,8,9,2,21.99),
(10,9,10,1,39.99);

/*Table structure for table `Orders` */

DROP TABLE IF EXISTS `Orders`;

CREATE TABLE `Orders` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `User_id` int DEFAULT NULL,
  `status` enum('new','processed','shipped','delivered','cancelled') DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  KEY `User_id` (`User_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `Users` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `Orders` */

insert  into `Orders`(`Id`,`User_id`,`status`,`Price`,`CreatedAt`,`Updated_at`) values 
(1,1,'new',49.99,'2025-02-22 12:42:23','2025-02-22 12:42:23'),
(2,2,'new',99.99,'2025-02-22 12:42:23','2025-02-22 12:42:23'),
(3,3,'shipped',29.99,'2025-02-22 12:42:23','2025-02-22 12:42:23'),
(4,4,'delivered',149.99,'2025-02-22 12:42:23','2025-02-22 12:42:23'),
(5,5,'cancelled',19.99,'2025-02-22 12:42:23','2025-02-22 12:42:23'),
(6,6,'new',79.99,'2025-02-22 12:42:23','2025-02-22 12:42:23'),
(7,7,'processed',59.99,'2025-02-22 12:42:23','2025-02-22 12:42:23'),
(8,8,'shipped',39.99,'2025-02-22 12:42:23','2025-02-22 12:42:23'),
(9,9,'delivered',199.99,'2025-02-22 12:42:23','2025-02-22 12:42:23'),
(10,10,'shipped',9.99,'2025-02-22 12:42:23','2025-03-03 11:02:04'),
(11,1,'new',NULL,'2025-03-03 10:11:13','2025-03-03 10:11:13'),
(12,1,'new',NULL,'2025-03-03 10:11:55','2025-03-03 10:11:55'),
(13,1,'new',NULL,'2025-03-03 10:24:34','2025-03-03 10:24:34'),
(14,1,'new',NULL,'2025-03-03 10:25:14','2025-03-03 10:25:14'),
(15,1,'new',NULL,'2025-03-03 10:26:50','2025-03-03 10:26:50'),
(16,1,'new',NULL,'2025-03-03 10:27:41','2025-03-03 10:27:41'),
(17,1,'new',NULL,'2025-03-03 10:27:59','2025-03-03 10:27:59'),
(18,1,'new',NULL,'2025-03-03 10:59:36','2025-03-03 10:59:36'),
(19,1,'new',NULL,'2025-03-03 11:02:04','2025-03-03 11:02:04');

/*Table structure for table `Reviews` */

DROP TABLE IF EXISTS `Reviews`;

CREATE TABLE `Reviews` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `book_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `Books` (`Id`),
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `Reviews` */

insert  into `Reviews`(`Id`,`book_id`,`user_id`,`rating`,`comment`,`createdAt`) values 
(1,1,1,5,'Отличная книга, рекомендую!','2025-02-22 12:56:28'),
(2,2,2,4,'Интересная антиутопия, но немного мрачно.','2025-02-22 12:56:28'),
(3,3,3,5,'Шедевр! Читал на одном дыхании.','2025-02-22 12:56:28'),
(4,4,4,3,'Хорошая книга, но сложновато для восприятия.','2025-02-22 12:56:28'),
(5,5,5,5,'Любимая книга детства!','2025-02-22 12:56:28'),
(6,6,6,4,'Очень трогательная и философская.','2025-02-22 12:56:28'),
(7,7,7,2,'Слишком сложно, не смог дочитать.','2025-02-22 12:56:28'),
(8,8,8,5,'Классика, которую должен прочитать каждый.','2025-02-22 12:56:28'),
(9,9,9,4,'Интересный сюжет, но концовка немного затянута.','2025-02-22 12:56:28'),
(10,10,10,5,'Великолепная книга, перечитываю уже в третий раз.','2025-02-22 12:56:28');

/*Table structure for table `Users` */

DROP TABLE IF EXISTS `Users`;

CREATE TABLE `Users` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `E-mail` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `E-mail` (`E-mail`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `Users` */

insert  into `Users`(`Id`,`name`,`E-mail`,`password`,`role`,`CreatedAt`) values 
(1,'Иван Иванов','ivan@example.com','hashed_password','user','2025-02-22 12:31:36'),
(2,'Петр Петров','petr@example.com','hashed_password2','user','2025-02-22 12:31:36'),
(3,'Анна Сидорова','anna@example.com','hashed_password3','user','2025-02-22 12:31:36'),
(4,'Мария Кузнецова','maria@example.com','hashed_password4','admin','2025-02-22 12:31:36'),
(5,'Сергей Васильев','sergey@example.com','hashed_password5','user','2025-02-22 12:31:36'),
(6,'Ольга Николаева','olga@example.com','hashed_password6','user','2025-02-22 12:31:36'),
(7,'Дмитрий Смирнов','dmitry@example.com','hashed_password7','user','2025-02-22 12:31:36'),
(8,'Елена Морозова','elena@example.com','hashed_password8','admin','2025-02-22 12:31:36'),
(9,'Алексей Павлов','alexey@example.com','hashed_password9','user','2025-02-22 12:31:36'),
(10,'Татьяна Федорова','tatiana@example.com','hashed_password10','user','2025-02-22 12:31:36'),
(11,'Николай Иванов','nikolay@example.com','hashed_password11','user','2025-02-22 12:31:36'),
(12,'Екатерина Соколова','ekaterina@example.com','hashed_password12','user','2025-02-22 12:31:36'),
(13,'Андрей Лебедев','andrey@example.com','hashed_password13','admin','2025-02-22 12:31:36'),
(14,'Виктория Козлова','viktorija@example.com','hashed_password14','user','2025-02-22 12:31:36'),
(15,'Артем Новиков','artem@example.com','hashed_password15','user','2025-02-22 12:31:36');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
