<?php
/**
 * pages/admin/charts.php
 * Charts page — included by admin.php router.
 * Data functions live in database/admin.php, database/order.php, database/member.php.
 */

$summary = getChartSummaryStats();
$chartDir = $_SERVER['DOCUMENT_ROOT'] . '/pages/admin/chart/';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<div class="section-header">
    <div class="admin-section-title">Data Charts</div>
    <div class="admin-section-sub">Visual overview of members, orders and revenue</div>
    <div class="line"></div>
</div>

<!-- KPI Row -->
<div class="stats-grid charts-kpi-row">
    <div class="stat-card">
        <div class="stat-label">Total Members</div>
        <div class="stat-num"><?= $summary->total_members ?></div>
    </div>
    <div class="stat-card s-valid">
        <div class="stat-label">Total Orders</div>
        <div class="stat-num"><?= $summary->total_orders ?></div>
    </div>
    <div class="stat-card s-locked">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-num">RM <?= number_format($summary->total_revenue, 2) ?></div>
    </div>
    <div class="stat-card s-invalid">
        <div class="stat-label">Products Listed</div>
        <div class="stat-num"><?= $summary->total_products ?></div>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <?php include $chartDir . 'revenue_chart.php'; ?>
    <?php include $chartDir . 'memberStatus_chart.php'; ?>
    <?php include $chartDir . 'orderStatus_chart.php'; ?>
    <?php include $chartDir . 'genderBreakdown_chart.php'; ?>
    <?php include $chartDir . 'topProducts_chart.php'; ?>
</div>