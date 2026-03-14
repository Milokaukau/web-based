<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/member.php";
require_once $project_root."logic/auth_helper.php";

$_title = 'Home';

include $project_root.'components/header.php';
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
include $project_root.'components/footer.php';
?>
