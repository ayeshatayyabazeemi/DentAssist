<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lab Order History</title>
<link rel="stylesheet" href="<?= base_url('assets/css/laborder.css') ?>">
<link rel="icon" type="image/png" href="<?= base_url('assets/images/mylogo.png') ?>">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="icon" type="image/png" href="<?= base_url('assets/images/mylogo.png') ?>">
<style>
.form-section{
    background-color:#fff;
    padding:30px;
    border-radius:15px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
    max-width:1000px;
    margin:40px auto;
}

.form-section h2{
    color:#FFBDBD;
    margin-bottom:20px;
    font-weight:700;
    font-size:1.8em;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-bottom:20px;
}

table th, table td{
    border:1px solid #BADFDB;
    padding:10px;
    text-align:left;
}

table th{
    background-color:#FFBDBD;
    color:#fff;
}

table tbody tr:nth-child(even){
    background-color:#f9f9f9;
}

.status-select{
    padding:4px 8px;
    border-radius:8px;
    border:1px solid #ccc;
}

.btn-add-appt{
    display:inline-block;
    background-image:linear-gradient(135deg,#FFA4A4,#FFBDBD);
    color:#fff;
    border:none;
    padding:10px 20px;
    border-radius:20px;
    cursor:pointer;
    font-weight:600;
    text-decoration:none;
}

.filter-container{margin-bottom:20px;}
.delete-btn{background:#ff4d4d;color:white;border:none;padding:6px 10px;border-radius:8px;cursor:pointer;font-size:14px;}
.delete-btn:hover{background:#e60000;}

.delete-btn{
    background:#ff4d4d;
    color:white;
    border:none;
    padding:6px 10px;
    border-radius:8px;
    cursor:pointer;
}
</style>
</head>

<body>

<div class="form-section">
<h2>Lab Order History</h2>

<!-- FILTER -->
<div class="filter-container">
    <form method="get" action="<?= base_url('laborders/history') ?>">
        <label>Filter by Status:</label>

        <select name="status" onchange="this.form.submit()">
            <option value="">All</option>

            <?php foreach([
                'Sent',
                'Resend',
                'Received',
                'Re-Received',
                'Completed'
            ] as $filterStatus): ?>

                <option value="<?= $filterStatus ?>"
                    <?= (isset($_GET['status']) && $_GET['status'] == $filterStatus) ? 'selected' : '' ?>>
                    <?= $filterStatus ?>
                </option>

            <?php endforeach; ?>
        </select>
    </form>
</div>

<table>
<thead>
<tr>
<th>ID</th>
<th>Patient Name</th>
<th>Lab Name</th>
<th>Lab Item</th>
<th>Shade</th>
<th>Comments</th>
<th>Status</th>
<th>Order Date</th>
<th>Delete</th>
</tr>
</thead>

<tbody>

<?php if(!empty($orders)): ?>
<?php foreach($orders as $order): ?>

<tr id="row-<?= $order['lab_order_id'] ?>">

<td><?= $order['lab_order_id'] ?></td>
<td><?= $order['patient_name'] ?></td>
<td><?= $order['lab_name'] ?></td>
<td><?= $order['lab_item'] ?></td>
<td><?= $order['shade'] ?></td>
<td><?= $order['comments'] ?></td>

<!-- STATUS -->
<td>
<form method="post" action="<?= base_url('laborders/updateStatus') ?>">
    <input type="hidden" name="lab_order_id" value="<?= $order['lab_order_id'] ?>">

    <?php
    $statusColors = [
        'Sent' => '#FFBDBD',
        'Resend' => '#FF8A80',
        'Received' => '#FFD580',
        'Re-Received' => '#FFB74D',
        'Completed' => '#4CAF50'
    ];

    $textColors = [
        'Received' => '#333',
        'Re-Received' => '#333'
    ];

    $bg = $statusColors[$order['status']] ?? '#ccc';
    $txt = $textColors[$order['status']] ?? '#fff';
    ?>

    <select name="status"
            onchange="this.form.submit()"
            class="status-select"
            style="background-color: <?= $bg ?>; color: <?= $txt ?>;">

        <?php foreach([
            'Sent',
            'Resend',
            'Received',
            'Re-Received',
            'Completed'
        ] as $status): ?>

            <option value="<?= $status ?>"
                <?= $order['status'] == $status ? 'selected' : '' ?>>
                <?= $status ?>
            </option>

        <?php endforeach; ?>

    </select>
</form>
</td>

<td><?= $order['order_date'] ?></td>

<td>
<button class="delete-btn" data-id="<?= $order['lab_order_id'] ?>">🗑️</button>
</td>

</tr>

<?php endforeach; ?>
<?php else: ?>

<tr>
<td colspan="9" style="text-align:center;">No lab orders found.</td>
</tr>

<?php endif; ?>

</tbody>
</table>

<div style="text-align:center; margin-top:20px;">
    <a href="<?= base_url('laborders/create') ?>" class="btn-add-appt">Back to Lab Order Form</a>
    <a href="<?= base_url('laborders/analytics') ?>" class="btn-add-appt" style="margin-left:15px;">View Lab Analytics</a>
</div>

<script>
// DELETE
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        const id = this.dataset.id;

        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete order!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4CAF50',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if(result.isConfirmed){

                fetch(`<?= base_url('laborders/delete') ?>/${id}`, {
                    method: 'POST',
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                })
                .then(res => res.json())
                .then(data => {

                    if(data.status === 'success'){
                        const row = document.getElementById('row-'+id);
                        row.style.opacity = 0;
                        setTimeout(() => row.remove(), 400);

                        Swal.fire('Deleted!', data.message, 'success');
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }

                })
                .catch(() => {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                });
            }
        });
    });
});
</script>

</body>
</html>