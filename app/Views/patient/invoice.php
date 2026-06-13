<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Generate Invoice – Fatima Dental Hospital</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"/>
  <link rel="stylesheet" href="<?= base_url('assets/css/invoice.css') ?>">
  <link rel="icon" type="image/png" href="<?= base_url('assets/images/mylogo.png') ?>">

  <style>
    .invoice-card{
      background:#fff;
      padding:25px;
      border-radius:12px;
      box-shadow:0 8px 25px rgba(0,0,0,0.08);
    }

    .print-invoice{ display:none; }

    .print-header{
      text-align:center;
      margin-bottom:15px;
    }

    .print-header h1{
      margin:0;
      font-size:22px;
      letter-spacing:1px;
    }

    .print-header p{
      margin:3px 0;
      font-size:12px;
    }

    .print-row{
      display:flex;
      justify-content:space-between;
      margin:8px 0;
      font-size:14px;
    }

    .print-section h3{
      border-bottom:1px solid #000;
      padding-bottom:5px;
      margin-bottom:10px;
    }

    @media print {

      @page { margin: 0; }

      body *{ visibility:hidden; }

      .print-invoice,
      .print-invoice *{
        visibility:visible;
      }

      .print-invoice{
        display:block;
        position:absolute;
        top:0;
        left:0;
        width:100%;
        padding:30px;
        border:2px solid #000;
        font-family:Arial;
      }

      .print-footer{
        margin-top:30px;
        text-align:center;
        border-top:1px solid #000;
        padding-top:10px;
        font-size:12px;
      }

      header,
      .form-actions,
      .add-procedure-btn,
      .modal-overlay,
      .dropdown-list{
        display:none !important;
      }

      .invoice-card{ box-shadow:none !important; }

      img{
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }
  </style>
</head>

<body>

<header>
  <div class="logo-btn" onclick="window.location.href='<?= base_url('dashboard') ?>'">
    Fatima Dental Hospital
  </div>
</header>

<section class="invoice-section">
  <div class="invoice-card">

    <h2>Generate Invoice</h2>

    <form id="invoiceForm" method="post" action="<?= base_url('patient/invoice/save') ?>">

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

      <div class="form-row">
        <div class="form-group full-width">
          <label>Description</label>
          <textarea name="description" id="descriptionField" rows="2" readonly><?= esc($description ?? '') ?></textarea>
        </div>
      </div>

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
                    <?= esc($proc['procedure_name']) ?>
                    — <?= esc($proc['department']) ?>
                    (Rs <?= esc($proc['price']) ?>)
                  </span>
                </div>
              <?php endforeach; ?>

            </div>

            <input type="hidden" name="procedure_ids" id="procedureIds">
          </div>

        </div>
      </div>

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
          <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group full-width">
          <label>User Name</label>
          <input type="text" name="user_name" value="<?= esc(session('username')) ?>" readonly>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary">Generate Invoice</button>
        <button type="button" class="btn-primary" onclick="printInvoice()">Print Invoice</button>
      </div>

    </form>

    <!-- PRINT -->
    <div class="print-invoice">

      <div class="print-header">
        <img src="<?= base_url('assets/images/smile.jpg') ?>"
             style="width:130px;height:130px;object-fit:contain;margin:0 auto;display:block;" />

        <h1>Fatima <span style="color:#e63946;">Dental Hospital</span></h1>
        <p>Generate Invoice</p>
      </div>

      <div class="print-row">
        <div><b>Invoice ID:</b> <?= esc($invoice_id) ?></div>
        <div><b>Date:</b> <?= date('Y-m-d') ?></div>
      </div>

      <div class="print-row">
        <div><b>Patient:</b> <?= esc($patient_name) ?></div>
      </div>

      <hr>

      <div class="print-section">
        <h3>Procedures</h3>
        <table style="width:100%">
          <tbody id="printProcedures"></tbody>
        </table>
      </div>

      <hr>

      <div class="print-section">
        <h3>Billing</h3>
        <p><b>Total:</b> <span id="pTotal"></span></p>
        <p><b>Paid:</b> <span id="pPaid"></span></p>
        <p><b>Dues:</b> <span id="pDues"></span></p>
      </div>

      <div class="print-footer">
        Thank you for visiting Fatima Dental Hospital
      </div>

    </div>

  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="<?= base_url('assets/js/invoice.js') ?>"></script>

<script>
let invoiceGenerated = false;

const patientId = "<?= $patient_id ?>";
const appointmentId = "<?= $appointment_id ?? '' ?>";
const treatmentProcedures = <?= json_encode($selectedProcedures ?? []) ?>;

/* =========================
   AUTO LOAD DOCTOR DATA
========================= */
document.addEventListener("DOMContentLoaded", () => {

  // auto description
  if (document.getElementById("descriptionField") && treatmentProcedures?.description) {
    document.getElementById("descriptionField").value = treatmentProcedures.description;
  }

  // auto select procedures
  if (Array.isArray(treatmentProcedures)) {

    treatmentProcedures.forEach(proc => {

      const checkbox = document.querySelector(
        `.procedure-checkbox[data-id="${proc.procedure_id}"]`
      );

      if (checkbox) checkbox.checked = true;
    });

    updateSelectedProceduresUI();
  }
});

/* update UI helper */
function updateSelectedProceduresUI() {
  let names = [];
  let ids = [];

  document.querySelectorAll(".procedure-checkbox:checked").forEach(el => {
    names.push(el.dataset.name);
    ids.push(el.dataset.id);
  });

  document.getElementById("procedureIds").value = ids.join(",");
  document.getElementById("selectedProcedures").innerHTML =
    names.length ? names.join(", ") : `<span class="placeholder">Select procedures</span>`;
}

/* mark invoice generated */
document.getElementById("invoiceForm").addEventListener("submit", function () {
  invoiceGenerated = true;
});

/* PRINT */
function printInvoice() {

  if (!invoiceGenerated) {
    const notyf = new Notyf();
    notyf.error("Please generate invoice first before printing.");
    return;
  }

  document.getElementById("pTotal").innerText = document.getElementById("totalPrice").value || 0;
  document.getElementById("pPaid").innerText = document.getElementById("paidAmount").value || 0;
  document.getElementById("pDues").innerText = document.getElementById("duesAmount").value || 0;

  let rows = "";
  document.querySelectorAll(".procedure-checkbox:checked").forEach(el => {
    rows += `<tr><td>${el.dataset.name}</td></tr>`;
  });

  document.getElementById("printProcedures").innerHTML = rows;

  window.print();
}
</script>

</body>
</html> 