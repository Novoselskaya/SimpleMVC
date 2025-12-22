<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<h2><?php echo htmlspecialchars($pageTitle ?? 'Редактирование категории')?></h2>

<form method="post" action="<?= WebRouter::link("admin/admincategories/" . ($formAction ?? 'edit')) . ($category->id ? "&id=" . $category->id : '')?>">
    <input type="hidden" name="id" value="<?php echo $category->id ?? '' ?>">

    <div class="form-group">
        <label for="name">Название категории</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Название категории" required maxlength="255" value="<?php echo htmlspecialchars($category->name ?? '')?>" />
    </div>

    <div class="form-group">
        <label for="description">Описание</label>
        <textarea class="form-control" name="description" id="description" placeholder="Описание категории" required maxlength="1000" style="height: 5em;"><?php echo htmlspecialchars($category->description ?? '')?></textarea>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" name="saveChanges" value="Сохранить изменения" />
        <input type="submit" class="btn" name="cancel" value="Отмена" />
    </div>
</form>

<?php if ($category->id): ?>
    <p><a href="<?= WebRouter::link("admin/admincategories/delete&id=" . $category->id)?>" onclick="return confirm('Удалить эту категорию?')">
            Удалить категорию
        </a>
    </p>
<?php endif; ?>

