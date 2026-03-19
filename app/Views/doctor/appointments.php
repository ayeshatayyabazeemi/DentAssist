<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Doctor Appointments</title>
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/receptionistDashboard.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
</head>
<body>
<header class="main-header">
  <div class="logo-btn">Doctor Dashboard</div>
  <nav>
    <ul class="nav-list">
      <li><a href="<?= base_url('doctor/appointments') ?>">Appointments</a></li>
    </ul>
    <form action="<?= base_url('logout') ?>" method="post">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </nav>
</header>

<div class="content-wrapper">
  <h2>All Future Appointments</h2>
  <div class="appointments-card">
    <div class="appointments-card-body">
      <table class="appointments-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>MR Number</th>
            <th>Patient Name</th>
            <th>Slot</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($appointments as $apt): ?>
            <tr class="status-<?= $apt['status'] ?>">
              <td><?= esc($apt['appointment_id']) ?></td>
              <td><?= esc($apt['mr_number']) ?></td>
              <td><?= esc($apt['patient_name']) ?></td>
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
              <td>
                <a href="<?= base_url('doctor/patientForm/'.$apt['appointment_id']) ?>" class="btn btn-primary">Form</a>
                <a href="<?= base_url('invoice/view/'.$apt['appointment_id']) ?>" class="btn btn-success">Invoice</a>
                <a href="<?= base_url('assistant/symptoms/'.$apt['appointment_id']) ?>" class="btn btn-info">Assistant</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="<?= base_url('assets/js/doctor_dashboard.js') ?>"></script>
</body>
</html>