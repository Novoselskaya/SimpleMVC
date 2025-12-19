<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<ul style="list-style: none; padding: 0; margin: 20px 0;">
    <li style="display: inline-block; margin-right: 10px;">
        <a href="<?= WebRouter::link("admin/adminarticles/index")?>">Список статей</a>
    </li>
    <li style="display: inline-block; margin-right: 10px;">
        <a href="<?= WebRouter::link("admin/adminarticles/add")?>">Добавить статью</a>
    </li>
    <li style="display: inline-block;">
        <a href="<?= WebRouter::link("admin/adminusers/index")?>">Пользователи</a>
    </li>
</ul>


