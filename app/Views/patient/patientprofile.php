<!-- File: app/Views/patient/patientprofile.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Profile</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/patientprofile.css') ?>">
</head>
<body>

  <div class="profile-header">
    <h1><?= esc($patient['name'] ?? 'none') ?></h1>
    <div class="header-icons">
      <span class="icon edit">✏️</span>
      <span class="icon delete">🗑️</span>
    </div>
  </div>

  <div class="form-section">
    <h2>Patient Information</h2>
    <div class="form-grid">
      <!-- Row 1: Name, Email -->
      <div class="form-group">
        <label for="name" class="required-label">Name</label>
        <span><?= esc($patient['name'] ?? 'none') ?></span>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <span><?= esc($patient['email'] ?? 'none') ?></span>
      </div>

      <!-- Row 2: Mobile No, Gender, DOB -->
      <div class="form-group">
        <label for="mobile_no" class="required-label">Mobile No</label>
        <span><?= esc($patient['mobile_no'] ?? 'none') ?></span>
      </div>
      <div class="form-group">
        <label for="gender" class="required-label">Gender</label>
        <span><?= esc(ucfirst($patient['gender'] ?? 'none')) ?></span>
      </div>
      <div class="form-group">
        <label for="dob">Date of Birth</label>
        <span><?= esc($patient['dob'] ?? 'none') ?></span>
      </div>

      <!-- Row 3: CNIC, Blood Group, Occupation -->
      <div class="form-group">
        <label for="cnic">CNIC (13 digits, no “-”)</label>
        <span><?= esc($patient['cnic'] ?? 'none') ?></span>
      </div>
      <div class="form-group">
        <label for="bloodGroup">Blood Group</label>
        <span><?= esc($patient['bloodGroup'] ?? 'none') ?></span>
      </div>
      <div class="form-group">
        <label for="occupation">Occupation</label>
        <span><?= esc($patient['occupation'] ?? 'none') ?></span>
      </div>

      <!-- Row 4: Address (wide), Reg Date -->
      <div class="form-group address-field">
        <label for="address">Address</label>
        <span><?= esc($patient['address'] ?? 'none') ?></span>
      </div>
      <div class="form-group">
        <label for="regdate">Registration Date</label>
        <span><?= esc($patient['regdate'] ?? 'none') ?></span>
      </div>

      <!-- Row 5: Guardian Name, Guardian Phone, Doctor Name -->
      <div class="form-group">
        <label for="guardianname">Guardian Name</label>
        <span><?= esc($patient['guardianname'] ?? 'none') ?></span>
      </div>
      <div class="form-group">
        <label for="guardianphonenumber">Guardian Phone</label>
        <span><?= esc($patient['guardianphonenumber'] ?? 'none') ?></span>
      </div>
      <div class="form-group">
        <label for="doctorName">Doctor Name</label>
        <span><?= esc($patient['doctorName'] ?? 'none') ?></span>
      </div>

      <!-- Row 6: Insurance -->
      <div class="form-group">
        <label for="insurance">Insurance</label>
        <span><?= esc($patient['insurance'] ?? 'none') ?></span>
      </div>
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
      <!-- existing appointments rows here -->
    </tbody>
  </table>
</div>

<!-- Modal / Overlay Container for “Make Appointment” form -->
<div class="modal-overlay" id="apptModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Make Appointment</h3>
      <button type="button" class="modal-close" id="closeApptForm">&times;</button>
    </div>
    <form id="makeAppointmentForm">
      <div class="form-grid">
        <div class="form-group">
          <label for="patient_id" class="required-label">Patient ID</label>
          <input  id="patient_id" name="patient name" required />
        </div>
        <div class="form-group">
          <label for="doctor_id" class="required-label">Doctor</label>
          <select id="doctor_id" name="doctor_id" required>
            <option value="">Select Doctor</option>
            <!-- doctor options dynamically filled later -->
          </select>
        </div>
        <div class="form-group">
          <label for="date" class="required-label">Date</label>
          <input type="date" id="date" name="date" required />
        </div>
       
        <div class="form-group">
          <label for="slot" class="required-label">Slot</label>
          <select id="slot" name="slot" required>
            <option value="">Select Slot</option>
            <!-- slot options logic later -->
          </select>
        </div>
      </div> <!-- end form-grid -->
      <button type="submit" class="btn-add-appt">Save Appointment</button>
    </form>
  </div>
</div>
<script src="\assets\js\patientprofile.js"></script>

</body>
</html>
