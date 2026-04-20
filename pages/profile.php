<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

requireMember(); // non-member not allowed

require $project_root . "logic/profile.php";

$_title = 'Your Profile';
include $project_root . "components/header.php";

$photo_url = !empty($member->photo)
    ? '/images/members/' . htmlspecialchars($member->photo)
    : 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&background=F39E9E&color=fff';
?>

<div class="profile-container">

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="profile-header">
        <img src="<?= $photo_url ?>" alt="Profile Photo" class="profile-avatar photo-lightbox-trigger" id="avatar-preview">
        <div>
            <h2><?= htmlspecialchars($member->name) ?></h2>
            <p class="text-muted"><?= htmlspecialchars($member->email) ?></p>
        </div>
    </div>

    <?php
        $active_tab = 'tab-info';
        if (isset($_POST['update_password']) && !empty($errors)) $active_tab = 'tab-password';
        if (isset($_POST['update_photo'])    && !empty($errors)) $active_tab = 'tab-photo';
    ?>

    <div class="tab-nav">
        <button class="tab-btn <?= $active_tab === 'tab-info'     ? 'active' : '' ?>" data-tab="tab-info">Profile Info</button>
        <button class="tab-btn <?= $active_tab === 'tab-password' ? 'active' : '' ?>" data-tab="tab-password">Change Password</button>
        <button class="tab-btn <?= $active_tab === 'tab-photo'    ? 'active' : '' ?>" data-tab="tab-photo">Profile Photo</button>
    </div>

    <!-- TAB: PROFILE INFO -->
    <div class="tab-content <?= $active_tab === 'tab-info' ? 'active' : '' ?>" id="tab-info">
        <form id="form-profile" method="POST" novalidate>
            <input type="hidden" name="update_profile" value="1">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name"
                    value="<?= htmlspecialchars($member->name) ?>"
                    class="<?= isset($errors['name']) ? 'input-error' : '' ?>">
                <?php if (!empty($errors['name'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                    value="<?= htmlspecialchars($member->email) ?>"
                    class="<?= isset($errors['email']) ? 'input-error' : '' ?>">
                <?php if (!empty($errors['email'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="<?= isset($errors['gender']) ? 'input-error' : '' ?>">
                        <option value="">-- Select --</option>
                        <option value="male"   <?= $member->gender === 'male'   ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $member->gender === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other"  <?= $member->gender === 'other'  ? 'selected' : '' ?>>Other</option>
                    </select>
                    <?php if (!empty($errors['gender'])): ?>
                        <span class="error-msg"><?= htmlspecialchars($errors['gender']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone"
                        placeholder="e.g. 012-3456789"
                        value="<?= htmlspecialchars($member->phone) ?>"
                        class="<?= isset($errors['phone']) ? 'input-error' : '' ?>">
                    <?php if (!empty($errors['phone'])): ?>
                        <span class="error-msg"><?= htmlspecialchars($errors['phone']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- TAB: CHANGE PASSWORD -->
    <div class="tab-content <?= $active_tab === 'tab-password' ? 'active' : '' ?>" id="tab-password">
        <form id="form-password" method="POST" novalidate>
            <input type="hidden" name="update_password" value="1">

            <div class="form-group">
                <label>Current Password</label>
                <div class="password-wrapper">
                    <input type="password" name="current_password" id="current_password"
                        class="<?= isset($errors['current_password']) ? 'input-error' : '' ?>">
                    <button type="button" class="toggle-password" data-target="current_password" aria-label="Toggle password visibility">
                        <span class="eye-icon">👁</span>
                    </button>
                    </div>
                <?php if (!empty($errors['current_password'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['current_password']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <div class="password-wrapper">
                    <input type="password" name="new_password" id="new_password"
                        class="<?= isset($errors['new_password']) ? 'input-error' : '' ?>">
                    <button type="button" class="toggle-password" data-target="new_password" aria-label="Toggle password visibility">
                        <span class="eye-icon">👁</span>
                    </button>
                </div>
                <?php if (!empty($errors['new_password'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['new_password']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <div class="password-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password"
                        class="<?= isset($errors['confirm_password']) ? 'input-error' : '' ?>">
                    <button type="button" class="toggle-password" data-target="confirm_password" aria-label="Toggle password visibility">
                        <span class="eye-icon">👁</span>
                    </button>
                </div>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['confirm_password']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
        <p class="forgot-link" style="margin-top: 14px;">
            <a href="/pages/forgot_password.php?email=<?= urlencode($member->email) ?>">Forgot your password?</a>
        </p>
    </div>

    <!-- TAB: PROFILE PHOTO -->
    <div class="tab-content <?= $active_tab === 'tab-photo' ? 'active' : '' ?>" id="tab-photo">
        <form id="form-photo" method="POST" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="update_photo" value="1">

            <div class="photo-upload-area">
                <img src="<?= $photo_url ?>" alt="Current Photo" class="photo-preview photo-lightbox-trigger" id="photo-preview">
                <div class="form-group">
                    <label for="photo">Choose New Photo</label>
                    <input type="file" id="photo" name="photo" accept="image/*">
                    <?php if (!empty($errors['photo'])): ?>
                        <span class="error-msg"><?= htmlspecialchars($errors['photo']) ?></span>
                    <?php endif; ?>
                    <small class="text-muted">JPG, PNG, GIF, WEBP. Max 2MB.</small>
                </div>
            </div>
            <!-- Hidden input to hold webcam-captured photo as base64 -->
            <input type="hidden" id="webcam-photo-data" name="webcam_photo">
            <div class="photo-action-buttons">
                <button type="submit" class="btn btn-primary">Upload Photo</button>
                <button type="button" class="btn btn-webcam" id="open-webcam-btn">📷 Use Webcam</button>
            </div>
        </form>
    </div>

    <!-- WEBCAM MODAL -->
    <div id="webcam-modal" class="webcam-modal" aria-modal="true" role="dialog" aria-label="Webcam Capture">
        <div class="webcam-modal-inner">
            <div class="webcam-modal-header">
                <h3>Take a Photo</h3>
                <button type="button" id="webcam-close-btn" class="webcam-close-btn" title="Close">&times;</button>
            </div>
            <div class="webcam-body">
                <video id="webcam-video" autoplay playsinline style="display:none;"></video>
                <canvas id="webcam-canvas" style="display:none;"></canvas>
                <img id="webcam-snapshot" src="" alt="Snapshot Preview" style="display:none;">
            </div>
            <div class="webcam-controls">
                <button type="button" id="webcam-capture-btn" class="btn btn-primary">📸 Capture</button>
                <button type="button" id="webcam-retake-btn" class="btn btn-secondary" style="display:none;">↩ Retake</button>
                <button type="button" id="webcam-use-btn" class="btn btn-primary" style="display:none;">✔ Use This Photo</button>
            </div>
            <p id="webcam-error-msg" class="webcam-error" style="display:none;"></p>
        </div>
    </div>
    <!-- PHOTO LIGHTBOX -->
            <div id="photo-lightbox">
                <button id="photo-lightbox-close" title="Close">&times;</button>
                <img id="photo-lightbox-img" src="" alt="Profile Photo">
            </div>
</div>

<?php include $project_root . 'components/footer.php'; ?>