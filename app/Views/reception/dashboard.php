<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reception Dashboard - DentAssist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
  <link rel="stylesheet" href="assets/css/admin.css"> <!-- your admin form CSS -->

  <link rel="stylesheet" href="assets/css/receptionistDashboard.css">
</head>
<body>

<header class="main-header">
  <div class="logo-btn" id="logo">Reception Dashboard</div>
  <!-- <button id="reception-btn-patients" class="patient-btn">Patients</button> -->
   <nav>
    <ul class="nav-list">
      <li><button id="reception-btn-patients" class="patient-btn">Patients</button></li>
      
    </ul>
   <form id="logoutForm" action="<?= base_url('logout') ?>" method="post">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
</nav>
</header>

<div class="content-wrapper">

  <!-- Chart Section -->
  <section id="tab-dashboard" class="tab-content active">
    <h2>Today's Appointments</h2>

    <!-- Table Card -->
    <div class="appointments-card">
      <div class="appointments-card-header"><span>Appointments</span></div>
      <div class="appointments-card-body">
        <table class="appointments-table">
          <thead>
            <tr>
              <th>Appointment ID</th>
              <th>Patient MR Number</th>
               <th>Patient Name</th>
              <th>Doctor</th>
              <th>Appointment Date</th>
              <th>Slot</th>
              <th>Status</th>
              <!-- <th>Status Updated At</th> -->
            </tr>
          </thead>
          <tbody id="appointments-body"></tbody>
        </table>
      </div>
    </div>
  </section>



  <!-- Patients Tab Content (hidden by default) -->
<section id="reception-tab-patients" class="tab-content" style="display: none;">

  <!-- Search Row -->
  <div class="top-row">
    <div class="search-bar">
      <input 
        type="text"
        id="patientSearch"
        placeholder="Search patients..." 
      />
      <div 
        id="searchResults"
        class="autocomplete-items">
      </div>
    </div>
  </div>

  <!-- Patient Registration Form -->
  <div class="form-section">
    <h2>Add Patient</h2>

    <form id="reception-patientForm">
      <div class="form-grid">

        <div class="form-group">
          <label for="reception-name" class="required-label">Name</label>
          <input type="text" id="reception-name" name="name" required />
        </div>

        <div class="form-group">
          <label for="reception-email">Email</label>
          <input type="email" id="reception-email" name="email" />
        </div>

        <div class="form-group">
          <label for="reception-mobile_no" class="required-label">Mobile No</label>
          <input type="text" id="reception-mobile_no" name="mobile_no" required />
        </div>

        <div class="form-group">
          <label for="reception-gender" class="required-label">Gender</label>
          <select id="reception-gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </div>

        <div class="form-group">
          <label for="reception-dob">Date of Birth</label>
          <input type="date" id="reception-dob" name="dob" />
        </div>

        <div class="form-group">
          <label for="reception-cnic">CNIC (13 digits)</label>
          <input type="text" id="reception-cnic" name="cnic" />
        </div>

        <div class="form-group">
          <label for="reception-bloodGroup">Blood Group</label>
          <select id="reception-bloodGroup" name="bloodGroup">
            <option value="">Select Blood Group</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
          </select>
        </div>

        <div class="form-group address-field">
          <label for="reception-address">Address</label>
          <textarea id="reception-address" name="address" rows="2"></textarea>
        </div>

        <div class="form-group">
          <label for="reception-regdate">Registration Date</label>
          <input type="date" id="reception-regdate" name="regdate" />
        </div>

        <div class="form-group">
          <label for="reception-guardianname">Guardian Name</label>
          <input type="text" id="reception-guardianname" name="guardianname" />
        </div>

        <div class="form-group">
          <label for="reception-guardianphonenumber">Guardian Phone</label>
          <input type="text" id="reception-guardianphonenumber" name="guardianphonenumber" />
        </div>

        <div class="form-group">
          <label for="reception-insurance" class="required-label">Insurance</label>
          <select id="reception-insurance" name="insurance" required>
            <option value="">Select Insurance</option>
            <option value="GEN">General</option>
            <option value="NICL">NICL</option>
            <option value="PBC">PBC</option>
            <option value="KE">KE</option>
            <option value="LCDC">LCDC</option>
            <option value="OGDC">OGDC</option>
            <option value="PCSIR">PCSIR</option>
            <option value="PMTF">PMTF</option>
            <option value="NESPAK">NESPAK</option>
            <option value="SSGC">SSGC</option>
            <option value="SIEMENS">SIEMENS</option>
            <option value="EFU">EFU</option>
            <option value="PPL">PPL</option>
            <option value="SBP">SBP</option>
            <option value="ABBOTT LAB">ABBOTT LAB</option>
            <option value="PTV">PTV</option>
            <option value="KDA">KDA</option>
            <option value="NHA">NHA</option>
            <option value="KWSC">KWSC</option>
          </select>
        </div>

      </div>

      <button type="submit" class="submit-btn">Add Patient</button>
    </form>
  </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<!-- JS -->

<script src="assets/js/receptionistDashboard.js"></script>

<script>
  // Return to dashboard on logo click
  document.getElementById('logo').addEventListener('click', () => {
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.style.display = 'none';
    });

    document.getElementById('tab-dashboard').style.display = 'block';
  });

  // Receptionist Patients tab
  document.getElementById('reception-btn-patients').addEventListener('click', () => {
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.style.display = 'none';
    });

    document.getElementById('reception-tab-patients').style.display = 'block';
  });
</script>

<script src="assets/js/admin.js"></script>


</body>
</html>
