<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Profile</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/patientprofile.css') ?>">

  <!-- Logo added as favicon -->
  <link rel="icon" href="<?= base_url('assets/images/mylogo.png') ?>" type="image/png">

  <style>
    /* ===================== EDIT PATIENT MODAL ===================== */
    #editPatientModal .edit-modal-content {
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
    #editPatientModal .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #f2d9c3;
      margin-bottom: 15px;
      padding-bottom: 5px;
    }
    #editPatientModal .modal-header h3 {
      margin: 0;
      font-size: 1.35rem;
      color: #c47f3a;
      font-weight: 600;
    }
    #editPatientModal .modal-close {
      font-size: 1.5rem;
      background: none;
      border: none;
      cursor: pointer;
      color: #c47f3a;
      transition: all 0.3s ease;
    }
    #editPatientModal .modal-close:hover {
      color: #a35d2b;
      transform: rotate(90deg);
    }
    #editPatientModal .form-group {
      background: #fff8f0;
      padding: 10px 12px;
      margin-bottom: 10px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      display:flex;
      flex-direction:column;
    }
    #editPatientModal .form-group label {
      display: block;
      font-weight: 500;
      margin-bottom: 4px;
      color: #8c5a32;
      font-size: 0.95rem;
    }
    #editPatientModal input,
    #editPatientModal select {
      width: 100%;
      padding: 7px 10px;
      border-radius: 6px;
      border: 1px solid #e6cbb2;
      background: #fffefc;
      font-size: 0.93rem;
      transition: all 0.3s ease;
      box-sizing: border-box;
    }
    #editPatientModal input:focus,
    #editPatientModal select:focus {
      border-color: #c47f3a;
      box-shadow: 0 0 5px rgba(196, 127, 58, 0.35);
      outline: none;
    }
    #editPatientModal .submit-btn {
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
    #editPatientModal .submit-btn:hover {
      background: #a35d2b;
      transform: translateY(-2px);
      box-shadow: 0 5px 12px rgba(163, 93, 43, 0.3);
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(-15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ===================== NOTIFICATION ===================== */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #4caf50;
      color: white;
      padding: 12px 18px;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
      z-index: 5000;
      opacity: 0;
      transform: translateY(-10px);
      animation: slideIn 0.4s forwards;
    }
    .notification.error { background: #f44336; }
    @keyframes slideIn {
      to { opacity: 1; transform: translateY(0); }
    }

    /* ===================== NEW BUTTON ===================== */
    .btn-generate-id {
      background-image: linear-gradient(135deg, #FFA4A4, #FFBDBD);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 0.9em;
      text-decoration: none;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      margin-left: 10px;
    }
    .btn-generate-id:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(255,189,189,0.5);
    }
    .btn-generate-invoice {
      background-image: linear-gradient(135deg, #FFA4A4, #FFBDBD);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 0.9em;
      text-decoration: none;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      margin-left: 10px;
    }
    .btn-generate-invoice:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(255,189,189,0.5);
    }
  </style>
</head>
<body>

<div class="profile-header">
  <h1><?= esc($patient['name'] ?? 'none') ?></h1>
  <div class="header-icons">
    
    <span class="icon edit" id="openEditPatientModal">✏️</span>
    <span class="icon delete" id="deletePatient" data-id="<?= esc($patient['patient_id']); ?>">🗑️</span>
        <!-- <a href="<?= base_url('patient/invoice/'.$patient['patient_id']) ?>" class="btn-generate-invoice" target="_blank">Invoice</a> -->

<a href="<?= base_url('patient/invoiceView/'.$patient['patient_id'].'?appointment_id='.(isset($appointment_id) ? $appointment_id : '')) ?>" 
   class="btn-generate-invoice" 
   target="_blank">
   Invoice
</a>
        
    <a href="<?= base_url('patientcard/'.$patient['patient_id']) ?>" class="btn-generate-id" target="_blank">Generate Patient Card</a>

    <!-- NEW BUTTON -->
  </div>
</div>

<input type="hidden" id="patient_id" value="<?= esc($patient['patient_id']); ?>"/>

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
    <!-- new -->
     <div class="form-group">
    <label>MR Number</label>
    <input type="text" id="mr_number" value="<?= esc($patient['mr_number'] ?? ''); ?>" readonly>
    </div>

    <!-- <div class="form-group"><label>MR Number</label><span id="mr_numberSpan"><?= esc($patient['mr_number'] ?? 'none') ?></span></div> -->
  </div>
</div>
<div class="appointments-section-invoice">
  <h2>Invoices</h2>

  <table class="appointments-table-invoice">
    <thead>
      <tr>
        <th>Invoice ID</th>
        <th>Date</th>
        <th>Description</th>
        <th>Paid</th>
        <th>Dues</th>
        <th>User</th>
      </tr>
    </thead>

    <tbody>
      <?php if (!empty($invoices)): ?>
        <?php foreach ($invoices as $inv): ?>
          <tr>
            <td><?= esc($inv['invoice_id'] ?? '-') ?></td>
            <td><?= esc($inv['payment_date'] ?? '-') ?></td>
            <td><?= esc($inv['description'] ?? '-') ?></td>
            <td>Rs <?= esc($inv['paid_amount'] ?? '0') ?></td>
            <td>Rs <?= esc($inv['dues'] ?? '0') ?></td>
            <td><?= esc($inv['user_name'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center;">
            No invoices found
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
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
    <tbody></tbody>
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
        <!-- SHOW MR NUMBER (VISIBLE TO USER) -->
<div class="form-group">
  <label>MR Number</label>
  <input value="<?= esc($patient['mr_number']); ?>" readonly>
</div>

<!-- REAL PATIENT ID (HIDDEN, FOR SYSTEM) -->
<input
  type="hidden"
  id="patient_id"
  name="patient_id"
  value="<?= esc($patient['patient_id']); ?>"
>

        <!-- <div class="form-group"><label>Patient ID</label><input id="patient_id" name="patient_id" value="<?= esc($patient['patient_id']); ?>" readonly/></div> -->
        <div class="form-group"><label>Doctor</label><select id="doctor_id" name="doctor_id" required><option value="">Select Doctor</option></select></div>
        <div class="form-group"><label>Date</label><input type="date" id="date" name="date" required /></div>
        <div class="form-group"><label>Slot</label><select id="slot" name="slot" required><option value="">Select Slot</option></select></div>
      </div>
      <button type="submit" class="btn-add-appt">Save Appointment</button>
    </form>
  </div>
</div>

<!-- Edit Patient Modal -->
<div class="modal-overlay" id="editPatientModal" style="display:none;">
  <div class="edit-modal-content">
    <div class="modal-header">
      <h3>Edit Patient</h3>
      <button type="button" class="modal-close" id="closeEditPatientModal">&times;</button>
    </div>
    <form id="editPatientForm">
      <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
        <div class="form-group"><label>Name</label><input type="text" id="edit_name" required/></div>
        <div class="form-group"><label>Email</label><input type="email" id="edit_email"/></div>
        <div class="form-group"><label>Mobile No</label><input type="text" id="edit_mobile_no"/></div>
        <div class="form-group"><label>Gender</label>
          <select id="edit_gender">
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="form-group"><label>DOB</label><input type="date" id="edit_dob"/></div>
        <div class="form-group"><label>Address</label><input type="text" id="edit_address"/></div>
        <div class="form-group"><label>Occupation</label><input type="text" id="edit_occupation"/></div>
        <div class="form-group"><label>Guardian Name</label><input type="text" id="edit_guardianname"/></div>
        <div class="form-group"><label>Guardian Phone</label><input type="text" id="edit_guardianphonenumber"/></div>
        <div class="form-group"><label>Guardian Relation</label>
          <select id="edit_guardianrelation">
            <option value="">Select Relation</option>
            <option value="Parent">Parent</option>
            <option value="Spouse">Spouse</option>
            <option value="Brother">Brother</option>
            <option value="Sister">Sister</option>
            <option value="Son">Son</option>
            <option value="Daughter">Daughter</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="form-group"><label>Insurance</label><input type="text" id="edit_insurance" readonly/></div>
        <div class="form-group"><label>Doctor Name</label><input type="text" id="edit_doctorName" readonly/></div>
        <div class="form-group"><label>Blood Group</label><input type="text" id="edit_bloodGroup" readonly/></div>
        <div class="form-group"><label>MR Number</label><input type="text" id="edit_mr_number" readonly/></div>
      </div>
      <button type="submit" class="submit-btn">Save Changes</button>
    </form>
  </div>
</div>

<script src="<?= base_url('assets/js/patientprofile.js') ?>"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const patientId = document.getElementById("patient_id").value;
  const generateBtn = document.querySelector(".btn-generate-id");

  generateBtn.addEventListener("click", function(e) {
    e.preventDefault();
    window.open(`/patientcard/${patientId}`, "_blank");
  });
});
</script>
</body>
</html>
