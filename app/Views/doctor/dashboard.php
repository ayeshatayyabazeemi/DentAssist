<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Doctor Dashboard</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/receptionistDashboard.css') ?>">

<style>
.tab-box { margin-top: 15px; }
/* ===== TAB BUTTONS (THEME MATCHED) ===== */
.tab-buttons {
  display: flex;
  gap: 12px;
  margin: 15px 0;
}

.tab-btn {
  padding: 10px 18px;
  border: none;
  cursor: pointer;
  border-radius: 25px;
  background: #BADFDB;
  color: #333;
  font-weight: 500;
  transition: all 0.3s ease;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.tab-btn:hover {
  background: #FFA4A4;
  color: white;
  transform: translateY(-2px);
}

.tab-btn.active {
  background: #FFA4A4;
  color: white;
}

/* ===== TAB BOX CLEAN LAYOUT ===== */
.tab-box {
  margin-top: 20px;
  animation: fadeIn 0.3s ease-in-out;
}
</style>

</head>

<body>

<header class="main-header">
  <div class="logo-btn">Doctor Dashboard</div>

  <nav>
    <ul class="nav-list">
      <li><a href="<?= base_url('doctor/dashboard') ?>">Home</a></li>
    </ul>

    <form action="<?= base_url('logout') ?>" method="post">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </nav>
</header>

<div class="content-wrapper">

  <!-- KPI -->
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

  <!-- ================= TAB BUTTONS ================= -->
<div class="tab-buttons">
  <button class="tab-btn active" onclick="showTab('today', this)">Today</button>
  <button class="tab-btn" onclick="showTab('upcoming', this)">Upcoming</button>
  <button class="tab-btn" onclick="showTab('completed', this)">Completed</button>
</div>

  <!-- ================= TODAY ================= -->
  <div id="today" class="tab-box">

    <h2>Today's Appointments</h2>

    <div class="appointments-card">

      <div class="appointments-card-header">
        <span>Appointments</span>
      </div>

      <div class="appointments-card-body">

        <table class="appointments-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>MR #</th>
              <th>Patient</th>
              <th>Date</th>
              <th>Slot</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody>
          <?php if (!empty($todaysAppointments)): ?>
            <?php foreach($todaysAppointments as $apt): ?>
              <tr class="status-<?= esc($apt['status']) ?>">

                <td><?= esc($apt['appointment_id']) ?></td>

                <td><?= esc($apt['mr_number']) ?></td>

                <td>
                  <a href="<?= base_url('doctor/patient/'.$apt['patient_id'].'?appointment_id='.$apt['appointment_id']) ?>">
                    <?= esc($apt['patient_name']) ?>
                  </a>
                </td>

                <td><?= esc($apt['appointment_date']) ?></td>

                <td><?= esc($apt['slot'] ?? '-') ?></td>

                <td>
                  <select class="status-select"
                          data-appointment="<?= esc($apt['appointment_id']) ?>">

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
          <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No appointments today</td></tr>
          <?php endif; ?>
          </tbody>

        </table>

      </div>
    </div>
  </div>

  <!-- ================= UPCOMING ================= -->
  <div id="upcoming" class="tab-box" style="display:none;">

    <h2>Upcoming Appointments</h2>

    <table class="appointments-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>MR #</th>
          <th>Patient</th>
          <th>Date</th>
          <th>Slot</th>
        </tr>
      </thead>

      <tbody>
      <?php if (!empty($futureAppointments)): ?>
        <?php foreach($futureAppointments as $apt): ?>
          <tr>
            <td><?= esc($apt['appointment_id']) ?></td>
            <td><?= esc($apt['mr_number']) ?></td>
            <td><?= esc($apt['patient_name']) ?></td>
            <td><?= esc($apt['appointment_date']) ?></td>
            <td><?= esc($apt['slot'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5" style="text-align:center;">No upcoming appointments</td></tr>
      <?php endif; ?>
      </tbody>

    </table>
  </div>

  <!-- ================= COMPLETED ================= -->
  <div id="completed" class="tab-box" style="display:none;">

    <h2>Completed Appointments</h2>

    <table class="appointments-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>MR #</th>
          <th>Patient</th>
          <th>Date</th>
        </tr>
      </thead>

      <tbody>
      <?php if (!empty($completedAppointments)): ?>
        <?php foreach($completedAppointments as $apt): ?>
          <tr>
            <td><?= esc($apt['appointment_id']) ?></td>
            <td><?= esc($apt['mr_number']) ?></td>
            <td><?= esc($apt['patient_name']) ?></td>
            <td><?= esc($apt['appointment_date']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">No completed appointments</td></tr>
      <?php endif; ?>
      </tbody>

    </table>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="<?= base_url('assets/js/doctor_dashboard.js') ?>"></script>

<<script>
function showTab(tab, btn) {
  document.querySelectorAll('.tab-box').forEach(el => el.style.display = 'none');
  document.getElementById(tab).style.display = 'block';

  // active button styling
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}
</script>

</body>
</html>