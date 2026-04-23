document.addEventListener("DOMContentLoaded", function () {

  // =========================
  // TABS & DASHBOARD LOGIC
  // =========================
//   const tabButtons = document.querySelectorAll('header nav ul li button[data-tab]');
//   const tabSections = document.querySelectorAll('.tab-content');
//   const dashboardLogo = document.getElementById('dashboardLogo');

//   function showTab(tabName) {
//     tabSections.forEach(section => section.style.display = 'none');
//     tabButtons.forEach(btn => btn.classList.remove('active'));

//     const targetSec = document.getElementById(`tab-${tabName}`);
//     if (targetSec) targetSec.style.display = 'block';

//     const clickedBtn = document.querySelector(`button[data-tab="${tabName}"]`);
//     if (clickedBtn) clickedBtn.classList.add('active');
//   }

//   tabButtons.forEach(btn => {
//     btn.addEventListener('click', () => showTab(btn.getAttribute('data-tab')));
//   });

//   if (dashboardLogo) {
//     dashboardLogo.addEventListener('click', () => showTab('dashboard'));
//   }

//   const logoutBtn = document.getElementById('logoutBtn');
//   if (logoutBtn) logoutBtn.addEventListener('click', () => window.location.href = '/admin/logout');

  
//   showTab('dashboard');

  
  // =========================
  // todays appoitment
  // =========================
 
// Utility function to create a status badge
function createStatusSelect(currentStatus, appointmentId) {
  const select = document.createElement('select');
  select.classList.add('status-select');

  const statuses = [
    { value: 'scheduled', label: 'Scheduled' },
    { value: 'checked_in', label: 'Checked In' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'completed', label: 'Completed' },
    { value: 'no_show', label: 'No Show' },
    { value: 'cancelled', label: 'Cancelled' },
  ];

  statuses.forEach(s => {
    const option = document.createElement('option');
    option.value = s.value;
    option.textContent = s.label;
    if (s.value === currentStatus) option.selected = true;
    select.appendChild(option);
  });

  // Apply color initially
  applyStatusStyle(select, currentStatus);

  // Change handler
  select.addEventListener('change', async () => {
    const newStatus = select.value;
    applyStatusStyle(select, newStatus);

    try {
      await fetch(`/api/appointments/update-status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          appointment_id: appointmentId,
          status: newStatus
        })
      });
    } catch (err) {
      console.error('Failed to update status', err);
    }
  });

  return select;
}
function applyStatusStyle(select, status) {
  const styles = {
    scheduled:   { bg: '#E5E7EB', color: '#374151' },
    checked_in:  { bg: '#BFDBFE', color: '#1E40AF' },
    in_progress: { bg: '#FED7AA', color: '#C2410C' },
    completed:   { bg: '#BBF7D0', color: '#166534' },
    no_show:     { bg: '#FECACA', color: '#991B1B' },
    cancelled:  { bg: '#CBD5E1', color: '#334155' },
  };

  const style = styles[status] || { bg: '#D1D5DB', color: '#111' };
  select.style.background = style.bg;
  select.style.color = style.color;
}


async function fetchAppointments() {
  try {
    const response = await fetch('/api/appointments/today');
    const appointments = await response.json();

    const tbody = document.getElementById('appointments-body');
    tbody.innerHTML = ''; // Clear existing rows

   appointments.forEach((apt) => {
  const tr = document.createElement('tr');

  // Add row class based on status
  tr.classList.add(`status-${apt.status.replace(/ /g, '-').toLowerCase()}`);

  // Appointment ID
  const tdID = document.createElement('td');
  tdID.textContent = apt.appointment_id;
  tr.appendChild(tdID);

  // Patient MR Number
  const tdMR = document.createElement('td');
  tdMR.textContent = apt.mr_number;
  tr.appendChild(tdMR);

  // Patient Name
  const tdPatientName = document.createElement('td');
  tdPatientName.textContent = apt.patient_name || '-';
  tr.appendChild(tdPatientName);

  // Doctor Name
  const tdDoc = document.createElement('td');
  tdDoc.textContent = apt.doctor_name || '-';
  tr.appendChild(tdDoc);

  // Appointment Date
  const tdDate = document.createElement('td');
  tdDate.textContent = apt.appointment_date;
  tr.appendChild(tdDate);

  // Slot
  const tdSlot = document.createElement('td');
  tdSlot.textContent = apt.slot || '-';
  tr.appendChild(tdSlot);

  // Status
  const tdStatus = document.createElement('td');
 const statusSelect = createStatusSelect(
  apt.status,
  apt.appointment_id
);
tdStatus.appendChild(statusSelect);

  tr.appendChild(tdStatus);

  // 🧾 ACTION COLUMN
const tdAction = document.createElement('td');

if (apt.status === 'completed') {
  const btn = document.createElement('button');
  btn.textContent = "Generate Invoice";
  btn.classList.add('invoice-btn');

  btn.onclick = () => {
    window.open(`/invoice/${apt.patient_id}/${apt.appointment_id}`, '_blank');
  };

  tdAction.appendChild(btn);
} else {
  tdAction.textContent = '-';
}

tr.appendChild(tdAction);
// Status Updated At
// const tdUpdatedAt = document.createElement('td');

// if (apt.status_updated_at) {
//   const date = new Date(apt.status_updated_at);

//   // Show only time (HH:MM AM/PM)
//   tdUpdatedAt.textContent = date.toLocaleTimeString([], {
//     hour: '2-digit',
//     minute: '2-digit'
//   });
// } else {
//   tdUpdatedAt.textContent = '-';
// }

// tr.appendChild(tdUpdatedAt);

  tbody.appendChild(tr);
});

  } catch (err) {
    console.error('Error fetching appointments:', err);
  }
}

fetchAppointments();
setInterval(fetchAppointments, 30000);


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

const patientForm =
  document.getElementById('patientForm') ||
  document.getElementById('reception-patientForm');

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

    // Do NOT send mr_number
    delete dataObj.mr_number;

    // VALIDATION
    const cnicRegex = /^\d{13}$/;
    const phoneRegex = /^0\d{10}$/;

    if (dataObj.cnic && !cnicRegex.test(dataObj.cnic)) {
      notyf.error('CNIC must be 13 digits');
      unlock(); return;
    }
    if (dataObj.mobile_no && !phoneRegex.test(dataObj.mobile_no)) {
      notyf.error('Mobile must be 11 digits');
      unlock(); return;
    }
    if (dataObj.guardianphonenumber && !phoneRegex.test(dataObj.guardianphonenumber)) {
      notyf.error('Guardian phone invalid');
      unlock(); return;
    }

    // NORMALIZATION
    if (dataObj.mobile_no) dataObj.mobile_no = dataObj.mobile_no.replace(/^0/, "");

    if (!dataObj.regdate || dataObj.regdate.trim() === "") {
      const today = new Date();
      dataObj.regdate = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
    }

    // SUBMIT
    fetch('/api/patient/add', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dataObj)
    })
    .then(res => res.json())
    .then(json => {
      if (json.status === 'success') {
        showMRPopup(json.mr_number);
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
// SEARCH AUTOCOMPLETE (RECEPTIONIST)
// =========================
// function setupSearch({ inputId, dropdownId, apiUrl, onSelect }) {
//   const searchInput = document.getElementById(inputId);
//   const dropdown = document.getElementById(dropdownId);

//   if (!searchInput || !dropdown) {
//     console.warn('Search elements not found:', inputId, dropdownId);
//     return;
//   }

//   let debounceTimer;

//   searchInput.addEventListener('input', e => {
//     const query = e.target.value.trim();

//     // Hide dropdown if empty
//     if (query.length < 2) {
//       dropdown.style.display = 'none';
//       return;
//     }

//     clearTimeout(debounceTimer);
//     debounceTimer = setTimeout(() => {
//       fetch(`${apiUrl}?q=${encodeURIComponent(query)}`)
//         .then(res => res.json())
//         .then(data => {
//           // API returns ARRAY, not {status,data}
//           if (Array.isArray(data) && data.length > 0) {
//             showDropdown(data, dropdown, onSelect);
//           } else {
//             dropdown.style.display = 'none';
//           }
//         })
//         .catch(err => {
//           console.error('Search error:', err);
//           dropdown.style.display = 'none';
//         });
//     }, 300);
//   });

//   // Close dropdown on outside click
//  document.addEventListener('mousedown', e => {
//   if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
//     dropdown.style.display = 'none';
//   }
// });

// }

// function showDropdown(data, dropdown, onSelect) {
//   dropdown.innerHTML = '';

//   data.forEach(item => {
//     const div = document.createElement('div');
//     div.classList.add('autocomplete-item');

//     div.innerHTML = `
//       <strong>MR:</strong> ${item.mr_number ?? '-'} |
//       <strong>Name:</strong> ${item.name ?? '-'} |
//       <strong>Phone:</strong> ${item.mobile_no ?? '-'}
//     `;

//     div.addEventListener('mousedown', () => onSelect(item));
//     dropdown.appendChild(div);
//   });

//   dropdown.style.display = 'block';
// }

// // =========================
// // INIT PATIENT SEARCH
// // =========================
// setupSearch({
//   inputId: 'reception-patientSearch',
//   dropdownId: 'reception-searchResults',
//   apiUrl: '/api/patient/search',
//   onSelect: item => {
//     const patientId = item.patient_id;

//     if (!patientId) {
//       console.error('Patient ID missing', item);
//       return;
//     }

//     // Receptionist: view-only profile
//     window.location.href = `/patient/profile/${patientId}`;
//   }
// });
}); 