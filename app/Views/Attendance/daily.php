<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Daily Attendance</title>

<!-- Favicon changed to your logo -->
<link rel="icon" type="image/png" href="<?= base_url('assets/images/mylogo.png') ?>">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
/* Card container with stronger pastel pink */
.attendance-card { 
    background: #ffe6f0; /* pastel pink */
    border: 1px solid #f5c6dd; /* slightly darker pink border */
    padding: 25px 30px;
    border-radius: 12px; 
    box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
}

/* Header */
.attendance-card h3 {
    margin-bottom: 20px;
    font-weight: 600;
    color: #343a40;
}

/* Table */
.table {
    border-radius: 8px;
    overflow: hidden;
}
.table thead {
    background: #d63384; /* deep pink header */
    color: #fff; 
    font-weight: 600;
}
.table tbody tr:nth-child(even) {
    background: #ffd9eb; /* light pink alternating rows */
}
.table tbody tr:hover {
    background: #ffb6d2; /* slightly darker pink on hover */
    transition: background 0.2s;
}

/* Radio buttons */
.status {
    cursor: pointer;
}
.present { 
    color: #28a745; 
    font-weight: bold; 
}
.absent { 
    color: #dc3545; 
    font-weight: bold; 
}

/* Input times */
.check_in, .check_out {
    width: 110px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 3px;
}

/* Buttons */
.btn-primary, .btn-success {
    border-radius: 6px;
    font-weight: 500;
    padding: 6px 18px;
}

/* Flash messages */
.alert {
    border-radius: 6px;
    padding: 10px 15px;
}
</style>
</head>

<body>
<div class="container mt-5">
<div class="attendance-card">

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daily Attendance - <span id="currentDate"><?= date('F d, Y', strtotime($today)) ?></span></h3>
    <a href="<?= site_url('attendance/monthlyCalendar') ?>" class="btn btn-primary">View Monthly Calendar</a>
</div>

<!-- Flash message -->
<?php if(session()->getFlashdata('success')): ?>
<div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<!-- Attendance Form -->
<form method="post" action="<?= base_url('attendance/save') ?>">
    <input type="hidden" name="date" id="attendanceDate" value="<?= $today ?>">

    <table class="table table-bordered table-hover align-middle text-center">
    <thead>
    <tr>
        <th class="text-start">Employee Name</th>
        <th>Role</th>
        <th>Present</th>
        <th>Absent</th>
        <th>Check In</th>
        <th>Check Out</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($employees as $emp): ?>
    <tr>
        <td class="text-start">
            <?= esc($emp['name']) ?>
            <input type="hidden" name="employee_id[]" value="<?= $emp['employee_id'] ?>">
        </td>
        <td>
            <?= $emp['is_doctor']?'Doctor':($emp['is_staff']?'Staff':($emp['is_receptionist']?'Receptionist':'Other')) ?>
        </td>
        <td>
            <input type="radio" class="status" value="Present"
            name="status[<?= $emp['employee_id'] ?>][<?= $today ?>]">
        </td>
        <td>
            <input type="radio" class="status" value="Absent"
            name="status[<?= $emp['employee_id'] ?>][<?= $today ?>]">
        </td>
        <td>
            <input type="time" class="check_in"
            name="check_in[<?= $emp['employee_id'] ?>][<?= $today ?>]"
            value="<?= $attendanceData[$emp['employee_id']]['check_in'] ?? '' ?>">
        </td>
        <td>
            <input type="time" class="check_out"
            name="check_out[<?= $emp['employee_id'] ?>][<?= $today ?>]"
            value="<?= $attendanceData[$emp['employee_id']]['check_out'] ?? '' ?>">
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>

    <div class="text-end mt-3">
        <button class="btn btn-success">Save Attendance</button>
    </div>
</form>

</div>
</div>
</body>
</html>