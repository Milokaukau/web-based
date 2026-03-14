<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

if (isLoggedIn()){
    header("Location: /pages/home.php");
    exit;
}

require $project_root . "logic/login.php";

$_title = 'Login';
include $project_root . 'components/header.php';
?>

<div class="auth-container">
    <div class ="auth-box">
        <h2 class="auth-title">LOGIN</h2>

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>

            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php if (!empty($errors['general'])):?>
            <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>

        <form id="login-form" method="POST" novalidate>
            <input type="hidden" name="role" value="member">

            <div class="form-group">
                <input type="text" id="login" name="login" placeholder="Email"
                    value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                    class="<?= isset($errors['login']) ? 'input-error' : '' ?>">
                <?php if (!empty($errors['login'])):?>
                    <span class="error-msg"><?= htmlspecialchars($errors['login']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group password-group">
                <input type="password" id="password" name="password" placeholder="Password"
                    class="<?= isset($errors['password']) ? 'input-error' : '' ?>">
                <button type="button" class="toggle-password" id="toggle-password" title="Show/hide password">👁</button>
                <?php if (!empty($errors['password'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['password']) ?></span>
                <?php endif; ?>
            </div>

            <div class="forgot-link">
                <a href="/pages/forgot_password.php">Forgot Password</a>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Login</button>
        </form>

        <p class="auth-footer">New User? <a href="/pages/register.php">Create</a></p>
        <p class="auth-footer admin-link"><a href="/pages/admin/login.php">Login as Admin</a></p>
    </div>
</div>

<?php include $project_root . 'components/footer.php'; ?>