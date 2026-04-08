<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lab Order History</title>
<link rel="stylesheet" href="<?= base_url('assets/css/laborder.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.form-section{background-color:#fff;padding:30px;border-radius:15px;box-shadow:0 4px 10px rgba(0,0,0,0.05);max-width:1000px;margin:40px auto;}
.form-section h2{color:#FFBDBD;margin-bottom:20px;font-weight:700;font-size:1.8em;}
table{width:100%;border-collapse:collapse;margin-bottom:20px;}
table th, table td{border:1px solid #BADFDB;padding:10px;text-align:left;}
table th{background-color:#FFBDBD;color:#fff;}
table tbody tr:nth-child(even){background-color:#f9f9f9;}
.status-select{padding:4px 8px;border-radius:8px;border:1px solid #ccc;}
.btn-add-appt{display:inline-block;background-image:linear-gradient(135deg,#FFA4A4,#FFBDBD);color:#fff;border:none;padding:10px 20px;border-radius:20px;cursor:pointer;font-weight:600;text-decoration:none;transition:transform 0.2s,box-shadow 0.2s;}
.btn-add-appt:hover{transform:scale(1.05);box-shadow:0 5px 12px rgba(255,189,189,0.3);}
.filter-container{margin-bottom:20px;}
.delete-btn{background:#ff4d4d;color:white;border:none;padding:6px 10px;border-radius:8px;cursor:pointer;font-size:14px;}
.delete-btn:hover{background:#e60000;}

/* Status colors */
.status-sent{background:#FFBDBD;color:#fff;}
.status-received{background:#28a745;color:#fff;}
.status-resend{background:#ffc107;color:#333;}
.status-re-received{background:#17a2b8;color:#fff;}
.status-completed{background:#6c757d;color:#fff;}
</style>
</head>
<body>

<div class="form-section">
<h2>Lab Order History</h2>

<div class="filter-container">
<form method="get" action="<?= base_url('laborders/history') ?>">
    <label for="status_filter">Filter by Status: </label>
    <select name="status" id="status_filter" onchange="this.form.submit()">
        <option value="">All</option>
        <?php foreach($statusOptions as $filterStatus): ?>
        <option value="<?= $filterStatus ?>" <?= (isset($_GET['status']) && $_GET['status']==$filterStatus)?'selected':'' ?>><?= $filterStatus ?></option>
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
        <td>
            <?php $statusClass = strtolower(str_replace([' ','_'], '-', $order['status'])); ?>
            <form method="post" action="<?= base_url('laborders/updateStatus') ?>">
                <input type="hidden" name="lab_order_id" value="<?= $order['lab_order_id'] ?>">
                <select name="status" class="status-select status-<?= $statusClass ?>">
                    <?php foreach($statusOptions as $status): ?>
                        <option value="<?= $status ?>" <?= $order['status']==$status?'selected':'' ?>><?= $status ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </td>
        <td><?= $order['order_date'] ?></td>
        <td><button class="delete-btn" data-id="<?= $order['lab_order_id'] ?>">🗑️</button></td>
    </tr>
    <?php endforeach; ?>
<?php else: ?>
<tr><td colspan="9" style="text-align:center;">No lab orders found.</td></tr>
<?php endif; ?>
</tbody>
</table>

<div style="text-align:center; margin-top:20px;">
    <a href="<?= base_url('laborders/create') ?>" class="btn-add-appt">Back to Lab Order Form</a>
    <a href="<?= base_url('laborders/analytics') ?>" class="btn-add-appt" style="margin-left:15px;">View Lab Analytics</a>
</div>

<script>
// Status class mapping
const statusClasses = {
    'Sent': 'status-sent',
    'Received': 'status-received',
    'Resend': 'status-resend',
    'Re-Received': 'status-re-received',
    'Completed': 'status-completed'
};

// AJAX status update
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function () {
        const form = this.closest('form');
        const lab_order_id = form.querySelector('input[name="lab_order_id"]').value;
        const status = this.value;
        const selectElement = this;

        fetch('<?= base_url('laborders/updateStatus') ?>', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: new URLSearchParams({
                'lab_order_id': lab_order_id,
                'status': status
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                // Remove previous status classes
                Object.values(statusClasses).forEach(cls => selectElement.classList.remove(cls));
                // Add new status class
                if(statusClasses[status]) selectElement.classList.add(statusClasses[status]);
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error!', 'Something went wrong.', 'error');
            console.error(err);
        });
    });
});

// AJAX Delete with SweetAlert2
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        const id = this.dataset.id;
        Swal.fire({
            title:'Are you sure?',
            text:"You won't be able to revert this!",
            icon:'warning',
            showCancelButton:true,
            confirmButtonColor:'#4CAF50',
            cancelButtonColor:'#d33',
            confirmButtonText:'Yes, delete it!',
            reverseButtons:true
        }).then((result)=>{
            if(result.isConfirmed){
                fetch(`<?= base_url('laborders/delete') ?>/${id}`,{
                    method:'POST',
                    headers:{'X-Requested-With':'XMLHttpRequest'}
                })
                .then(res=>res.json())
                .then(data=>{
                    if(data.status==='success'){
                        const row = document.getElementById('row-'+id);
                        row.style.transition='opacity 0.5s';
                        row.style.opacity = 0;
                        setTimeout(()=>row.remove(),500);
                        Swal.fire('Deleted!', data.message, 'success');
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                }).catch(err => Swal.fire('Error!','Something went wrong.','error'));
            }
        });
    });
});
</script>

</body>
</html>
