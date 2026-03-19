<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dental Form</title>
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<style>
/* Your form styling goes here (same as your HTML) */
</style>
</head>
<body>
<div class="form-container">
<h2>Dental Form for <?= $appointment['name'] ?></h2>
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<form action="<?= base_url('doctor/saveDentalForm') ?>" method="post">
    <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
    <input type="hidden" name="patient_id" value="<?= $appointment['patient_id'] ?>">

    <h3>Personal Info</h3>
    <label>Name:</label>
    <input type="text" name="name" value="<?= $appointment['name'] ?>" required>
    <label>Age:</label>
    <input type="number" name="age" value="<?= $appointment['age'] ?>">
    <label>Gender:</label>
    <select name="gender">
        <option value="male" <?= $appointment['gender']=='male'?'selected':'' ?>>Male</option>
        <option value="female" <?= $appointment['gender']=='female'?'selected':'' ?>>Female</option>
    </select>

    <h3>Medical Info</h3>
    <label>Medical Conditions:</label>
    <?php
        $conditions = json_decode($appointment['medical_conditions'] ?? '[]', true) ?: [];
        $all_conditions = ["AIDS","Asthma","Arthritis","Blood Disease","Blood Pressure","Cancer","Diabetes","Epilepsy","Hepatitis","Herpes","Jaundice","Psychiatric Treatment","Rheumatic Fever","TB","Thyroid Problems","Ulcer","Venereal Disease","Other"];
    ?>
    <?php foreach($all_conditions as $c): ?>
        <label><input type="checkbox" name="medical[]" value="<?= $c ?>" <?= in_array($c,$conditions)?'checked':'' ?>> <?= $c ?></label>
    <?php endforeach; ?>

    <h3>Habits</h3>
    <label>Pan Masala Chewing:</label>
    <label><input type="radio" name="pan" value="Yes">Yes</label>
    <label><input type="radio" name="pan" value="No" checked>No</label>

    <label>Pan Chewing (Tobacco):</label>
    <label><input type="radio" name="tobacco" value="Yes">Yes</label>
    <label><input type="radio" name="tobacco" value="No" checked>No</label>

    <label>Smoking:</label>
    <label><input type="radio" name="smoking" value="Yes">Yes</label>
    <label><input type="radio" name="smoking" value="No" checked>No</label>

    <h3>Dental Info</h3>
    <label>Main Complaint:</label>
    <textarea name="mainComplaint" required></textarea>
    <label>Treatment History (last year):</label>
    <textarea name="treatmentHistory"></textarea>
    <label>Current Medicines:</label>
    <textarea name="medicines"></textarea>
    <label>Allergies:</label>
    <textarea name="allergies"></textarea>

    <button type="submit">Submit Form</button>
</form>

<?php if($history): ?>
<h3>Previous Visits</h3>
<table border="1">
<tr><th>Date</th><th>Main Complaint</th><th>Treatment History</th><th>Medicines</th><th>Allergies</th></tr>
<?php foreach($history as $h): ?>
<tr>
<td><?= $h['visit_date'] ?></td>
<td><?= $h['main_complaint'] ?></td>
<td><?= $h['treatment_history'] ?></td>
<td><?= $h['medicines'] ?></td>
<td><?= $h['allergies'] ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
</div>
</body>
</html>