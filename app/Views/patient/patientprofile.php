<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Profile</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/patientprofile.css') ?>">

  <style>
    .submit-btn { margin-top: 15px; padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
    .submit-btn:hover { background-color: #45a049; }
    .form-group { margin-bottom: 10px; }
    .form-group label { font-weight: bold; display:block; }
    .form-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap:15px; }
    .header-icons { float:right; }
    .header-icons .icon { cursor:pointer; margin-left:10px; }

    /* Notification Styles */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 12px 20px;
      border-radius: 6px;
      color: #fff;
      font-weight: 500;
      z-index: 9999;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      opacity: 0.95;
      transition: all 0.3s ease;
    }
    .notification.success { background-color: #4CAF50; }
    .notification.error { background-color: #f44336; }
  </style>
</head>
<body>

<div class="profile-header">
  <h1><?= esc($patient['name'] ?? 'none') ?></h1>
  <div class="header-icons">
    <span class="icon edit">✏️</span>
    <span class="icon delete" id="deletePatient" data-id="<?= esc($patient['patient_id']); ?>">🗑️</span>
  </div>
</div>

<div class="form-section">
  <h2>Patient Information</h2>
  <div class="form-grid">
    <div class="form-group"><label>Name</label><span id="nameSpan"><?= esc($patient['name'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Email</label><span id="emailSpan"><?= esc($patient['email'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Mobile No</label><span id="mobile_noSpan"><?= esc($patient['mobile_no'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Gender</label><span id="genderSpan"><?= esc($patient['gender'] ?? 'none') ?></span></div>
    <div class="form-group"><label>DOB</label><span id="dobSpan"><?= esc($patient['dob'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Address</label><span id="addressSpan"><?= esc($patient['address'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Occupation</label><span id="occupationSpan"><?= esc($patient['occupation'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Guardian Name</label><span id="guardiannameSpan"><?= esc($patient['guardianname'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Guardian Phone</label><span id="guardianphonenumberSpan"><?= esc($patient['guardianphonenumber'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Guardian Relation</label><span id="guardianrelationSpan"><?= esc($patient['guardianrelation'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Insurance</label><span id="insuranceSpan"><?= esc($patient['insurance'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Doctor Name</label><span id="doctorNameSpan"><?= esc($patient['doctorName'] ?? 'none') ?></span></div>
    <div class="form-group"><label>CNIC</label><span id="cnicSpan"><?= esc($patient['cnic'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Blood Group</label><span id="bloodGroupSpan"><?= esc($patient['bloodGroup'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Registration Date</label><span id="regdateSpan"><?= esc($patient['regdate'] ?? 'none') ?></span></div>
  </div>
</div>

<div class="appointments-section">
  <h2>
    Appointments
    <button type="button" class="btn-add-appt" id="openApptForm">+ Add Appointment</button>
  </h2>
  <table class="appointments-table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Day</th>
        <th>Doctor</th>
        <th>Slots</th>
      </tr>
    </thead>
    <tbody>
      <!-- dynamic appointments here -->
    </tbody>
  </table>
</div>

<!-- Appointment Modal -->
<div class="modal-overlay" id="apptModal" style="display:none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Make Appointment</h3>
      <button type="button" class="modal-close" id="closeApptForm">&times;</button>
    </div>
    <form id="makeAppointmentForm">
      <div class="form-grid">
        <div class="form-group">
          <label>Patient ID</label>
          <input id="patient_id" name="patient_id" value="<?= esc($patient['patient_id']); ?>" readonly/>
        </div>
        <div class="form-group">
          <label>Doctor</label>
          <select id="doctor_id" name="doctor_id" required><option value="">Select Doctor</option></select>
        </div>
        <div class="form-group">
          <label>Date</label>
          <input type="date" id="date" name="date" required />
        </div>
        <div class="form-group">
          <label>Slot</label>
          <select id="slot" name="slot" required><option value="">Select Slot</option></select>
        </div>
      </div>
      <button type="submit" class="btn-add-appt">Save Appointment</button>
    </form>
  </div>
</div>

<!-- Link external JS -->
<script src="<?= base_url('assets/js/patientprofile.js') ?>"></script>
</body>
</html>
