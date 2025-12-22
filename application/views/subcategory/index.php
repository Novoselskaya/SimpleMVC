<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<h2>Подкатегории</h2>

<p><a href="<?= WebRouter::link("admin/adminsubcategories/add")?>">Добавить новую подкатегорию</a></p>

<?php if (!empty($subcategories)): ?>
<table class="table">
    <thead>
    <tr>
      <th scope="col">Название</th>
      <th scope="col">Категория</th>
      <th scope="col"></th>
    </tr>
     </thead>
    <tbody>
    <?php foreach($subcategories as $subcategory): ?>
    <tr>
        <td>
            <a href="<?= WebRouter::link("admin/adminsubcategories/edit&id=" . $subcategory->id)?>">
                <?php echo htmlspecialchars($subcategory->name ?? '')?>
            </a>
        </td>
        <td>
            <?php 
            if(isset($subcategory->category_id) && isset($categories[$subcategory->category_id])) {
                echo htmlspecialchars($categories[$subcategory->category_id]->name ?? '');                        
            } else {
                echo "Без категории";
            }?>
        </td>
        <td>
            <a href="<?= WebRouter::link("admin/adminsubcategories/delete&id=" . $subcategory->id)?>">Удалить</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p>Всего подкатегорий: <?php echo $totalRows ?? 0 ?></p>
<?php else: ?>
    <p>Подкатегорий пока нет.</p>
<?php endif; ?>

