<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/admin.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/images/mylogo.png" />

  <!-- Navbar & Buttons Style -->
  <style>
    /* Navbar links and buttons (Attendance, Lab Order, AI Assistant, Logout) */
    nav a,
    nav button {
      background-color: #ffb6c1; /* light pink */
      color: #fff;
      padding: 8px 14px;
      border-radius: 6px;
      text-decoration: none;
      border: none;
      font-size: 14px;
      margin-right: 10px;
      cursor: pointer;
      transition: background 0.3s;
    }

    /* Hover effect for all navbar buttons */
    nav a:hover,
    nav button:hover {
      background-color: #ff99aa; /* slightly darker pink */
    }

    /* Tab buttons (Patients, Employees) */
    .tabbtn {
      background-color: #ffb6c1;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 8px 14px;
      font-size: 14px;
      cursor: pointer;
      transition: background 0.3s;
    }

    /* Hover for tab buttons */
    .tabbtn:hover {
      background-color: #ff99aa;
    }

    /* Active tab button */
    .tabbtn.active {
      background-color: #ff85a2;
    }

    /* Logout button */
    .logout-btn {
      background-color: #ffb6c1;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .logout-btn:hover {
      background-color: #ff99aa;
    }
  </style>

</head>

<body>

<header>
  <div class="logo-btn" id="dashboardLogo">Admin Dashboard</div>

  <nav>

    <ul class="nav-list">
      <li><button class="tabbtn" data-tab="patients">Patients</button></li>
      <li><button class="tabbtn" data-tab="staffs">Employees</button></li>
    </ul>

    <!-- Employee Attendance Button -->
    <a href="<?= base_url('attendance') ?>">
      <i class="fa fa-calendar-check-o"></i> Attendance
    </a>

    <!-- Lab Order Button -->
    <a href="<?= base_url('laborders/create') ?>">
      <i class="fa fa-flask"></i> Lab Order
    </a>

    <!-- AI Assistant Button -->
    <a href="<?= base_url('ai-assistant') ?>">
      <i class="fa fa-robot"></i> Dentistry Assistant
    </a>

    <form id="logoutForm" action="<?= base_url('logout') ?>" method="post">
      <button type="submit" class="logout-btn">Logout</button>
    </form>

  </nav>
</header>

<main class="main-content">

  <!-- Dashboard Tab -->
  <section id="tab-dashboard" class="tab-content">

    <div class="kpi-container">

      <div class="kpi-card border-primary">
        <h3>Total Patients (All-Time)</h3>
        <p class="value"><?= esc(number_format($totalPatients ?? 0)) ?></p>
      </div>

      <div class="kpi-card border-secondary">
        <h3>Registrations in Last 12 Months</h3>
        <p class="value"><?= esc(number_format($last12 ?? 0)) ?></p>
      </div>

      <div class="kpi-card border-warm">
        <h3>Avg. Monthly Reg.</h3>
        <?php $avgMonthly = round($last12 / 12); ?>
        <p class="value"><?= esc(number_format($avgMonthly ?? 0, 1)) ?></p>
      </div>

      <div class="kpi-card border-success">
        <h3>Growth YoY</h3>
        <p class="value"><?= $growthPercent !== null ? esc($growthPercent) . '%' : 'N/A' ?></p>
      </div>

    </div>
<div class="chart-container">
  <h2>
    <?php echo (ENVIRONMENT === 'development') 
        ? "Revenue Prediction vs Actual (Development Mode)" 
        : "Registration Trend (Last 24 Months)"; ?>
  </h2>

  <?php if (ENVIRONMENT === 'production'): ?>
    <!-- Production: full-width chart -->
    <div class="chart-wrapper" style="height: 350px; width: 100%;">
      <canvas id="regChart"></canvas>
    </div>

  <?php else: ?>
    <!-- Development: chart left, recommendations right (50/50) -->
    <div style="display: flex; gap: 20px; height: 350px;">
      <div style="flex: 1;">
        <canvas id="predictionChart"></canvas>
      </div>
      <div id="forecastRecommendations" style="flex: 1; font-size: 14px; color: #333;">
        <!-- JS will populate recommendations here -->
      </div>

    </div>
  <?php endif; ?>
</div>

    
</div>
        </div>
<?php if (ENVIRONMENT === 'development'): ?>
<div class="chart-container chart-container-pie">

  <h2>Procedure Revenue Share</h2>

  <div class="chart-container-pie-row">

      <div class="chart-wrapper-pie">
          <h3>Overall Procedure Revenue Share</h3>
          <canvas id="pieChart"></canvas>
      </div>

      <div class="chart-wrapper-pie">
          <h3>Predicted Procedure Share (June 2026)</h3>
          <canvas id="predictedPieChart"></canvas>
      </div>

  </div>

</div>
<div class="page-insight-container-pie">
    <div class="insight-boxs-pie" ></div>
</div>
<?php endif; ?>
<?php if (ENVIRONMENT === 'development'): ?>
<div class="chart-container chart-container-bar">
  <h2>Patient Revenue Contribution (Pareto Analysis)</h2>
<canvas id="barChart" ></canvas>

</div>


 <?php endif; ?>

 <?php if (ENVIRONMENT === 'development'): ?>
<div class="page-insight-container">
    <div class="insight-boxs" id="paretoInsight"></div>
</div>
<?php endif; ?>




<!-- Modal backdrop -->
<div id="paretoModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
  
  <!-- Modal content -->
  <div style="background:#fff; padding:20px; border-radius:10px; width:90%; max-width:600px; max-height:80%; overflow:auto; position:relative;">
    
    <!-- Heading + Cross -->
    <div style="display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:#fff; z-index:10; padding-bottom:10px; border-bottom:1px solid #ddd;">
      <h3 style="margin:0;">Loyal Patients List</h3>
      <span id="paretoModalClose" style="cursor:pointer; font-size:20px; font-weight:bold;">&times;</span>
    </div>

    <table style="width:100%; border-collapse:collapse; margin-top:10px;">
      <thead>
        <tr>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #ccc;">Name</th>
          <th style="text-align:right; padding:8px; border-bottom:1px solid #ccc;">Revenue</th>
          <th style="text-align:right; padding:8px; border-bottom:1px solid #ccc;">Visits</th>
        </tr>
      </thead>
      <tbody id="paretoPatientTable">
        <!-- Rows added dynamically -->
      </tbody>
    </table>
  </div>
</div>




       
  </section>

  <!-- Patients Tab -->
  <section id="tab-patients" class="tab-content" style="display: none;">

    <div class="top-row">
      <div class="search-bar">
        <input type="text" id="patientSearch" placeholder="Search patients..." />
        <div id="searchResults" class="autocomplete-items"></div>
      </div>
    </div>

    <div class="form-section">

      <h2>Add Patient</h2>

      <form id="patientForm">

        <div class="form-grid">

          <div class="form-group">
            <label for="name" class="required-label">Name</label>
            <input type="text" id="name" name="name" required />
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" />
          </div>

          <div class="form-group">
            <label for="mobile_no" class="required-label">Mobile No</label>
            <input type="text" id="mobile_no" name="mobile_no" required />
          </div>

          <div class="form-group">
            <label for="gender" class="required-label">Gender</label>
            <select id="gender" name="gender" required>
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
          </div>

          <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" />
          </div>

          <div class="form-group">
            <label for="cnic">CNIC (13 digits, no “-”)</label>
            <input type="text" id="cnic" name="cnic" />
          </div>

          <div class="form-group">
            <label for="bloodGroup">Blood Group</label>
            <select id="bloodGroup" name="bloodGroup">
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

          <div class="form-group">
            <label for="occupation">Occupation</label>
            <input type="text" id="occupation" name="occupation" />
          </div>

          <div class="form-group address-field">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="2"></textarea>
          </div>

          <div class="form-group">
            <label for="regdate">Registration Date</label>
            <input type="date" id="regdate" name="regdate" />
          </div>

          <div class="form-group">
            <label for="guardianname">Guardian Name</label>
            <input type="text" id="guardianname" name="guardianname" />
          </div>

          <div class="form-group">
            <label for="guardianphonenumber">Guardian Phone</label>
            <input type="text" id="guardianphonenumber" name="guardianphonenumber" />
          </div>

          <div class="form-group">
            <label for="insurance" class="required-label">Insurance</label>
            <select id="insurance" name="insurance" required>
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

  <!-- Employees Tab -->
  <section id="tab-staffs" class="tab-content" style="display: none;">

    <div class="top-row">
      <div class="search-bar">
        <input type="text" id="employeeSearch" placeholder="Search employee..." />
        <div id="employeeResults" class="autocomplete-items"></div>
      </div>
    </div>

    <div class="form-section">

      <h2>Employee / Staff Details</h2>

      <form id="employeeForm">

        <div class="form-grid">

          <div class="form-group">
            <label for="emp_name" class="required-label">Name</label>
            <input type="text" id="emp_name" name="name" required />
          </div>

          <div class="form-group">
            <label for="emp_email" class="required-label">Email</label>
            <input type="email" id="emp_email" name="email" required/>
          </div>

          <div class="form-group">
            <label for="emp_mobile_no" class="required-label">Mobile No</label>
            <input type="text" id="emp_mobile_no" name="mobile_no" required />
          </div>

          <div class="form-group">
            <label for="emp_gender" class="required-label">Gender</label>
            <select id="emp_gender" name="gender" required>
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
          </div>

          <div class="form-group">
            <label for="emp_dob">Date of Birth</label>
            <input type="date" id="emp_dob" name="dob" />
          </div>

          <div class="form-group">
            <label for="emp_regdate">Registration Date</label>
            <input type="date" id="emp_regdate" name="regdate" />
          </div>

          <div class="form-group">
            <label for="emp_designation" class="required-label">Designation</label>
            <select id="emp_designation" name="designation" required>
              <option value="">Select Designation</option>
              <option value="admin">Admin</option>
              <option value="doctor">Doctor</option>
              <option value="receptionist">Receptionist</option>
              <option value="staff">Staff</option>
            </select>
          </div>

        </div>

        <button type="submit" class="submit-btn">Add Employee</button>

      </form>

    </div>

  </section>

</main>

<script>
window.chartLabels = <?= json_encode($chartLabels) ?>;
window.chartData   = <?= json_encode($chartData) ?>;

document.querySelectorAll('.tabbtn').forEach(btn=>{
  btn.addEventListener('click',function(){
    const tab=this.dataset.tab;
    document.querySelectorAll('.tabbtn').forEach(b=>b.classList.remove('active'));
    this.classList.add('active');
    document.querySelectorAll('.tab-content').forEach(sec=>sec.style.display='none');
    const section=document.getElementById('tab-'+tab);
    if(section) section.style.display='block';
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script src="assets/js/chart.js"></script>
<script src="assets/js/admin.js"></script>

</body>
</html>
