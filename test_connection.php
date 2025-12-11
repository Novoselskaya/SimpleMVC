<?php
// test_connection.php
header('Content-Type: text/plain; charset=utf-8');

$config = [
    'dsn' => 'mysql:host=localhost;dbname=smvcbase;charset=utf8',
    'username' => 'root',
    'password' => 'ваш_пароль'  // ваш реальный пароль
];

echo "Тест подключения к MySQL\n";
echo "=========================\n";
echo "DSN: " . $config['dsn'] . "\n";
echo "User: " . $config['username'] . "\n";

try {
    $pdo = new PDO(
        $config['dsn'],
        $config['username'],
        $config['password'],
        [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "✓ Подключение успешно!\n\n";
    
    // Проверим версию MySQL
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "Версия MySQL: " . $version['version'] . "\n";
    
    // Проверим список баз данных
    echo "\nДоступные базы данных:\n";
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($databases as $db) {
        echo "- $db\n";
    }
    
    // Проверим существование нужной базы
    if (in_array('smvcbase', $databases)) {
        echo "\n✓ База данных 'smvcbase' существует\n";
    } else {
        echo "\n✗ База данных 'smvcbase' НЕ существует\n";
    }
    
} catch (PDOException $e) {
    echo "\n✗ Ошибка подключения: " . $e->getMessage() . "\n";
    
    // Дополнительная диагностика
    echo "\nДополнительная диагностика:\n";
    
    // Проверяем, запущен ли MySQL
    echo "Проверка сервиса MySQL: ";
    $output = shell_exec('systemctl is-active mysql 2>/dev/null || service mysql status 2>/dev/null');
    if (strpos($output, 'active') !== false) {
        echo "✓ Запущен\n";
    } else {
        echo "✗ Не запущен\n";
    }
    
    // Проверяем сокет
    echo "Проверка сокета: ";
    if (file_exists('/var/run/mysqld/mysqld.sock')) {
        echo "✓ Сокет существует\n";
    } else {
        echo "✗ Сокет не найден\n";
        echo "Поиск сокета в других местах...\n";
        $sockets = [
            '/tmp/mysql.sock',
            '/var/lib/mysql/mysql.sock',
            '/Applications/MAMP/tmp/mysql/mysql.sock'
        ];
        foreach ($sockets as $socket) {
            if (file_exists($socket)) {
                echo "Найден сокет: $socket\n";
            }
        }
    }
}