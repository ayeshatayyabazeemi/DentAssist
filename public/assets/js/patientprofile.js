// document.addEventListener('DOMContentLoaded', () => {
//   const openBtn  = document.getElementById('openApptForm');
//   const closeBtn = document.getElementById('closeApptForm');
//   const modal    = document.getElementById('apptModal');
//   console.log('llllllllllllllllllllll')

//   if (openBtn && modal) {
//     openBtn.addEventListener('click', () => {
//           console.log('llllllllllllllllllllll')

//       modal.style.display = 'flex';
//     });
//   }

//   if (closeBtn && modal) {
//     closeBtn.addEventListener('click', () => {
//       modal.style.display = 'none';
//     });
//   }

//   // Also clicking outside modal content should close it
//   if (modal) {
//     modal.addEventListener('click', (e) => {
//       if (e.target === modal) {
//         modal.style.display = 'none';
//       }
//     });
//   }
// });
document.addEventListener('DOMContentLoaded', () => {
  // Modal open/close
  const openBtn  = document.getElementById('openApptForm');
  const closeBtn = document.getElementById('closeApptForm');
  const modal    = document.getElementById('apptModal');

  const patientIdInput = document.getElementById('patient_id');
  const doctorSelect   = document.getElementById('doctor_id');
  const dateInput      = document.getElementById('date');
  const slotSelect     = document.getElementById('slot');
  const makeAppointmentForm = document.getElementById('makeAppointmentForm');

// ------------------ LOAD EXISTING APPOINTMENTS ------------------
function loadAppointments() {
  const patientId = patientIdInput.value;
  fetch(`/api/patient/getAppointments/${patientId}`)
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success' && data.appointments) {
        const tableBody = document.querySelector('.appointments-table tbody');
        tableBody.innerHTML = ''; // clear previous rows
        if (data.appointments.length) {
          data.appointments.forEach(appt => {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${appt.appointment_date}</td>
              <td>${new Date(appt.appointment_date).toLocaleDateString('en-US', { weekday: 'long' })}</td>
              <td>${appt.doctor_name}</td>
              <td>${appt.slot}</td>
            `;
            tableBody.appendChild(row);
          });
        } else {
          tableBody.innerHTML = '<tr><td colspan="4">No appointments yet</td></tr>';
        }
      }
    })
    .catch(err => console.error('Error loading appointments:', err));
}

// Call on page load
loadAppointments();



  // ------------------ MODAL OPEN ------------------
  if (openBtn && modal) {
    openBtn.addEventListener('click', () => {
      modal.style.display = 'flex';
      loadDoctors(); // Load doctors dynamically
    });
  }

  // ------------------ MODAL CLOSE ------------------
  if (closeBtn && modal) {
    closeBtn.addEventListener('click', () => modal.style.display = 'none');
  }

  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.style.display = 'none';
    });
  }

  // ------------------ LOAD DOCTORS ------------------
  function loadDoctors() {
    fetch(`/api/appointments/form/${patientIdInput.value}`)
    .then(res => res.json())
    .then(data => {
      doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
      if (data.doctors && data.doctors.length) {
        data.doctors.forEach(doc => {
          const option = document.createElement('option');
          option.value = doc.employee_id;
          option.text  = doc.name;
          doctorSelect.appendChild(option);
        });
      }
    })
    .catch(err => console.error('Error loading doctors:', err));
  }

  // ------------------ LOAD SLOTS ------------------
  // function loadSlots() {
  //   const doctorId = doctorSelect.value;
  //   const date = dateInput.value;

  //   if (!doctorId || !date) {
  //     slotSelect.innerHTML = '<option value="">Select Slot</option>';
  //     return;
  //   }

  //   fetch(`/api/appointments/getSlots?doctor_id=${doctorId}&date=${date}`)
  //   .then(res => res.json())
  //   .then(data => {
  //     slotSelect.innerHTML = '<option value="">Select Slot</option>';
  //     if (data.slots && data.slots.length) {
  //       data.slots.forEach(slot => {
  //         const option = document.createElement('option');
  //         option.value = slot;
  //         option.text  = slot;
  //         slotSelect.appendChild(option);
  //       });
  //     }
  //   })
  //   .catch(err => console.error('Error loading slots:', err));
  // }

  function loadSlots() {
    const doctorId = doctorSelect.value;
    const date     = dateInput.value;

    if (!doctorId || !date) {
        slotSelect.innerHTML = '<option value="">Select Slot</option>';
        return;
    }

    fetch(`/api/appointments/getSlots?doctor_id=${doctorId}&date=${date}`)
    .then(res => res.json())
    .then(data => {
        slotSelect.innerHTML = '<option value="">Select Slot</option>';

        if (data.slots && data.slots.length) {
            data.slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.start;    // store start time in DB
                option.text  = slot.display;  // show range in dropdown
                slotSelect.appendChild(option);
            });
        }
    });
}



  doctorSelect.addEventListener('change', loadSlots);
  dateInput.addEventListener('change', loadSlots);

  // ------------------ SAVE APPOINTMENT ------------------
  if (makeAppointmentForm) {
    makeAppointmentForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const data = {
        patient_id: patientIdInput.value,
        doctor_id: doctorSelect.value,
        date: dateInput.value,
        slot: slotSelect.value
      };

      if (!data.doctor_id || !data.date || !data.slot) {
        showNotification('Please select doctor, date and slot.', 'error');

        return;
      }

      fetch('/api/appointments/save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(res => {
        if (res.status === 'success') {
          showNotification('Appointment saved successfully!', 'success');

          modal.style.display = 'none';
          makeAppointmentForm.reset();

          // Add appointment to table
          const tableBody = document.querySelector('.appointments-table tbody');
          const appt = res.appointment;
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${appt.appointment_date}</td>
            <td>${new Date(appt.appointment_date).toLocaleDateString('en-US', { weekday: 'long' })}</td>
            <td>${appt.doctor_name}</td>
            <td>${appt.slot}</td>
          `;
          tableBody.appendChild(row);
        } else {
          showNotification(res.message || 'Failed to save appointment.', 'error');

        }
      })
      .catch(err => console.error('Error saving appointment:', err));
    });
  }
});


function showNotification(message, type = 'success') {
  const notif = document.getElementById('notification');
  notif.textContent = message;
  notif.className = 'notification ' + (type === 'error' ? 'error' : '');
  notif.classList.add('show');

  // Auto hide after 3 seconds
  setTimeout(() => notif.classList.remove('show'), 3000);
}
