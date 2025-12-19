<?php
/**
 * Используйте этот файл для разовой инициализации компонентов до старта приложения, 
 * которые не могут быть инициализированы средствами ядра (через файл конфигурации)
 */

// Включаем отображение ошибок для отладки (в продакшене отключить!)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Устанавливаем часовой пояс (Europe/Moscow = UTC+3)
date_default_timezone_set('Europe/Moscow');

\ItForFree\SimpleAsset\SimpleAssetManager::$assetsPath = '/assets'; //инициализация пути добавления ассетов


