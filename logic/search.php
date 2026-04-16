<?php
// logic/search.php

require_once $project_root . "database/search.php";

$search_results = [];
$search_term    = '';
$total_found    = 0;

if (isset($_GET['q']) && trim($_GET['q']) !== '') {
    $search_term    = trim($_GET['q']);
    $search_results = searchProducts($search_term);
    $total_found    = count($search_results);
}