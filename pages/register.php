<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

if(isLoggedIn()){
    header("Location: /pages/home.php");
    exit;
}

require $project_root . "logic/register.php";

$_title = 'Register';
include $project_root . 'components/header.php';
?>

<div class="auth-container">
    <div class="auth-box auth-box--wide">
        <h2 class="auth-title">Create Account</h2>

        <?php if (!empty($errors['general'])):?>
            <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>

        <form id="register-form" method="POST" novalidate>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name"
                                value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                                class="<?= isset($errors['name']) ? 'input-error' : '' ?>">
                        <?php if (!empty($errors['name'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors['name']) ?></span>
                        <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                                value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                class="<?= isset($errors['email']) ? 'input-error' : '' ?>">
                        <?php if (!empty($errors['email'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors['email']) ?></span>
                        <?php endif; ?>
                    </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender"
                                class="<?= isset($errors['gender']) ? 'input-error' : '' ?>">
                            <option value="">-- Select --</option>
                            <option value="male"    <?= ($old['gender'] ?? '') === 'male'   ? 'selected' : '' ?>>Male</option>
                            <option value="female"  <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other"   <?= ($old['gender'] ?? '') === 'other'  ? 'selected' : '' ?>>Other</option>
                        </select>
                        <?php if (!empty($errors['gender'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors['gender']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone"
                                value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                                class="<?= isset($errors['phone']) ? 'input-error' : '' ?>">
                        <?php if (!empty($errors['phone'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors['phone']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

            <div class="form-row">
                <div class="form-group">
                <label for="password">Password</label>
                <div class="password-group">
                    <input type="password" id="password" name="password"
                            class="<?= isset($errors['password']) ? 'input-error' : '' ?>">
                    <button type="button" class="toggle-password" title="Show/Hide password">👁</button>
                </div>
                <?php if (!empty($errors['password'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['password']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password2">Confirm Password</label>
                <div class="password-group">
                    <input type="password" id="password2" name="password2"
                            class="<?= isset($errors['password2']) ? 'input-error' : '' ?>">
                    <button type="button" class="toggle-password" title="Show/Hide password">👁</button>
                </div>
                    <?php if (!empty($errors['password2'])): ?>
                        <span class="error-msg"><?= htmlspecialchars($errors['password2']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="/pages/login.php">Login here</a></p>
    </div>
</div>

<?php include $project_root . 'components/footer.php'; ?>