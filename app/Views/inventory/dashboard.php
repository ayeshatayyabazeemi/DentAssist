<!DOCTYPE html>
<html>
<head>

<title>Inventory Dashboard</title>

<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<header>

    <div class="logo-btn">
        Dental Inventory System
    </div>

    <nav>

        <ul class="nav-list">

            <li>
                <a href="<?= base_url('inventory/dashboard') ?>">
                    <button class="active">Dashboard</button>
                </a>
            </li>

            <li>
                <a href="<?= base_url('inventory/stock-in') ?>">
                    <button>Stock</button>
                </a>
            </li>

            <li>
                <a href="<?= base_url('inventory/transactions') ?>">
                    <button>Transactions</button>
                </a>
            </li>

            <li>
                <a href="<?= base_url('inventory/reports') ?>">
                    <button>Reports</button>
                </a>
            </li>

        </ul>

    </nav>

</header>

<div class="dashboard-content">

<h2>Inventory Dashboard</h2>

<div class="kpi-container">

    <div class="kpi-card border-primary">
        <h3>Total Items</h3>
        <div class="value"><?= $totalItems ?></div>
    </div>

    <div class="kpi-card border-secondary">
        <h3>Low Stock</h3>
        <div class="value"><?= $lowStock ?></div>
    </div>

    <div class="kpi-card border-warm">
        <h3>Out Of Stock</h3>
        <div class="value"><?= $outStock ?></div>
    </div>

    <div class="kpi-card border-success">
        <h3>Total Value</h3>
        <div class="value">Rs <?= number_format($totalValue,2) ?></div>
    </div>

</div>

<div class="top-row">

    <div class="search-bar">

        <input type="text" id="searchInput" placeholder="Search inventory item...">

    </div>

</div>

<div class="inventory-table-container">

<table class="inventory-table" id="inventoryTable">

<tr>
    <th>Item</th>
    <th>Qty</th>
    <th>Reorder</th>
    <th>Unit</th>
    <th>Status</th>
</tr>

<?php foreach($items as $item): ?>

<tr class="<?= ($item['available_qty'] <= $item['reorder_level']) ? 'low-stock' : 'good-stock' ?>">

    <td><?= $item['item_name'] ?></td>

    <td><?= $item['available_qty'] ?></td>

    <td><?= $item['reorder_level'] ?></td>

    <td><?= $item['unit'] ?></td>

    <td>

        <?php if($item['available_qty'] <= $item['reorder_level']): ?>

            🔴 LOW STOCK

        <?php else: ?>

            🟢 AVAILABLE

        <?php endif; ?>

    </td>

</tr>

<?php endforeach; ?>

</table>

</div>

</div>

<script>

document.getElementById('searchInput').addEventListener('keyup', function(){

    let value = this.value.toLowerCase();

    let rows = document.querySelectorAll('#inventoryTable tr');

    rows.forEach((row, index) => {

        if(index === 0) return;

        row.style.display = row.innerText.toLowerCase().includes(value)
            ? ''
            : 'none';
    });
});

</script>

</body>
</html>