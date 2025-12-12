<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Employee Profile</title>
<link rel="stylesheet" href="<?= base_url('assets/css/employeeprofile.css') ?>">
</head>
<body>

<?php
$employee = $employee ?? null;
if (!$employee) { echo '<div style="padding:20px">No employee provided.</div>'; exit; }

$roles = [];
if (!empty($employee['is_admin'])) $roles[] = 'Admin';
if (!empty($employee['is_doctor'])) $roles[] = 'Doctor';
if (!empty($employee['is_staff'])) $roles[] = 'Staff';
if (!empty($employee['is_receptionist'])) $roles[] = 'Receptionist';
$designation = count($roles) ? implode(', ', $roles) : 'None';
$empId = $employee['employee_id'];
?>

<div class="container">
  <div class="profile-header">
    <div class="header-left">
      <h1><?= esc($employee['name']) ?></h1>
      <div>Designation: <?= esc($designation) ?></div>
    </div>
    <div class="header-actions">
      <button id="editBtn" class="icon-btn">✏️ Edit</button>
      <button id="deleteBtn" class="icon-btn danger" data-id="<?= $empId ?>">🗑️ Delete</button>
    </div>
  </div>

  <div class="form-section">
    <h2>Employee Information</h2>
    <div class="form-grid">
      <div class="form-group"><label>Name</label><span><?= esc($employee['name']) ?></span></div>
      <div class="form-group"><label>Email</label><span><?= esc($employee['email'] ?? 'none') ?></span></div>
      <div class="form-group"><label>Phone</label><span><?= esc($employee['mobile_no']) ?></span></div>
      <div class="form-group"><label>Gender</label><span><?= esc($employee['gender'] ?? 'none') ?></span></div>
      <div class="form-group"><label>DOB</label><span><?= esc($employee['dob'] ?? 'none') ?></span></div>
      <div class="form-group"><label>CNIC</label><span><?= esc($employee['cnic'] ?? 'none') ?></span></div>
      <div class="form-group"><label>Resignation Date</label><span><?= esc($employee['regdate'] ?? 'none') ?></span></div>
      <div class="form-group address-field"><label>Address</label><span><?= esc($employee['address'] ?? 'none') ?></span></div>
    </div>
  </div>

  <?php if (!empty($employee['is_doctor'])): ?>
  <div class="form-section">
    <h2>Doctor Schedule</h2>
    <table class="appointments-table">
      <thead>
        <tr><th>Day</th><th>Start</th><th>End</th></tr>
      </thead>
      <tbody>
        <?php foreach ($schedules ?? [] as $sch): ?>
          <tr>
            <td><?= esc($sch['day_of_week']) ?></td>
            <td><?= esc($sch['start_time']) ?></td>
            <td><?= esc($sch['end_time']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="appointments-section">
    <h2>Doctor Appointments</h2>
    <?php if (!empty($appointments)): ?>
    <table class="appointments-table">
      <thead><tr><th>Patient</th><th>Date</th><th>Day</th><th>Slot</th></tr></thead>
      <tbody>
        <?php foreach ($appointments as $appt): ?>
          <tr>
            <td><?= esc($appt['patient_name']) ?></td>
            <td><?= esc($appt['appointment_date']) ?></td>
            <td><?= esc(date('l', strtotime($appt['appointment_date']))) ?></td>
            <td><?= esc($appt['slot']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>No appointments yet.</p>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Edit Modal (Patient-style) -->
<div class="modal-overlay" id="editModal" aria-hidden="true">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Employee</h3>
      <button id="closeEdit">&times;</button>
    </div>
    <form id="editForm">
      <div class="form-grid">
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" value="<?= esc($employee['name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= esc($employee['email']) ?>">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="mobile_no" value="<?= esc($employee['mobile_no']) ?>" required pattern="^\d{11}$" title="Phone must be 11 digits">
        </div>
        <div class="form-group">
          <label>Gender</label>
          <select name="gender">
            <option value="">Select</option>
            <option value="male" <?= ($employee['gender']=='male')?'selected':'' ?>>Male</option>
            <option value="female" <?= ($employee['gender']=='female')?'selected':'' ?>>Female</option>
          </select>
        </div>
        <div class="form-group">
          <label>DOB</label>
          <input type="date" name="dob" value="<?= esc($employee['dob']) ?>">
        </div>
        <div class="form-group">
          <label>CNIC</label>
          <input type="text" name="cnic" value="<?= esc($employee['cnic']) ?>" required pattern="^\d{13}$" title="CNIC must be 13 digits">
        </div>
        <div class="form-group address-field">
          <label>Address</label>
          <textarea name="address"><?= esc($employee['address']) ?></textarea>
        </div>
      </div>

      <?php if(!empty($employee['is_doctor'])): ?>
      <h4>Doctor Schedule</h4>
      <div id="scheduleEdit">
        <?php foreach ($schedules ?? [] as $i=>$sch): ?>
        <div class="schedule-row">
          <input type="text" name="schedule[<?= $i ?>][day]" value="<?= esc($sch['day_of_week']) ?>" placeholder="Day">
          <input type="time" name="schedule[<?= $i ?>][start_time]" value="<?= esc($sch['start_time']) ?>">
          <input type="time" name="schedule[<?= $i ?>][end_time]" value="<?= esc($sch['end_time']) ?>">
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <button type="submit" class="btn">Save Changes</button>
    </form>
  </div>
</div>

<script src="<?= base_url('assets/js/employeeprofile.js') ?>"></script>
</body>
</html>