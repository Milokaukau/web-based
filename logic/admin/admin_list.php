<?php
// logic/admin/admin_list.php
requireSuperAdmin();

// Capture GET filters
$filters = [
    'search_name' => $_GET['search_name'] ?? '',
    'status'      => $_GET['status'] ?? ''
];

// Fetch the filtered list of admins
$admins = getFilteredAdmins($filters);
?>