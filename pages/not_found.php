<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

// Determine where the "Go Back" button should take the user
$return_url = "/pages/home.php"; // Default for public/members
if (isAdmin()) {
    $return_url = "/pages/admin/order_listing.php";
} elseif (isMember()) {
    $return_url = "/pages/home.php";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOAIR — Page Not Found</title>
    <link rel="stylesheet" href="/css/style.css"> <!-- Replace with your global CSS if needed -->
    <style>
        .notfound-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            color: #333;
        }
        .notfound-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }
        .notfound-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
        }
        .notfound-desc {
            font-size: 1.1rem;
            color: #666;
            max-width: 500px;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .btn-return {
            background-color: #111;
            color: #fff;
            padding: 12px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-return:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

<div class="notfound-wrapper">
    <div class="notfound-icon">🚫</div>
    <h1 class="notfound-title">Access Restricted / Not Found</h1>
    <p class="notfound-desc">
        The page you are looking for does not exist or you do not have permission to view it. 
        If you believe this is an error, please contact the administrator.
    </p>
    <a href="<?= htmlspecialchars($return_url) ?>" class="btn-return">OK</a>
</div>

</body>
</html>