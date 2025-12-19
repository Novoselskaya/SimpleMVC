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

