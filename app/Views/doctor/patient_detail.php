<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Detail</title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/doctor.css') ?>">
</head>

<body>

<div class="doctor-container">

  <div class="patient-card">
    <h2>Patient Detail</h2>
    <p><b>Name:</b> <?= $patient['name'] ?></p>
    <p><b>MR#:</b> <?= $patient['mr_number'] ?></p>
  </div>

  <div style="margin:10px 0;">
    <a href="<?= base_url('ai-assistant') ?>" class="ai-btn">
      🤖 Dentistry Assistant
    </a>
  </div>

  <div class="card">
    <h3>Symptoms</h3>
    <textarea id="symptoms"></textarea>
    <button onclick="getSuggestion()">Get Suggestion</button>
    <div id="suggestion-box"></div>
  </div>

  <div class="card">
    <h3>Assign Procedure</h3>

    <form method="post" action="<?= base_url('doctor/saveTreatment') ?>">

      <input type="hidden" name="patient_id" value="<?= $patient['patient_id'] ?>">
      <input type="hidden" name="patient_name" value="<?= $patient['name'] ?>">
      <input type="hidden" name="mr_number" value="<?= $patient['mr_number'] ?>">

      <select name="procedure_id">
        <?php foreach($procedures as $p): ?>
          <option value="<?= $p['procedure_id'] ?>">
            <?= $p['procedure_name'] ?>
          </option>
        <?php endforeach; ?>
      </select>

      <textarea name="notes"></textarea>

      <button type="submit">Save Treatment</button>
    </form>
  </div>

  <div class="card">
    <h3>History</h3>

    <table class="table">
      <tr>
        <th>Date</th>
        <th>Description</th>
        <th>Amount</th>
      </tr>

      <?php foreach($history as $h): ?>
      <tr>
        <td><?= $h['payment_date_new'] ?></td>
        <td><?= $h['description'] ?></td>
        <td><?= $h['dues'] ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

</div>

<script>
function getSuggestion() {
    let symptoms = document.getElementById("symptoms").value.toLowerCase();
    let box = document.getElementById("suggestion-box");

    if(symptoms.includes("hole")){
        box.innerHTML = "<b>Tooth Decay → Filling</b>";
    } else {
        box.innerHTML = "No suggestion";
    }
}
</script>

</body>
</html>