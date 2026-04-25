document.addEventListener("DOMContentLoaded", () => {

  const tagsBox = document.getElementById('procedureTags');

  const checkboxes = document.querySelectorAll('input[name="procedure_id[]"]');

  checkboxes.forEach(cb => {
    cb.addEventListener('change', updateProcedureTags);
  });

  function updateProcedureTags() {
    tagsBox.innerHTML = '';

    const selected = document.querySelectorAll('input[name="procedure_id[]"]:checked');

    if (selected.length === 0) {
      tagsBox.innerHTML = `<span class="placeholder">Select procedures</span>`;
      return;
    }

    selected.forEach(cb => {
      const tag = document.createElement('span');
      tag.classList.add('tag');
      tag.textContent = cb.nextElementSibling.textContent;
      tagsBox.appendChild(tag);
    });
  }

});