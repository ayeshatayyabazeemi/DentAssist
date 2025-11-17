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
    .modal-overlay { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; justify-content:center; align-items:center; z-index:999; }
    .modal-content { background:white; padding:20px; border-radius:8px; width:90%; max-width:500px; position:relative; }
    .modal-close { position:absolute; top:10px; right:10px; cursor:pointer; font-size:20px; background:none; border:none; }
  </style>
</head>
<body>

<div class="profile-header">
  <h1><?= esc($patient['name'] ?? 'none') ?></h1>
  <div class="header-icons">
    <span class="icon edit" id="editPatient">✏️</span>
    <span class="icon delete" id="deletePatient" data-id="<?= esc($patient['patient_id']); ?>">🗑️</span>
  </div>
</div>

<div class="form-section">
  <h2>Patient Information</h2>
  <div class="form-grid">

    <?php 
    $fields = [
      'name'=>'Name','email'=>'Email','mobile_no'=>'Mobile No','gender'=>'Gender','dob'=>'DOB',
      'address'=>'Address','occupation'=>'Occupation','guardianname'=>'Guardian Name',
      'guardianphonenumber'=>'Guardian Phone','guardianrelation'=>'Guardian Relation',
      'insurance'=>'Insurance','doctorName'=>'Doctor Name','cnic'=>'CNIC'
    ]; 
    ?>

    <?php foreach($fields as $key=>$label): ?>
      <div class="form-group">
        <label><?= $label ?></label>
        <span id="<?= $key ?>Span"><?= esc($patient[$key] ?? 'none') ?></span>
        <input type="text" id="<?= $key ?>Input" value="<?= esc($patient[$key] ?? '') ?>" style="display:none;">
      </div>
    <?php endforeach; ?>

    <!-- Non-editable -->
    <div class="form-group"><label>Blood Group</label><span><?= esc($patient['bloodGroup'] ?? 'none') ?></span></div>
    <div class="form-group"><label>Registration Date</label><span><?= esc($patient['regdate'] ?? 'none') ?></span></div>

  </div>
  <button class="submit-btn" id="savePatient" style="display:none;">Save</button>
</div>

<div class="appointments-section">
  <h2>
    Appointments
    <button type="button" class="btn-add-appt" id="openApptForm">+ Add Appointment</button>
  </h2>
  <table class="appointments-table" border="1" cellpadding="5" cellspacing="0">
    <thead>
      <tr>
        <th>Date</th>
        <th>Day</th>
        <th>Doctor</th>
        <th>Slot</th>
      </tr>
    </thead>
    <tbody id="appointmentsBody">
      <?php if(!empty($appointments)): ?>
        <?php foreach($appointments as $appt): ?>
          <tr>
            <td><?= esc($appt['appointment_date']) ?></td>
            <td><?= date('D', strtotime($appt['appointment_date'])) ?></td>
            <td><?= esc($appt['doctor_name']) ?></td>
            <td><?= esc($appt['slot']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4">No appointments yet</td></tr>
      <?php endif; ?>
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
          <label for="patient_id" class="required-label">Patient ID</label>
          <input id="patient_id" name="patient_id" value="<?= esc($patient['patient_id'] ?? '') ?>" readonly required />
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
      <button type="submit" class="submit-btn">Save Appointment</button>
    </form>
  </div>
</div>

<script src="/assets/js/patientprofile.js"></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{

  const apptModal=document.getElementById('apptModal');
  const openBtn=document.getElementById('openApptForm');
  const closeBtn=document.getElementById('closeApptForm');
  const doctorSelect=document.getElementById('doctor_id');
  const dateInput=document.getElementById('date');
  const slotSelect=document.getElementById('slot');
  const form=document.getElementById('makeAppointmentForm');
  const appointmentsBody=document.getElementById('appointmentsBody');
  const patientId=document.getElementById('patient_id').value;

  // Open modal
  openBtn.onclick=()=>apptModal.style.display='flex';
  closeBtn.onclick=()=>apptModal.style.display='none';

  // Load doctors
  fetch(`/api/appointments/form/${patientId}`)
    .then(res=>res.json())
    .then(json=>{
      json.doctors.forEach(d=>{
        const opt=document.createElement('option');
        opt.value=d.employee_id;
        opt.textContent=d.name;
        doctorSelect.appendChild(opt);
      });
    });

  // Load slots when doctor/date selected
  [doctorSelect,dateInput].forEach(el=>{
    el.addEventListener('change',()=>{
      const doctorId=doctorSelect.value;
      const date=dateInput.value;
      slotSelect.innerHTML='<option value="">Select Slot</option>';
      if(!doctorId || !date) return;
      fetch(`/api/appointments/getSlots?doctor_id=${doctorId}&date=${date}`)
        .then(res=>res.json())
        .then(json=>{
          json.slots.forEach(s=>{
            const opt=document.createElement('option');
            opt.value=s.start;
            opt.textContent=s.display;
            slotSelect.appendChild(opt);
          });
        });
    });
  });

  // Submit new appointment
  form.addEventListener('submit',e=>{
    e.preventDefault();
    const data={ patient_id, doctor_id:doctorSelect.value, date:dateInput.value, slot:slotSelect.value };
    fetch('/api/appointments/save',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify(data)
    }).then(res=>res.json()).then(json=>{
      if(json.status==='success'){
        const a=json.appointment;
        const row=document.createElement('tr');
        row.innerHTML=`<td>${a.appointment_date}</td><td>${new Date(a.appointment_date).toLocaleDateString('en-US',{weekday:'short'})}</td><td>${a.doctor_name}</td><td>${a.slot}</td>`;
        appointmentsBody.appendChild(row);
        apptModal.style.display='none';
        form.reset();
        slotSelect.innerHTML='<option value="">Select Slot</option>';
      } else alert(json.message);
    });
  });

});
</script>

</body>
</html>
