# Подробное объяснение миграции из my-first-cms-1 в SimpleMVC

## ЧАСТЬ 1: КАК БЫЛА СОЗДАНА ГЛАВНАЯ СТРАНИЦА

### 1.1. Исходный код в my-first-cms-1

**Файл:** `/home/user/Documents/my-first-cms-1/templates/homepage.php`

**Что было:**
- Простой PHP шаблон с циклом по статьям
- Использовал `include "templates/include/header.php"` для подключения шапки
- Выводил статьи в списке `<ul id="headlines">`
- Показывал дату, заголовок, категорию, подкатегорию, авторов, благодарности

**Структура:**
```php
<?php include "templates/include/header.php" ?>
<ul id="headlines">
    <?php foreach ($results['articles'] as $article) { 
        // Получение авторов и благодарностей
        // Вывод статьи
    } ?>
</ul>
```

### 1.2. Что было сделано для SimpleMVC

**Шаг 1: Создан контроллер**
**Файл:** `/var/www/mySimple/application/controllers/HomepageController.php`

**Что делает:**
- Наследуется от `\ItForFree\SimpleMVC\MVC\Controller`
- Метод `indexAction()` загружает статьи из БД
- Использует `ArticleModel` для получения данных
- Передает данные в представление через `$this->view->addVar()`

**Код:**
```php
public function indexAction()
{
    $results = array();
    
    // Получаем статьи (первые 5)
    $HOMEPAGE_NUM_ARTICLES = 5;
    $Article = new ArticleModel();
    $data = $Article->getListFiltered($HOMEPAGE_NUM_ARTICLES, null, null, "publicationDate DESC", true);
    $results['articles'] = $data['results'] ?? [];
    
    // Получаем категории
    $Category = new CategoryModel();
    $categoryData = $Category->getList();
    $results['categories'] = array();
    foreach ($categoryData['results'] as $category) {
        $results['categories'][$category->id] = $category;
    }
    
    // Передаем данные в представление
    $this->view->addVar('results', $results);
    $this->view->render('homepage/index.php');
}
```

**Шаг 2: Создано представление**
**Файл:** `/var/www/mySimple/application/views/homepage/index.php`

**Что было сделано:**
- Взят код из `my-first-cms-1/templates/homepage.php`
- Заменены ссылки:
  - Было: `.?action=viewArticle&articleId=<?php echo $article->id?>`
  - Стало: `<?= WebRouter::link("article/view&articleId=" . $article->id)?>`
- Убраны авторы (по вашему запросу)
- Добавлена обработка ошибок с `try-catch`
- Добавлены проверки на `null` для предотвращения ошибок

**Основные изменения:**
```php
// БЫЛО (my-first-cms-1):
<a href=".?action=viewArticle&articleId=<?php echo $article->id?>">

// СТАЛО (SimpleMVC):
<a href="<?= WebRouter::link("article/view&articleId=" . $article->id)?>">
```

---

## ЧАСТЬ 2: КАК БЫЛИ ПЕРЕНЕСЕНЫ СТИЛИ

### 2.1. Исходные стили

**Файл:** `/home/user/Documents/my-first-cms-1/style.css`

**Что содержал:**
- Стили для body, контейнера, логотипа
- Стили для заголовков, списков статей
- Стили для категорий, подкатегорий
- Стили для футера

**Основные стили:**
```css
body {
  margin: 0;
  color: #333;
  background-color: #00a0b0;  /* Голубой фон */
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
}

#container {
  width: 960px;
  background: #fff;
  margin: 20px auto;
  padding: 20px;
  border-radius: 5px;
}

#logo {
  display: block;
  width: 300px;
  border-bottom: 1px solid #00a0b0;
  margin-bottom: 40px;
}
```

### 2.2. Что было сделано

**Шаг 1: Скопирован CSS файл**
- **Откуда:** `/home/user/Documents/my-first-cms-1/style.css`
- **Куда:** `/var/www/mySimple/web/CSS/cms-style.css`
- **Почему в web/CSS:** В SimpleMVC статические файлы (CSS, JS, изображения) должны быть в папке `web/`

**Шаг 2: Создан новый layout**
**Файл:** `/var/www/mySimple/application/views/layouts/cms-main.php`

**Что делает:**
- Заменяет стандартный layout SimpleMVC (`main.php`)
- Подключает CSS файл: `<link rel="stylesheet" type="text/css" href="/CSS/cms-style.css" />`
- Включает структуру из оригинального проекта:
  - Логотип
  - Контейнер
  - Футер
  - Кнопки входа/выхода

**Структура layout:**
```php
<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?php echo $pageTitle ?></title>
    <link rel="stylesheet" type="text/css" href="/CSS/cms-style.css" />
    <script src="/JS/jquery-3.2.1.js"></script>
</head>
<body>
    <div id="container">
        <img id="logo" src="/images/logo.jpg" />
        <?= $CONTENT_DATA ?>  <!-- Здесь вставляется содержимое страницы -->
        <div id="footer">...</div>
    </div>
</body>
</html>
```

**Шаг 3: Скопированы изображения и JS**
- **Логотип:** `/home/user/Documents/my-first-cms-1/images/logo.jpg` → `/var/www/mySimple/web/images/logo.jpg`
- **JavaScript:** `/home/user/Documents/my-first-cms-1/JS/*` → `/var/www/mySimple/web/JS/*`

**Шаг 4: Указан layout в контроллерах**
В каждом контроллере добавлено:
```php
public string $layoutPath = 'cms-main.php';
```

Это говорит SimpleMVC использовать наш кастомный layout вместо стандартного.

---

## ЧАСТЬ 3: КАК БЫЛО ДОБАВЛЕНО РЕДАКТИРОВАНИЕ СТАТЕЙ

### 3.1. Создание модели ArticleModel

**Файл:** `/var/www/mySimple/application/models/ArticleModel.php`

**Что было сделано:**
- Наследование от `\ItForFree\SimpleMVC\MVC\Model`
- Добавлены свойства: `id`, `publicationDate`, `title`, `summary`, `content`, `categoryId`, `subcategory_id`, `is_visible`
- Реализованы методы:
  - `getListFiltered()` - получение статей с фильтрацией
  - `getById()` - получение статьи по ID
  - `insert()` - добавление новой статьи
  - `update()` - обновление статьи
  - `delete()` - удаление статьи
  - `getAuthors()` - получение авторов (потом не используется)
  - `getThanks()` - получение благодарностей

**Особенности:**
- Метод `getList()` должен соответствовать родительскому классу
- Используется `PDO` для работы с БД
- Обработка ошибок через `try-catch` для таблиц, которых может не быть

### 3.2. Создание контроллера AdminarticlesController

**Файл:** `/var/www/mySimple/application/controllers/admin/AdminarticlesController.php`

**Что делает:**
Управляет статьями в админке. Четыре основных метода:

**1. `indexAction()` - Список статей**
- Получает все статьи из БД
- Получает категории для отображения
- Передает данные в представление `article/index.php`

**2. `addAction()` - Добавление статьи**
- Если `$_POST` пустой - показывает форму
- Если `$_POST` заполнен - обрабатывает данные:
  - Преобразует дату в Unix timestamp
  - Обрабатывает категорию и подкатегорию
  - Обрабатывает видимость (чекбокс)
  - Обрабатывает благодарности
  - Сохраняет в БД через `$article->insert()`

**3. `editAction()` - Редактирование статьи**
- Получает ID статьи из `$_GET['id']`
- Если `$_POST` пустой - загружает статью и показывает форму
- Если `$_POST` заполнен - обновляет статью через `$article->update()`

**4. `deleteAction()` - Удаление статьи**
- Показывает подтверждение удаления
- Удаляет статью через `$article->delete()`

**Обработка данных формы:**
```php
// Дата публикации
if (isset($_POST['publicationDate']) && !empty($_POST['publicationDate'])) {
    $article->publicationDate = strtotime($_POST['publicationDate']);
}

// Видимость (чекбокс)
$article->is_visible = isset($_POST['is_visible']) ? 1 : 0;

// Категория
if (empty($_POST['categoryId']) || $_POST['categoryId'] == 0) {
    $article->categoryId = null;
}

// Благодарности
if (isset($_POST['thanksIds']) && is_array($_POST['thanksIds'])) {
    $article->thanksIds = array_map('intval', $_POST['thanksIds']);
}
```

### 3.3. Создание представлений

**1. Список статей:** `/var/www/mySimple/application/views/article/index.php`
- Таблица со статьями
- Колонки: Дата, Название, Категория, Видимость, Действия
- Ссылки на редактирование и удаление

**2. Форма редактирования:** `/var/www/mySimple/application/views/article/edit.php`
- Используется и для добавления, и для редактирования
- Поля:
  - Название статьи
  - Краткое описание
  - Содержание
  - Категория (выпадающий список)
  - Подкатегория (выпадающий список, сгруппирован по категориям)
  - Благодарности (множественный выбор)
  - Дата публикации
  - Видимость (чекбокс)

**3. Удаление:** `/var/www/mySimple/application/views/article/delete.php`
- Форма подтверждения удаления

**4. Навигация:** `/var/www/mySimple/application/views/article/includes/admin-articles-nav.php`
- Ссылки на список статей и добавление новой

### 3.4. Добавление в меню админки

**Файл:** `/var/www/mySimple/application/views/layouts/includes/admin-main/nav.php`

**Что добавлено:**
```php
<?php if ($User->isAllowed("admin/adminarticles/index")): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?= WebRouter::link("admin/adminarticles/index") ?>">Статьи</a>
    </li>
<?php endif; ?>
```

---

## ЧАСТЬ 4: КАК БЫЛИ ДОБАВЛЕНЫ СТАТЬИ И ЗАМЕТКИ

### 4.1. Статьи

**Откуда взялись:**
- Статьи уже были в базе данных `cms` из оригинального проекта
- При миграции мы просто подключились к той же БД
- Никаких дополнительных действий не требовалось

**Как они отображаются:**
- Контроллер `HomepageController` загружает их из БД
- Представление `homepage/index.php` выводит их в том же формате, что и в оригинале

### 4.2. Заметки (Notes)

**Что было:**
- В оригинальном проекте my-first-cms-1 заметок не было
- Но в SimpleMVC уже был контроллер `NotesController` и модель `Note`
- Они использовали таблицу `notes` в БД

**Что было сделано:**
1. Проверено наличие таблицы `notes` в БД `cms` - она уже существовала
2. Никаких изменений не требовалось - все уже работало

**Структура таблицы notes:**
```sql
CREATE TABLE `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `publicationDate` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
)
```

**Контроллер:** `/var/www/mySimple/application/controllers/admin/NotesController.php`
- `indexAction()` - список заметок
- `addAction()` - добавление заметки
- `editAction()` - редактирование заметки
- `deleteAction()` - удаление заметки

---

## ИТОГОВАЯ СХЕМА МИГРАЦИИ

### Что было скопировано:

1. **CSS стили:**
   - `my-first-cms-1/style.css` → `SimpleMVC/web/CSS/cms-style.css`

2. **JavaScript:**
   - `my-first-cms-1/JS/*` → `SimpleMVC/web/JS/*`

3. **Изображения:**
   - `my-first-cms-1/images/logo.jpg` → `SimpleMVC/web/images/logo.jpg`

4. **Шаблоны (преобразованы в представления):**
   - `my-first-cms-1/templates/homepage.php` → `SimpleMVC/application/views/homepage/index.php`
   - `my-first-cms-1/templates/viewArticle.php` → `SimpleMVC/application/views/article/view.php`
   - `my-first-cms-1/templates/archive.php` → `SimpleMVC/application/views/category/archive.php`

5. **Классы (преобразованы в модели):**
   - `my-first-cms-1/classes/Article.php` → `SimpleMVC/application/models/ArticleModel.php`
   - `my-first-cms-1/classes/Category.php` → `SimpleMVC/application/models/CategoryModel.php`
   - `my-first-cms-1/classes/Subcategory.php` → `SimpleMVC/application/models/SubcategoryModel.php`

### Что было создано с нуля:

1. **Контроллеры:**
   - `HomepageController.php` - главная страница
   - `ArticleController.php` - просмотр статьи
   - `CategoryController.php` - архив категорий
   - `SubcategoryController.php` - архив подкатегорий
   - `AdminarticlesController.php` - управление статьями в админке

2. **Layout:**
   - `cms-main.php` - основной макет страницы

3. **Представления:**
   - `article/index.php` - список статей в админке
   - `article/edit.php` - форма редактирования
   - `article/delete.php` - подтверждение удаления

### Что было изменено:

1. **Ссылки:**
   - Было: `.?action=viewArticle&articleId=1`
   - Стало: `WebRouter::link("article/view&articleId=1")`

2. **Работа с БД:**
   - Было: прямое использование классов `Article::getList()`
   - Стало: через модели `$Article = new ArticleModel(); $Article->getListFiltered()`

3. **Роутинг:**
   - Было: `switch ($action)` в `index.php`
   - Стало: автоматический роутинг через SimpleMVC

4. **Layout:**
   - Было: `include "templates/include/header.php"`
   - Стало: `$layoutPath = 'cms-main.php'` в контроллере

---

## КЛЮЧЕВЫЕ ОТЛИЧИЯ АРХИТЕКТУРЫ

### my-first-cms-1 (старый проект):
```
index.php (роутинг + логика)
  ↓
functions.php (функции)
  ↓
classes/ (модели)
  ↓
templates/ (представления)
```

### SimpleMVC (новый проект):
```
web/index.php (точка входа)
  ↓
Router (автоматический роутинг)
  ↓
Controllers/ (контроллеры - логика)
  ↓
Models/ (модели - работа с БД)
  ↓
Views/ (представления - HTML)
  ↓
Layouts/ (макеты страниц)
```

---

## ПОШАГОВЫЙ ПРОЦЕСС МИГРАЦИИ

### Шаг 1: Подготовка
1. Изучена структура оригинального проекта
2. Изучена структура SimpleMVC
3. Определены файлы для переноса

### Шаг 2: Перенос статических файлов
1. Скопирован CSS в `web/CSS/`
2. Скопированы JS в `web/JS/`
3. Скопированы изображения в `web/images/`

### Шаг 3: Создание моделей
1. Создан `ArticleModel` на основе `Article.php`
2. Создан `CategoryModel` на основе `Category.php`
3. Создан `SubcategoryModel` на основе `Subcategory.php`
4. Адаптированы методы под SimpleMVC

### Шаг 4: Создание контроллеров
1. Создан `HomepageController` - заменил функцию `homepage()`
2. Создан `ArticleController` - заменил функцию `viewArticle()`
3. Создан `CategoryController` - заменил функцию `archive()`
4. Создан `AdminarticlesController` - новый функционал для админки

### Шаг 5: Создание представлений
1. Преобразованы шаблоны в представления SimpleMVC
2. Заменены ссылки на `WebRouter::link()`
3. Добавлена обработка ошибок

### Шаг 6: Создание layout
1. Создан `cms-main.php` на основе `header.php` и `footer.php`
2. Подключен CSS файл
3. Добавлена структура контейнера

### Шаг 7: Настройка
1. Указан layout в контроллерах
2. Настроена конфигурация БД
3. Проверена работа всех страниц

---

## РЕЗУЛЬТАТ

✅ Главная страница выглядит так же, как в оригинале
✅ Все стили перенесены и работают
✅ Редактирование статей работает через админку
✅ Статьи отображаются из той же БД
✅ Заметки работают (были в SimpleMVC изначально)


