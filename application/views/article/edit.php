<?php 
use ItForFree\SimpleMVC\Router\WebRouter;

// Группируем подкатегории по категориям
$subcategoriesByCategory = array();
foreach ($allSubcategories as $subcat) {
    $subcategoriesByCategory[$subcat->category_id][] = $subcat;
}
?>
<?php include('includes/admin-articles-nav.php'); ?>

<h2><?php echo htmlspecialchars($pageTitle ?? 'Редактирование статьи')?></h2>

<form method="post" action="<?= WebRouter::link("admin/adminarticles/" . ($formAction ?? 'edit')) . ($article->id ? "&id=" . $article->id : '')?>">
    <input type="hidden" name="id" value="<?php echo $article->id ?? '' ?>">

    <div class="form-group">
        <label for="title">Название статьи</label>
        <input type="text" class="form-control" name="title" id="title" placeholder="Название статьи" required maxlength="255" value="<?php echo htmlspecialchars($article->title ?? '')?>" />
    </div>

    <div class="form-group">
        <label for="summary">Краткое описание</label>
        <textarea class="form-control" name="summary" id="summary" placeholder="Краткое описание статьи" required maxlength="1000" style="height: 5em;"><?php echo htmlspecialchars($article->summary ?? '')?></textarea>
    </div>

    <div class="form-group">
        <label for="content">Содержание статьи</label>
        <textarea class="form-control" name="content" id="content" placeholder="Содержание статьи" required maxlength="100000" style="height: 30em;"><?php echo htmlspecialchars($article->content ?? '')?></textarea>
    </div>

    <div class="form-group">
        <label for="categoryId">Категория</label>
        <select class="form-control" name="categoryId" id="categoryId">
            <option value="0"<?php echo !$article->categoryId ? " selected" : ""?>>(нет)</option>
            <?php foreach ($categories as $category) { ?>
                <option value="<?php echo $category->id?>"<?php echo ($category->id == $article->categoryId) ? " selected" : ""?>><?php echo htmlspecialchars($category->name ?? '')?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="subcategory_id">Подкатегория</label>
        <select class="form-control" name="subcategory_id" id="subcategory_id">
            <option value="0">(нет)</option>
            <?php 
            foreach ($subcategoriesByCategory as $categoryId => $subcats) {
                $category = null;
                foreach ($categories as $cat) {
                    if ($cat->id == $categoryId) {
                        $category = $cat;
                        break;
                    }
                }
                if ($category) {
                    echo '<optgroup label="' . htmlspecialchars($category->name ?? '') . '">';
                    foreach ($subcats as $subcat) {
                        $selected = ($subcat->id == $article->subcategory_id) ? " selected" : "";
                        echo '<option value="' . $subcat->id . '"' . $selected . '>' . 
                            htmlspecialchars($subcat->name ?? '') . '</option>';
                    }
                    echo '</optgroup>';
                }
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="authorIds">Авторы</label>
        <select class="form-control" name="authorIds[]" id="authorIds" multiple="multiple" size="5" style="height: auto; min-height: 100px;">
            <?php 
            // Получаем текущих авторов статьи
            $currentAuthors = $article->getAuthors();
            $currentAuthorIds = array();
            foreach ($currentAuthors as $author) {
                $currentAuthorIds[] = $author->id;
            }
            
            // Отображаем всех пользователей
            foreach ($users as $user) { 
                $selected = in_array($user->id, $currentAuthorIds) ? " selected" : "";
            ?>
                <option value="<?php echo $user->id?>"<?php echo $selected?>>
                    <?php echo htmlspecialchars($user->login ?? '') ?>
                </option>
            <?php } ?>
        </select>
        <small style="color: #666;">Удерживайте Ctrl (Cmd на Mac) для выбора нескольких авторов</small>
    </div>

    <div class="form-group">
        <label for="publicationDate">Дата публикации</label>
        <input type="date" class="form-control" name="publicationDate" id="publicationDate" placeholder="YYYY-MM-DD" required maxlength="10" value="<?php echo $article->publicationDate ? date("Y-m-d", $article->publicationDate) : date("Y-m-d") ?>" />
    </div>

    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="is_visible" id="is_visible" value="1" 
                   <?php echo ($article->is_visible ?? 1) ? 'checked="checked"' : '' ?> />
            <label class="form-check-label" for="is_visible">
                Видима для пользователей
            </label>
        </div>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" name="saveChanges" value="Сохранить изменения" />
        <input type="submit" class="btn" name="cancel" value="Отмена" />
    </div>
</form>

<?php if ($article->id): ?>
    <p><a href="<?= WebRouter::link("admin/adminarticles/delete&id=" . $article->id)?>" onclick="return confirm('Удалить эту статью?')">
            Удалить статью
        </a>
    </p>
<?php endif; ?>

