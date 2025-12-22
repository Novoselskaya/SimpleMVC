<?php 
use ItForFree\SimpleMVC\Router\WebRouter;

?>

<?php if (empty($results['articles'])): ?>
    <p>Статьи не найдены. Всего статей в базе: <?php echo $results['totalRows'] ?? 0 ?></p>
<?php else: ?>
<ul id="headlines">
<?php foreach ($results['articles'] as $article) { 
    try {
        $authors = $article->getAuthors();
        $authorNames = array();
        foreach ($authors as $author) {
            if (!empty($author->login)) {
                $authorNames[] = htmlspecialchars($author->login);
            }
        }
    } catch (\Exception $e) {
        $authorNames = array();
    }
    
    $Subcategory = new \application\models\SubcategoryModel();
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

            <?php if (isset($article->subcategory_id) && $article->subcategory_id) { ?>
                <?php 
                $subcategory = $Subcategory->getById($article->subcategory_id);
                if ($subcategory) { ?>
                    <span class="subcategory" style="font-size: 80%; color: #666;">
                        | 
                    <a href="<?= WebRouter::link("subcategory/archive&subcategoryId=" . $article->subcategory_id)?>" style="color:#666">
                        <?php echo htmlspecialchars($subcategory->name ?? '')?>
                    </a>
                    </span>
                <?php } ?>
            <?php } ?>
        </h2>
        
        <!-- Отображение авторов -->
        <?php if (!empty($authorNames)) { ?>
            <div class="article-authors" style="font-size: 0.8em; color: #666; margin: 3px 0;">
                <strong>Авторы:</strong> <?php echo implode(', ', $authorNames) ?>
            </div>
        <?php } ?>
        
        <p class="content"><?php echo htmlspecialchars($article->content ?? '')?></p>
    </li>
<?php } ?>
</ul>
<?php endif; ?>

<p><a href="<?= WebRouter::link("category/archive")?>">Article Archive</a></p>
