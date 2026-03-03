<?php
require_once "../logic/get_member.php";

$_title = 'title';
include '../components/header.php';
?>

<p><?= $member->name ?></p>

<?php
include '../components/footer.php';
?>
