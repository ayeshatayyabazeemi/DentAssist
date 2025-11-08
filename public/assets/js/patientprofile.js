document.addEventListener('DOMContentLoaded', () => {
  // ===== Delete Patient Button =====
  const deleteBtn = document.getElementById('deletePatient');
  if (deleteBtn) {
    deleteBtn.addEventListener('click', async function () {
      if (!confirm('Are you sure you want to delete this patient?')) return;

      const patientId = this.getAttribute('data-id');
      const BASE_URL = window.location.origin + '/DentAssist-mybranch/public';

      try {
        let response = await fetch(`${BASE_URL}/api/patient/${patientId}`, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' }
        });

        // fallback for browsers that cannot send DELETE
        if (response.status === 405) {
          response = await fetch(`${BASE_URL}/api/patient/delete/${patientId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
          });
        }

        const data = await response.json();
        const msg = data.message || 'Action completed';

        if (data.status === 'success') {
          alert('✅ ' + msg);
          window.location.href = `${BASE_URL}/adminDashboard`;
        } else {
          alert('❌ ' + msg);
        }
      } catch (err) {
        console.error(err);
        alert('❌ Something went wrong while deleting the patient.');
      }
    });
  }

  // ===== Appointment Modal =====
  const openBtn  = document.getElementById('openApptForm');
  const closeBtn = document.getElementById('closeApptForm');
  const modal    = document.getElementById('apptModal');

  if (openBtn && modal) {
    openBtn.addEventListener('click', () => {
      modal.style.display = 'flex';
    });
  }

  if (closeBtn && modal) {
    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });
  }

  // Clicking outside modal content closes it
  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
  }
});
