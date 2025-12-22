<?php
namespace application\models;

use ItForFree\SimpleMVC\MVC\Model;

/**
 * Класс для обработки статей
 */
class ArticleModel extends Model
{
    public string $tableName = 'articles';
    public string $orderBy = 'publicationDate DESC';
    
    public ?int $id = null;
    public $publicationDate = null;
    public $title = null;
    public $categoryId = null;
    public $summary = null;
    public $content = null;
    public $is_visible = 1;
    public $subcategory_id = null;
    public $authorIds = array();
    
    /**
     * Конструктор для загрузки данных из массива
     * 
     * @param array $data массив значений (столбцов) строки таблицы статей
     */
    public function __construct(?array $data = null)
    {
        // Вызываем родительский конструктор, который установит все свойства из массива
        parent::__construct($data);
        
        // Дополнительная обработка для правильных типов
        if (is_array($data)) {
            if (isset($data['id'])) {
                $this->id = (int) $data['id'];
            }
            
            if (isset($data['categoryId'])) {
                $this->categoryId = (int) $data['categoryId'];
            }
            
            if (isset($data['is_visible'])) {
                $this->is_visible = (int) $data['is_visible'];
            }
            
            if (isset($data['subcategory_id']) && $data['subcategory_id'] !== null) {
                $this->subcategory_id = (int) $data['subcategory_id'];
            } else {
                $this->subcategory_id = null;
            }
            
            // Преобразуем publicationDate в timestamp, если это строка
            if (isset($data['publicationDate'])) {
                if (is_string($data['publicationDate'])) {
                    $this->publicationDate = strtotime($data['publicationDate']);
                } else {
                    $this->publicationDate = (int) $data['publicationDate'];
                }
            }
        }
    }
    
    /**
     * Загрузить данные из массива с правильной обработкой типов
     * Переопределяем для корректной обработки типизированных свойств
     */
    public function loadFromArray(array $data): \ItForFree\SimpleMVC\MVC\Model
    {
        // Обрабатываем типы перед загрузкой
        if (isset($data['id'])) {
            if ($data['id'] === '' || $data['id'] === null) {
                unset($data['id']); // Убираем пустой id для новых записей
            } else {
                $data['id'] = (int) $data['id'];
            }
        }
        
        if (isset($data['categoryId'])) {
            if ($data['categoryId'] === '' || $data['categoryId'] === '0') {
                $data['categoryId'] = null;
            } else {
                $data['categoryId'] = (int) $data['categoryId'];
            }
        }
        
        if (isset($data['subcategory_id'])) {
            if ($data['subcategory_id'] === '' || $data['subcategory_id'] === '0') {
                $data['subcategory_id'] = null;
            } else {
                $data['subcategory_id'] = (int) $data['subcategory_id'];
            }
        }
        
        if (isset($data['is_visible'])) {
            $data['is_visible'] = ($data['is_visible'] === '1' || $data['is_visible'] === 1 || $data['is_visible'] === true) ? 1 : 0;
        }
        
        // Вызываем родительский метод с обработанными данными
        return parent::loadFromArray($data);
    }
    
    /**
     * Получить список статей с фильтрацией
     * Переопределяем родительский метод для поддержки фильтрации
     */
    public function getList(int $numRows = 1000000): array
    {
        // Используем метод getListFiltered для обратной совместимости
        return $this->getListFiltered($numRows, null, null, "publicationDate DESC", true);
    }
    
    /**
     * Получить список статей с фильтрацией по категории, подкатегории и видимости
     */
    public function getListFiltered($numRows = 1000000, $categoryId = null, $subcategoryId = null, $order = "publicationDate DESC", $onlyVisible = true)
    {
        $fromPart = "FROM $this->tableName";
        $whereClauses = array();
        $params = array();
        
        if ($categoryId) {
            $whereClauses[] = "categoryId = :categoryId";
            $params[':categoryId'] = $categoryId;
        }
        
        if ($subcategoryId) {
            $whereClauses[] = "subcategory_id = :subcategoryId";
            $params[':subcategoryId'] = $subcategoryId;
        }
        
        if ($onlyVisible) {
            $whereClauses[] = "is_visible = 1";
        }
        
        $whereClause = "";
        if (!empty($whereClauses)) {
            $whereClause = "WHERE " . implode(" AND ", $whereClauses);
        }
        
        $sql = "SELECT id, categoryId, subcategory_id, title, summary, content, is_visible, UNIX_TIMESTAMP(publicationDate) AS publicationDate 
                $fromPart $whereClause
                ORDER BY $order LIMIT :numRows";
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $st->bindValue($key, $value, \PDO::PARAM_INT);
        }
        
        $st->execute();
        $list = array();
        
        while ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
            $article = new ArticleModel($row);
            $list[] = $article;
        }
        
        $sql = "SELECT COUNT(*) AS totalRows $fromPart $whereClause";
        $st = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $st->bindValue($key, $value, \PDO::PARAM_INT);
        }
        
        $st->execute();
        $totalRows = $st->fetch(\PDO::FETCH_ASSOC);
        
        return array(
            "results" => $list,
            "totalRows" => $totalRows ? $totalRows['totalRows'] : 0
        );
    }
    
    /**
     * Получить статью по ID
     * Переопределяем для преобразования даты в timestamp
     */
    public function getById(int $id, string $tableName = ''): ?\ItForFree\SimpleMVC\MVC\Model
    {
        $sql = "SELECT id, categoryId, subcategory_id, title, summary, content, is_visible, UNIX_TIMESTAMP(publicationDate) AS publicationDate 
                FROM $this->tableName 
                WHERE id = :id";
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":id", $id, \PDO::PARAM_INT);
        $st->execute();
        
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        
        if ($row) {
            return new ArticleModel($row);
        }
        
        return null;
    }
    
    /**
     * Получить авторов статьи
     */
    public function getAuthors()
    {
        if (is_null($this->id)) {
            return array();
        }
        
        try {
            $sql = "SELECT u.* FROM users u 
                    INNER JOIN article_authors aa ON u.id = aa.user_id 
                    WHERE aa.article_id = :articleId 
                    ORDER BY u.login";
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":articleId", $this->id, \PDO::PARAM_INT);
            $st->execute();
            
            $authors = array();
            while ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
                $user = new UserModel($row);
                $authors[] = $user;
            }
            
            return $authors;
        } catch (\PDOException $e) {
            return array();
        }
    }
    
    /**
     * Сохранить авторов статьи
     */
    public function saveAuthors()
    {
        if (is_null($this->id)) {
            return;
        }
        
        try {
            // Удаляем старых авторов
            $sql = "DELETE FROM article_authors WHERE article_id = :articleId";
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":articleId", $this->id, \PDO::PARAM_INT);
            $st->execute();
            
            // Добавляем новых авторов
            if (!empty($this->authorIds)) {
                $sql = "INSERT INTO article_authors (article_id, user_id) VALUES ";
                $values = array();
                $params = array();
                
                foreach ($this->authorIds as $index => $authorId) {
                    $values[] = "(:articleId, :authorId$index)";
                    $params[":authorId$index"] = $authorId;
                }
                
                $sql .= implode(", ", $values);
                $st = $this->pdo->prepare($sql);
                $st->bindValue(":articleId", $this->id, \PDO::PARAM_INT);
                
                foreach ($params as $key => $value) {
                    $st->bindValue($key, $value, \PDO::PARAM_INT);
                }
                
                $st->execute();
            }
        } catch (\PDOException $e) {
            // Игнорируем ошибку, если таблица не существует
        }
    }
    
    public function insert()
    {
        if (!is_null($this->id)) {
            trigger_error("ArticleModel::insert(): Attempt to insert an Article object that already has its ID property set.", E_USER_ERROR);
        }
        
        $sql = "INSERT INTO $this->tableName (publicationDate, categoryId, subcategory_id, title, summary, content, is_visible) 
                VALUES (FROM_UNIXTIME(:publicationDate), :categoryId, :subcategory_id, :title, :summary, :content, :is_visible)";
        
        $st = $this->pdo->prepare($sql);
        
        // Если publicationDate не установлена, используем текущее время
        if (is_null($this->publicationDate)) {
            $this->publicationDate = time();
        }
        
        $st->bindValue(":publicationDate", $this->publicationDate, \PDO::PARAM_INT);
        
        // categoryId NOT NULL в БД, поэтому если не указано, используем первую категорию
        if (is_null($this->categoryId) || $this->categoryId == 0) {
            // Получаем первую категорию из БД
            $catSt = $this->pdo->query("SELECT id FROM categories LIMIT 1");
            $firstCat = $catSt->fetch(\PDO::FETCH_ASSOC);
            $st->bindValue(":categoryId", $firstCat ? (int)$firstCat['id'] : 1, \PDO::PARAM_INT);
        } else {
            $st->bindValue(":categoryId", $this->categoryId, \PDO::PARAM_INT);
        }
        
        if (is_null($this->subcategory_id) || $this->subcategory_id == 0) {
            $st->bindValue(":subcategory_id", null, \PDO::PARAM_NULL);
        } else {
            $st->bindValue(":subcategory_id", $this->subcategory_id, \PDO::PARAM_INT);
        }
        
        $st->bindValue(":title", $this->title, \PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary, \PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, \PDO::PARAM_STR);
        $st->bindValue(":is_visible", $this->is_visible, \PDO::PARAM_INT);
        
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
        
        $this->saveAuthors();
    }
    
    public function update()
    {
        if (is_null($this->id)) {
            trigger_error("ArticleModel::update(): Attempt to update an Article object that does not have its ID property set.", E_USER_ERROR);
        }
        
        $sql = "UPDATE $this->tableName SET publicationDate=FROM_UNIXTIME(:publicationDate), categoryId=:categoryId, 
                subcategory_id=:subcategory_id, title=:title, summary=:summary, content=:content, is_visible=:is_visible 
                WHERE id = :id";
        
        $st = $this->pdo->prepare($sql);
        
        // Если publicationDate не установлена, используем текущее время
        if (is_null($this->publicationDate)) {
            $this->publicationDate = time();
        }
        
        $st->bindValue(":publicationDate", $this->publicationDate, \PDO::PARAM_INT);
        
        // categoryId NOT NULL в БД, поэтому если не указано, используем первую категорию
        if (is_null($this->categoryId) || $this->categoryId == 0) {
            // Получаем первую категорию из БД
            $catSt = $this->pdo->query("SELECT id FROM categories LIMIT 1");
            $firstCat = $catSt->fetch(\PDO::FETCH_ASSOC);
            $st->bindValue(":categoryId", $firstCat ? $firstCat['id'] : 1, \PDO::PARAM_INT);
        } else {
            $st->bindValue(":categoryId", $this->categoryId, \PDO::PARAM_INT);
        }
        
        if (is_null($this->subcategory_id) || $this->subcategory_id == 0) {
            $st->bindValue(":subcategory_id", null, \PDO::PARAM_NULL);
        } else {
            $st->bindValue(":subcategory_id", $this->subcategory_id, \PDO::PARAM_INT);
        }
        
        $st->bindValue(":title", $this->title, \PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary, \PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, \PDO::PARAM_STR);
        $st->bindValue(":is_visible", $this->is_visible, \PDO::PARAM_INT);
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        
        $st->execute();
        
        $this->saveAuthors();
    }
    
    public function delete(): void
    {
        if (is_null($this->id)) {
            trigger_error("ArticleModel::delete(): Attempt to delete an Article object that does not have its ID property set.", E_USER_ERROR);
        }
        
        // Удаляем связи с авторами
        try {
            $st = $this->pdo->prepare("DELETE FROM article_authors WHERE article_id = :id");
            $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
            $st->execute();
        } catch (\PDOException $e) {
            // Игнорируем ошибку
        }
        
        // Удаляем саму статью
        $st = $this->pdo->prepare("DELETE FROM $this->tableName WHERE id = :id LIMIT 1");
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        $st->execute();
    }
}

