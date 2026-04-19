<?php
/**
 * chart/orderStatus_chart.php
 * Order Status doughnut chart.
 * Data: getOrderStatusBreakdown() from database/order.php
 */

$orderStatus  = getOrderStatusBreakdown();
$orderLabels  = array_column($orderStatus, 'status');
$orderData    = array_column($orderStatus, 'total');
?>

<div class="chart-card chart-narrow">
    <div class="chart-card-header">
        <span class="chart-card-title">Order Status</span>
    </div>
    <div class="chart-wrap chart-wrap-sm">
        <canvas id="chartOrderStatus"></canvas>
    </div>
</div>

<script>
(function () {
    const palette = ['#F39E9E','#86EFAC','#FCD34D','#93C5FD','#C4B5FD','#F9A8D4','#6EE7B7','#FDB575'];

    new Chart(document.getElementById('chartOrderStatus'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_map('ucfirst', $orderLabels) ?: ['No orders']) ?>,
            datasets: [{
                data: <?= json_encode($orderData ?: [1]) ?>,
                backgroundColor: palette,
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 16, font: { size: 11, weight: '600' } }
                },
                tooltip: {
                    backgroundColor: '#fff', titleColor: '#111',
                    bodyColor: '#374151', borderColor: '#E5E7EB',
                    borderWidth: 1, padding: 12, cornerRadius: 10
                }
            }
        }
    });
}());
</script>