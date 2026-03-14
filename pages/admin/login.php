<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

if(isAdmin()){
    header("Location: /pages/admin/dashboard.php");
    exit;
}

require $project_root . "logic/login.php";

$_title = 'Admin Login';
include $project_root . 'components/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h2 class="auth-title">Admin Login</h2>

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form id="admin-login-form" method="POST" novalidate>
            <input type="hidden" name="role" value="admin">

            <div class="form-group">
                <label for="login">Admin Email</label>
                <input type="email" id="login" name="login"
                    value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                    class="<?= isset($errors['login']) ? 'input-error' : '' ?>">
                <?php if (!empty($errors['login'])): ?>
                    <span class="error msg"><?= htmlspecialchars($errors['login']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                    class="<?= isset($errors['password']) ? 'input-error' : '' ?>">
                <?php if (!empty($errors['password'])): ?>
                    <span class="error msg"><?= htmlspecialchars($errors['password']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Login as Admin</button>
        </form>

        <p class="auth-footer"><a href="/pages/login.php">← Member Login</a></p>
    </div>
</div>