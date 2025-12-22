<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<h2><?php echo htmlspecialchars($pageTitle ?? 'Удаление подкатегории')?></h2>

<?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage) ?></div>
<?php endif; ?>

<form method="post" action="<?= WebRouter::link("admin/adminsubcategories/delete&id=" . $subcategory->id)?>">
    <input type="hidden" name="id" value="<?php echo $subcategory->id ?>">
    
    <p>Вы уверены, что хотите удалить подкатегорию <strong><?php echo htmlspecialchars($subcategory->name ?? '')?></strong>?</p>
    
    <div class="form-group">
        <input type="submit" class="btn btn-danger" name="deleteSubcategory" value="Удалить" />
        <input type="submit" class="btn" name="cancel" value="Отмена" />
    </div>
</form>

