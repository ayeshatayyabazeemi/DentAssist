<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generate Invoice – DentAssist</title>

<!-- Notyf CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"/>

<!-- Invoice CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/invoice.css') ?>">

<style>
/* Unified Button Style */
.btn-primary {
  background-color: #6c63ff; /* Purple */
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: bold;
  margin-right: 10px;
}
.btn-primary:hover {
  background-color: #5848d6;
}

/* Print layout */
@media print {
  body * { visibility: hidden; }
  #printInvoiceSection, #printInvoiceSection * { visibility: visible; }
  #printInvoiceSection {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    font-family: Arial, sans-serif;
    padding: 20px;
  }
  #printInvoiceSection table {
    width: 100%;
    border-collapse: collapse;
  }
  #printInvoiceSection table, #printInvoiceSection th, #printInvoiceSection td {
    border: 1px solid #000;
  }
  #printInvoiceSection th, #printInvoiceSection td {
    padding: 6px 8px;
    text-align: left;
  }
  .btn-primary, #openProcedureModal, .close-modal {
    display: none !important;
  }
  @page { size: A4; margin: 20mm; }
}

/* Hospital Name Style */
.hospital-name {
  font-family: 'Georgia', serif;
  font-size: 32px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 10px;
}
.hospital-name span.red {
  color: #e74c3c;
}
.hospital-name span.black {
  color: #000000;
}
</style>
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

<!-- PATIENT + INVOICE INFO -->
<div class="form-row">
  <div class="form-group">
    <label>Invoice ID</label>
    <input type="text" name="invoice_id" value="<?= esc($invoice_id) ?>" readonly>
  </div>
  <div class="form-group">
    <label>MR Number</label>
    <input type="text" name="mr_number" value="<?= esc($mr_number) ?>" readonly>
  </div>
</div>

<div class="form-row">
  <div class="form-group full-width">
    <label>Patient Name</label>
    <input type="text" name="patient_name" value="<?= esc($patient_name) ?>" readonly>
    <input type="hidden" name="patient_id" value="<?= esc($patient_id) ?>">
  </div>
</div>

<!-- DESCRIPTION -->
<div class="form-row">
  <div class="form-group full-width">
    <label>Description</label>
    <textarea name="description" id="descriptionField" rows="2" readonly><?= esc($description ?? '') ?></textarea>
  </div>
</div>

<!-- PROCEDURE SELECT -->
<div class="form-row">
<div class="form-group full-width">
  <div class="procedure-header">
    <label>Select Procedures</label>
    <button type="button" class="add-procedure-btn" id="openProcedureModal">+</button>
  </div>

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
          <?= esc($proc['procedure_name']) ?> —
          <?= esc($proc['department']) ?> (Rs <?= esc($proc['price']) ?>)
        </span>
      </div>
      <?php endforeach; ?>
    </div>
    <input type="hidden" name="procedure_ids" id="procedureIds">
  </div>
</div>
</div>

<!-- FINANCIAL SECTION -->
<div class="form-row two-col">
  <div class="form-group">
    <label>Total Price</label>
    <input type="number" id="totalPrice" name="total_price" readonly>
  </div>
  <div class="form-group">
    <label>Paid Amount</label>
    <input type="number" step="0.01" name="paid_amount" id="paidAmount">
  </div>
  <div class="form-group">
    <label>Dues</label>
    <input type="number" step="0.01" name="dues" id="duesAmount" readonly>
  </div>
</div>

<div class="form-row two-col">
  <div class="form-group">
    <label>Advance</label>
    <input type="number" name="advance">
  </div>
  <div class="form-group">
    <label>Payment Date</label>
    <input type="datetime-local" name="payment_date">
  </div>
</div>

<!-- USER -->
<div class="form-row">
<div class="form-group full-width">
  <label>User Name</label>
  <input type="text" name="user_name" value="<?= esc(session('username')) ?>" readonly>
</div>
</div>

<!-- SUBMIT + PRINT -->
<div class="form-actions">
  <button type="submit" class="btn-primary">Generate Invoice</button>
  <button type="button" class="btn-primary" onclick="printInvoice()">Print Invoice</button>
</div>

</form>
</div>
</section>

<!-- Hidden div for print -->
<div id="printInvoiceSection"></div>

<!-- PRINT FUNCTION -->
<script>
function printInvoice() {
  const form = document.getElementById('invoiceForm');

  // Grab selected procedures
  const procedures = [];
  document.querySelectorAll('.procedure-checkbox:checked').forEach(cb=>{
      procedures.push({
          name: cb.dataset.name,
          price: cb.dataset.price
      });
  });

  const description = document.getElementById('descriptionField').value.trim();
  const total = document.getElementById('totalPrice').value || 0;
  const paid = document.getElementById('paidAmount').value || 0;
  const dues = document.getElementById('duesAmount').value || 0;
  const advance = form.advance.value || 0;
  const paymentDate = form.payment_date.value || '';
  const patientName = form.patient_name.value || '';
  const mrNumber = form.mr_number.value || '';
  const invoiceId = form.invoice_id.value || '';
  const userName = form.user_name.value || '';

  // Procedures table
  let procHtml = '';
  if (procedures.length === 0) {
      procHtml = '<tr><td colspan="2" style="text-align:center;">No procedures selected</td></tr>';
  } else {
      procedures.forEach(p=>{
          procHtml += `<tr><td>${p.name}</td><td>Rs ${p.price}</td></tr>`;
      });
  }

  const html = `
  <div class="hospital-name">
    <span class="black">FATIMA </span><span class="red">DENTAL HOSPITAL</span>
  </div>
  <div style="text-align:center; margin-bottom:10px;">
    <p>Invoice ID: ${invoiceId}</p>
  </div>
  <div style="margin-bottom:10px;">
    <p><strong>Patient Name:</strong> ${patientName}</p>
    <p><strong>MR Number:</strong> ${mrNumber}</p>
    <p><strong>Description:</strong> ${description}</p>
    <p><strong>Payment Date:</strong> ${paymentDate}</p>
  </div>
  <table style="width:100%; margin-top:10px; border:1px solid #000; border-collapse:collapse;">
    <thead>
      <tr>
        <th style="border:1px solid #000; padding:5px;">Procedure</th>
        <th style="border:1px solid #000; padding:5px;">Price (Rs)</th>
      </tr>
    </thead>
    <tbody>${procHtml}</tbody>
  </table>
  <div style="margin-top:10px;">
    <p><strong>Total:</strong> Rs ${total}</p>
    <p><strong>Paid:</strong> Rs ${paid}</p>
    <p><strong>Dues:</strong> Rs ${dues}</p>
    <p><strong>Advance:</strong> Rs ${advance}</p>
    <p><strong>Generated By:</strong> ${userName}</p>
  </div>`;

  const printSection = document.getElementById('printInvoiceSection');
  printSection.innerHTML = html;

  window.print();
}
</script>

<!-- Notyf JS -->
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<!-- Invoice JS -->
<script src="<?= base_url('assets/js/invoice.js') ?>"></script>

</body>
</html>
