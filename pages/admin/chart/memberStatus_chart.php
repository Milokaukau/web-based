<?php
/**
 * chart/memberStatus_chart.php
 * Member Status pie chart.
 * Data: getMemberStatusBreakdown() from database/member.php
 */

$memberStats = getMemberStatusBreakdown();
?>

<div class="chart-card chart-narrow">
    <div class="chart-card-header">
        <span class="chart-card-title">Member Status</span>
    </div>
    <div class="chart-wrap chart-wrap-sm">
        <canvas id="chartMemberStatus"></canvas>
    </div>
</div>

<script>
(function () {
    new Chart(document.getElementById('chartMemberStatus'), {
        type: 'pie',
        data: {
            labels: ['Active', 'Blocked'],
            datasets: [{
                data: [
                    <?= (int)$memberStats->active ?>,
                    <?= (int)$memberStats->locked ?>
                ],
                backgroundColor: ['#86EFAC', '#FCA5A5'],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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