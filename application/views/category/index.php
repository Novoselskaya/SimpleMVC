<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<h2>Категории</h2>

<p><a href="<?= WebRouter::link("admin/admincategories/add")?>">Добавить новую категорию</a></p>

<?php if (!empty($categories)): ?>
<table class="table">
    <thead>
    <tr>
      <th scope="col">Название</th>
      <th scope="col">Описание</th>
      <th scope="col"></th>
    </tr>
     </thead>
    <tbody>
    <?php foreach($categories as $category): ?>
    <tr>
        <td>
            <a href="<?= WebRouter::link("admin/admincategories/edit&id=" . $category->id)?>">
                <?php echo htmlspecialchars($category->name ?? '')?>
            </a>
        </td>
        <td><?php echo htmlspecialchars($category->description ?? '')?></td>
        <td>
            <a href="<?= WebRouter::link("admin/admincategories/delete&id=" . $category->id)?>">Удалить</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p>Всего категорий: <?php echo $totalRows ?? 0 ?></p>
<?php else: ?>
    <p>Категорий пока нет.</p>
<?php endif; ?>

