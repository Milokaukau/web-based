<?php
/**
 * chart/topProducts_chart.php
 * Top 5 Selling Products horizontal bar chart.
 * Data: getTopSellingProducts() from database/product.php
 */

$topProducts   = getTopSellingProducts(5);
$topProdLabels = array_column($topProducts, 'product_label');
$topProdData   = array_column($topProducts, 'units_sold');
?>

<div class="chart-card chart-full">
    <div class="chart-card-header">
        <span class="chart-card-title">Top 5 Selling Products</span>
        <span class="chart-badge">By units sold</span>
    </div>
    <div class="chart-wrap chart-wrap-bar">
        <canvas id="chartTopProducts"></canvas>
    </div>
</div>

<script>
(function () {
    const palette = ['#F39E9E','#86EFAC','#FCD34D','#93C5FD','#C4B5FD'];

    new Chart(document.getElementById('chartTopProducts'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($topProdLabels ?: ['No data']) ?>,
            datasets: [{
                label: 'Units Sold',
                data: <?= json_encode($topProdData ?: [0]) ?>,
                backgroundColor: palette,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff', titleColor: '#111',
                    bodyColor: '#374151', borderColor: '#E5E7EB',
                    borderWidth: 1, padding: 12, cornerRadius: 10
                }
            },
            scales: {
                x: {
                    grid: { color: '#E5E7EB', drawBorder: false },
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                },
                y: { grid: { display: false } }
            }
        }
    });
}());
</script>