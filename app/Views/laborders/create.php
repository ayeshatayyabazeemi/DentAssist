<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Order Form</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/patientprofile.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/laborder.css') ?>">
    <style>
        /* Simple suggestion box styling */
        .suggestions-box {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 999;
            background: #fff;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }
        .suggestion-item {
            padding: 8px 12px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

<div class="form-section">
    <h2>Lab Order Form</h2>

    <!-- Success & Error messages -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="notification success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="notification error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form id="labOrderForm" action="<?= base_url('laborders/save') ?>" method="post">
        <div class="form-grid">

            <!-- Patient Name -->
            <div class="form-group" style="position:relative;">
                <label class="required-label">Patient Name</label>
                <input type="text" name="patient_name" id="patient_name" required autocomplete="off">
                <input type="hidden" name="patient_id" id="patient_id">
                <div id="patient_suggestions" class="suggestions-box"></div>
            </div>

            <!-- Lab Name -->
            <div class="form-group">
                <label class="required-label">Lab Name</label>
                <input type="text" name="lab_name" required>
            </div>

            <!-- Lab Item -->
            <div class="form-group">
                <label class="required-label">Lab Item</label>
                <input type="text" name="lab_item" required>
            </div>

            <!-- Shade -->
            <div class="form-group">
                <label>Shade</label>
                <input type="text" name="shade">
            </div>

            <!-- Comment -->
            <div class="form-group">
                <label>Comment</label>
                <input type="text" name="comments">
            </div>

            <!-- Status -->
            <div class="form-group">
                <label class="required-label">Status</label>
                <select name="status">
                    <option value="Sent" selected>Sent</option>
                    <option value="Received">Received</option>
                    <option value="Complete">Complete</option>
                </select>
            </div>

        </div>

        <button type="submit" class="btn-add-appt" style="margin-top:15px;">Place Order</button>
    </form>

    <div style="text-align:center; margin-top:30px;">
        <a href="<?= base_url('laborders/history') ?>" class="btn-add-appt">View Lab Order History</a>
    </div>
</div>

<!-- JS file -->
<script src="<?= base_url('assets/js/laborder.js') ?>"></script>

</body>
</html>
