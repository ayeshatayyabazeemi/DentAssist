document.addEventListener("DOMContentLoaded", function () {

  // =========================
  // TABS & DASHBOARD LOGIC
  // =========================
  const tabButtons = document.querySelectorAll('header nav ul li button[data-tab]');
  const tabSections = document.querySelectorAll('.tab-content');
  const dashboardLogo = document.getElementById('dashboardLogo');

  function showTab(tabName) {
    tabSections.forEach(section => section.style.display = 'none');
    tabButtons.forEach(btn => btn.classList.remove('active'));

    const targetSec = document.getElementById(`tab-${tabName}`);
    if (targetSec) targetSec.style.display = 'block';

    const clickedBtn = document.querySelector(`button[data-tab="${tabName}"]`);
    if (clickedBtn) clickedBtn.classList.add('active');
  }

  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => showTab(btn.getAttribute('data-tab')));
  });

  if (dashboardLogo) {
    dashboardLogo.addEventListener('click', () => showTab('dashboard'));
  }

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) logoutBtn.addEventListener('click', () => window.location.href = '/admin/logout');

  // Initialize default tab
  showTab('dashboard');

  
  // =========================
  // MR NUMBER AUTO-GENERATION
  // =========================
 

  // =========================
  // POPUP FOR MR NUMBER
  // =========================
  function showMRPopup(mr) {
    const overlay = document.createElement("div");
    overlay.style.cssText = `
      position:fixed; inset:0; background:rgba(0,0,0,0.55);
      display:flex; align-items:center; justify-content:center; z-index:9999;
    `;
    const box = document.createElement("div");
    box.style.cssText = `
      background:#fff; padding:30px 25px; border-radius:14px; text-align:center;
      width:360px; box-shadow:0 10px 35px rgba(0,0,0,0.25);
      font-family: system-ui, -apple-system, BlinkMacSystemFont;
    `;
    box.innerHTML = `
      <div style="width:70px;height:70px;margin:0 auto 15px;background:#28a745;border-radius:50%;
      display:flex;align-items:center;justify-content:center;color:white;font-size:36px;">✓</div>
      <h2 style="margin:10px 0 6px;color:#222;">Patient Registered</h2>
      <p style="color:#666;margin-bottom:18px;">Registration completed successfully</p>
      <div style="background:#f4f6f8;padding:14px;border-radius:10px;margin-bottom:20px;font-size:18px;letter-spacing:1px;">
        <strong>MR Number</strong><br>
        <span style="color:#28a745;font-size:22px;">${mr}</span>
      </div>
      <button id="mrOkBtn" style="width:100%;padding:12px;background:#28a745;color:#fff;border:none;border-radius:8px;font-size:16px;cursor:pointer;">OK</button>
    `;
    overlay.appendChild(box);
    document.body.appendChild(overlay);
    document.getElementById("mrOkBtn").onclick = () => overlay.remove();
  }

  // =========================
  // NOTIFICATION HELPER
  // =========================
  const notyf = new Notyf({ duration: 3000, position: { x: 'center', y: 'top' } });

  // =========================
  // ADD PATIENT FORM LOGIC
  // =========================
  let isSubmitting = false;
  const patientForm = document.getElementById('patientForm');
  if (patientForm) {
    patientForm.addEventListener('submit', e => {
      e.preventDefault();
      if (isSubmitting) return;
      isSubmitting = true;
      e.stopImmediatePropagation?.();

      const submitBtn = patientForm.querySelector('button[type="submit"], input[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;

      const dataObj = {};
      new FormData(patientForm).forEach((value, key) => dataObj[key] = value);

      // AGE CALCULATION
      if (dataObj.dob) {
        const dob = new Date(dataObj.dob);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) age--;
        dataObj.age = `${age} Years`;
      }

    // do NOT send mr_number
      delete dataObj.mr_number;


      // VALIDATION
      const cnicRegex = /^\d{13}$/;
      const phoneRegex = /^0\d{10}$/;
      if (dataObj.cnic && !cnicRegex.test(dataObj.cnic)) { notyf.error('CNIC must be 13 digits'); unlock(); return; }
      if (dataObj.mobile_no && !phoneRegex.test(dataObj.mobile_no)) { notyf.error('Mobile must be 11 digits'); unlock(); return; }
      if (dataObj.guardianphonenumber && !phoneRegex.test(dataObj.guardianphonenumber)) { notyf.error('Guardian phone invalid'); unlock(); return; }

      // NORMALIZATION
      if (dataObj.mobile_no) dataObj.mobile_no = dataObj.mobile_no.replace(/^0/, "");
      if (!dataObj.regdate || dataObj.regdate.trim() === "") {
        const today = new Date();
        dataObj.regdate = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
      }

      // ADD MR NUMBER
      
      // SUBMIT
          fetch('/api/patient/add', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dataObj)
    })
    .then(res => res.json())
    .then(json => {
      if (json.status === 'success') {
        showMRPopup(json.mr_number); // backend-generated MR
        patientForm.reset();
      } else {
        notyf.error(json.message || "Registration failed");
      }
    })
    .catch(() => notyf.error("Network error"))
    .finally(unlock);

    function unlock() {
      isSubmitting = false;
      if (submitBtn) submitBtn.disabled = false;
    }

    });
  }


  // =========================
  // SEARCH AUTOCOMPLETE
  // =========================
  function setupSearch({ inputId, dropdownId, apiUrl, onSelect }) {
    const searchInput = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    if (!searchInput || !dropdown) return;

    let debounceTimer;
    searchInput.addEventListener('input', e => {
      const query = e.target.value.trim().toLowerCase();
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        fetch(`${apiUrl}?q=${encodeURIComponent(query)}`)
          .then(res => res.json())
          .then(json => {
            if (json.status === 'success' && json.data.length > 0) showDropdown(json.data, dropdown, onSelect);
            else dropdown.style.display = 'none';
          })
          .catch(err => console.error(err));
      }, 300);
    });

    document.addEventListener('click', e => {
      if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) dropdown.style.display = 'none';
    });
  }

  function showDropdown(data, dropdown, onSelect) {
    dropdown.innerHTML = '';
    if (!data.length) return;
    data.forEach(item => {
      const div = document.createElement('div');
      div.classList.add('autocomplete-item');
      div.innerHTML = `<strong>ID:</strong> ${item.id ?? 'none'} | <strong>Name:</strong> ${item.name ?? 'none'} | <strong>Phone:</strong> ${item.mobile_no ?? 'none'} | <strong>Email:</strong> ${item.email ?? 'none'} | <strong>CNIC:</strong> ${item.cnic ?? 'none'}`;
      div.addEventListener('mousedown', () => onSelect(item));
      dropdown.appendChild(div);
    });
    dropdown.style.display = 'block';
  }

  // Patient search
  setupSearch({ inputId: 'patientSearch', dropdownId: 'searchResults', apiUrl: '/api/patient/search', onSelect: item => {
        // Use patient_id for redirect
        const patientId = item.patient_id; // fetch patient_id from API response
        window.location.href = `/patient/profile/${patientId}`;
    }
  });
  // Employee search
  setupSearch({ inputId: 'employeeSearch', dropdownId: 'employeeResults', apiUrl: '/api/employee/search', onSelect: item => window.location.href = `/employee/profile/${item.id}` });

  // =========================
  // ADD EMPLOYEE FORM
  // =========================
  const form = document.getElementById('employeeForm');
  const scheduleSection = document.getElementById('scheduleSection');
  const designation = document.getElementById('emp_designation');

  if (designation) {
    designation.addEventListener('change', () => { scheduleSection.style.display = designation.value === 'doctor' ? 'block' : 'none'; });
  }

  if (form) {
    form.addEventListener('submit', e => {
      e.preventDefault();
      let valid = true;
      const cnicRegex = /^\d{13}$/;
      const phoneRegex = /^0\d{10}$/;
      const name = form.elements['name'].value.trim();
      const mobile = form.elements['mobile_no'].value.trim();
      const email = form.elements['email'].value.trim();
      const cnic = form.elements['cnic'].value.trim();
      const gender = form.elements['gender'].value;
      const pwd = form.elements['password'].value;
      const cpwd = form.elements['c_password'].value;
      const desig = form.elements['designation'].value;

      if (!name) { notyf.error('Name required'); valid = false; }
      if (!phoneRegex.test(mobile)) { notyf.error('Mobile invalid'); valid = false; }
      if (email && !/\S+@\S+\.\S+/.test(email)) { notyf.error('Email invalid'); valid = false; }
      if (!gender) { notyf.error('Select gender'); valid = false; }
      if (cnic && !cnicRegex.test(cnic)) { notyf.error('CNIC invalid'); valid = false; }
      if (!desig) { notyf.error('Select designation'); valid = false; }

      if (desig === 'staff') {
        if (pwd || cpwd) { notyf.error('Staff accounts cannot have password'); valid = false; }
      } else {
        if (!pwd || pwd.length < 6) { notyf.error('Password min 6 chars'); valid = false; }
        if (pwd !== cpwd) { notyf.error('Passwords do not match'); valid = false; }
      }

      let scheduleData = [];
      if (desig === 'doctor') {
        const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        days.forEach(day => {
          const start = form.elements[`start_time[${day}]`]?.value;
          const end = form.elements[`end_time[${day}]`]?.value;
          if ((start && !end) || (!start && end)) { notyf.error(`For ${day}, start & end required`); valid = false; }
          if (start && end) scheduleData.push({ day, start_time: start, end_time: end });
        });
      }

      if (!valid) return;

      const dataObj = { name, mobile_no: mobile, email, cnic, gender, designation: desig, schedule: scheduleData };
      if (desig !== 'staff' && pwd) dataObj.password = pwd;

      fetch('/api/employee/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dataObj)
      })
      .then(res => res.json())
      .then(json => {
        if (json.status === 'success') {
          notyf.success('Employee added! ID: ' + json.id);
          form.reset();
          scheduleSection.style.display = 'none';
          const totalCountElem = document.getElementById('totalCount');
          if (totalCountElem) totalCountElem.textContent = (parseInt(totalCountElem.textContent) || 0) + 1;
        } else notyf.error(json.message || 'Something went wrong');
      })
      .catch(err => { console.error(err); notyf.error('Network error'); });
    });
  }

  // =========================
  // PATIENT EDIT & DELETE
  // =========================
  const deleteBtn = document.getElementById("deletePatient");
  const editBtn = document.querySelector(".icon.edit");
  const nonEditable = ["regdate", "bloodGroup"];
  const allFields = ["name","mobile_no","email","gender","dob","address","occupation","guardianname","guardianphonenumber","guardianrelation","insurance","doctorName","cnic"];
  const editableFields = allFields.filter(f => !nonEditable.includes(f));

  if (deleteBtn) {
    deleteBtn.addEventListener("click", function () {
      const patientId = this.dataset.id;
      if (!confirm("Are you sure?")) return;
      fetch(`/api/patient/${patientId}`, { method: "DELETE" })
        .then(res => res.json())
        .then(data => {
          if (data.status === "success") { showNotification("Patient deleted", "success"); setTimeout(() => window.location.href="/adminDashboard",1500);}
          else showNotification(data.message || "Delete failed","error");
        }).catch(()=>showNotification("Server error","error"));
    });
  }

  if (editBtn) {
    editBtn.addEventListener("click", function () {
      editableFields.forEach(field => {
        const span = document.getElementById(field + "Span");
        if (!span) return;

        let input;
        if (field === "gender" || field === "guardianrelation") {
          input = document.createElement("select");
          const options = field === "gender" ? ["","male","female","other"] : ["","Parent","Spouse","Brother","Sister","Son","Daughter","Other"];
          options.forEach(optVal => { const opt = document.createElement("option"); opt.value=optVal; opt.textContent=optVal; if(span.textContent===optVal) opt.selected=true; input.appendChild(opt); });
        } else if (field==="dob") { input=document.createElement("input"); input.type="date"; input.value=span.textContent==="none"?"":span.textContent; }
        else { input=document.createElement("input"); input.type="text"; input.value=span.textContent==="none"?"":span.textContent; }

        input.id = field + "Input";
        span.replaceWith(input);
      });

      let saveBtn = document.getElementById("savePatientBtn");
      if (!saveBtn) {
        saveBtn = document.createElement("button");
        saveBtn.id = "savePatientBtn";
        saveBtn.textContent = "Save";
        saveBtn.className = "submit-btn";
        document.querySelector(".form-section").appendChild(saveBtn);
      }

      saveBtn.onclick = function () {
        const patientId = deleteBtn.dataset.id;
        const data = {};
        editableFields.forEach(field => { const input = document.getElementById(field+"Input"); if(input) data[field]=input.value; });

        fetch(`/api/patient/update/${patientId}`, { method:"PUT", headers:{ "Content-Type":"application/json" }, body: JSON.stringify(data) })
        .then(res=>res.json())
        .then(resp=>{
          if(resp.status==="success"){
            showNotification("Patient updated","success");
            editableFields.forEach(field=>{
              const input=document.getElementById(field+"Input");
              if(!input) return;
              const span=document.createElement("span");
              span.id=field+"Span";
              span.textContent=data[field]||"none";
              input.replaceWith(span);
            });
            saveBtn.remove();
          } else showNotification(resp.message||"Update failed","error");
        }).catch(()=>showNotification("Server error","error"));
      };
    });
  }

  // =========================
  // APPOINTMENT MODAL
  // =========================
  const openBtn = document.getElementById("openApptForm");
  const closeBtn = document.getElementById("closeApptForm");
  const modal = document.getElementById("apptModal");
  const patientIdInput = document.getElementById("patient_id");
  const doctorSelect = document.getElementById("doctor_id");
  const dateInput = document.getElementById("date");
  const slotSelect = document.getElementById("slot");
  const makeAppointmentForm = document.getElementById("makeAppointmentForm");

  function loadAppointments() {
    const patientId = patientIdInput.value;
    fetch(`/api/patient/getAppointments/${patientId}`)
      .then(res=>res.json())
      .then(data=>{
        const tableBody=document.querySelector(".appointments-table tbody");
        tableBody.innerHTML="";
        if(data.status==="success" && data.appointments.length){
          data.appointments.forEach(appt=>{
            const row=document.createElement("tr");
            row.innerHTML=`<td>${appt.appointment_date}</td><td>${new Date(appt.appointment_date).toLocaleDateString('en-US',{ weekday:'long'})}</td><td>${appt.doctor_name}</td><td>${appt.slot}</td>`;
            tableBody.appendChild(row);
          });
        } else tableBody.innerHTML=`<tr><td colspan="4">No appointments yet</td></tr>`;
      });
  }
  loadAppointments();

  if(openBtn) openBtn.addEventListener("click", ()=>{ modal.style.display="flex"; loadDoctors(); });
  if(closeBtn) closeBtn.addEventListener("click", ()=>modal.style.display="none");
  if(modal) modal.addEventListener("click", e=>{ if(e.target===modal) modal.style.display="none"; });

  function loadDoctors(){
    fetch(`/api/appointments/form/${patientIdInput.value}`)
      .then(res=>res.json())
      .then(data=>{
        doctorSelect.innerHTML=`<option value="">Select Doctor</option>`;
        data.doctors?.forEach(doc=>{
          const opt=document.createElement("option");
          opt.value=doc.employee_id;
          opt.textContent=doc.name;
          doctorSelect.appendChild(opt);
        });
      });
  }

  function loadSlots(){
    const doctorId = doctorSelect.value;
    const date = dateInput.value;
    if(!doctorId||!date){ slotSelect.innerHTML=`<option value="">Select Slot</option>`; return; }
    fetch(`/api/appointments/getSlots?doctor_id=${doctorId}&date=${date}`)
      .then(res=>res.json())
      .then(data=>{
        slotSelect.innerHTML=`<option value="">Select Slot</option>`;
        data.slots?.forEach(slot=>{
          const opt=document.createElement("option");
          opt.value=slot.start;
          opt.textContent=slot.display;
          slotSelect.appendChild(opt);
        });
      });
  }

  doctorSelect.addEventListener("change", loadSlots);
  dateInput.addEventListener("change", loadSlots);

  if(makeAppointmentForm){
    makeAppointmentForm.addEventListener("submit", e=>{
      e.preventDefault();
      const data={ patient_id: patientIdInput.value, doctor_id: doctorSelect.value, date: dateInput.value, slot: slotSelect.value };
      if(!data.doctor_id||!data.date||!data.slot){ showNotification("Please select doctor, date & slot","error"); return; }
      fetch("/api/appointments/save",{ method:"POST", headers:{ "Content-Type":"application/json" }, body: JSON.stringify(data) })
      .then(res=>res.json())
      .then(res=>{
        if(res.status==="success"){ showNotification("Appointment saved","success"); modal.style.display="none"; makeAppointmentForm.reset(); loadAppointments(); }
        else showNotification(res.message||"Failed to save","error");
      });
    });
  }

});
