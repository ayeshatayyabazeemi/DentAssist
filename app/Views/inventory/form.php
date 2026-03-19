<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($item)?'Edit Item':'Add Item' ?></title>
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<style>
body {font-family: Arial,sans-serif; padding:20px; background:#f4f4f4;}
h2 {margin-bottom:20px;}
form {background:#fff; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
label {display:block; margin-top:10px;}
input, select {padding:6px; width:100%; margin-top:4px; box-sizing:border-box;}
.ai-btn {background:#6c63ff;color:#fff;padding:6px 12px;border-radius:4px;margin-top:10px;}
.ai-btn:hover{background:#5848d6;}
.form-actions {margin-top:15px;}
</style>
</head>
<body>

<h2><?= isset($item)?'Edit Item':'Add New Item' ?></h2>

<form method="post" action="<?= isset($item)? site_url('inventory/edit/'.$item['id']): site_url('inventory/add') ?>">
    <?= csrf_field() ?>

    <label>Item Name</label>
    <input type="text" name="item_name" value="<?= esc($item['item_name'] ?? '') ?>" required>

    <label>Generic Name</label>
    <input type="text" name="generic_name" value="<?= esc($item['generic_name'] ?? '') ?>">

    <label>Barcode</label>
    <input type="text" name="barcode" value="<?= esc($item['barcode'] ?? '') ?>">

    <label>Manufacturer</label>
    <input type="text" name="manufacturer" value="<?= esc($item['manufacturer'] ?? '') ?>">

    <label>Supplier</label>
    <input type="text" name="supplier" value="<?= esc($item['supplier'] ?? '') ?>">

    <label>Conversion Unit</label>
    <input type="number" name="conversion_unit" value="<?= esc($item['conversion_unit'] ?? 1) ?>">

    <label>Reorder Level</label>
    <input type="number" name="reorder_level" value="<?= esc($item['reorder_level'] ?? 0) ?>">

    <label>Unit</label>
    <input type="text" name="unit" value="<?= esc($item['unit'] ?? '') ?>">

    <label>Rack Number</label>
    <input type="text" name="rack_number" value="<?= esc($item['rack_number'] ?? '') ?>">

    <div class="form-actions">
        <button type="submit" class="ai-btn">Save</button>
        <a href="<?= site_url('inventory') ?>" class="ai-btn" style="background:#6c757d">Cancel</a>
    </div>
</form>

</body>
</html>