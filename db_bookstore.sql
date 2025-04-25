/*
SQLyog Ultimate v13.1.1 (32 bit)
MySQL - 5.6.51-log : Database - BookStore
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`BookStore` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `BookStore`;

/*Table structure for table `activity_logs` */

DROP TABLE IF EXISTS `activity_logs`;

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_logs_ibfk_1` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `activity_logs` */

/*Table structure for table `authors` */

DROP TABLE IF EXISTS `authors`;

CREATE TABLE `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `authors` */

insert  into `authors`(`id`,`name`) values 
(0,'Нет'),
(1,'Лев Толстой');

/*Table structure for table `book` */

DROP TABLE IF EXISTS `book`;

CREATE TABLE `book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` int(11) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ISBN` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `page` int(4) NOT NULL DEFAULT '0',
  `year` int(4) DEFAULT '0',
  `language_id` int(11) DEFAULT NULL,
  `publisher_id` int(11) DEFAULT NULL,
  `format_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default.JPG',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `availability` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_books_price` (`price`),
  KEY `idx_books_year` (`year`),
  KEY `idx_books_author` (`author_id`),
  KEY `language_id` (`language_id`),
  KEY `publisher_id` (`publisher_id`),
  KEY `format_id` (`format_id`),
  CONSTRAINT `book_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `book_ibfk_3` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `book_ibfk_4` FOREIGN KEY (`format_id`) REFERENCES `formats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `book_ibfk_5` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `book` */

insert  into `book`(`id`,`title`,`author_id`,`description`,`ISBN`,`page`,`year`,`language_id`,`publisher_id`,`format_id`,`image_path`,`price`,`quantity`,`availability`,`created_at`) values 
(1,'Сорок правил любви',0,'Роман, который раскрывает философию суфизма через историю любви. Главная героиня — Эла — узнаёт о 40 правилах любви, которые изменяют её взгляд на жизнь.','0',0,NULL,0,0,3,'40_rule-of-love.png',60.00,0,0,'2025-03-06 11:31:02'),
(3,'Курс активного трейдера',0,'Практическое руководство по трейдингу, включающее стратегии торговли на фондовом рынке, управление рисками и психология трейдера.','0',0,NULL,0,4,1,'kurs-activnogo-treidera.jpg',80.00,0,0,'2025-03-06 11:31:02'),
(4,'50 великих книг по психологии',0,'Сборник обзоров на лучшие книги по психологии, охватывающий ключевые идеи и концепции, от классической психологии до современных исследований.','0',0,NULL,0,0,1,'50--great-books-psichology.png',345.00,0,0,'2025-03-06 11:31:02'),
(5,'Шантарам',0,'Эпический роман, основанный на реальных событиях, повествующий о беглом австралийском заключённом, который находит убежище в Индии и погружается в её криминальный мир.','0',0,NULL,0,3,1,'shantaram.jpg',130.00,0,0,'2025-03-06 11:31:02'),
(6,'НИ СЫ.',0,'Мотивационная книга, которая помогает преодолеть страхи и сомнения, научиться верить в себя и добиваться успеха.','0',0,NULL,0,0,1,'ni-si.jpg',500.00,0,0,'2025-03-06 11:31:02'),
(7,'Комплект Из 7 Книг Гарри Поттер в футляре',0,'Полный комплект книг о Гарри Поттере, рассказывающий историю мальчика-волшебника, который сражается со злом в мире магии.','0',0,NULL,0,0,1,'garry-potter-completed.jpg',200.00,0,0,'2025-03-06 11:31:02'),
(8,'Сказать жизни \"Да!\". Психолог в концлагере',0,'Автобиографическая книга австрийского психиатра, пережившего концлагерь, о поиске смысла жизни даже в самых тяжёлых условиях.','0',0,NULL,0,2,1,'say-to-life-yes.jpg',160.00,0,0,'2025-03-06 11:31:02'),
(9,'Думай и богатей',0,'Одна из самых популярных книг по саморазвитию, основанная на интервью с успешными людьми и описывающая принципы достижения богатства.','0',0,NULL,0,0,1,'thing-and-rich.png',400.00,0,0,'2025-03-06 11:31:02'),
(10,'Гравити Фолз. Дневник 3 (Ультрафиолетовая краска)',0,'Книга из популярного мультсериала Gravity Falls, содержащая загадки, дневниковые записи и скрытые послания с эффектом ультрафиолетовой краски.','0',0,NULL,0,0,1,'gravity-falls.jpg',80.00,0,0,'2025-03-06 11:31:02'),
(11,'Тайна скрытого мира',0,'Книга о путешествии через параллельные миры и попытке разгадать древние тайны.','0',0,2023,0,0,2,'mystery-world.jpg',120.00,0,0,'2025-03-07 10:00:00'),
(12,'Путь самурая',0,'История о самурае, который должен спасти свою страну от вторжения.','0',0,2023,0,3,2,'samurai-path.jpg',200.00,0,0,'2025-03-07 10:05:00'),
(13,'Магия чисел',0,'Учебник по магии чисел, раскрывающий их связь с судьбой и вселенной.','0',0,2024,0,0,2,'magic-numbers.jpg',150.00,0,0,'2025-03-07 10:10:00'),
(14,'Незабудка',0,'История о том, как маленькая девочка научилась ценить дружбу и взаимопомощь.','0',0,2022,0,0,1,'nezabudka.jpg',180.00,0,0,'2025-03-07 10:15:00'),
(15,'Бойцовский клуб: возвращение',0,'Продолжение культового романа о темных сторонах общества и человека.','0',0,2025,0,0,2,'fight-club-2.jpg',350.00,0,0,'2025-03-07 10:20:00'),
(16,'Искусство ускользания',0,'Книга о ловкости и хитроумности, что позволяют выжить в самых сложных ситуациях.','0',0,2024,0,1,1,'art-of-escape.jpg',220.00,0,0,'2025-03-07 10:25:00'),
(17,'Письма к себе',0,'Сборник писем, в которых автор рассказывает о своем пути к счастью и самопознанию.','0',0,2023,0,0,2,'letters-to-self.jpg',100.00,0,0,'2025-03-07 10:30:00'),
(18,'Секреты древних цивилизаций',0,'Книга о потерянных цивилизациях и их тайных знаниях, сохранившихся до наших дней.','0',0,2022,0,0,1,'ancient-secrets.jpg',300.00,0,0,'2025-03-07 10:35:00'),
(19,'Механика счастья',0,'Книга о том, как научиться быть счастливыми с помощью науки и логики.','0',0,2024,0,0,2,'mechanics-of-happiness.jpg',250.00,0,0,'2025-03-07 10:40:00'),
(20,'Проклятие сердца',1,'Готическая драма о запретной любви и трагической судьбе.','0',0,2023,0,3,1,'curse-of-heart.jpg',350.00,0,0,'2025-03-07 10:45:00'),
(21,'Гарри Поттер и магия прошлого',0,'Новая часть приключений Гарри Поттера, которая погружает нас в историю магического мира до его основания.','0',0,2025,0,0,1,'garry-potter-completed.jpg',400.00,0,0,'2025-03-07 10:50:00'),
(22,'Тайна затмения',0,'Книга о том, как затмения влияют на жизнь людей и что скрывает ночное небо.','0',0,2024,0,0,1,'eclipse-secret.jpg',270.00,0,0,'2025-03-07 11:00:00'),
(23,'Забытые миры',0,'История об ученом, который обнаружил портал в параллельную реальность.','0',0,2023,0,0,2,'forgotten-worlds.jpg',320.00,0,0,'2025-03-07 11:05:00'),
(24,'Гроза любви',0,'Роман о страстной любви, развернувшейся на фоне бури и мощного урагана.','0',0,2023,0,2,1,'storm-of-love.jpg',150.00,0,0,'2025-03-07 11:10:00'),
(25,'Время для героев',0,'Потрясающая история о том, как обыкновенные люди стали героями.','0',0,2025,0,0,1,'time-for-heroes.jpg',200.00,0,0,'2025-03-07 11:15:00'),
(26,'Тёмная реальность',0,'Триллер о том, как человек стал частью странной и опасной реальности, о которой все забыли.','0',0,2024,0,0,2,'dark-reality.jpg',180.00,0,0,'2025-03-07 11:20:00'),
(27,'Палитра чувств',0,'Книга о том, как искусство помогает найти путь в жизни, несмотря на все трудности.','0',0,2023,0,0,1,'palette-of-feelings.jpg',250.00,0,0,'2025-04-08 11:25:00'),
(28,'Сказания древних волшебников',0,'Волшебные истории о том, как сила магии меняла судьбы народов.','0',0,2025,0,1,2,'ancient-wizards.jpg',280.00,0,0,'2025-03-07 11:30:00'),
(29,'Путеводитель по темному лесу',0,'Путеводитель по магическим и опасным лесам мира, который откроет перед вами неизведанные тайны.','0',0,2023,0,0,1,'dark-forest-guide.jpg',150.00,0,0,'2025-03-07 11:35:00'),
(30,'Шаги к победе',0,'Книга, которая научит вас побеждать не только в жизни, но и в самых сложных ситуациях.','0',0,2024,0,0,2,'steps-to-victory.jpg',220.00,0,0,'2025-03-07 11:40:00'),
(31,'Легенды и мифы Древней Греции',0,'Сборник самых захватывающих мифов Древней Греции, в которых переплетаются боги, герои и невероятные приключения.','0',0,2022,0,0,3,'greek-legends.jpg',330.00,0,0,'2025-03-07 11:45:00'),
(33,'Древняя магия',0,'История о том, как магия древности возрождается в наше время, меняя мир вокруг.','0',0,2024,0,2,3,'ancient-magic.jpg',230.00,0,0,'2025-03-07 11:55:00'),
(34,'Огненная душа',0,'Роман о том, как девушка с огненной душой находит свой путь в жизни среди бурных событий.','0',0,2023,0,0,2,'',1852424.00,0,0,'2025-03-07 12:00:00'),
(35,'Черные крылья',0,'Триллер о загадочных существах, которые оставляют за собой только смерть и разрушение.','0',0,2023,0,0,1,'50--great-books-psichology.png',350.00,0,0,'2025-03-07 12:05:00'),
(36,'Танец фауны',0,'Книга о том, как приручить зверей и найти свою настоящую природу.','0',0,2022,0,3,2,'3267d78cb7c558e87c22e22af0a5a886.jpg',200.00,0,0,'2025-03-07 12:10:00');

/*Table structure for table `book_genre` */

DROP TABLE IF EXISTS `book_genre`;

CREATE TABLE `book_genre` (
  `book_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  PRIMARY KEY (`book_id`,`genre_id`),
  UNIQUE KEY `book_id` (`book_id`,`genre_id`),
  KEY `genre_id` (`genre_id`),
  CONSTRAINT `book_genre_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `book_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `book_genre` */

/*Table structure for table `book_likes` */

DROP TABLE IF EXISTS `book_likes`;

CREATE TABLE `book_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`book_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `book_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_likes_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `book_likes` */

/*Table structure for table `book_ratings` */

DROP TABLE IF EXISTS `book_ratings`;

CREATE TABLE `book_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`book_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `book_ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_ratings_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `book_ratings` */

/*Table structure for table `book_reviews` */

DROP TABLE IF EXISTS `book_reviews`;

CREATE TABLE `book_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `admin_reviewed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `book_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `book_reviews_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `book_reviews` */

/*Table structure for table `book_views` */

DROP TABLE IF EXISTS `book_views`;

CREATE TABLE `book_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `viewed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `book_views_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_views_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `book_views` */

/*Table structure for table `cart` */

DROP TABLE IF EXISTS `cart`;

CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart` (`user_id`,`book_id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `constr_tb_book_id` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `constr_tb_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cart` */

/*Table structure for table `faq` */

DROP TABLE IF EXISTS `faq`;

CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `asked_user_id` int(11) NOT NULL,
  `answered_user_id` int(11) NOT NULL,
  `liked` int(11) NOT NULL DEFAULT '0',
  `viewed` int(11) NOT NULL DEFAULT '0',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `admin_reviewed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_faq_asked_user` (`asked_user_id`),
  KEY `fk_faq_answered_user` (`answered_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `faq` */

insert  into `faq`(`id`,`question`,`answer`,`asked_user_id`,`answered_user_id`,`liked`,`viewed`,`createdAt`,`updatedAt`,`admin_reviewed`) values 
(1,'Как долго длится доставка книг?','Самолётом 3-4 дня, машиной 10+ дней.',1,2,0,2,'2025-03-14 13:05:40','2025-04-15 12:53:02',0),
(2,'Какие способы оплаты доступны?','Мы принимаем банковские карты (Visa, MasterCard), электронные кошельки (PayPal, Stripe) и оплату при доставке.',1,2,0,3,'2025-03-14 13:05:40','2025-04-15 12:53:03',0),
(3,'Можно ли вернуть или обменять книгу?','Физические книги можно вернуть или обменять в течение 14 дней, если они не повреждены. Электронные книги возврату не подлежат.',1,2,0,3,'2025-03-14 13:05:40','2025-04-15 12:53:04',0),
(4,'Как использовать купоны и скидки?','При оформлении заказа введите код купона в специальное поле, и скидка применится автоматически.',1,2,0,2,'2025-03-14 13:05:40','2025-04-15 12:53:04',0),
(5,'Как скачать купленную электронную книгу?','После оплаты книга появится в разделе \"Мои покупки\" в личном кабинете, где её можно скачать в удобном формате (PDF, EPUB, MOBI).',1,2,0,1,'2025-03-14 13:05:40','2025-04-15 12:53:05',0),
(6,'Когда будет доставка в Душанбе?','',1,2,0,0,'2025-03-14 13:26:50','2025-04-15 12:53:05',0);

/*Table structure for table `favorites` */

DROP TABLE IF EXISTS `favorites`;

CREATE TABLE `favorites` (
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`book_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `favorites` */

/*Table structure for table `formats` */

DROP TABLE IF EXISTS `formats`;

CREATE TABLE `formats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `formats` */

insert  into `formats`(`id`,`name`) values 
(3,'Аудиокнига'),
(1,'печатный'),
(2,'электронный');

/*Table structure for table `genre` */

DROP TABLE IF EXISTS `genre`;

CREATE TABLE `genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Имя жанра',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Описание жанра',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `genre` */

/*Table structure for table `languages` */

DROP TABLE IF EXISTS `languages`;

CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `languages` */

insert  into `languages`(`id`,`name`) values 
(3,'english'),
(0,'неть'),
(2,'русский'),
(1,'тоҷикӣ');

/*Table structure for table `publishers` */

DROP TABLE IF EXISTS `publishers`;

CREATE TABLE `publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `publishers` */

insert  into `publishers`(`id`,`name`) values 
(0,'not'),
(3,'Джинджа'),
(4,'Книголиб'),
(5,'Нур'),
(2,'Офсет'),
(1,'Попури');

/*Table structure for table `stock` */

DROP TABLE IF EXISTS `stock`;

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL,
  `description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `stock` */

/*Table structure for table `stock_transfers` */

DROP TABLE IF EXISTS `stock_transfers`;

CREATE TABLE `stock_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `from_location` enum('warehouse','store') COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_location` enum('warehouse','store') COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `stock_transfers_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `stock_transfers` */

/*Table structure for table `user_adresses` */

DROP TABLE IF EXISTS `user_adresses`;

CREATE TABLE `user_adresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_adresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `user_adresses` */

/*Table structure for table `user_book_views` */

DROP TABLE IF EXISTS `user_book_views`;

CREATE TABLE `user_book_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `view_count` int(11) DEFAULT '1',
  `first_viewed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_viewed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_book` (`user_id`,`book_id`),
  KEY `book_id` (`book_id`),
  KEY `idx_user_views` (`user_id`,`last_viewed_at`),
  CONSTRAINT `user_book_views_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_book_views_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `user_book_views` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`email`,`password`,`role`,`createdAt`) values 
(1,'komyor','komyor@gmail.com','b59c67bf196a4758191e42f76670ceba','admin','2025-04-24 15:23:58'),
(2,'admin','admin@gmail.com','b59c67bf196a4758191e42f76670ceba','user','2025-04-24 15:30:03');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
