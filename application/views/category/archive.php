<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<?php if (isset($results['category']) && $results['category']): ?>
    <h1><?php echo htmlspecialchars($results['category']->name ?? '') ?></h1>
    <?php if ($results['category']->description): ?>
        <h3 class="categoryDescription"><?php echo htmlspecialchars($results['category']->description ?? '') ?></h3>
    <?php endif; ?>
<?php else: ?>
    <h1>Архив статей</h1>
<?php endif; ?>

<ul id="headlines" class="archive">
<?php foreach ($results['articles'] as $article) { 
    $thanks = $article->getThanks();
    $thanksNames = array();  
    foreach ($thanks as $user) {
        $thanksNames[] = htmlspecialchars($user->login ?? '');
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
        
        <!-- Отображение благодарностей -->
        <?php if (!empty($thanksNames)) { ?>
            <div class="article-thanks" style="font-size: 0.8em; color: #888; margin: 3px 0;">
                <strong>Благодарности:</strong> <?php echo implode('| ', $thanksNames) ?>
            </div>
        <?php } ?>
        
        <p class="content"><?php echo htmlspecialchars(mb_substr($article->content ?? '', 0, 50) . "...")?></p>
        
        <a href="<?= WebRouter::link("article/view&articleId=" . $article->id)?>" class="showContent">
            Показать полностью
        </a>
    </li>
<?php } ?>
</ul>

<p><a href="<?= WebRouter::link("homepage/index")?>">Return to Homepage</a></p>


