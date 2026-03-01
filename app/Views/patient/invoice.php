<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Generate Invoice – DentAssist</title>

  <!-- Notyf UI -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"/>

  <!-- Invoice CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/invoice.css') ?>">
</head>
<body>

<header>
  <div class="logo-btn" onclick="window.location.href='<?= base_url('dashboard') ?>'">
    DentAssist
  </div>
</header>

<section class="invoice-section">
  <div class="invoice-card">
    <h2>Generate Invoice</h2>

   <form id="invoiceForm" method="post" action="<?= base_url('patient/invoice/save') ?>">

<!-- ========================= -->
<!-- PATIENT + INVOICE INFO -->
<!-- ========================= -->

<div class="form-row">

    <div class="form-group">
        <label>Invoice ID</label>
        <input type="text" name="invoice_id"
               value="<?= esc($invoice_id) ?>"
               readonly>
    </div>

    <div class="form-group">
        <label>MR Number</label>
        <input type="text" name="mr_number"
               value="<?= esc($mr_number) ?>"
               readonly>
    </div>

</div>


<div class="form-row">

    <div class="form-group full-width">
        <label>Patient Name</label>

        <input type="text"
               name="patient_name"
               value="<?= esc($patient_name) ?>"
               readonly>

        <input type="hidden"
               name="patient_id"
               value="<?= esc($patient_id) ?>">
    </div>

</div>


<!-- ========================= -->
<!-- DESCRIPTION -->
<!-- ========================= -->

<div class="form-row">

    <div class="form-group full-width">
        <label>Description</label>

        <textarea name="description"
                  id="descriptionField"
                  rows="2"
                  readonly>
        </textarea>

    </div>

</div>


<!-- ========================= -->
<!-- PROCEDURE SELECT -->
<!-- ========================= -->

<div class="form-row">
  <div class="form-group full-width">
    <div class="procedure-header">
      <label>Select Procedures</label>
      <button type="button" class="add-procedure-btn" id="openProcedureModal">+</button>
    </div>

    <!-- existing dropdown stays here -->
    <div class="multi-select" id="procedureDropdown">

<div class="selected-box" id="selectedProcedures">
<span class="placeholder">Select procedures</span>
<span class="caret">^</span>
</div>

<div class="dropdown-list">

<?php foreach ($procedures as $proc): ?>

<div class="dropdown-item">

<input type="checkbox"
class="procedure-checkbox"
data-id="<?= esc($proc['procedure_id']) ?>"
data-name="<?= esc($proc['procedure_name']) ?>"
data-price="<?= esc($proc['price']) ?>">

<span>
<?= esc($proc['procedure_name']) ?>
—
<?= esc($proc['department']) ?>
(Rs <?= esc($proc['price']) ?>)
</span>

</div>

<?php endforeach; ?>

</div>

<input type="hidden" name="procedure_ids"
id="procedureIds">

</div>

</div>

</div>


<!-- ========================= -->
<!-- FINANCIAL SECTION -->
<!-- ========================= -->

<div class="form-row two-col">

<div class="form-group">
<label>Total Price</label>
<input type="number"
id="totalPrice"
name="total_price"
readonly>
</div>

<div class="form-group">
<label>Discount</label>
<input type="number"
id="discount"
name="discount"
value="0">
</div>

</div>


<div class="form-row two-col">

<div class="form-group">
<label>Paid Amount</label>
<input type="number"
step="0.01"
name="paid_amount"
id="paidAmount">
</div>

<div class="form-group">
<label>Dues</label>
<input type="number"
step="0.01"
name="dues"
id="duesAmount"
readonly>
</div>

</div>


<div class="form-row two-col">

<div class="form-group">
<label>Advance</label>
<input type="number"
name="advance">
</div>

<div class="form-group">
<label>Payment Date</label>
<input type="datetime-local"
name="payment_date">
</div>

</div>


<!-- ========================= -->
<!-- USER -->
<!-- ========================= -->

<div class="form-row">

<div class="form-group full-width">

<label>User Name</label>

<input type="text"
name="user_name"
value="<?= esc(session('username')) ?>"
readonly>

</div>

</div>


<!-- ========================= -->
<!-- SUBMIT -->
<!-- ========================= -->

<div class="form-actions">
<button type="submit" class="btn-primary">
Generate Invoice
</button>
</div>

</form>
  <div class="modal-overlay" id="procedureModal">
  <div class="modal">
    <div class="modal-header">
      <h3>Manage Procedures</h3>
      <button class="close-modal" id="closeProcedureModal">✕</button>
    </div>

    <div class="modal-body">
      <table class="procedure-table">
        <thead>
          <tr>
            <th>Procedure Name</th>
            <th>Department</th>
            <th>Price (Rs)</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody id="procedureTableBody">
          <!-- ADD NEW PROCEDURE ROW -->
          <tr class="new-row">
            <td>
              <input type="text" id="newProcName" placeholder="New procedure name">
            </td>
            <td>
              <input type="text" id="newProcDepartment" placeholder="Department">
            </td>
            <td>
              <input type="number" id="newProcPrice" placeholder="Price">
            </td>
            <td>
              <button class="save-btn" id="addNewProcedure">Add</button>
            </td>
          </tr>

          <!-- EXISTING PROCEDURES -->
          <?php foreach ($procedures as $proc): ?>
            <tr data-id="<?= esc($proc['procedure_id']) ?>">
              <td class="editable name"><?= esc($proc['procedure_name']) ?></td>
              <td class="editable department"><?= esc($proc['department']) ?></td>
              <td class="editable price"><?= esc($proc['price']) ?></td>
              <td>
                <button class="delete-btn">Delete</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</section>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<!-- Notyf JS -->
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<!-- Invoice JS -->
<script src="<?= base_url('assets/js/invoice.js') ?>"></script>

</body>
</html>