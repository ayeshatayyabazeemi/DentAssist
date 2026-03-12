<!DOCTYPE html>
<html>

<head>

<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">

<style>

.appointment-table{
width:100%;
border-collapse:collapse;
margin-top:30px;
}

.appointment-table th{
background:#343a40;
color:white;
padding:12px;
text-align:left;
}

.appointment-table td{
padding:12px;
border-bottom:1px solid #ddd;
}

.appointment-table tr:hover{
background:#f5f5f5;
}

.status-pending{
background:#ffc107;
padding:4px 8px;
border-radius:4px;
}

.status-completed{
background:#28a745;
color:white;
padding:4px 8px;
border-radius:4px;
}

</style>

</head>

<body>

<header>
<div class="logo-btn">Doctor Dashboard</div>

<nav>
<ul>
<li><a href="/doctor/dashboard">Dashboard</a></li>
</ul>

<form action="<?= base_url('logout') ?>" method="post">
<button class="logout-btn">Logout</button>
</form>

</nav>
</header>

<main class="main-content">

<h2>All Appointments</h2>

<table class="appointment-table">

<tr>
<th>Date</th>
<th>Patient Name</th>
<th>MR Number</th>
<th>Slot</th>
<th>Status</th>
</tr>

<?php foreach($appointments as $app): ?>

<tr>

<td><?= $app['appointment_date'] ?></td>
<td><?= $app['name'] ?></td>
<td><?= $app['mr_number'] ?></td>
<td><?= $app['slot'] ?></td>

<td>

<?php if($app['status']=="completed"): ?>

<span class="status-completed">Completed</span>

<?php else: ?>

<span class="status-pending">Pending</span>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</table>

</main>

</body>
</html>