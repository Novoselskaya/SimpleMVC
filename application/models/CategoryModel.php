<?php
namespace application\models;

use ItForFree\SimpleMVC\MVC\Model;

/**
 * Класс для обработки категорий статей
 */
class CategoryModel extends Model
{
    public string $tableName = 'categories';
    public string $orderBy = 'name ASC';
    
    public ?int $id = null;
    public $name = null;
    public $description = null;
    
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
        
        return parent::loadFromArray($data);
    }
    
    public function insert()
    {
        if (!is_null($this->id)) {
            trigger_error("CategoryModel::insert(): Attempt to insert a Category object that already has its ID property set.", E_USER_ERROR);
        }
        
        $sql = "INSERT INTO $this->tableName (name, description) VALUES (:name, :description)";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":name", $this->name, \PDO::PARAM_STR);
        $st->bindValue(":description", $this->description, \PDO::PARAM_STR);
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
    }
    
    public function update()
    {
        if (is_null($this->id)) {
            trigger_error("CategoryModel::update(): Attempt to update a Category object that does not have its ID property set.", E_USER_ERROR);
        }
        
        $sql = "UPDATE $this->tableName SET name=:name, description=:description WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":name", $this->name, \PDO::PARAM_STR);
        $st->bindValue(":description", $this->description, \PDO::PARAM_STR);
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        $st->execute();
    }
}

