-- SQL скрипт для добавления новой статьи в базу данных

-- Структура таблицы articles:
-- id - автоматически генерируется (AUTO_INCREMENT)
-- publicationDate - дата публикации (формат: 'YYYY-MM-DD')
-- categoryId - ID категории (обязательное поле, NOT NULL)
-- subcategory_id - ID подкатегории (может быть NULL)
-- title - заголовок статьи (varchar(255))
-- summary - краткое описание (text)
-- content - содержание статьи (mediumtext)
-- is_visible - видимость (1 = видима, 0 = скрыта)

-- ПРИМЕР 1: Добавление статьи с категорией (без подкатегории)
INSERT INTO articles (publicationDate, categoryId, subcategory_id, title, summary, content, is_visible) 
VALUES (
    CURDATE(),                    -- Дата публикации (сегодня)
    1,                            -- ID категории (замените на нужную)
    NULL,                         -- Подкатегория (NULL = нет подкатегории)
    'Название статьи',           -- Заголовок
    'Краткое описание статьи',   -- Краткое описание
    'Полное содержание статьи здесь. Можно использовать HTML теги.',  -- Содержание
    1                             -- 1 = видима, 0 = скрыта
);

-- ПРИМЕР 2: Добавление статьи с категорией и подкатегорией
INSERT INTO articles (publicationDate, categoryId, subcategory_id, title, summary, content, is_visible) 
VALUES (
    '2024-01-15',                 -- Конкретная дата
    1,                            -- ID категории
    2,                            -- ID подкатегории
    'Еще одна статья',            -- Заголовок
    'Описание',                   -- Краткое описание
    'Содержание статьи',          -- Содержание
    1                             -- Видима
);

-- ПРИМЕР 3: Добавление скрытой статьи
INSERT INTO articles (publicationDate, categoryId, subcategory_id, title, summary, content, is_visible) 
VALUES (
    CURDATE(),
    1,
    NULL,
    'Черновик статьи',
    'Это черновик',
    'Содержание черновика',
    0                             -- 0 = скрыта от пользователей
);

-- ПРИМЕР 4: Добавление статьи с HTML разметкой
INSERT INTO articles (publicationDate, categoryId, subcategory_id, title, summary, content, is_visible) 
VALUES (
    CURDATE(),
    1,
    NULL,
    'Статья с HTML',
    'Статья с форматированием',
    '<h2>Заголовок</h2><p>Абзац текста с <strong>жирным</strong> и <em>курсивом</em>.</p><ul><li>Пункт 1</li><li>Пункт 2</li></ul>',
    1
);

-- ПРИМЕР 5: Добавление статьи с конкретной датой в прошлом
INSERT INTO articles (publicationDate, categoryId, subcategory_id, title, summary, content, is_visible) 
VALUES (
    '2023-12-25',                 -- Дата в прошлом
    1,
    NULL,
    'Старая статья',
    'Описание',
    'Содержание',
    1
);

-- ПРИМЕР 6: Добавление статьи с датой в будущем (для планирования публикации)
INSERT INTO articles (publicationDate, categoryId, subcategory_id, title, summary, content, is_visible) 
VALUES (
    '2024-12-31',                 -- Дата в будущем
    1,
    NULL,
    'Статья на Новый год',
    'Поздравление',
    'С Новым годом!',
    1
);

-- КАК УЗНАТЬ ID КАТЕГОРИЙ И ПОДКАТЕГОРИЙ:

-- Посмотреть все категории:
-- SELECT id, name FROM categories;

-- Посмотреть все подкатегории:
-- SELECT id, name, category_id FROM subcategories;

-- Посмотреть подкатегории конкретной категории:
-- SELECT id, name FROM subcategories WHERE category_id = 1;

-- КАК ПРОВЕРИТЬ РЕЗУЛЬТАТ:

-- Посмотреть все статьи:
-- SELECT id, publicationDate, title, categoryId, is_visible FROM articles ORDER BY publicationDate DESC;

-- Посмотреть последнюю добавленную статью:
-- SELECT * FROM articles ORDER BY id DESC LIMIT 1;

-- Посмотреть статью по ID:
-- SELECT * FROM articles WHERE id = 1;


