document.addEventListener("DOMContentLoaded", function () {

  /* ------------------------------------------------------------------
     DELETE + EDIT PATIENT SECTION
  ------------------------------------------------------------------ */

  const deleteBtn = document.getElementById("deletePatient");
  const editBtn = document.querySelector(".icon.edit");

  const nonEditable = ["regdate", "bloodGroup"];
  const allFields = [
    "name", "mobile_no", "email", "gender", "dob", "address",
    "occupation", "guardianname", "guardianphonenumber",
    "guardianrelation", "insurance", "doctorName", "cnic"
  ];

  const editableFields = allFields.filter(f => !nonEditable.includes(f));

  // Notification
  function showNotification(message, type = "success") {
    const notif = document.createElement("div");
    notif.className = `notification ${type}`;
    notif.textContent = message;
    document.body.appendChild(notif);

    setTimeout(() => notif.remove(), 3000);
  }

  /* --------------------- DELETE PATIENT --------------------- */
  if (deleteBtn) {
    deleteBtn.addEventListener("click", function () {
      const patientId = this.dataset.id;

      if (!confirm("Are you sure you want to delete this patient?"))
        return;

      fetch(`/api/patient/${patientId}`, { method: "DELETE" })
        .then(res => res.json())
        .then(data => {
          if (data.status === "success") {
            showNotification("Patient deleted!", "success");
            setTimeout(() => window.location.href = "/adminDashboard", 1500);
          } else {
            showNotification(data.message || "Delete failed", "error");
          }
        })
        .catch(() => showNotification("Server error", "error"));
    });
  }

  /* --------------------- EDIT PATIENT --------------------- */
  if (editBtn) {
    editBtn.addEventListener("click", function () {
      editableFields.forEach(field => {
        const span = document.getElementById(field + "Span");
        if (!span) return;

        let input;
        if (field === "gender" || field === "guardianrelation") {
          input = document.createElement("select");
          const options =
            field === "gender"
              ? ["", "male", "female", "other"]
              : ["", "Parent", "Spouse", "Brother", "Sister", "Son", "Daughter", "Other"];

          options.forEach(optVal => {
            const opt = document.createElement("option");
            opt.value = optVal;
            opt.textContent = optVal;
            if (span.textContent === optVal) opt.selected = true;
            input.appendChild(opt);
          });

        } else if (field === "dob") {
          input = document.createElement("input");
          input.type = "date";
          input.value = span.textContent === "none" ? "" : span.textContent;

        } else {
          input = document.createElement("input");
          input.type = "text";
          input.value = span.textContent === "none" ? "" : span.textContent;
        }

        input.id = field + "Input";
        span.replaceWith(input);
      });

      // Add Save Button
      let saveBtn = document.getElementById("savePatientBtn");
      if (!saveBtn) {
        saveBtn = document.createElement("button");
        saveBtn.id = "savePatientBtn";
        saveBtn.textContent = "Save";
        saveBtn.className = "submit-btn";
        document.querySelector(".form-section").appendChild(saveBtn);
      }

      // Save Logic
      saveBtn.onclick = function () {
        const patientId = deleteBtn.dataset.id;
        const data = {};

        editableFields.forEach(field => {
          const input = document.getElementById(field + "Input");
          if (input) data[field] = input.value;
        });

        fetch(`/api/patient/update/${patientId}`, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data)
        })
          .then(res => res.json())
          .then(resp => {
            if (resp.status === "success") {
              showNotification("Patient updated!", "success");

              editableFields.forEach(field => {
                const input = document.getElementById(field + "Input");
                if (!input) return;

                const span = document.createElement("span");
                span.id = field + "Span";
                span.textContent = data[field] || "none";
                input.replaceWith(span);
              });

              saveBtn.remove();
            } else {
              showNotification(resp.message || "Update failed", "error");
            }
          })
          .catch(() => showNotification("Server error", "error"));
      };
    });
  }

  /* ------------------------------------------------------------------
     APPOINTMENT MODAL SECTION
  ------------------------------------------------------------------ */

  const openBtn = document.getElementById("openApptForm");
  const closeBtn = document.getElementById("closeApptForm");
  const modal = document.getElementById("apptModal");

  const patientIdInput = document.getElementById("patient_id");
  const doctorSelect = document.getElementById("doctor_id");
  const dateInput = document.getElementById("date");
  const slotSelect = document.getElementById("slot");
  const makeAppointmentForm = document.getElementById("makeAppointmentForm");

  /* ------------------ LOAD APPOINTMENTS LIST ------------------ */
  function loadAppointments() {
    const patientId = patientIdInput.value;

    fetch(`/api/patient/getAppointments/${patientId}`)
      .then(res => res.json())
      .then(data => {
        const tableBody = document.querySelector(".appointments-table tbody");
        tableBody.innerHTML = "";

        if (data.status === "success" && data.appointments.length) {
          data.appointments.forEach(appt => {
            const row = document.createElement("tr");
            row.innerHTML = `
              <td>${appt.appointment_date}</td>
              <td>${new Date(appt.appointment_date).toLocaleDateString('en-US',{ weekday:'long'})}</td>
              <td>${appt.doctor_name}</td>
              <td>${appt.slot}</td>
            `;
            tableBody.appendChild(row);
          });
        } else {
          tableBody.innerHTML = `<tr><td colspan="4">No appointments yet</td></tr>`;
        }
      });
  }
  loadAppointments(); // load on page open

  /* ------------------ OPEN MODAL ------------------ */
  if (openBtn) {
    openBtn.addEventListener("click", () => {
      modal.style.display = "flex";
      loadDoctors();
    });
  }

  /* ------------------ CLOSE MODAL ------------------ */
  if (closeBtn) closeBtn.addEventListener("click", () => modal.style.display = "none");
  if (modal) {
    modal.addEventListener("click", e => {
      if (e.target === modal) modal.style.display = "none";
    });
  }

  /* ------------------ LOAD DOCTORS ------------------ */
  function loadDoctors() {
    fetch(`/api/appointments/form/${patientIdInput.value}`)
      .then(res => res.json())
      .then(data => {
        doctorSelect.innerHTML = `<option value="">Select Doctor</option>`;
        data.doctors?.forEach(doc => {
          const opt = document.createElement("option");
          opt.value = doc.employee_id;
          opt.textContent = doc.name;
          doctorSelect.appendChild(opt);
        });
      });
  }

  /* ------------------ LOAD SLOTS (30min format) ------------------ */
  function loadSlots() {
    const doctorId = doctorSelect.value;
    const date = dateInput.value;

    if (!doctorId || !date) {
      slotSelect.innerHTML = `<option value="">Select Slot</option>`;
      return;
    }

    fetch(`/api/appointments/getSlots?doctor_id=${doctorId}&date=${date}`)
      .then(res => res.json())
      .then(data => {
        slotSelect.innerHTML = `<option value="">Select Slot</option>`;
        data.slots?.forEach(slot => {
          const opt = document.createElement("option");
          opt.value = slot.start;
          opt.textContent = slot.display;
          slotSelect.appendChild(opt);
        });
      });
  }

  doctorSelect.addEventListener("change", loadSlots);
  dateInput.addEventListener("change", loadSlots);

  /* ------------------ SAVE APPOINTMENT ------------------ */
  if (makeAppointmentForm) {
    makeAppointmentForm.addEventListener("submit", e => {
      e.preventDefault();

      const data = {
        patient_id: patientIdInput.value,
        doctor_id: doctorSelect.value,
        date: dateInput.value,
        slot: slotSelect.value
      };

      if (!data.doctor_id || !data.date || !data.slot) {
        showNotification("Please select doctor, date & slot", "error");
        return;
      }

      fetch("/api/appointments/save", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      })
        .then(res => res.json())
        .then(res => {
          if (res.status === "success") {
            showNotification("Appointment saved!", "success");
            modal.style.display = "none";
            makeAppointmentForm.reset();
            loadAppointments();
          } else {
            showNotification(res.message || "Failed to save", "error");
          }
        });
    });
  }

});