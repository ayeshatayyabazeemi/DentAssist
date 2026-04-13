<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lab Order Form</title>

<link rel="stylesheet" href="<?= base_url('assets/css/patientprofile.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/laborder.css') ?>">

<!-- Notyf Popup CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

<style>
.suggestions-box {
    position:absolute;
    top:100%;
    left:0;
    right:0;
    z-index:999;
    background:#fff;
    border:1px solid #ccc;
    max-height:200px;
    overflow-y:auto;
    display:none;
}
.suggestion-item {
    padding:8px 12px;
    cursor:pointer;
}
.suggestion-item:hover {
    background-color:#f0f0f0;
}
</style>
</head>

<body>

<div class="form-section">
<h2>Lab Order Form</h2>

<?php if(session()->getFlashdata('success')): ?>
    <div id="successMessage" data-message="<?= session()->getFlashdata('success') ?>"></div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div id="errorMessage" data-message="<?= session()->getFlashdata('error') ?>"></div>
<?php endif; ?>

<form id="labOrderForm" action="<?= base_url('laborders/save') ?>" method="post">
    <div class="form-grid">

        <div class="form-group" style="position:relative;">
            <label class="required-label">Patient Name</label>
            <input type="text" name="patient_name" id="patient_name" required autocomplete="off">
            <input type="hidden" name="patient_id" id="patient_id">
            <div id="patient_suggestions" class="suggestions-box"></div>
        </div>

        <div class="form-group">
            <label class="required-label">Lab Name</label>
            <input type="text" name="lab_name" required>
        </div>

        <div class="form-group">
            <label class="required-label">Lab Item</label>
            <input type="text" name="lab_item" required>
        </div>

        <div class="form-group">
            <label>Shade</label>
            <input type="text" name="shade">
        </div>

        <div class="form-group">
            <label>Comment</label>
            <input type="text" name="comments">
        </div>

        <div class="form-group">
            <label class="required-label">Status</label>
            <select name="status">
                <?php foreach(['Sent','Received','Resend','Re-Received','Completed'] as $status): ?>
                    <option value="<?= $status ?>"><?= $status ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>

    <button type="submit" class="btn-add-appt" style="margin-top:15px;">Place Order</button>
</form>

<div style="text-align:center; margin-top:30px;">
    <a href="<?= base_url('laborders/history') ?>" class="btn-add-appt">View Lab Order History</a>
</div>

<script src="<?= base_url('assets/js/laborder.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<script>
const notyf = new Notyf({ duration: 4000, position: { x: 'right', y: 'top' } });
const successDiv = document.getElementById('successMessage');
if(successDiv) notyf.success(successDiv.dataset.message);
const errorDiv = document.getElementById('errorMessage');
if(errorDiv) notyf.error(errorDiv.dataset.message);

// Patient autocomplete
document.addEventListener('DOMContentLoaded', function () {
    const patientInput = document.getElementById('patient_name');
    const patientIdInput = document.getElementById('patient_id');
    const suggestionsBox = document.getElementById('patient_suggestions');
    let timeout = null;

    patientInput.addEventListener('input', function () {
        const query = this.value.trim();
        if (timeout) clearTimeout(timeout);
        if (query.length < 2) { suggestionsBox.innerHTML=''; suggestionsBox.style.display='none'; return; }

        timeout = setTimeout(() => {
            fetch(`/api/patient/search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                suggestionsBox.innerHTML='';
                if(data.status==='success' && data.data.length){
                    data.data.forEach(patient=>{
                        const div=document.createElement('div');
                        div.classList.add('suggestion-item');
                        div.textContent=`${patient.name} (${patient.mobile_no})`;
                        div.dataset.id=patient.patient_id;
                        div.dataset.name=patient.name;
                        div.addEventListener('click',function(){
                            patientInput.value=this.dataset.name;
                            patientIdInput.value=this.dataset.id;
                            suggestionsBox.innerHTML='';
                            suggestionsBox.style.display='none';
                        });
                        suggestionsBox.appendChild(div);
                    });
                    suggestionsBox.style.display='block';
                } else { suggestionsBox.style.display='none'; }
            }).catch(err=>{ console.error(err); suggestionsBox.style.display='none'; });
        }, 300);
    });

    document.addEventListener('click', function(e){
        if(!suggestionsBox.contains(e.target) && e.target!==patientInput){
            suggestionsBox.innerHTML='';
            suggestionsBox.style.display='none';
        }
    });
});
</script>

</body>
</html>
