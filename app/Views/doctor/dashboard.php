<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Doctor Dashboard</title>

  <!-- Admin CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="icon" type="image/png" href="<?= base_url('assets/images/mylogo.png') ?>" />

 

<header>
  <div class="logo-btn">Doctor Dashboard</div>
  <nav>
    <ul>
      
      <li><a href="<?= base_url('doctor/appointments') ?>">Appointments</a></li>
    </ul>
    <form action="<?= base_url('logout') ?>" method="post" style="display:inline;">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </nav>
</header>

<main class="main-content">

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

  <!-- Today's Schedule -->
<!-- Today's Schedule -->
<section class="table-section" style="margin-top: 30px;">
  <div class="row">
    <div class="col-12">
      <!-- Heading -->
      <h2 style="margin-bottom: 15px; font-size: 1.5rem; font-weight: 600;">Today's Schedule</h2>

      <!-- Responsive Table -->
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse: collapse; text-align: left;" border="1">
          <thead style="background-color:#343a40; color:white;">
            <tr>
              <th style="padding: 10px;">MR Number</th>
              <th style="padding: 10px;">Patient Name</th>
              <th style="padding: 10px;">Slot</th>
              <th style="padding: 10px;">Status</th>
              <th style="padding: 10px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($todaysAppointments)): ?>
              <?php foreach ($todaysAppointments as $app): ?>
                <tr>
                  <td style="padding: 8px;"><?= esc($app['mr_number']) ?></td>
                  <td style="padding: 8px;"><?= esc($app['name']) ?></td>
                  <td style="padding: 8px;"><?= esc($app['slot']) ?></td>
                  <td style="padding: 8px;">
                    <?php if ($app['status'] === 'completed'): ?>
                      <span style="background-color: #28a745; color: white; padding: 3px 8px; border-radius: 5px;">Completed</span>
                    <?php else: ?>
                      <span style="background-color: #ffc107; color: black; padding: 3px 8px; border-radius: 5px;">Pending</span>
                    <?php endif; ?>
                  </td>
                  <td style="padding: 8px;">
                    <?php if ($app['status'] === 'pending'): ?>
                   <form method="post" action="<?= site_url('doctor/updateStatus') ?>" class="complete-form">
    <input type="hidden" name="appointment_id" value="<?= esc($app['appointment_id']) ?>">
    <button type="submit" class="complete-btn">Mark Completed</button>
</form>                  
             <?php else: ?>
                      <span style="color:#6c757d;">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align:center; padding: 15px; color:#6c757d;">No appointments scheduled for today.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

</main>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="<?= base_url('assets/js/admin.js') ?>"></script>
</body>
</html>