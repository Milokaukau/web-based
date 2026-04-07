<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/home.php";

$_title = "Home";
require $project_root."components/header.php";
?>

<?php 
    if ($arr){
        foreach ($arr as $data){
?>
    <p><?= $data->name ?></p>
<?php

        }
    }
?>

<?php 
// Ensure footer is placed at the most bottom
require $project_root."components/footer.php"; 
?>

<script src="../js/home.js"></script>