<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>
<?php include('includes/admin-articles-nav.php'); ?>

<h2>Все статьи</h2>

<p><a href="<?= WebRouter::link("admin/adminarticles/add")?>">Добавить новую статью</a></p>

<?php if (!empty($articles)): ?>
<table class="table">
    <thead>
    <tr>
      <th scope="col">Дата публикации</th>
      <th scope="col">Название</th>
      <th scope="col">Категория</th>
      <th scope="col">Видимость</th>
      <th scope="col"></th>
    </tr>
     </thead>
    <tbody>
    <?php foreach($articles as $article): ?>
    <tr>
        <td><?php echo $article->publicationDate ? date('j M Y', $article->publicationDate) : ''?></td>
        <td>
            <a href="<?= WebRouter::link("admin/adminarticles/edit&id=" . $article->id)?>">
                <?php echo htmlspecialchars($article->title ?? '')?>
            </a>
        </td>
        <td>
            <?php 
            if(isset($article->categoryId) && isset($categories[$article->categoryId])) {
                echo htmlspecialchars($categories[$article->categoryId]->name ?? '');                        
            } else {
                echo "Без категории";
            }?>
        </td>
        <td><?php echo $article->is_visible ? 'Видима' : 'Скрыта'?></td>
        <td>
            <a href="<?= WebRouter::link("admin/adminarticles/delete&id=" . $article->id)?>">Удалить</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p>Всего статей: <?php echo $totalRows ?? 0 ?></p>
<?php else: ?>
    <p>Статей пока нет.</p>
<?php endif; ?>

