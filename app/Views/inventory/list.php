<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventory Items</title>
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<style>
body { font-family: Arial,sans-serif; padding:20px; background:#f4f4f4; }
h2 { margin-bottom:20px; }
.ai-btn { background:#6c63ff; color:#fff; padding:6px 12px; text-decoration:none; border-radius:4px; margin:2px; display:inline-block; }
.ai-btn:hover { background:#5848d6; }
.styled-table { border-collapse: collapse; width:100%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.1); }
.styled-table th, .styled-table td { padding:10px; text-align:left; border-bottom:1px solid #ddd; }
.styled-table th { background:#6c63ff; color:#fff; }
.top-row { margin-bottom:15px; display:flex; justify-content:space-between; align-items:center; }
input[type=text] { padding:6px; width:250px; }
.form-message { padding:8px; border-radius:4px; margin-bottom:10px; }
.success { background:#d4edda; color:#155724; }
.error { background:#f8d7da; color:#721c24; }
</style>
</head>
<body>

<h2>Inventory Items</h2>

<!-- Notifications -->
<?php if(session()->getFlashdata('success')): ?>
    <div class="form-message success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="form-message error"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="top-row">
    <form method="get">
        <input type="text" name="search" placeholder="Search items..." value="<?= esc($search ?? '') ?>">
        <button type="submit" class="ai-btn">Search</button>
    </form>

    <div>
        <a href="<?= site_url('inventory/add') ?>" class="ai-btn">Add New Item</a>
        <a href="<?= site_url('inventory/history') ?>" class="ai-btn">Transaction History</a>
    </div>
</div>

<table class="styled-table">
<thead>
<tr>
    <th>ID</th>
    <th>Item Name</th>
    <th>Generic Name</th>
    <th>Unit</th>
    <th>Rack</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach($items as $row): ?>
<tr>
    <td><?= esc($row['id']) ?></td>
    <td><?= esc($row['item_name']) ?></td>
    <td><?= esc($row['generic_name']) ?></td>
    <td><?= esc($row['unit']) ?></td>
    <td><?= esc($row['rack_number']) ?></td>
    <td>
        <a href="<?= site_url('inventory/edit/'.$row['id']) ?>" class="ai-btn" style="background:#ffc107">Edit</a>
        <form action="<?= site_url('inventory/delete/'.$row['id']) ?>" method="post" style="display:inline-block;">
            <?= csrf_field() ?>
            <button type="submit" onclick="return confirm('Delete this item?')" class="ai-btn" style="background:#dc3545">Delete</button>
        </form>
        <a href="<?= site_url('inventory/stock/IN/'.$row['id']) ?>" class="ai-btn" style="background:#28a745">Stock IN</a>
        <a href="<?= site_url('inventory/stock/OUT/'.$row['id']) ?>" class="ai-btn" style="background:#007bff">Stock OUT</a>
        <a href="<?= site_url('inventory/stock/ADJUSTMENT/'.$row['id']) ?>" class="ai-btn" style="background:#6c757d">Adjust</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<script>
// AJAX live search suggestions
document.addEventListener('DOMContentLoaded', function(){
    const searchInput = document.querySelector('input[name="search"]');
    const resultsBox = document.createElement('div');
    resultsBox.style.position = 'absolute';
    resultsBox.style.background = '#fff';
    resultsBox.style.border = '1px solid #ccc';
    resultsBox.style.width = searchInput.offsetWidth+'px';
    searchInput.parentNode.appendChild(resultsBox);

    searchInput.addEventListener('input', function(){
        const term = this.value;
        if(term.length<1){ resultsBox.innerHTML=''; return; }

        fetch('<?= site_url('inventory/search') ?>?term='+encodeURIComponent(term))
        .then(res=>res.json())
        .then(data=>{
            resultsBox.innerHTML='';
            data.forEach(item=>{
                const div = document.createElement('div');
                div.textContent=item.label;
                div.style.padding='4px';
                div.style.cursor='pointer';
                div.addEventListener('click', function(){
                    searchInput.value=item.label;
                    resultsBox.innerHTML='';
                });
                resultsBox.appendChild(div);
            });
        });
    });

    document.addEventListener('click', e=>{
        if(e.target!==searchInput) resultsBox.innerHTML='';
    });
});
</script>

</body>
</html>