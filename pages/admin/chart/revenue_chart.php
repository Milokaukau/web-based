<?php
/**
 * chart/revenue_chart.php
 * Monthly Revenue line chart.
 * Data: getMonthlyRevenue() from database/order.php
 */

$revenueRows = getMonthlyRevenue();
$revLabels   = array_column($revenueRows, 'month_label');
$revData     = array_column($revenueRows, 'revenue');
?>

<div class="chart-card chart-wide">
    <div class="chart-card-header">
        <span class="chart-card-title">Monthly Revenue</span>
        <span class="chart-badge">Last 12 months</span>
    </div>
    <div class="chart-wrap">
        <canvas id="chartRevenue"></canvas>
    </div>
</div>

<script>
(function () {
    const coral = '#F39E9E';
    const dark  = '#111111';

    new Chart(document.getElementById('chartRevenue'), {
        type: 'line',
        data: {
            labels: <?= json_encode($revLabels ?: ['No data']) ?>,
            datasets: [{
                label: 'Revenue (RM)',
                data: <?= json_encode($revData ?: [0]) ?>,
                borderColor: coral,
                backgroundColor: 'rgba(243,158,158,0.12)',
                pointBackgroundColor: coral,
                pointRadius: 5,
                pointHoverRadius: 7,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff', titleColor: dark,
                    bodyColor: '#374151', borderColor: '#E5E7EB',
                    borderWidth: 1, padding: 12, cornerRadius: 10,
                    callbacks: { label: ctx => ' RM ' + Number(ctx.raw).toLocaleString() }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { maxRotation: 45 } },
                y: {
                    grid: { color: '#E5E7EB', drawBorder: false },
                    beginAtZero: true,
                    ticks: { callback: v => 'RM ' + v.toLocaleString() }
                }
            }
        }
    });
}());
</script>