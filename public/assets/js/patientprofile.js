
document.addEventListener("DOMContentLoaded", function() {
  const deleteBtn = document.getElementById("deletePatient");
  const editBtn = document.querySelector('.icon.edit');

  // Make all fields editable except these
  const nonEditable = ['regdate','bloodGroup'];
  const allFields = ['name','mobile_no','email','gender','dob','address','occupation',
                     'guardianname','guardianphonenumber','guardianrelation','insurance','doctorName','cnic'];
  const editableFields = allFields.filter(f => !nonEditable.includes(f));

  // ===== Notification function =====
  function showNotification(msg, type = 'success') {
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();

    const notif = document.createElement('div');
    notif.className = `notification ${type}`;
    notif.textContent = msg;
    document.body.appendChild(notif);

    setTimeout(() => notif.remove(), 3000);
  }

  // ===== DELETE =====
  if (deleteBtn) {
    deleteBtn.addEventListener("click", function() {
      const patientId = this.getAttribute("data-id");
      if (confirm("Are you sure you want to delete this patient? This action cannot be undone.")) {
        fetch(`/api/patient/${patientId}`, { method: "DELETE" })
        .then(res => res.json())
        .then(data => {
          if(data.status === 'success'){
            showNotification("Patient deleted!", 'success');
            setTimeout(() => window.location.href = "/adminDashboard", 1500);
          } else {
            showNotification(data.message || 'Error deleting patient', 'error');
          }
        })
        .catch(err => {
          console.error(err);
          showNotification('Something went wrong while deleting the patient.', 'error');
        });
      }
    });
  }

  // ===== EDIT & SAVE =====
  if(editBtn){
    editBtn.addEventListener('click', function(){
      editableFields.forEach(field => {
        const span = document.getElementById(field+'Span');
        if(!span) return;

        let input;
        if(field==='gender' || field==='guardianrelation'){
          input = document.createElement('select');
          const opts = field==='gender'?['','male','female','other']:['','Parent','Spouse','Brother','Sister','Son','Daughter','Other'];
          opts.forEach(val=>{
            const opt = document.createElement('option'); 
            opt.value = val; 
            opt.textContent = val; 
            if(span.textContent===val) opt.selected=true; 
            input.appendChild(opt);
          });
        } else if(field==='dob'){
          input = document.createElement('input'); 
          input.type='date'; 
          input.value = span.textContent==='none'?'':span.textContent;
        } else {
          input = document.createElement('input'); 
          input.type='text'; 
          input.value = span.textContent==='none'?'':span.textContent;
        }

        input.id = field+'Input'; 
        span.replaceWith(input);
      });

      // ===== Save Button =====
      let saveBtn = document.getElementById('savePatientBtn');
      if(!saveBtn){
        saveBtn = document.createElement('button');
        saveBtn.id = 'savePatientBtn'; 
        saveBtn.textContent = 'Save'; 
        saveBtn.className = 'submit-btn';
        document.querySelector('.form-section').appendChild(saveBtn);
      }

      saveBtn.onclick = function(){
        const patientId = deleteBtn.getAttribute('data-id'); 
        const data = {};
        editableFields.forEach(field=>{
          const input = document.getElementById(field+'Input'); 
          if(input) data[field] = input.value;
        });

        fetch(`/api/patient/update/${patientId}`,{
          method:'PUT', 
          headers:{'Content-Type':'application/json'}, 
          body: JSON.stringify(data)
        })
        .then(res=>res.json())
        .then(resp=>{
          if(resp.status==='success'){
            showNotification('Patient info updated!', 'success');
            editableFields.forEach(field=>{
              const input = document.getElementById(field+'Input');
              const span = document.createElement('span'); 
              span.id = field+'Span'; 
              span.textContent = data[field]||'none';
              input.replaceWith(span);
            });
            saveBtn.remove();
          } else {
            showNotification(resp.message || 'Error updating patient', 'error');
          }
        })
        .catch(err=>{
          console.error(err);
          showNotification('Something went wrong while updating patient.', 'error');
        });
      }
    });
  }

  // ===== Appointment modal =====
  const openAppt = document.getElementById('openApptForm');
  const closeAppt = document.getElementById('closeApptForm');
  const modal = document.getElementById('apptModal');
  openAppt.addEventListener('click', ()=>{ modal.style.display='block'; });
  closeAppt.addEventListener('click', ()=>{ modal.style.display='none'; });

});
