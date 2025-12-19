<?php
namespace application\models;

use ItForFree\SimpleMVC\MVC\Model;
/**
 * Класс для обработки пользователей
 */
class UserModel extends Model
{
    // Свойства
    /**
    * @var string логин пользователя
    */
    public $login = null;
    
    public ?int $id = null;

    /**
    * @var string пароль пользователя
    */
    public $pass = null;
    
    /**
    * @var string роль пользователя
    */
    public $role = null;
    
    public $email = null;
    
    public $timestamp = null;
    
    /**
     * @var string последнее время удачного логина
     */
    public $last_successful_login = null;
    
    /**
     * @var string последнее время неудачного логина
     */
    public $last_failed_login = null;
    
    /**
     * @var string Критерий сортировки строк таблицы
     */
    public string $orderBy = "login ASC";
    
    /**
     *  @var string название таблицы
     */
    public string $tableName = 'users';
    
    public $salt = null;
    

    public function insert()
    {
        $sql = "INSERT INTO $this->tableName (timestamp, login, salt, pass, role, email) VALUES (:timestamp, :login, :salt, :pass, :role, :email)"; 
        $st = $this->pdo->prepare ( $sql );
        $st->bindValue( ":timestamp", (new \DateTime('NOW'))->format('Y-m-d H:i:s'), \PDO::PARAM_STMT);
        $st->bindValue( ":login", $this->login, \PDO::PARAM_STR );
        
        //Хеширование пароля
        $this->salt = rand(0,1000000);
        $st->bindValue( ":salt", $this->salt, \PDO::PARAM_STR );
//        \DebugPrinter::debug($this->salt);
        
        $this->pass .= $this->salt;
        $hashPass = password_hash($this->pass, PASSWORD_BCRYPT);
//        \DebugPrinter::debug($hashPass);
        $st->bindValue( ":pass", $hashPass, \PDO::PARAM_STR );
        
        $st->bindValue( ":role", $this->role, \PDO::PARAM_STR );
        $st->bindValue( ":email", $this->email, \PDO::PARAM_STR );
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
    }
    
    public function update()
    {
        // Получаем текущие данные пользователя из БД
        $currentUser = $this->getById($this->id);
        
        // Если пароль пустой, используем текущий пароль и 
        if (empty($this->pass)) {
            $passToUpdate = $currentUser->pass;
            $saltToUpdate = $currentUser->salt;
        } else {
            // Хеширование нового пароля 
            $this->salt = rand(0,1000000);
            $saltToUpdate = $this->salt;
            $this->pass .= $this->salt;
            $passToUpdate = password_hash($this->pass, PASSWORD_BCRYPT);
        }
        
        $sql = "UPDATE $this->tableName SET timestamp=:timestamp, login=:login, salt=:salt, pass=:pass, role=:role, email=:email WHERE id = :id";  
        $st = $this->pdo->prepare ( $sql );
        
        $st->bindValue( ":timestamp", (new \DateTime('NOW'))->format('Y-m-d H:i:s'), \PDO::PARAM_STMT);
        $st->bindValue( ":login", $this->login, \PDO::PARAM_STR );
        $st->bindValue( ":salt", $saltToUpdate, \PDO::PARAM_STR );
        $st->bindValue( ":pass", $passToUpdate, \PDO::PARAM_STR );
        $st->bindValue( ":role", $this->role, \PDO::PARAM_STR );
        $st->bindValue( ":email", $this->email, \PDO::PARAM_STR );
        $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
        $st->execute();
    }
    
    /**
     * Вернёт id пользователя
     * 
     * @return ?int
     */
    public function getId()
    {
        if ($this->userName !== 'guest'){
            $sql = "SELECT id FROM users where login = :userName";
            $st = $this->pdo->prepare($sql); 
            $st -> bindValue( ":userName", $this->userName, \PDO::PARAM_STR );
            $st -> execute();
            $row = $st->fetch();
            return $row['id']; 
        } else  {
            return null;
        }  
    }
    
    /**
     * Проверка логина и пароля пользователя.
     */
    public function getAuthData($login): ?array {
	$sql = "SELECT salt, pass FROM users WHERE login = :login";
	$st = $this->pdo->prepare($sql);
	$st->bindValue(":login", $login, \PDO::PARAM_STR);
	$st->execute();
	$authData = $st->fetch(\PDO::FETCH_ASSOC);
	return $authData ? $authData : null;
    }
    
    /**
     * Проверяем активность пользователя.
     */
    public function getRole($login): array {
	$sql = "SELECT role FROM users WHERE login = :login";
	$st = $this->pdo->prepare($sql);
	$st->bindValue(":login", $login, \PDO::PARAM_STR);
	$st->execute();	
	return $st->fetch();
    }
    
    /**
     * Обновить время успешного логина
     */
    public function updateSuccessfulLogin($login) {
        $sql = "UPDATE $this->tableName SET last_successful_login = :loginTime WHERE login = :login";
        $st = $this->pdo->prepare($sql);
        $dateTime = new \DateTime('NOW', new \DateTimeZone('Europe/Moscow'));
        $st->bindValue(":loginTime", $dateTime->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $st->bindValue(":login", $login, \PDO::PARAM_STR);
        $st->execute();
    }
    
    /**
     * Обновить время неудачного логина
     */
    public function updateFailedLogin($login) {
        $sql = "SELECT id FROM $this->tableName WHERE login = :login";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":login", $login, \PDO::PARAM_STR);
        $st->execute();
        $user = $st->fetch(\PDO::FETCH_ASSOC);
        
        if ($user) {
            $sql = "UPDATE $this->tableName SET last_failed_login = :loginTime WHERE login = :login";
            $st = $this->pdo->prepare($sql);
            $dateTime = new \DateTime('NOW', new \DateTimeZone('Europe/Moscow'));
            $st->bindValue(":loginTime", $dateTime->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
            $st->bindValue(":login", $login, \PDO::PARAM_STR);
            $st->execute();
        }
    }

}