<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Profile</title> 
<link rel="stylesheet" href="<?= base_url('assets/css/employeeprofile.css') ?>">

<!-- Logo as favicon -->
<link rel="icon" href="<?= base_url('assets/images/mylogo.png') ?>" type="image/png">

<style>
/* ===================== EMPLOYEE EDIT MODAL STYLING ===================== */
#editModal .modal-content {
  width: 520px;
  max-width: 90%;
  background: #fff5e6;
  border-radius: 15px;
  padding: 20px 25px;
  box-shadow: 0 10px 20px rgba(0,0,0,0.25);
  animation: fadeInUp 0.4s ease;
  border: 1px solid #f2d9c3;
  max-height: 480px;
  overflow-y: auto;
}
#editModal .modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #f2d9c3;
  margin-bottom: 15px;
  padding-bottom: 5px;
}
#editModal .modal-header h3 {
  margin: 0;
  font-size: 1.35rem;
  color: #c47f3a;
  font-weight: 600;
}
#editModal #closeEdit {
  font-size: 1.5rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #c47f3a;
  transition: all 0.3s ease;
}
#editModal #closeEdit:hover {
  color: #a35d2b;
  transform: rotate(90deg);
}
#editModal .form-group {
  background: #fff8f0;
  padding: 10px 12px;
  margin-bottom: 10px;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
  display:flex;
  flex-direction:column;
}
#editModal .form-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 4px;
  color: #8c5a32;
  font-size: 0.95rem;
}
#editModal input,
#editModal select,
#editModal textarea {
  width: 100%;
  padding: 7px 10px;
  border-radius: 6px;
  border: 1px solid #e6cbb2;
  background: #fffefc;
  font-size: 0.93rem;
  transition: all 0.3s ease;
  box-sizing: border-box;
}
#editModal input:focus,
#editModal select:focus,
#editModal textarea:focus {
  border-color: #c47f3a;
  box-shadow: 0 0 5px rgba(196, 127, 58, 0.35);
  outline: none;
}
#editModal .btn {
  width: 100%;
  background: #c47f3a;
  color: white;
  padding: 9px 0;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  margin-top: 8px;
}
#editModal .btn:hover {
  background: #a35d2b;
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(163, 93, 43, 0.3);
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(-15px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
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

      <!-- LAB ORDER BUTTON FOR ADMIN -->
      <?php if(!empty($employee['is_admin'])): ?>
        <a href="<?= base_url('laborders/create') ?>" class="icon-btn">📋 Lab Order</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Employee Information -->
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

  <!-- Doctor Schedule & Appointments -->
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

<!-- Employee Edit Modal -->
<div class="modal-overlay" id="editModal" aria-hidden="true">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Employee</h3>
      <button id="closeEdit">&times;</button>
    </div>
    <form id="editForm">
      <div class="form-grid" style="gap:10px;">
        <div class="form-group"><label>Name</label><input type="text" name="name" value="<?= esc($employee['name']) ?>" required/></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= esc($employee['email']) ?>"/></div>
        <div class="form-group"><label>Phone</label><input type="text" name="mobile_no" value="<?= esc($employee['mobile_no']) ?>" required/></div>
        <div class="form-group"><label>Gender</label>
          <select name="gender">
            <option value="">Select</option>
            <option value="male" <?= ($employee['gender']=='male')?'selected':'' ?>>Male</option>
            <option value="female" <?= ($employee['gender']=='female')?'selected':'' ?>>Female</option>
          </select>
        </div>
        <div class="form-group"><label>DOB</label><input type="date" name="dob" value="<?= esc($employee['dob']) ?>"/></div>
        <div class="form-group"><label>CNIC <small>(Optional, 13 digits if entered)</small></label>
          <input type="text" name="cnic" value="<?= esc($employee['cnic'] ?? '') ?>" placeholder="Enter 13 digits or leave empty"/>
        </div>
        <div class="form-group"><label>Address</label><textarea name="address"><?= esc($employee['address']) ?></textarea></div>
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
