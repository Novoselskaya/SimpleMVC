<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<h2><?php echo htmlspecialchars($pageTitle ?? 'Редактирование подкатегории')?></h2>

<form method="post" action="<?= WebRouter::link("admin/adminsubcategories/" . ($formAction ?? 'edit')) . ($subcategory->id ? "&id=" . $subcategory->id : '')?>">
    <input type="hidden" name="id" value="<?php echo $subcategory->id ?? '' ?>">

    <div class="form-group">
        <label for="name">Название подкатегории</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Название подкатегории" required maxlength="255" value="<?php echo htmlspecialchars($subcategory->name ?? '')?>" />
    </div>

    <div class="form-group">
        <label for="category_id">Категория</label>
        <select class="form-control" name="category_id" id="category_id" required>
            <option value="">(выберите категорию)</option>
            <?php foreach ($categories as $category) { ?>
                <option value="<?php echo $category->id?>"<?php echo ($category->id == $subcategory->category_id) ? " selected" : ""?>><?php echo htmlspecialchars($category->name ?? '')?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" name="saveChanges" value="Сохранить изменения" />
        <input type="submit" class="btn" name="cancel" value="Отмена" />
    </div>
</form>

<?php if ($subcategory->id): ?>
    <p><a href="<?= WebRouter::link("admin/adminsubcategories/delete&id=" . $subcategory->id)?>" onclick="return confirm('Удалить эту подкатегорию?')">
            Удалить подкатегорию
        </a>
    </p>
<?php endif; ?>

