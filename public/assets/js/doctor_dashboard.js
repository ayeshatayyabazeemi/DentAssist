document.addEventListener('DOMContentLoaded', () => {

  // --- Existing modal & procedure code ---
  const modal = document.getElementById('procedureModal');
  const closeBtn = document.getElementById('closeProcedureModal');
  const form = document.getElementById('procedureForm');

  document.querySelectorAll('.appointment-row').forEach(row => {
    row.addEventListener('click', async () => {
      const appointmentId = row.dataset.appointmentId;
      try {
        const res = await fetch(`/doctor/getPatient/${appointmentId}`);
        const data = await res.json();
        if(data.status === 'success'){
          document.getElementById('procedure_appointment_id').value = appointmentId;
          document.getElementById('procedure_patient_name').textContent = data.patient.name;
          modal.style.display = 'flex';
        } else {
          alert(data.message || 'Failed to load patient');
        }
      } catch(err){
        console.error(err);
        alert('Server error');
      }
    });
  });

  closeBtn.addEventListener('click', () => modal.style.display = 'none');
  modal.addEventListener('click', e => { if(e.target === modal) modal.style.display = 'none'; });

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(form);
    try {
      const res = await fetch('/doctor/saveProcedure', { method: 'POST', body: formData });
      const data = await res.json();
      if(data.status === 'success'){
        alert('Procedure saved!');
        modal.style.display = 'none';
        window.location.reload();
      } else {
        alert(data.message || 'Failed to save procedure');
      }
    } catch(err){
      console.error(err);
      alert('Server error');
    }
  });

  // --- NEW: Mark Completed code ---
  document.querySelectorAll('.complete-form').forEach(form => {
        form.addEventListener('submit', async function(e){
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const res = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });

                if(res.ok){
                    const data = await res.json();
                    if(data.status === 'success'){
                        // Remove the row from table
                        this.closest('tr').remove();

                        // Update Pending count
                        const pendingElem = document.querySelector('.kpi-card.border-primary .value');
                        pendingElem.textContent = parseInt(pendingElem.textContent) - 1;

                        // Optional: Update Completed count
                        const completedElem = document.querySelector('.kpi-card.border-success .value');
                        completedElem.textContent = parseInt(completedElem.textContent) + 1;
                    } else {
                        alert(data.message || 'Failed to update status');
                    }
                } else {
                    alert('Server error');
                }

            } catch(err){
                console.error(err);
                alert('Server error');
            }
        });
    });

});