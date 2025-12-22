<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');
?>

<div id="article">
    <h1><?php echo htmlspecialchars($results['article']->title) ?></h1>
    
    <div class="article-meta">
        <span class="pubDate">
            Published on <?php echo date('j F Y', $results['article']->publicationDate) ?>
        </span>
        
        <?php if ($results['category']) { ?>
            <span class="category">
                in 
                <a href="<?= WebRouter::link("category/archive&categoryId=" . $results['category']->id)?>">
                    <?php echo htmlspecialchars($results['category']->name) ?>
                </a>
            </span>
        <?php } ?>
        
        <?php if ($results['subcategory']) { ?>
            <span class="subcategory">
                | 
                <a href="<?= WebRouter::link("subcategory/archive&subcategoryId=" . $results['subcategory']->id)?>">
                    <?php echo htmlspecialchars($results['subcategory']->name) ?>
                </a>
            </span>
        <?php } ?>
    </div>
    
    <?php 
    // Отображение авторов
    $authors = $results['article']->getAuthors();
    $authorNames = array();
    if (!empty($authors)) { 
        foreach ($authors as $author) {
            if (!empty($author->login)) {
                $authorNames[] = htmlspecialchars($author->login);
            }
        }
    }
    if (!empty($authorNames)) { ?>
        <div class="article-authors" style="margin: 10px 0; padding: 8px; background: #e8f4f8; border-radius: 5px;">
            <strong>Авторы: </strong> <?php echo implode(', ', $authorNames) ?>
        </div>
    <?php } ?>
    
    <div class="article-content">
        <?php echo $results['article']->content ?>
    </div>
</div>

<p><a href="<?= WebRouter::link("homepage/index")?>">Return to Homepage</a></p>

