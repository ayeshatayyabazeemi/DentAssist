<!DOCTYPE html>
<html>
<head>

<title>Inventory Reports</title>

<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

</head>

<body>

<div class="dashboard-content">

<h2>Today's Usage Report</h2>

<div class="inventory-table-container">

<table class="inventory-table">

<tr>
    <th>Item</th>
    <th>Used Qty</th>
    <th>Unit Cost</th>
    <th>Total</th>
</tr>

<?php foreach($usedItems as $u): ?>

<tr>

    <td><?= $u['item_name'] ?></td>

    <td><?= $u['total_used'] ?></td>

    <td>Rs <?= number_format($u['unit_cost'],2) ?></td>

    <td>Rs <?= number_format($u['total_cost'],2) ?></td>

</tr>

<?php endforeach; ?>

<tr>

    <td colspan="3">
        <strong>Grand Total</strong>
    </td>

    <td>
        <strong>
            Rs <?= number_format($grandTotal,2) ?>
        </strong>
    </td>

</tr>

</table>

</div>

</div>

</body>
</html>