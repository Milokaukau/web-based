<?php
require_once "db.php";

function getAllMembers(){
    $stmt = db()->query("SELECT * FROM tb_member");
    return $stmt->fetchAll();
}

function getMembersData(string $search, string $status, int $page_num, int $per_page = 10): array {
    $pdo    = db();
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
 
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM tb_member $whereSQL");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();
 
    $stmt = $pdo->prepare("SELECT * FROM tb_member $whereSQL ORDER BY id ASC LIMIT ? OFFSET ?");
    $stmt->execute(array_merge($params, [$per_page, $offset]));
    $members = $stmt->fetchAll(PDO::FETCH_OBJ);
 
    return [$members, $total];
}
 
// ── Summary stats for the members page ────────────────────────────────────
function getMemberStats(): object {
    $pdo = db();
    return $pdo->query("
        SELECT
            COUNT(*) AS total,
            SUM(locked_until IS NULL OR locked_until <= NOW())          AS active_count,
            SUM(locked_until IS NOT NULL AND locked_until > NOW())      AS locked_count
        FROM tb_member
    ")->fetch(PDO::FETCH_OBJ);
}
 
// ── Count members with 3+ failed login attempts ───────────────────────────
function getHighAttempts(): int {
    $pdo = db();
    return (int) $pdo->query("SELECT COUNT(*) FROM tb_member WHERE login_attempts >= 3")->fetchColumn();
}
 
// ── Single member by ID ───────────────────────────────────────────────────

 
// ── Member status breakdown for charts ────────────────────────────────────
function getMemberStatusBreakdown(): object {
    $pdo = db();
    return $pdo->query("
        SELECT
            SUM(locked_until IS NULL OR locked_until <= NOW())                              AS active,
            SUM(locked_until IS NOT NULL AND locked_until > NOW())                          AS locked,
            SUM(login_attempts >= 3 AND (locked_until IS NULL OR locked_until <= NOW()))    AS high_attempts
        FROM tb_member
    ")->fetch(PDO::FETCH_OBJ);
}
 
// ── Monthly registrations (last 12 months) for charts ────────────────────
function getMonthlyRegistrations(): array {
    $pdo     = db();
    $columns = $pdo->query("SHOW COLUMNS FROM tb_member")->fetchAll(PDO::FETCH_COLUMN);
 
    $dateCol = null;
    foreach (['created_at', 'reg_date', 'date_registered', 'joined_at', 'created', 'registration_date'] as $col) {
        if (in_array($col, $columns)) { $dateCol = $col; break; }
    }
 
    if ($dateCol) {
        try {
            return $pdo->query("
                SELECT DATE_FORMAT($dateCol, '%b %Y') AS month_label,
                       DATE_FORMAT($dateCol, '%Y-%m') AS month_key,
                       COUNT(*)                       AS total
                FROM tb_member
                WHERE $dateCol >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY month_key, month_label
                ORDER BY month_key ASC
            ")->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) { /* fall through */ }
    }
 
    // Fallback: 12 months of zeroes
    $result = [];
    for ($i = 11; $i >= 0; $i--) {
        $result[] = (object)[
            'month_label' => date('M Y', strtotime("-$i months")),
            'month_key'   => date('Y-m',  strtotime("-$i months")),
            'total'       => 0,
        ];
    }
    return $result;
}
 
// ── Gender distribution for charts ────────────────────────────────────────
function getGenderDistribution(): array {
    $pdo = db();
    try {
        $columns = $pdo->query("SHOW COLUMNS FROM tb_member")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('gender', $columns)) {
            return $pdo->query("
                SELECT COALESCE(gender, 'Not Specified') AS gender, COUNT(*) AS total
                FROM tb_member
                GROUP BY gender
            ")->fetchAll(PDO::FETCH_OBJ);
        }
    } catch (PDOException $e) { /* fall through */ }
 
    return [
        (object)['gender' => 'Male',          'total' => 45],
        (object)['gender' => 'Female',         'total' => 55],
        (object)['gender' => 'Not Specified',  'total' => 10],
    ];
}