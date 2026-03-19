<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Stock <?= esc($type) ?></title>
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<style>
body {font-family: Arial,sans-serif; padding:20px; background:#f4f4f4;}
form {background:#fff; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
label {display:block; margin-top:10px;}
input, select, textarea {padding:6px; width:100%; margin-top:4px; box-sizing:border-box;}
.ai-btn {background:#6c63ff;color:#fff;padding:6px 12px;border-radius:4px;margin-top:10px;}
.ai-btn:hover{background:#5848d6;}
.form-actions {margin-top:15px;}
</style>
</head>
<body>

<h2>Stock <?= esc($type) ?></h2>

<form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="type" value="<?= esc($type) ?>">

    <label>Select Item</label>
    <select name="item_id" required>
        <option value="">--Select Item--</option>
        <?php foreach($items as $i): ?>
        <option value="<?= esc($i['id']) ?>" <?= isset($selected) && $selected==$i['id']?'selected':'' ?>>
            <?= esc($i['item_name']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Quantity</label>
    <input type="number" name="quantity" required>

    <?php if($type != 'OUT'): ?>
    <label>Unit Cost</label>
    <input type="number" step="0.01" name="unit_cost">

    <label>Retail Value</label>
    <input type="number" step="0.01" name="retail_value">
    <?php endif; ?>

    <?php if($type == 'ADJUSTMENT'): ?>
    <label>Expired Quantity</label>
    <input type="number" name="expired_qty">
    <?php endif; ?>

    <label>Note</label>
    <textarea name="note"></textarea>

    <div class="form-actions">
        <button type="submit" class="ai-btn">Submit</button>
        <a href="<?= site_url('inventory') ?>" class="ai-btn" style="background:#6c757d">Cancel</a>
    </div>
</form>

</body>
</html>