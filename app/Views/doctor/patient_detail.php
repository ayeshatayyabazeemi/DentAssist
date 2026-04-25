<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Detail</title>

<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/doctor.css') ?>">

<style>
/* ===== AI BUTTON FIX ===== */
.ai-section{
    margin: 15px 0 25px 0;
    display: flex;
    justify-content: flex-end;
}

/* ===== PATIENT CARD THEME FIX ===== */
.patient-card{
    background: linear-gradient(135deg, #ffffff 0%, #FCF9EA 100%);
    border-left: 5px solid #FFA4A4;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    padding: 18px 20px;
    border-radius: 12px;
    margin-bottom: 15px;
    transition: 0.3s;
}

.patient-card:hover{
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,164,164,0.15);
}

.patient-card h2{
    margin-bottom:10px;
    color:#333;
}

.patient-card p{
    margin:5px 0;
    color:#444;
}

/* ===== REMOVE SYMPTOMS SECTION STYLE CLEANUP ===== */
.hidden-section{
    display:none;
}

/* ===== LAYOUT CLEAN ===== */
.doctor-container{
    max-width: 1100px;
    margin: auto;
    padding: 20px;
}

/* ===== CARD FIX ===== */
.card{
    background: #fff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
</style>

</head>

<body>

<div class="doctor-container">

  <!-- ================= PATIENT INFO ================= -->
  <div class="patient-card">
    <h2>Patient Detail</h2>
    <p><b>Name:</b> <?= $patient['name'] ?></p>
    <p><b>MR#:</b> <?= $patient['mr_number'] ?></p>
  </div>

  <!-- ================= AI BUTTON (FIXED POSITION) ================= -->
  <div class="ai-section">
    <a href="<?= base_url('ai-assistant') ?>" class="ai-btn">
      Dentistry Assistant
    </a>
  </div>

  <!-- ================= APPOINTMENT LOCK CHECK ================= -->
  <?php
    $appointment_id = $_GET['appointment_id'] ?? null;
    $isLocked = false;

    if($appointment_id && !empty($history)){
        foreach($history as $h){
            if(isset($h['appointment_id']) && $h['appointment_id'] == $appointment_id){
                $isLocked = true;
                break;
            }
        }
    }
  ?>

  <!-- ================= TREATMENT FORM ================= -->
  <div class="card">
    <h3>Assign Procedure</h3>

    <?php if($isLocked): ?>
        <p style="color:red;font-weight:bold;">
            ⚠ This appointment is completed and locked.
        </p>
    <?php endif; ?>

    <form method="post" action="<?= base_url('doctor/saveTreatment') ?>">

      <input type="hidden" name="patient_id" value="<?= $patient['patient_id'] ?>">
      <input type="hidden" name="patient_name" value="<?= $patient['name'] ?>">
      <input type="hidden" name="mr_number" value="<?= $patient['mr_number'] ?>">
      <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>">

      <!-- <select name="procedure_id" <?= $isLocked ? 'disabled' : '' ?> required>
        <?php foreach($procedures as $p): ?>
          <option value="<?= $p['procedure_id'] ?>">
            <?= $p['procedure_name'] ?>
          </option>
        <?php endforeach; ?>
      </select> -->

          <div class="procedure-dropdown">

  <label class="dropdown-label">Procedures</label>

  <div class="dropdown-box" id="procedureBox">
    <div class="dropdown-selected" id="selectedText">
      Select procedures
    </div>

    <div class="dropdown-menu" id="procedureMenu">

      <?php foreach($procedures as $p): ?>
        <label class="dropdown-item">
          <input type="checkbox" name="procedure_id[]" value="<?= $p['procedure_id'] ?>">
          <span><?= $p['procedure_name'] ?></span>
        </label>
      <?php endforeach; ?>

    </div>
  </div>

  <div id="selectedTags" class="tags"></div>

</div>


      <textarea name="notes" <?= $isLocked ? 'disabled' : '' ?>></textarea>

      <button type="submit" <?= $isLocked ? 'disabled' : '' ?>>
        Save Treatment
      </button>

    </form>
  </div>

  <!-- ================= HISTORY ================= -->
  <div class="card">
    <h3>History</h3>

    <table class="table">
      <tr>
        <th>Date</th>
        <th>Procedure</th>
        <th>Notes</th>
        <th>Doctor</th>
      </tr>

      <?php if(!empty($history)): ?>
        <?php foreach($history as $h): ?>
        <tr>
          <td><?= date('Y-m-d', strtotime($h['created_at'])) ?></td>
          <td><?= $h['procedure_name'] ?? '-' ?></td>
          <td><?= $h['notes'] ?></td>
          <td><?= $h['doctor_name'] ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="4">No history found</td>
        </tr>
      <?php endif; ?>

    </table>
  </div>

</div>

</body>
</html>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const box = document.getElementById("procedureBox");
  const menu = document.getElementById("procedureMenu");
  const selectedText = document.getElementById("selectedText");
  const tags = document.getElementById("selectedTags");

  if (!box) return; // safety check

  // toggle dropdown
  box.addEventListener("click", (e) => {
    menu.style.display = menu.style.display === "block" ? "none" : "block";
  });

  // close outside click
  document.addEventListener("click", (e) => {
    if (!box.contains(e.target)) {
      menu.style.display = "none";
    }
  });

  const checkboxes = document.querySelectorAll("input[name='procedure_id[]']");

  checkboxes.forEach(cb => {
    cb.addEventListener("change", updateUI);
  });

  function updateUI() {

    const selected = document.querySelectorAll("input[name='procedure_id[]']:checked");

    tags.innerHTML = "";

    if (selected.length === 0) {
      selectedText.innerText = "Select procedures";
      return;
    }

    let names = [];

    selected.forEach(cb => {
      names.push(cb.nextElementSibling.innerText);

      const tag = document.createElement("span");
      tag.innerText = cb.nextElementSibling.innerText;
      tags.appendChild(tag);
    });

    selectedText.innerText = names.join(", ");
  }

});
</script>