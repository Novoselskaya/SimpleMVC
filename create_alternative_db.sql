-- Создание альтернативной БД для SimpleMVC
-- Структура такая же, как в my-first-cms-1, но с другими данными

CREATE DATABASE IF NOT EXISTS `cms_simplemvc` DEFAULT CHARACTER SET utf8mb3 COLLATE utf8_general_ci;
USE `cms_simplemvc`;

-- Таблица категорий
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Таблица подкатегорий
DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE `subcategories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Таблица статей
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `publicationDate` date NOT NULL,
  `categoryId` smallint(5) unsigned NOT NULL,
  `subcategory_id` smallint(5) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `content` mediumtext NOT NULL,
  `is_visible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId`),
  KEY `subcategory_id` (`subcategory_id`),
  CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`),
  CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Таблица пользователей (структура для SimpleMVC)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` smallint NOT NULL AUTO_INCREMENT,
  `login` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` date NOT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `salt` int NOT NULL,
  `role` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_successful_login` datetime DEFAULT NULL COMMENT 'Последнее время удачного логина',
  `last_failed_login` datetime DEFAULT NULL COMMENT 'Последнее время неудачного логина',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица связи статей и авторов
DROP TABLE IF EXISTS `article_authors`;
CREATE TABLE `article_authors` (
  `article_id` smallint(5) unsigned NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`article_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `article_authors_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Таблица связи статей и благодарностей (опционально, может не существовать)
DROP TABLE IF EXISTS `article_thanks`;
CREATE TABLE IF NOT EXISTS `article_thanks` (
  `article_id` smallint(5) unsigned NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`article_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `article_thanks_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Вставка тестовых данных (отличающихся от оригинальных)

-- Категории
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Технологии', 'Статьи о современных технологиях и программировании'),
(2, 'Наука', 'Научные статьи и исследования'),
(3, 'Искусство', 'Статьи об искусстве и культуре');

-- Подкатегории
INSERT INTO `subcategories` (`id`, `name`, `category_id`) VALUES
(1, 'Веб-разработка', 1),
(2, 'Мобильные приложения', 1),
(3, 'Физика', 2),
(4, 'Химия', 2),
(5, 'Живопись', 3),
(6, 'Музыка', 3);

-- Пользователи (с хешированными паролями)
-- Пароль для admin: "admin" (при входе вводите "admin")
-- Пароль для editor и author1: "test123" (при входе вводите "test123")
-- Соль: 12345
-- Хеш создается как: password_hash("пароль" + "соль", PASSWORD_BCRYPT)
-- Например: password_hash("admin12345", PASSWORD_BCRYPT) для пароля "admin"
INSERT INTO `users` (`id`, `login`, `pass`, `timestamp`, `email`, `salt`, `role`) VALUES
(1, 'admin', '$2y$10$6LtDYNPELlJoRxPgZROo7uDvnV.VVtlJ2u1t5oOFu.qG6ags8ylIq', CURDATE(), 'admin@example.com', 12345, 'admin'),
(2, 'editor', '$2y$10$tBhCStLFQqGVsMg3V1.bmuBaklS.P48AImfWRbZpieGROQHxqEBFG', CURDATE(), 'editor@example.com', 12345, 'auth_user'),
(3, 'author1', '$2y$10$tBhCStLFQqGVsMg3V1.bmuBaklS.P48AImfWRbZpieGROQHxqEBFG', CURDATE(), 'author1@example.com', 12345, 'user');

-- Статьи
INSERT INTO `articles` (`id`, `publicationDate`, `categoryId`, `subcategory_id`, `title`, `summary`, `content`, `is_visible`) VALUES
(1, CURDATE(), 1, 1, 'Введение в PHP', 'PHP - это популярный язык программирования для веб-разработки', 'PHP (рекурсивный акроним PHP: Hypertext Preprocessor) - это широко используемый язык программирования общего назначения с открытым исходным кодом, который особенно подходит для веб-разработки и может быть встроен в HTML.', 1),
(2, CURDATE(), 1, 2, 'Создание мобильных приложений', 'Современные подходы к разработке мобильных приложений', 'Разработка мобильных приложений требует понимания различных платформ и фреймворков. Сегодня популярны React Native, Flutter и нативные решения для iOS и Android.', 1),
(3, CURDATE(), 2, 3, 'Квантовая физика', 'Основы квантовой механики для начинающих', 'Квантовая механика - это фундаментальная теория в физике, которая описывает физические свойства природы на масштабе атомов и субатомных частиц.', 1),
(4, CURDATE(), 3, 5, 'История живописи', 'Развитие живописи от древности до наших дней', 'Живопись прошла долгий путь развития от наскальных рисунков до современных цифровых произведений искусства. Каждая эпоха внесла свой вклад в развитие этого вида искусства.', 1);

-- Связи авторов со статьями
INSERT INTO `article_authors` (`article_id`, `user_id`) VALUES
(1, 3),
(2, 3),
(3, 2),
(4, 2);

-- Таблица заметок
DROP TABLE IF EXISTS `notes`;
CREATE TABLE `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `publicationDate` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Тестовые заметки
INSERT INTO `notes` (`publicationDate`, `title`, `content`) VALUES
(CURDATE(), 'Первая заметка', 'Это первая тестовая заметка в новой базе данных.'),
(CURDATE(), 'Вторая заметка', 'Это вторая тестовая заметка для проверки функционала.');

