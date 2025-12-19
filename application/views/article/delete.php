<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>
<?php include('includes/admin-articles-nav.php'); ?>

<h2><?php echo htmlspecialchars($pageTitle ?? 'Удаление статьи')?></h2>

<p>Вы уверены, что хотите удалить статью "<?php echo htmlspecialchars($article->title ?? '')?>"?</p>

<form method="post" action="<?= WebRouter::link("admin/adminarticles/delete&id=" . $article->id)?>">
    <input type="hidden" name="id" value="<?php echo $article->id ?>">
    
    <input type="submit" class="btn btn-danger" name="deleteArticle" value="Удалить" />
    <input type="submit" class="btn" name="cancel" value="Отмена" />
</form>

