// assets/js/admin.js
document.addEventListener('DOMContentLoaded', () => {
  // Tab switching logic
  const tabButtons = document.querySelectorAll('header nav ul li button[data-tab]');
  const tabSections = document.querySelectorAll('.tab-content');

  function showTab(tabName) {
    // Hide all sections
    tabSections.forEach(section => {
      section.style.display = 'none';
    });
    // Remove active from all buttons
    tabButtons.forEach(btn => {
      btn.classList.remove('active');
    });
    // Show the target section
    const targetSec = document.getElementById(`tab-${tabName}`);
    if (targetSec) {
      targetSec.style.display = 'block';
    }
    // Set the clicked button as active
    const clickedBtn = document.querySelector(`button[data-tab="${tabName}"]`);
    if (clickedBtn) {
      clickedBtn.classList.add('active');
    }
  }

  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.getAttribute('data-tab');
      showTab(target);
    });
  });

  // Initialize - show default tab (patients)
  showTab('patients');

  // Form submission logic for Add Patient form
  const patientForm = document.getElementById('patientForm');
    const notyf = new Notyf({
  duration: 3000,
  position: { x: 'center', y: 'top' }
});
  if (patientForm) {
    patientForm.addEventListener('submit', (e) => {
      e.preventDefault();
      // Example: gather form data
      const formData = new FormData(patientForm);
      // inside patientForm submit listener
const dataObj = {};
new FormData(patientForm).forEach((value, key) => {
  dataObj[key] = value;
});
const cnicRegex    = /^\d{13}$/;
const phoneRegex   = /^0\d{10}$/; // starts with 0, total 11 digits

if (dataObj.cnic) {
  if (!cnicRegex.test(dataObj.cnic)) {
    notyf.error('CNIC must be exactly 13 digits and numeric (no “-”).');
    return;
  }
}

if (dataObj.mobile_no) {
  if (!phoneRegex.test(dataObj.mobile_no)) {
    notyf.error('Mobile number must be 11 digits and start with 0.');
    return;
  }
}

// Guardian phone is optional
if (dataObj.guardianphonenumber) {
  if (!phoneRegex.test(dataObj.guardianphonenumber)) {
    notyf.error('Guardian phone must be 11 digits and start with 0.');
    return;
  }
}



fetch('/api/patient/add', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(dataObj)
})
.then(res => res.json())
.then(json => {
  if (json.status === 'success') {
        notyf.success('Patient added successfully! patient id: '
 + json.id);
    // reset form & update count etc
    patientForm.reset();
    const totalCountElem = document.getElementById('totalCount');
    if (totalCountElem) {
      totalCountElem.textContent = (parseInt(totalCountElem.textContent) || 0) + 1;
    }
  } else {
    notyf.error('Error: ' + (json.message || 'Something went wrong.'));
  }
})
.catch(err => {
  console.error('Fetch error:', err);
  notyf.error('Network error. Please try again.');
});

      console.log('Submitting patient data:', dataObj);
      

      // Reset form and optionally update total count
     
      // Example update of Total Patients count if you track it:
     
    });
  }
  
function setupSearch({ inputId, dropdownId, apiUrl, onSelect }) {
  const searchInput = document.getElementById(inputId);
  const dropdown = document.getElementById(dropdownId);
  if (!searchInput || !dropdown) return;

  let debounceTimer;

  searchInput.addEventListener('input', (e) => {
    const query = e.target.value.trim().toLowerCase();
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      fetch(`${apiUrl}?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(json => {
          if (json.status === 'success' && json.data.length > 0) {
            showDropdown(json.data, dropdown, onSelect);
          } else {
            dropdown.style.display = 'none';
          }
        })
        .catch(err => console.error(err));
    }, 300);
  });

  // hide dropdown on outside click
  document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });
}

function showDropdown(data, dropdown, onSelect) {
  dropdown.innerHTML = '';
  if (!data || !data.length) return;

  data.forEach((item) => {
  const div = document.createElement('div');
  div.classList.add('autocomplete-item');
  div.innerHTML = `
    <strong>ID:</strong> ${item.id ?? 'none'} |
    <strong>Name:</strong> ${item.name ?? 'none'} |
    <strong>Phone:</strong> ${item.mobile_no ?? 'none'} |
    <strong>Email:</strong> ${item.email ?? 'none'} |
    <strong>CNIC:</strong> ${item.cnic ?? 'none'}
  `;
  div.addEventListener('mousedown', () => {
    window.location.href = `/patient/profile/${item.id}`;
  });
  dropdown.appendChild(div);
});
dropdown.style.display = 'block';
}
// --- Initialize for both ---

// Patient search
setupSearch({
  inputId: 'patientSearch',
  dropdownId: 'searchResults',
  apiUrl: '/api/patient/search',
  onSelect: (item) => window.location.href = `/patient/profile/${item.id}`
});

// Employee search




const form = document.getElementById('employeeForm');
  const scheduleSection = document.getElementById('scheduleSection');
  const designation = document.getElementById('emp_designation');
setupSearch({
  inputId: 'employeeSearch',
  dropdownId: 'employeeResults',
  apiUrl: '/api/employee/search',
  onSelect: (item) => window.location.href = `/employee/profile/${item.id}`
});
  // Show schedule section only if designation is doctor
  designation.addEventListener('change', () => {
    if (designation.value === 'doctor') {
      scheduleSection.style.display = 'block';
    } else {
      scheduleSection.style.display = 'none';
    }
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    let valid = true;
    const cnicRegex = /^\d{13}$/;
    const phoneRegex = /^0\d{10}$/;

    const name   = form.elements['name'].value.trim();
    const mobile = form.elements['mobile_no'].value.trim();
    const email  = form.elements['email'].value.trim();
    const cnic   = form.elements['cnic'].value.trim();
    const gender = form.elements['gender'].value;
    const pwd    = form.elements['password'].value;
    const cpwd   = form.elements['c_password'].value;
    const desig  = form.elements['designation'].value;

    // --- Validation ---
    if (!name) {
      notyf.error('Name is required');
      valid = false;
    }

    if (!phoneRegex.test(mobile)) {
      notyf.error('Mobile number must be 11 digits and start with 0');
      valid = false;
    }

    if (email && !/\S+@\S+\.\S+/.test(email)) {
      notyf.error('Please enter a valid email');
      valid = false;
    }

    if (!gender) {
      notyf.error('Please select gender');
      valid = false;
    }

    if (cnic && !cnicRegex.test(cnic)) {
      notyf.error('CNIC must be 13 digits (no “-”)');
      valid = false;
    }

    if (pwd.length < 6) {
      notyf.error('Password must be at least 6 characters');
      valid = false;
    }

    if (pwd !== cpwd) {
      notyf.error('Passwords do not match');
      valid = false;
    }

    if (!desig) {
      notyf.error('Please select a designation');
      valid = false;
    }

    // 🕐 Doctor schedule validation & collection
    let scheduleData = [];
    if (desig === 'doctor') {
      const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      days.forEach(day => {
        const start = form.elements[`start_time[${day}]`]?.value;
        const end = form.elements[`end_time[${day}]`]?.value;
        if ((start && !end) || (!start && end)) {
          notyf.error(`For ${day}, both start and end times must be filled or empty`);
          valid = false;
        }
        if (start && end) {
          scheduleData.push({ day, start_time: start, end_time: end });
        }
      });
    }

    if (!valid) return;

    // 🧾 Prepare payload
    const dataObj = {
      name,
      mobile_no: mobile,
      email,
      cnic,
      gender,
      password: pwd,
      designation: desig,
      schedule: scheduleData
    };

    // 🚀 Send API Request
    fetch('/api/employee/add', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dataObj)
    })
    .then(res => res.json())
    .then(json => {
      if (json.status === 'success') {
        notyf.success('Employee added successfully! ID: ' + json.id);
        form.reset();
        scheduleSection.style.display = 'none';

        const totalCountElem = document.getElementById('totalCount');
        if (totalCountElem) {
          totalCountElem.textContent = (parseInt(totalCountElem.textContent) || 0) + 1;
        }
      } else {
        notyf.error('Error: ' + (json.message || 'Something went wrong.'));
      }
    })
    .catch(err => {
      console.error('Fetch error:', err);
      notyf.error('Network error. Please try again.');
    });
  
  });
  


});
