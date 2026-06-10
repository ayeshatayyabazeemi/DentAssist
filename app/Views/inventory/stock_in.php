<!DOCTYPE html>
<html>
<head>

<title>Stock Management</title>

<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

</head>

<body>

<header>

    <div class="logo-btn">
        Dental Inventory System
    </div>

</header>

<div class="dashboard-content">

<h2>Stock Management</h2>

<div class="form-section">

<h3>Add Stock</h3>

<form id="stockForm">

<select name="item_id" required>

<?php foreach($items as $i): ?>

<option value="<?= $i['item_id'] ?>">
    <?= $i['item_name'] ?> (<?= $i['available_qty'] ?>)
</option>

<?php endforeach; ?>

</select>

<input type="number" name="quantity" placeholder="Quantity" required>

<input type="text" name="note" placeholder="Note">

<button class="submit-btn" type="submit">
    Add Stock
</button>

</form>

<hr style="margin:30px 0;">

<h3>Deduct / Use Item</h3>

<form id="removeStockForm">

<select name="item_id" required>

<?php foreach($items as $i): ?>

<option value="<?= $i['item_id'] ?>">
    <?= $i['item_name'] ?> (<?= $i['available_qty'] ?>)
</option>

<?php endforeach; ?>

</select>

<input type="number" name="quantity" placeholder="Quantity Used" required>

<input type="text" name="note" placeholder="Usage Note">

<button class="submit-btn" type="submit">
    Deduct Stock
</button>

</form>

</div>

</div>

<script>

// ADD STOCK

document.getElementById('stockForm')
.addEventListener('submit', async function(e){

    e.preventDefault();

    const formData = new FormData(this);

    const res = await fetch('/inventory/add-stock', {
        method:'POST',
        body:formData
    });

    const data = await res.json();

    alert(data.message);

    if(data.status === 'success'){
        location.reload();
    }
});

// REMOVE STOCK

document.getElementById('removeStockForm')
.addEventListener('submit', async function(e){

    e.preventDefault();

    const formData = new FormData(this);

    const res = await fetch('/inventory/remove-stock', {
        method:'POST',
        body:formData
    });

    const data = await res.json();

    alert(data.message);

    if(data.status === 'success'){
        location.reload();
    }
});

</script>

</body>
</html>