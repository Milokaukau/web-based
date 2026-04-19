<?php
/**
 * chart/genderBreakdown_chart.php
 * Gender Breakdown pie chart.
 * Data: getGenderDistribution() from database/member.php
 */

$genderRows   = getGenderDistribution();
$genderLabels = array_column($genderRows, 'gender');
$genderData   = array_column($genderRows, 'total');
?>

<div class="chart-card chart-narrow">
    <div class="chart-card-header">
        <span class="chart-card-title">Gender Breakdown</span>
    </div>
    <div class="chart-wrap chart-wrap-sm">
        <canvas id="chartGender"></canvas>
    </div>
</div>

<script>
(function () {
    new Chart(document.getElementById('chartGender'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($genderLabels ?: ['No data']) ?>,
            datasets: [{
                data: <?= json_encode($genderData ?: [1]) ?>,
                backgroundColor: ['#93C5FD', '#F9A8D4', '#C4B5FD', '#D1D5DB'],
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