<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";

function getMembersData($search, $status, $page_num, $per_page = 10) {
    $pdo = db();

    $offset = ($page_num - 1) * $per_page;

    $where  = [];
    $params = [];

    if ($search !== '') {
        $where[]  = "(name LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($status === 'locked') {
        $where[] = "(locked_until IS NOT NULL AND locked_until > NOW())";
    } elseif ($status === 'active') {
        $where[] = "(locked_until IS NULL OR locked_until <= NOW())";
    }

    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM tb_member $whereSQL");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    // Data
    $pageParams = array_merge($params, [$per_page, $offset]);
    $stmt = $pdo->prepare("SELECT * FROM tb_member $whereSQL ORDER BY id ASC LIMIT ? OFFSET ?");
    $stmt->execute($pageParams);
    $members = $stmt->fetchAll(PDO::FETCH_OBJ);

    return [$members, $total];
}

function getStats() {
    $pdo = db();

    return $pdo->query("SELECT
        COUNT(*) AS total,
        SUM(locked_until IS NULL OR locked_until <= NOW()) AS active_count,
        SUM(locked_until IS NOT NULL AND locked_until > NOW()) AS locked_count
    FROM tb_member")->fetch(PDO::FETCH_OBJ);
}

function getHighAttempts() {
    $pdo = db();
    return $pdo->query("SELECT COUNT(*) FROM tb_member WHERE login_attempts >= 3")->fetchColumn();
}

function getMemberById($id) {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM tb_member WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function getOrders() {
    $pdo = db();
    return $pdo->query("
        SELECT o.*, m.name AS username 
        FROM tb_order o 
        LEFT JOIN tb_member m ON o.member_id = m.id 
        ORDER BY o.id DESC
    ")->fetchAll(PDO::FETCH_OBJ);
}

function getProducts() {
    $pdo = db();
    return $pdo->query("SELECT * FROM tb_product ORDER BY stock ASC")->fetchAll(PDO::FETCH_OBJ);
}

function getAdmin($id) {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM tb_admin WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}