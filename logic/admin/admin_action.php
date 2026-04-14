<?php
// logic/admin/admin_action.php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/admin.php"; 

requireSuperAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['admin_id'])) {
    $action = $_POST['action'];
    $admin_id = $_POST['admin_id'];

    // Prevent self-sabotage
    if ($admin_id == $_SESSION['admin_id'] && in_array($action, ['suspend', 'activate', 'remove_superadmin'])) {
        redirectWith('/pages/admin/admin_list.php', 'error', 'You cannot alter your own account status or demote yourself.');
    }

    // Call database functions instead of raw queries
    if ($action === 'make_superadmin') {
        setAdminSuperadminStatus($admin_id, 1);
        $msg = "Admin successfully promoted to Superadmin.";
    } elseif ($action === 'remove_superadmin') {
        setAdminSuperadminStatus($admin_id, 0);
        $msg = "Admin successfully demoted to standard Admin.";
    } elseif ($action === 'suspend') {
        setAdminActiveStatus($admin_id, 0);
        $msg = "Admin account deactivated.";
    } elseif ($action === 'activate') {
        setAdminActiveStatus($admin_id, 1);
        $msg = "Admin account activated.";
    }

    $redirect_url = $_SERVER['HTTP_REFERER'] ?? '/pages/admin/admin_list.php';
    redirectWith($redirect_url, 'success', $msg ?? 'Action completed.');
}
?>