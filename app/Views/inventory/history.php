<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Stock Transactions</title>
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<style>
body {font-family: Arial,sans-serif; padding:20px; background:#f4f4f4;}
.styled-table {border-collapse: collapse; width: 100%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.1);}
.styled-table th, .styled-table td {padding: 10px; text-align:left; border-bottom:1px solid #ddd;}
.styled-table th {background:#6c63ff;color:#fff;}
</style>
</head>
<body>

<h2>Stock Transactions</h2>

<table class="styled-table">
<thead>
<tr>
    <th>ID</th>
    <th>Item</th>
    <th>Type</th>
    <th>Quantity</th>
    <th>Unit Cost</th>
    <th>Retail Value</th>
    <th>Note</th>
    <th>Date</th>
</tr>
</thead>
<tbody>
<?php foreach($transactions as $t): ?>
<tr>
    <td><?= esc($t['id']) ?></td>
    <td><?= esc($t['item_name']) ?></td>
    <td><?= esc($t['type']) ?></td>
    <td><?= esc($t['quantity']) ?></td>
    <td><?= esc($t['unit_cost']) ?></td>
    <td><?= esc($t['retail_value']) ?></td>
    <td><?= esc($t['note']) ?></td>
    <td><?= esc($t['created_at']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</body>
</html>