<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<h1><?php echo htmlspecialchars($results['subcategory']->name ?? '') ?></h1>

<ul id="headlines" class="archive">
<?php foreach ($results['articles'] as $article) { 
?>
    <li class='article-<?php echo $article->id?>'>
        <h2>
            <span class="pubDate">
                <?php echo $article->publicationDate ? date('j F', $article->publicationDate) : ''?>
            </span>
            
            <a href="<?= WebRouter::link("article/view&articleId=" . $article->id)?>">
                <?php echo htmlspecialchars($article->title ?? '')?>
            </a>
            
            <?php if (isset($article->categoryId) && isset($results['categories'][$article->categoryId])) { ?>
                <span class="category">
                    in 
                    <a href="<?= WebRouter::link("category/archive&categoryId=" . $article->categoryId)?>">
                        <?php echo htmlspecialchars($results['categories'][$article->categoryId]->name ?? '')?>
                    </a>
                </span>
            <?php } else { ?>
                <span class="category">
                    <?php echo "Без категории"?>
                </span>
            <?php } ?>
        </h2>
        
        <p class="content"><?php echo htmlspecialchars(mb_substr($article->content ?? '', 0, 50) . "...")?></p>
        
        <a href="<?= WebRouter::link("article/view&articleId=" . $article->id)?>" class="showContent">
            Показать полностью
        </a>
    </li>
<?php } ?>
</ul>

<p><a href="<?= WebRouter::link("homepage/index")?>">Return to Homepage</a></p>


