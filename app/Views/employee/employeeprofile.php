<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Profile</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/employeeprofile.css') ?>">
</head>
<body>

<?php
$roles = [];
if(!empty($employee['is_admin'])) $roles[]='Admin';
if(!empty($employee['is_doctor'])) $roles[]='Doctor';
if(!empty($employee['is_staff'])) $roles[]='Staff';
if(!empty($employee['is_receptionist'])) $roles[]='Receptionist';
$designation = count($roles)? implode(', ', $roles) : 'None';
?>

<div class="container" data-employee-id="<?= esc($employee['employee_id']) ?>">
    <div class="profile-header">
        <div class="header-left">
            <h1 class="employee-name"><?= esc($employee['name']) ?></h1>
            <div class="header-sub">
                <span class="designation">Designation: <?= esc($designation) ?></span>
            </div>
        </div>

        <div class="header-actions">
            <button id="editBtn" class="icon-btn">✏️ Edit</button>
            <button id="deleteBtn" class="icon-btn danger">🗑️ Delete</button>
        </div>
    </div>

    <div class="form-section">
        <h2>Employee Information</h2>
        <div class="form-grid">
            <div class="form-group"><label>Name</label><span id="name"><?= esc($employee['name']) ?></span></div>
            <div class="form-group"><label>Email</label><span id="email"><?= esc($employee['email'] ?: 'none') ?></span></div>
            <div class="form-group"><label>Phone</label><span id="mobile_no"><?= esc($employee['mobile_no']) ?></span></div>
            <div class="form-group"><label>Gender</label><span id="gender"><?= esc(ucfirst($employee['gender'] ?? 'none')) ?></span></div>
            <div class="form-group"><label>DOB</label><span id="dob"><?= esc($employee['dob'] ?? 'none') ?></span></div>
            <div class="form-group"><label>CNIC</label><span id="cnic"><?= esc($employee['cnic'] ?? 'none') ?></span></div>
            <div class="form-group"><label>Registration Date</label><span id="regdate"><?= esc($employee['regdate'] ?? 'none') ?></span></div>
            <div class="form-group"><label>Address</label><span id="address"><?= esc($employee['address'] ?? 'none') ?></span></div>
        </div>
    </div>

    <?php if(!empty($employee['is_doctor'])): ?>
    <div class="form-section">
        <h2>Doctor Schedule</h2>
        <table class="appointments-table" id="scheduleTable">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($schedules as $sch): ?>
                <tr data-day="<?= esc($sch['day_of_week']) ?>">
                    <td><?= esc($sch['day_of_week']) ?></td>
                    <td><?= esc($sch['start_time']) ?></td>
                    <td><?= esc($sch['end_time']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-section">
        <h2>Doctor Appointments</h2>
        <table class="appointments-table">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($appointments)): ?>
                    <?php foreach($appointments as $app): ?>
                    <tr>
                        <td><?= esc($app['patient_name']) ?></td>
                        <td><?= esc($app['appointment_date']) ?></td>
                        <td><?= esc($app['appointment_time']) ?></td>
                        <td><?= esc($app['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No appointments</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script src="<?= base_url('assets/js/employeeprofile.js') ?>"></script>
</body>
</html>
