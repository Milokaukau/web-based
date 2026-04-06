<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/member.php";

$_title = 'Home';

include $project_root.'components/header.php';
?>



<div style="text-align:center; margin-top:50px;">
    
    <a href="/product/index.php">
        <button style="
            padding:12px 25px;
            font-size:18px;
            background:#007bff;
            color:white;
            border:none;
            border-radius:6px;
            cursor:pointer;
        ">
            Product Maintenance
        </button>
    </a>

</div>

<?php
include $project_root.'components/footer.php';
?>
