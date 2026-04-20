<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

// Redirect away if already logged in as admin
if (isAdmin()) {
    header("Location: /pages/admin/dashboard.php");
    exit;
}

require $project_root . "logic/forgot_password_admin.php";

$_title = 'Admin Forgot Password';
include $project_root . 'components/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h2 class="auth-title">Forgot Password</h2>

        <?php
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        ?>
        <?php if ($flash && $flash['type'] === 'error'): ?>
            <div class="alert alert-error"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>

            <div class="alert alert-success">
                A reset link has been sent to your email address.<br>
                Please check your inbox and spam folder.
            </div>

            <p style="font-size:13px;color:#666;text-align:center;margin-top:8px;">
                The link will expire in <strong>15 minutes</strong>.
            </p>

            <p style="text-align:center;margin-top:16px;font-size:14px;">
                Didn't receive an email?
                <a href="/pages/admin/forgot_password.php" class="try-again-link">Try again</a>
            </p>

            <p class="auth-footer"><a href="/pages/admin/login.php">Back to Admin Login</a></p>

        <?php else: ?>

            <p class="forgot-desc">A reset password link will be sent to your admin email address.</p>

            <form id="forgot-form" method="POST" novalidate>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        placeholder="you@example.com"
                        class="<?= isset($errors['email']) ? 'input-error' : '' ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error-msg"><?= htmlspecialchars($errors['email']) ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Send Reset Link</button>
            </form>

            <p class="auth-footer"><a href="/pages/admin/login.php">Back to Admin Login</a></p>

        <?php endif; ?>
    </div>
</div>

<?php include $project_root . 'components/footer.php'; ?>