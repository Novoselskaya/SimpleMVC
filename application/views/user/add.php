<?php include('includes/admin-users-nav.php'); ?>
<h2><?= $addAdminusersTitle ?></h2>

<?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<form id="addUser" method="post" action="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("admin/adminusers/add")?>"> 

    <div class="form-group">
        <label for="login">Введите имя пользователя</label>
        <input type="text" class="form-control" name="login" id="login" placeholder="имя пользователя" value="<?= isset($formData['login']) ? htmlspecialchars($formData['login']) : '' ?>">
    </div>
    <div class="form-group">
        <label for="pass">Введите пароль (часть 1)</label>
        <input type="text" class="form-control" name="pass" id="pass" placeholder="пароль часть 1">
    </div>
    <div class="form-group">
        <label for="pass_part2">Введите пароль (часть 2)</label>
        <input type="text" class="form-control" name="pass_part2" id="pass_part2" placeholder="пароль часть 2">
    </div>
    <div class="form-group">
        <label for="pass_part3">Введите пароль (часть 3)</label>
        <input type="text" class="form-control" name="pass_part3" id="pass_part3" placeholder="пароль часть 3">
    </div>
    <div class="form-group">   
        <label for="role">Права доступа</label>
        <select name="role" id="role" class="form-control"> 
            <option value="admin" <?= (isset($formData['role']) && $formData['role'] == 'admin') ? 'selected' : '' ?>>Администратор</option>
            <option value="auth_user" <?= (isset($formData['role']) && $formData['role'] == 'auth_user') ? 'selected' : '' ?>>Зарегистрированный пользователь</option>
        </select>
    </div>
    <div class="form-group">
        <label for="email">Введите e-mail </label>
        <input type="text" class="form-control" name="email" id="email" placeholder="адрес электропочты" value="<?= isset($formData['email']) ? htmlspecialchars($formData['email']) : '' ?>">
    </div>
    <input type="submit" class="btn btn-primary" name="saveNewUser" value="Сохранить">
    <input type="submit" class="btn" name="cancel" value="Назад">
</form>


