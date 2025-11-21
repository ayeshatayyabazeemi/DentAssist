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
    if (empty($employee)) {
        echo '<div style="padding:20px">No employee provided to view.</div>';
        exit;
    }

    $roles = [];
    if (!empty($employee['is_admin']))         $roles[] = 'Admin';
    if (!empty($employee['is_doctor']))        $roles[] = 'Doctor';
    if (!empty($employee['is_staff']))         $roles[] = 'Staff';
    if (!empty($employee['is_receptionist'])) $roles[] = 'Receptionist';
    $designation = count($roles) ? implode(', ', $roles) : 'None';
    $empId = $employee['id'] ?? $employee['employee_id'] ?? null;

    // Determine whether we allow change password button:
    $allowChangePassword = empty($employee['is_staff']);  // only if not staff
  ?>

  <div class="container">
    <div class="profile-header">
      <div class="header-left">
        <h1 class="employee-name"><?= esc($employee['name'] ?? 'none') ?></h1>
        <div class="header-sub">
          <span class="designation">Designation: <?= esc($designation) ?></span>
        </div>
      </div>

      <div class="header-actions">
        <?php if ($allowChangePassword): ?>
          <button id="changePwdBtn" class="icon-btn" title="Change Password">🔒 Change Password</button>
        <?php endif; ?>
        <button id="editBtn" class="icon-btn" title="Edit">✏️ Edit</button>
        <button id="deleteBtn" class="icon-btn danger" title="Delete">🗑️ Delete</button>
      </div>
    </div>

    <div class="form-section">
      <h2>Employee Information</h2>

      <div class="form-grid">
        <!-- Row 1: Name, Email, Mobile No -->
        <div class="form-group">
          <label for="name" class="required-label">Name</label>
          <span id="name"><?= esc($employee['name'] ?? 'none') ?></span>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <span id="email"><?= esc($employee['email'] ?? 'none') ?></span>
        </div>

        <div class="form-group">
          <label for="mobile_no" class="required-label">Phone Number</label>
          <span id="mobile_no"><?= esc($employee['mobile_no'] ?? 'none') ?></span>
        </div>

        <!-- Row 2: Gender, DOB, CNIC -->
        <div class="form-group">
          <label for="gender" class="required-label">Gender</label>
          <span id="gender"><?= esc(!empty($employee['gender']) ? ucfirst($employee['gender']) : 'none') ?></span>
        </div>

        <div class="form-group">
          <label for="dob">Date of Birth</label>
          <span id="dob"><?= esc($employee['dob'] ?? 'none') ?></span>
        </div>

        <div class="form-group">
          <label for="cnic">CNIC (13 digits, no “-”)</label>
          <span id="cnic"><?= esc($employee['cnic'] ?? 'none') ?></span>
        </div>

        <!-- Row 3: Resignation Date and Designation -->
        <div class="form-group">
          <label for="regdate">Resignation Date</label>
          <span id="regdate"><?= esc($employee['regdate'] ?? 'none') ?></span>
        </div>

        <div class="form-group">
          <label for="designation">Designation</label>
          <span id="designation"><?= esc($designation) ?></span>
        </div>

        <!-- Row 4: Address (wide) -->
        <div class="form-group address-field">
          <label for="address">Address</label>
          <span id="address"><?= esc($employee['address'] ?? 'none') ?></span>
        </div>

      </div> <!-- end form-grid -->
    </div> <!-- end form-section -->

    <?php if (!empty($employee['is_doctor']) && isset($schedules) && is_array($schedules)): ?>
      <div class="form-section">
        <h2>Doctor Schedule</h2>
        <?php if (!empty($schedules)): ?>
          <table class="appointments-table">
            <thead>
              <tr>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($schedules as $sch): ?>
                <tr>
                  <td><?= esc($sch['day_of_week'] ?? $sch['day'] ?? '—') ?></td>
                  <td><?= esc(isset($sch['start_time']) ? date('H:i', strtotime($sch['start_time'])) : ($sch['start'] ?? '—')) ?></td>
                  <td><?= esc(isset($sch['end_time']) ? date('H:i', strtotime($sch['end_time'])) : ($sch['end'] ?? '—')) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No schedule provided.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($employee['is_doctor'])): ?>
      <div class="appointments-section">
        <h2>Doctor's Appointments</h2>

        <?php if (isset($appointments) && is_array($appointments) && !empty($appointments)): ?>
          <table class="appointments-table">
            <thead>
              <tr>
                <th>Patient</th>
                <th>Date</th>
                <th>Day</th>
                <th>Slot</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($appointments as $appt): 
                $patientName = $appt['patient_name'] ?? ($appt['patient'] ?? ($appt['patient_id'] ? 'Patient #' . esc($appt['patient_id']) : '—'));
                $date = $appt['date'] ?? $appt['appointment_date'] ?? null;
                $day = $date ? date('l', strtotime($date)) : ($appt['day'] ?? ($appt['day_of_week'] ?? '—'));
                $slot = $appt['slot'] ?? ($appt['time_slot'] ?? ((isset($appt['start_time']) && isset($appt['end_time'])) ? (date('H:i',strtotime($appt['start_time'])) . ' - ' . date('H:i',strtotime($appt['end_time']))) : '—'));
              ?>
                <tr>
                  <td><?= esc($patientName) ?></td>
                  <td><?= esc($date ? date('Y-m-d', strtotime($date)) : '—') ?></td>
                  <td><?= esc($day) ?></td>
                  <td><?= esc($slot) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No appointments provided (controller can pass $appointments array later).</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div> <!-- end container -->

  <!-- Change Password Modal -->
  <?php if ($allowChangePassword): ?>
    <div class="modal-overlay" id="changePwdModal" aria-hidden="true">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Change Password</h3>
          <button type="button" class="modal-close" id="closeChangePwd">&times;</button>
        </div>

        <form id="changePasswordForm" action="<?= site_url('employee/changePassword') ?>" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="employee_id" value="<?= esc($empId) ?>">
          <div class="form-grid">
            <div class="form-group">
              <label for="current_password" class="required-label">Current Password</label>
              <input type="password" name="current_password" id="current_password" autocomplete="current-password" required>
            </div>
            <div class="form-group">
              <label for="new_password" class="required-label">New Password</label>
              <input type="password" name="new_password" id="new_password" autocomplete="new-password" required>
            </div>
            <div class="form-group">
              <label for="confirm_new_password" class="required-label">Confirm New Password</label>
              <input type="password" name="confirm_new_password" id="confirm_new_password" autocomplete="new-password" required>
            </div>
          </div>

          <button type="submit" class="btn">Save Password</button>
        </form>
      </div>
    </div>
  <?php endif; ?>

<script src="<?= base_url('assets/js/employeeprofile.js') ?>"></script>
</body>
</html>
