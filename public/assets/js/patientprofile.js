document.addEventListener('DOMContentLoaded', () => {
  const deleteBtn = document.getElementById('deletePatient');
  if (!deleteBtn) return;

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
});
