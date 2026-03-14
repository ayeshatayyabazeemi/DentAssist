<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Doctor Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/receptionistDashboard.css') ?>">
</head>
<body>

<header class="main-header">
  <div class="logo-btn" id="logo">Doctor Dashboard</div>
  <nav>
    <ul class="nav-list">
      <li><a href="<?= base_url('doctor/appointments') ?>">Appointments</a></li>
    </ul>
    <form id="logoutForm" action="<?= base_url('logout') ?>" method="post">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </nav>
</header>

<div class="content-wrapper">
  <!-- KPI Boxes -->
  <div class="kpi-container">
    <div class="kpi-card border-primary">
      <h3>Pending Appointments</h3>
      <p class="value"><?= $pendingCount ?></p>
    </div>
    <div class="kpi-card border-success">
      <h3>Completed Appointments</h3>
      <p class="value"><?= $completedCount ?></p>
    </div>
  </div>

  <!-- Today's Appointments -->
  <section id="tab-dashboard" class="tab-content active">
    <h2>Today's Appointments</h2>
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
            </tr>
          </thead>
          <tbody id="appointments-body">
            <?php foreach($todaysAppointments as $apt): ?>
              <tr class="status-<?= $apt['status'] ?>">
                <td><?= esc($apt['appointment_id']) ?></td>
                <td><?= esc($apt['mr_number']) ?></td>
                <td><?= esc($apt['patient_name']) ?></td>
                <td><?= esc($doctor_name) ?></td>
                <td><?= esc($apt['appointment_date']) ?></td>
                <td><?= esc($apt['slot'] ?? '-') ?></td>
                <td>
                  <select class="status-select" data-appointment="<?= $apt['appointment_id'] ?>">
                    <option value="scheduled" <?= $apt['status']=='scheduled'?'selected':'' ?>>Scheduled</option>
                    <option value="checked_in" <?= $apt['status']=='checked_in'?'selected':'' ?>>Checked In</option>
                    <option value="in_progress" <?= $apt['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                    <option value="completed" <?= $apt['status']=='completed'?'selected':'' ?>>Completed</option>
                    <option value="no_show" <?= $apt['status']=='no_show'?'selected':'' ?>>No Show</option>
                    <option value="cancelled" <?= $apt['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                  </select>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="<?= base_url('assets/js/doctor_dashboard.js') ?>"></script>
</body>
</html>
