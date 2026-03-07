<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root."database/member.php";

$arr = [];
$arr = getAllMembers();