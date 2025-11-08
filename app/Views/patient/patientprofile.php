<!-- File: app/Views/patient/patientprofile.php -->
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

<script>
document.addEventListener("DOMContentLoaded", function() {
  const deleteBtn = document.getElementById("deletePatient");
  const editBtn = document.querySelector('.icon.edit');
  const editableFields = ['name','mobile_no','email','gender','dob','address','occupation','guardianname','guardianphonenumber','guardianrelation','insurance'];

  // DELETE
  if (deleteBtn) {
    deleteBtn.addEventListener("click", function() {
      const patientId = this.getAttribute("data-id");
      if (confirm("Are you sure you want to delete this patient? This action cannot be undone.")) {
        fetch(`/api/patient/${patientId}`, { method: "DELETE" })
        .then(res=>res.json())
        .then(data=>{
          if(data.status==='success'){ alert("Patient deleted!"); window.location.href="/adminDashboard"; }
          else alert(data.message);
        }).catch(err=>{ console.error(err); alert("Error deleting patient"); });
      }
    });
  }

  // EDIT & SAVE
  if(editBtn){
    editBtn.addEventListener('click', function(){
      editableFields.forEach(field=>{
        const span = document.getElementById(field+'Span');
        if(!span) return;

        let input;
        if(field==='gender' || field==='guardianrelation'){
          input = document.createElement('select');
          const opts = field==='gender'?['','male','female','other']:['','Parent','Spouse','Brother','Sister','Son','Daughter','Other'];
          opts.forEach(val=>{
            const opt=document.createElement('option'); opt.value=val; opt.textContent=val; if(span.textContent===val) opt.selected=true; input.appendChild(opt);
          });
        } else if(field==='dob'){ input=document.createElement('input'); input.type='date'; input.value=span.textContent==='none'?'':span.textContent; }
        else { input=document.createElement('input'); input.type='text'; input.value=span.textContent==='none'?'':span.textContent; }

        input.id=field+'Input'; span.replaceWith(input);
      });

      // SAVE button
      let saveBtn = document.getElementById('savePatientBtn');
      if(!saveBtn){
        saveBtn=document.createElement('button');
        saveBtn.id='savePatientBtn'; saveBtn.textContent='Save'; saveBtn.className='submit-btn';
        document.querySelector('.form-section').appendChild(saveBtn);
      }

      saveBtn.onclick=function(){
        const patientId=deleteBtn.getAttribute('data-id'); const data={};
        editableFields.forEach(field=>{
          const input=document.getElementById(field+'Input'); if(input) data[field]=input.value;
        });

        fetch(`/api/patient/update/${patientId}`,{
          method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)
        })
        .then(res=>res.json())
        .then(resp=>{
          if(resp.status==='success'){
            alert('Patient info updated!');
            editableFields.forEach(field=>{
              const input=document.getElementById(field+'Input');
              const span=document.createElement('span'); span.id=field+'Span'; span.textContent=data[field]||'none';
              input.replaceWith(span);
            });
            saveBtn.remove();
          } else alert(resp.message);
        })
        .catch(err=>{ console.error(err); alert('Error updating patient'); });
      }
    });
  }

  // Appointment modal
  const openAppt = document.getElementById('openApptForm');
  const closeAppt = document.getElementById('closeApptForm');
  const modal = document.getElementById('apptModal');
  openAppt.addEventListener('click',()=>{ modal.style.display='block'; });
  closeAppt.addEventListener('click',()=>{ modal.style.display='none'; });
});
</script>

</body>
</html>
