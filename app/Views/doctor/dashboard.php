<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Dashboard</title>
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/receptionistDashboard.css') ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
</head>
<body>
<header class="main-header">
    <div class="logo-btn">Doctor Dashboard</div>
    <nav>
        <ul class="nav-list">
            <li><a href="<?= base_url('doctor/appointments') ?>">Appointments</a></li>
        </ul>
        <form action="<?= base_url('logout') ?>" method="post">
            <button type="submit">Logout</button>
        </form>
    </nav>
</header>

<div class="content-wrapper">
    <div class="kpi-container">
        <div class="kpi-card border-primary">
            <h3>Pending Appointments</h3>
            <p class="value"><?= $pendingCount ?></p>
        </div>
        <div class="kpi-card border-success">
            <h3>Completed Appointments</h3>
            <p class="value"><?= $completedCount ?></p>
        </div>
    </div>

    <h2>Today's Appointments</h2>
    <table class="appointments-table">
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Patient MR Number</th>
                <th>Patient Name</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Slot</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($todaysAppointments as $apt): ?>
            <tr>
                <td><?= $apt['appointment_id'] ?></td>
                <td><?= $apt['mr_number'] ?></td>
                <td><?= $apt['patient_name'] ?></td>
                <td><?= $doctor_name ?></td>
                <td><?= $apt['appointment_date'] ?></td>
                <td><?= $apt['slot'] ?? '-' ?></td>
                <td>
                    <select class="status-select" data-appointment="<?= $apt['appointment_id'] ?>">
                        <option value="scheduled" <?= $apt['status']=='scheduled'?'selected':'' ?>>Scheduled</option>
                        <option value="checked_in" <?= $apt['status']=='checked_in'?'selected':'' ?>>Checked In</option>
                        <option value="in_progress" <?= $apt['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                        <option value="completed" <?= $apt['status']=='completed'?'selected':'' ?>>Completed</option>
                        <option value="no_show" <?= $apt['status']=='no_show'?'selected':'' ?>>No Show</option>
                        <option value="cancelled" <?= $apt['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                    </select>
                </td>
                <td>
                    <a href="<?= base_url('doctor/dentalForm/'.$apt['appointment_id']) ?>" class="btn btn-primary">Form</a>
                    <a href="<?= base_url('invoice/view/'.$apt['appointment_id']) ?>" class="btn btn-success">Invoice</a>
                    <a href="<?= base_url('assistant/symptoms/'.$apt['appointment_id']) ?>" class="btn btn-info">Assistant</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="<?= base_url('assets/js/doctor_dashboard.js') ?>"></script>
</body>
</html>