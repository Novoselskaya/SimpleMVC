<?php
namespace application\models;

use ItForFree\SimpleMVC\MVC\Model;

/**
 * Класс для обработки подкатегорий статей
 */
class SubcategoryModel extends Model
{
    public string $tableName = 'subcategories';
    public string $orderBy = 'name ASC';
    
    public ?int $id = null;
    public $name = null;
    public $category_id = null;
    
    /**
     * Конструктор для загрузки данных из массива
     */
    public function __construct(?array $data = null)
    {
        // Обрабатываем типы ПЕРЕД вызовом родительского конструктора
        if (is_array($data)) {
            if (isset($data['id'])) {
                if ($data['id'] === '' || $data['id'] === null) {
                    unset($data['id']);
                } else {
                    $data['id'] = (int) $data['id'];
                }
            }
            
            if (isset($data['category_id'])) {
                if ($data['category_id'] === '' || $data['category_id'] === '0' || $data['category_id'] === null) {
                    $data['category_id'] = null;
                } else {
                    $data['category_id'] = (int) $data['category_id'];
                }
            }
        }
        
        parent::__construct($data);
    }
    
    /**
     * Загрузить данные из массива с правильной обработкой типов
     */
    public function loadFromArray(array $data): \ItForFree\SimpleMVC\MVC\Model
    {
        // Обрабатываем типы перед загрузкой
        if (isset($data['id'])) {
            if ($data['id'] === '' || $data['id'] === null) {
                unset($data['id']);
            } else {
                $data['id'] = (int) $data['id'];
            }
        }
        
        if (isset($data['category_id'])) {
            if ($data['category_id'] === '' || $data['category_id'] === '0') {
                $data['category_id'] = null;
            } else {
                $data['category_id'] = (int) $data['category_id'];
            }
        }
        
        return parent::loadFromArray($data);
    }
    
    /**
     * Получить подкатегории по ID категории
     */
    public function getByCategoryId($categoryId)
    {
        $sql = "SELECT * FROM $this->tableName WHERE category_id = :category_id ORDER BY name";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":category_id", $categoryId, \PDO::PARAM_INT);
        $st->execute();
        
        $list = array();
        while ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
            $subcategory = new SubcategoryModel();
            $subcategory->loadFromArray($row);
            $list[] = $subcategory;
        }
        
        return $list;
    }
    
    public function insert()
    {
        if (!is_null($this->id)) {
            trigger_error("SubcategoryModel::insert(): Attempt to insert a Subcategory object that already has its ID property set.", E_USER_ERROR);
        }
        
        $sql = "INSERT INTO $this->tableName (name, category_id) VALUES (:name, :category_id)";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":name", $this->name, \PDO::PARAM_STR);
        if (is_null($this->category_id)) {
            $st->bindValue(":category_id", null, \PDO::PARAM_NULL);
        } else {
            $st->bindValue(":category_id", $this->category_id, \PDO::PARAM_INT);
        }
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
    }
    
    public function update()
    {
        if (is_null($this->id)) {
            trigger_error("SubcategoryModel::update(): Attempt to update a Subcategory object that does not have its ID property set.", E_USER_ERROR);
        }
        
        $sql = "UPDATE $this->tableName SET name=:name, category_id=:category_id WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":name", $this->name, \PDO::PARAM_STR);
        if (is_null($this->category_id)) {
            $st->bindValue(":category_id", null, \PDO::PARAM_NULL);
        } else {
            $st->bindValue(":category_id", $this->category_id, \PDO::PARAM_INT);
        }
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        $st->execute();
    }
}

