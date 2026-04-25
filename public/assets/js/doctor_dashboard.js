document.addEventListener("DOMContentLoaded", () => {

const notyf = new Notyf({ duration: 3000, position: { x: 'center', y: 'top' } });

function applyStatusStyle(select, status) {
  const styles = {
    scheduled: { bg: '#E5E7EB', color: '#374151' },
    checked_in: { bg: '#BFDBFE', color: '#1E40AF' },
    in_progress: { bg: '#FED7AA', color: '#C2410C' },
    completed: { bg: '#BBF7D0', color: '#166534' },
    no_show: { bg: '#FECACA', color: '#991B1B' },
    cancelled: { bg: '#CBD5E1', color: '#334155' },
  };

  const style = styles[status] || { bg: '#D1D5DB', color: '#111' };
  select.style.background = style.bg;
  select.style.color = style.color;
}

document.querySelectorAll('.status-select').forEach(select => {

  applyStatusStyle(select, select.value);

  select.addEventListener('change', async () => {

    const appointmentId = select.dataset.appointment;
    const newStatus = select.value;

    applyStatusStyle(select, newStatus);

    try {
      const res = await fetch('/doctor/updateStatus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          appointment_id: appointmentId,
          status: newStatus
        })
      });

      const data = await res.json();

      if (data.status === 'success') {
        if (newStatus === 'completed') {
          location.reload(); // move to completed list
        }
      }

    } catch (err) {
      notyf.error('Server error');
    }
  });
});

});