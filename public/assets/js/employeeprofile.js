document.addEventListener('DOMContentLoaded', function () {
  const changePwdBtn = document.getElementById('changePwdBtn');
  const changePwdModal = document.getElementById('changePwdModal');
  const closeChangePwd = document.getElementById('closeChangePwd');
  const changePasswordForm = document.getElementById('changePasswordForm');

  if (changePwdBtn && changePwdModal) {
    changePwdBtn.addEventListener('click', function () {
      changePwdModal.classList.add('show');
      changePwdModal.setAttribute('aria-hidden', 'false');
    });
  }

  if (closeChangePwd && changePwdModal) {
    closeChangePwd.addEventListener('click', function () {
      hideModal();
    });
  }

  // hide on overlay click
  if (changePwdModal) {
    changePwdModal.addEventListener('click', function (e) {
      if (e.target === changePwdModal) {
        hideModal();
      }
    });
  }

  function hideModal() {
    changePwdModal.classList.remove('show');
    changePwdModal.setAttribute('aria-hidden', 'true');
    // Optionally reset form
    if (changePasswordForm) {
      changePasswordForm.reset();
    }
  }

  if (changePasswordForm) {
    changePasswordForm.addEventListener('submit', function (e) {
      const newPwd  = changePasswordForm.querySelector('#new_password').value;
      const confirmPwd = changePasswordForm.querySelector('#confirm_new_password').value;

      if (newPwd !== confirmPwd) {
        e.preventDefault();
        alert('New password and confirmation do not match.');
        return false;
      }
      // Optionally add password strength check
      return true;
    });
  }
});