<?php 
use ItForFree\SimpleMVC\Config;

try {
    $User = Config::getObject('core.user.class');
} catch (\Exception $e) {
    $User = null;
}

$pageTitle = 'Простой CMS блог';
if (isset($results) && is_array($results) && isset($results['pageTitle'])) {
    $pageTitle = htmlspecialchars($results['pageTitle']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?php echo $pageTitle ?></title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/CSS/cms-style.css" />
    <script src="/JS/jquery-3.2.1.js"></script>
    <script src="/JS/loaderIdentity.js"></script>
    <script src="/JS/showContent.js"></script>
</head>
<body>
    <div id="container">
        <div style="text-align: right; margin-bottom: 10px;">
            <?php if ($User && method_exists($User, 'isAllowed')): ?>
                <?php if ($User->isAllowed("login/logout")): ?>
                    <a href="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("login/logout")?>">Выход (<?= htmlspecialchars($User->userName ?? '') ?>)</a>
                <?php elseif ($User->isAllowed("login/login")): ?>
                    <a href="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("login/login")?>">Вход</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("login/login")?>">Вход</a>
            <?php endif; ?>
        </div>
        <a href="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("homepage/index")?>">
            <img id="logo" src="/images/logo.jpg" alt="Widget News" />
        </a>
        
        <?= $CONTENT_DATA ?>
        
        <div id="footer">
            Простая PHP CMS &copy; <?php echo date('Y') ?>. Все права принадлежат всем. ;) 
            <?php if ($User && method_exists($User, 'isAllowed')): ?>
                <?php if ($User->isAllowed("admin/adminusers/index")): ?>
                    <a href="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("admin/adminusers/index")?>">Site Admin</a>
                <?php elseif ($User->isAllowed("login/login")): ?>
                    <a href="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("login/login")?>">Вход</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("login/login")?>">Вход</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

