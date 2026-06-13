<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/laborder.css') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/mylogo.png') ?>">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: url('<?= base_url('assets/images/dental-bg.jpg') ?>') no-repeat center top;
            background-size: cover;
            background-attachment: fixed;
        }

        /* Overlay to slightly dim the background for better chart readability */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255,255,255,0.85);
            z-index: -1;
        }

        .dashboard {
            max-width: 1000px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .chart-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .chart-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .chart-card h3 {
            margin-top: 0;
            font-size: 1.1rem;
            padding: 8px 12px;
            border-radius: 10px;
            background: linear-gradient(135deg, #FFA4A4, #FFBDBD);
            color: #fff;
            text-align: center;
            margin-bottom: 15px;
        }

        canvas {
            max-height: 250px;
        }

        @media (max-width: 600px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="dashboard">
    <div class="chart-card">
        <h3>Most Ordered Lab Items</h3>
        <canvas id="mostOrderedItemsChart"></canvas>
    </div>

    <div class="chart-card">
        <h3>Most Active Patients</h3>
        <canvas id="mostActivePatientsChart"></canvas>
    </div>

    <div class="chart-card">
        <h3>Orders Per Lab</h3>
        <canvas id="ordersPerLabChart"></canvas>
    </div>
</div>

<script>
    // Most Ordered Lab Items
    const mostOrderedItemsData = {
        labels: <?= json_encode(array_column($mostOrderedItems, 'lab_item')) ?>,
        datasets: [{
            label: 'Orders',
            data: <?= json_encode(array_column($mostOrderedItems, 'count')) ?>,
            backgroundColor: ['#FF6B6B', '#FFA94D', '#FFD93D', '#6BCB77', '#4D96FF']
        }]
    };

    new Chart(document.getElementById('mostOrderedItemsChart'), {
        type: 'bar',
        data: mostOrderedItemsData,
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { mode: 'index' } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                x: { grid: { color: '#f0f0f0' } }
            }
        }
    });

    // Most Active Patients
    const mostActivePatientsData = {
        labels: <?= json_encode(array_column($mostActivePatients, 'patient_name')) ?>,
        datasets: [{
            label: 'Orders',
            data: <?= json_encode(array_column($mostActivePatients, 'count')) ?>,
            backgroundColor: '#FFA94D'
        }]
    };

    new Chart(document.getElementById('mostActivePatientsChart'), {
        type: 'bar',
        data: mostActivePatientsData,
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { mode: 'index' } },
            scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
        }
    });

    // Orders Per Lab
    const ordersPerLabData = {
        labels: <?= json_encode(array_column($ordersPerLab, 'lab_name')) ?>,
        datasets: [{
            label: 'Orders',
            data: <?= json_encode(array_column($ordersPerLab, 'count')) ?>,
            backgroundColor: ['#FF6B6B', '#FFA94D', '#FFD93D', '#6BCB77', '#4D96FF']
        }]
    };

    new Chart(document.getElementById('ordersPerLabChart'), {
        type: 'doughnut',
        data: ordersPerLabData,
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 20, padding: 10 } },
                tooltip: { mode: 'nearest' }
            }
        }
    });
</script>

</body>
</html>
