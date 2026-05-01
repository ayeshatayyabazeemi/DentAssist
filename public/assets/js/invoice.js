// =========================
// Notyf Notifications
// =========================
const notyf = new Notyf({
  duration: 3000,
  position: { x: 'center', y: 'top' }
});

// =========================
// FORM ELEMENTS
// =========================
const invoiceForm      = document.getElementById('invoiceForm');
const totalPriceInput  = document.getElementById('totalPrice');
const descriptionField = document.getElementById('descriptionField');
const paidAmountInput  = document.querySelector("input[name='paid_amount']");
const duesInput        = document.querySelector("input[name='dues']");
const advanceInput     = document.querySelector("input[name='advance']");
const paymentDateInput = document.querySelector("input[name='payment_date']");
const hiddenProcedureInput = document.getElementById('procedureIds');

// Dropdown elements
const dropdown    = document.getElementById('procedureDropdown');
const selectedBox = document.getElementById('selectedProcedures');

// =========================
// GET ITEMS (DYNAMIC FIX)
// =========================
function getItems() {
  return dropdown.querySelectorAll('.dropdown-item');
}

// =========================
// AUTO SELECT DOCTOR PROCEDURES
// =========================
// =========================
// AUTO SELECT DOCTOR PROCEDURES (FIXED)
// =========================
document.addEventListener('DOMContentLoaded', () => {

  if (!treatmentProcedures || treatmentProcedures.length === 0) return;

  setTimeout(() => {

    treatmentProcedures.forEach(id => {

      const checkbox = document.querySelector(
        `.procedure-checkbox[data-id="${id}"]`
      );

      if (checkbox) {
        checkbox.checked = true;
      }

    });

    updateProcedures(); // IMPORTANT SYNC

  }, 300);

});



// =========================
// DROPDOWN TOGGLE
// =========================
selectedBox.addEventListener('click', () => {
  dropdown.querySelector('.dropdown-list').classList.toggle('show');
});

// Close dropdown on outside click
document.addEventListener('click', e => {
  if (!dropdown.contains(e.target)) {
    dropdown.querySelector('.dropdown-list').classList.remove('show');
  }
});

// =========================
// CLICK HANDLING (FIXED)
// =========================
dropdown.addEventListener('click', (e) => {

  const item = e.target.closest('.dropdown-item');
  if (!item) return;

  const checkbox = item.querySelector('.procedure-checkbox');
  if (!checkbox) return;

  checkbox.checked = !checkbox.checked;
  updateProcedures();

});

// =========================
// UPDATE PROCEDURES (CORE)
// =========================
function updateProcedures() {
  let total = 0;
  let names = [];
  let ids   = [];

  selectedBox.querySelectorAll('.tag').forEach(tag => tag.remove());

  getItems().forEach(item => {
    const checkbox = item.querySelector('.procedure-checkbox');

    if (checkbox.checked) {
      total += parseFloat(checkbox.dataset.price) || 0;
      names.push(checkbox.dataset.name);
      ids.push(checkbox.dataset.id);

      const tag = document.createElement('span');
      tag.className = 'tag';
      tag.textContent = checkbox.dataset.name;

      selectedBox.insertBefore(tag, selectedBox.querySelector('.caret'));
    }
  });

  if (!ids.length) {
    selectedBox.querySelector('.placeholder').style.display = 'inline';
  } else {
    selectedBox.querySelector('.placeholder').style.display = 'none';
  }

  totalPriceInput.value = total.toFixed(2);
  descriptionField.value = names.join(' + ');
  hiddenProcedureInput.value = ids.join(',');

  calculateDues();
}

// =========================
// DUES CALCULATION
// =========================
paidAmountInput?.addEventListener('input', calculateDues);
advanceInput?.addEventListener('input', calculateDues);

function calculateDues() {
  const total = parseFloat(totalPriceInput.value) || 0;
  const paid  = parseFloat(paidAmountInput.value) || 0;
  const adv   = parseFloat(advanceInput.value) || 0;
  const dues  = total - paid - adv;

  duesInput.value = dues.toFixed(2);
}

// =========================
// FORM SUBMISSION
// =========================
invoiceForm?.addEventListener('submit', async function (e) {
  e.preventDefault();

  if (!hiddenProcedureInput.value) {
    notyf.error('Please select at least one procedure');
    return;
  }

  const total = parseFloat(totalPriceInput.value) || 0;
  if (total <= 0) {
    notyf.error('Total price must be greater than zero');
    return;
  }

  const paid = parseFloat(paidAmountInput.value) || 0;
  if (paid < 0) {
    notyf.error('Paid amount cannot be negative');
    return;
  }

  if (!paymentDateInput.value) {
    const now = new Date();
    paymentDateInput.value = now.toISOString().slice(0,16);
  }

  const formData = new FormData(invoiceForm);

  try {
    const response = await fetch(invoiceForm.action, {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.status === 'success') {
      notyf.success('Invoice submitted successfully');
      setTimeout(() => window.location.reload(), 1200);
    } else {
      notyf.error(result.error || 'Invoice submission failed');
    }

  } catch (err) {
    console.error(err);
    notyf.error('Network or server error');
  }
});

// =========================
// MODAL CONTROLS
// =========================
const modal = document.getElementById('procedureModal');
const openBtn = document.getElementById('openProcedureModal');
const closeBtn = document.getElementById('closeProcedureModal');

openBtn?.addEventListener('click', () => modal.style.display = 'flex');
closeBtn?.addEventListener('click', () => modal.style.display = 'none');

modal?.addEventListener('click', e => {
  if (e.target === modal) modal.style.display = 'none';
});

// =========================
// ADD NEW PROCEDURE
// =========================
document.getElementById('addNewProcedure')?.addEventListener('click', async () => {

  const name = document.getElementById('newProcName').value.trim();
  const dept = document.getElementById('newProcDepartment').value.trim();
  const price = document.getElementById('newProcPrice').value;

  if (!name || !dept || !price) {
    notyf.error('All fields are required');
    return;
  }

  const res = await fetch('/patient/procedures/create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, department: dept, price })
  });

  const data = await res.json();

  if (data.status !== 'success') {
    notyf.error(data.error || 'Failed to add procedure');
    return;
  }

  notyf.success('Procedure added');

  appendProcedureDropdown(data.procedure);

  document.getElementById('newProcName').value = '';
  document.getElementById('newProcDepartment').value = '';
  document.getElementById('newProcPrice').value = '';

});

// =========================
// DELETE PROCEDURE
// =========================
document.addEventListener('click', async e => {

  if (!e.target.classList.contains('delete-btn')) return;

  const tr = e.target.closest('tr');
  const id = tr.dataset.id;

  if (!confirm('Delete this procedure?')) return;

  const res = await fetch('/patient/procedures/delete', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  });

  const data = await res.json();

  if (data.status !== 'success') {
    notyf.error(data.error || 'Delete failed');
    return;
  }

  tr.remove();
  document.querySelector(`.procedure-checkbox[data-id="${id}"]`)?.closest('.dropdown-item')?.remove();

  notyf.success('Procedure deleted');

});

// =========================
// HELPER
// =========================
function appendProcedureDropdown(proc) {
  const list = document.querySelector('.dropdown-list');

  const div = document.createElement('div');
  div.className = 'dropdown-item';

  div.innerHTML = `
    <input type="checkbox" class="procedure-checkbox"
      data-id="${proc.id}"
      data-name="${proc.name}"
      data-price="${proc.price}">
    <span>${proc.name} — ${proc.department} (Rs ${proc.price})</span>
  `;

  list.appendChild(div);
}