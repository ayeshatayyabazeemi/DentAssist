<!DOCTYPE html>
<html>
<head>

<title>Transactions</title>

<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

</head>

<body>

<div class="dashboard-content">

<h2>Stock Transactions</h2>

<div class="inventory-table-container">

<table class="inventory-table">

<tr>
    <th>Date</th>
    <th>Item</th>
    <th>Type</th>
    <th>Qty</th>
    <th>Note</th>
</tr>

<?php foreach($transactions as $t): ?>

<tr>

    <td><?= $t['created_at'] ?></td>

    <td><?= $t['item_name'] ?></td>

    <td>
        <?= ($t['type'] == 'IN') ? '🟢 STOCK IN' : '🔴 STOCK OUT' ?>
    </td>

    <td><?= $t['quantity'] ?></td>

    <td><?= $t['note'] ?></td>

</tr>

<?php endforeach; ?>

</table>

</div>

</div>

</body>
</html>