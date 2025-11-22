document.addEventListener("DOMContentLoaded", function () {

  // ===================== NOTIFICATION =====================
  function showNotification(msg, type="success") {
    const notif = document.createElement("div");
    notif.className = `notification ${type}`;
    notif.textContent = msg;
    document.body.appendChild(notif);

    // Stay for 4 seconds
    setTimeout(() => {
      // Fade-out transition
      notif.style.transition = "opacity 0.5s ease, transform 0.5s ease";
      notif.style.opacity = "0";
      notif.style.transform = "translateX(50px)";
      setTimeout(() => notif.remove(), 500);
    }, 4000);
  }

  // ===================== DELETE PATIENT =====================
  const deleteBtn = document.getElementById("deletePatient");
  if(deleteBtn){
    deleteBtn.addEventListener("click", () => {
      const patientId = deleteBtn.dataset.id;
      if(!confirm("Are you sure to delete?")) return;

      fetch(`/api/patient/${patientId}`, { method: "DELETE" })
        .then(r => r.json())
        .then(d => {
          if(d.status === "success"){
            showNotification("Patient deleted!", "success");
            setTimeout(() => window.location.href="/adminDashboard", 1500);
          } else {
            showNotification(d.message || "Delete failed", "error");
          }
        })
        .catch(() => showNotification("Server error", "error"));
    });
  }

  // ===================== EDIT PATIENT MODAL =====================
  const editBtn = document.querySelector(".icon.edit");
  const editModal = document.getElementById("editPatientModal");
  const closeEditBtn = document.getElementById("closeEditPatientModal");
  const editForm = document.getElementById("editPatientForm");

  function populateEditForm(){
    const fields = ["name","email","mobile_no","gender","dob","address","occupation","guardianname","guardianphonenumber","guardianrelation","insurance","doctorName","bloodGroup","mr_number"];
    fields.forEach(f => {
      const span = document.getElementById(f+"Span");
      const input = document.getElementById("edit_" + f);
      if(span && input) input.value = span.textContent === "none" ? "" : span.textContent;
    });
  }

  if(editBtn){
    editBtn.addEventListener("click", () => {
      editModal.style.display = "flex";
      populateEditForm();
    });
  }

  closeEditBtn.addEventListener("click", () => editModal.style.display = "none");
  editModal.addEventListener("click", e => { if(e.target === editModal) editModal.style.display = "none"; });

  editForm.addEventListener("submit", function(e){
    e.preventDefault();
    const patientId = deleteBtn.dataset.id;

    const data = {
      name: document.getElementById("edit_name").value,
      email: document.getElementById("edit_email").value,
      mobile_no: document.getElementById("edit_mobile_no").value,
      gender: document.getElementById("edit_gender").value,
      dob: document.getElementById("edit_dob").value,
      address: document.getElementById("edit_address").value,
      occupation: document.getElementById("edit_occupation").value,
      guardianname: document.getElementById("edit_guardianname").value,
      guardianphonenumber: document.getElementById("edit_guardianphonenumber").value,
      guardianrelation: document.getElementById("edit_guardianrelation").value
    };

    fetch(`/api/patient/update/${patientId}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(resp => {
      if(resp.status === "success"){
        Object.keys(data).forEach(f => {
          const span = document.getElementById(f+"Span");
          if(span) span.textContent = data[f] || "none";
        });
        showNotification("Patient updated!", "success");
        editModal.style.display = "none";
      } else {
        showNotification(resp.message || "Update failed", "error");
      }
    })
    .catch(() => showNotification("Server error", "error"));
  });

  // ===================== APPOINTMENT MODAL =====================
  const openBtn = document.getElementById("openApptForm");
  const closeBtn = document.getElementById("closeApptForm");
  const modal = document.getElementById("apptModal");
  const patientIdInput = document.getElementById("patient_id");
  const doctorSelect = document.getElementById("doctor_id");
  const dateInput = document.getElementById("date");
  const slotSelect = document.getElementById("slot");
  const makeAppointmentForm = document.getElementById("makeAppointmentForm");

  function loadAppointments(){
    fetch(`/api/patient/getAppointments/${patientIdInput.value}`)
      .then(r => r.json())
      .then(d => {
        const tableBody = document.querySelector(".appointments-table tbody");
        tableBody.innerHTML = "";
        if(d.status === "success" && d.appointments.length){
          d.appointments.forEach(appt => {
            const row = document.createElement("tr");
            row.innerHTML = `<td>${appt.appointment_date}</td>
                             <td>${new Date(appt.appointment_date).toLocaleDateString('en-US', { weekday:'long'})}</td>
                             <td>${appt.doctor_name}</td>
                             <td>${appt.slot}</td>`;
            tableBody.appendChild(row);
          });
        } else {
          tableBody.innerHTML = `<tr><td colspan="4">No appointments yet</td></tr>`;
        }
      });
  }
  loadAppointments();

  if(openBtn) openBtn.addEventListener("click", () => {
    modal.style.display = "flex";
    fetch(`/api/appointments/form/${patientIdInput.value}`)
      .then(r => r.json())
      .then(d => {
        doctorSelect.innerHTML = `<option value="">Select Doctor</option>`;
        d.doctors?.forEach(doc => {
          const opt = document.createElement("option");
          opt.value = doc.employee_id;
          opt.textContent = doc.name;
          doctorSelect.appendChild(opt);
        });
      });
  });

  if(closeBtn) closeBtn.addEventListener("click", () => modal.style.display = "none");
  modal.addEventListener("click", e => { if(e.target === modal) modal.style.display = "none"; });

  function loadSlots(){
    const doctorId = doctorSelect.value;
    const date = dateInput.value;
    slotSelect.innerHTML = `<option value="">Select Slot</option>`;
    if(!doctorId || !date) return;

    fetch(`/api/appointments/getSlots?doctor_id=${doctorId}&date=${date}`)
      .then(r => r.json())
      .then(d => {
        d.slots?.forEach(slot => {
          const opt = document.createElement("option");
          opt.value = slot.start;
          opt.textContent = slot.display;
          slotSelect.appendChild(opt);
        });
      });
  }

  doctorSelect.addEventListener("change", loadSlots);
  dateInput.addEventListener("change", loadSlots);

  if(makeAppointmentForm){
    makeAppointmentForm.addEventListener("submit", e => {
      e.preventDefault();
      const data = {
        patient_id: patientIdInput.value,
        doctor_id: doctorSelect.value,
        date: dateInput.value,
        slot: slotSelect.value
      };
      if(!data.doctor_id || !data.date || !data.slot){
        showNotification("Select doctor, date & slot", "error");
        return;
      }

      fetch("/api/appointments/save", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      })
      .then(r => r.json())
      .then(resp => {
        if(resp.status === "success"){
          showNotification("Appointment saved!", "success");
          modal.style.display = "none";
          makeAppointmentForm.reset();
          loadAppointments();
        } else {
          showNotification(resp.message || "Failed to save", "error");
        }
      });
    });
  }

});
