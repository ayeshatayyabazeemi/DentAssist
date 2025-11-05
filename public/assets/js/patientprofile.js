document.addEventListener('DOMContentLoaded', () => {
  const openBtn  = document.getElementById('openApptForm');
  const closeBtn = document.getElementById('closeApptForm');
  const modal    = document.getElementById('apptModal');
  console.log('llllllllllllllllllllll')

  if (openBtn && modal) {
    openBtn.addEventListener('click', () => {
          console.log('llllllllllllllllllllll')

      modal.style.display = 'flex';
    });
  }

  if (closeBtn && modal) {
    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });
  }

  // Also clicking outside modal content should close it
  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
  }
});
