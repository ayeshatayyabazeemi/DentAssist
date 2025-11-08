<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="assets/css/admin.css" />
  <link rel="stylesheet" href="assets/css/patientprofile.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
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

<header>
  <div class="logo">Admin Dashboard</div>
  <nav>
    <ul>
      <li><button class="tabbtn active" data-tab="patients">Patients</button></li>
      <li><button class="tabbtn" data-tab="appointments">Appointments</button></li>
      <li><button class="tabbtn" data-tab="staffs">Staffs</button></li>
    </ul>
  </nav>
</header>

<main class="main-content">

  <!-- Patients Tab -->
  <section id="tab-patients" class="tab-content">
    <div class="top-row">
      <div class="total">Total Patients: <span id="totalCount">0</span></div>
      <div class="search-bar">
        <i class="fa fa-search search-icon"></i>
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
            <input type="text" id="mobile_no" name="mobile_no" required pattern="\d{10}" title="10 digits only" />
          </div>
          <div class="form-group">
            <label for="gender" class="required-label">Gender</label>
            <select id="gender" name="gender" required>
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" />
          </div>
          <div class="form-group">
            <label for="patient_cnic">CNIC (13 digits, no “-”)</label>
            <input type="text" id="patient_cnic" name="cnic" pattern="\d{13}" title="13 digits without dashes" />
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
            <label for="doctorName">Doctor Name</label>
            <input type="text" id="doctorName" name="doctorName" />
          </div>
          <div class="form-group">
            <label for="insurance">Insurance</label>
            <input type="text" id="insurance" name="insurance" />
          </div>
        </div>
        <button type="submit" class="submit-btn">Add Patient</button>
      </form>
    </div>
  </section>

  <!-- Appointments Tab -->
  <section id="tab-appointments" class="tab-content" style="display:none;">
    <div class="placeholder-section">
      <h2>Appointments</h2>
      <p>No content yet.</p>
    </div>
  </section>

  <!-- Staffs Tab -->
  <section id="tab-staffs" class="tab-content" style="display:none;">
    <div class="top-row">
      <div class="total">Total Employees: <span id="totalEmpCount">0</span></div>
      <div class="search-bar">
        <i class="fa fa-search search-icon"></i>
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
            <label for="emp_email">Email</label>
            <input type="email" id="emp_email" name="email" />
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
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="emp_dob">Date of Birth</label>
            <input type="date" id="emp_dob" name="dob" />
          </div>
          <div class="form-group">
            <label for="emp_cnic">CNIC (13 digits, no “-”)</label>
            <input type="text" id="emp_cnic" name="cnic" pattern="\d{13}" title="13 digits without dashes" />
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
          <div class="form-group address-field">
            <label for="emp_address">Address</label>
            <textarea id="emp_address" name="address" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label for="emp_password">Password (Not for Staff)</label>
            <input type="password" id="emp_password" name="password" />
          </div>
          <div class="form-group">
            <label for="emp_c_password">Confirm Password</label>
            <input type="password" id="emp_c_password" name="c_password" />
          </div>
        </div>

        <div class="form-section" id="scheduleSection" style="display:none;">
          <h2>Doctor Schedule</h2>
          <table class="schedule-table">
            <thead>
              <tr><th>Day</th><th>Start Time</th><th>End Time</th></tr>
            </thead>
            <tbody>
              <tr><td>Sunday</td><td><input type="time" name="start_time[Sun]" /></td><td><input type="time" name="end_time[Sun]" /></td></tr>
              <tr><td>Monday</td><td><input type="time" name="start_time[Mon]" /></td><td><input type="time" name="end_time[Mon]" /></td></tr>
              <tr><td>Tuesday</td><td><input type="time" name="start_time[Tue]" /></td><td><input type="time" name="end_time[Tue]" /></td></tr>
              <tr><td>Wednesday</td><td><input type="time" name="start_time[Wed]" /></td><td><input type="time" name="end_time[Wed]" /></td></tr>
              <tr><td>Thursday</td><td><input type="time" name="start_time[Thu]" /></td><td><input type="time" name="end_time[Thu]" /></td></tr>
              <tr><td>Friday</td><td><input type="time" name="start_time[Fri]" /></td><td><input type="time" name="end_time[Fri]" /></td></tr>
              <tr><td>Saturday</td><td><input type="time" name="start_time[Sat]" /></td><td><input type="time" name="end_time[Sat]" /></td></tr>
            </tbody>
          </table>
        </div>

        <button type="submit" class="submit-btn">Add Employee</button>
      </form>
    </div>

  </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="assets/js/admin.js"></script>
</body>
</html>
