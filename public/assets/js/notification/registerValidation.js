// ─── Register Form Inline Validation ─────────────────────────────────────────

(function () {
  function showError(fieldWrapperId, errorId, message) {
    const wrapper = document.getElementById(fieldWrapperId);
    const error = document.getElementById(errorId);
    if (error) {
      error.textContent = message;
      error.classList.remove('hidden');
      error.classList.add('block');
    }
    if (wrapper) {
      wrapper.classList.remove('border-gray-300');
      wrapper.classList.add('border-red-500');
    }
  }

  function clearError(fieldWrapperId, errorId) {
    const wrapper = document.getElementById(fieldWrapperId);
    const error = document.getElementById(errorId);
    if (error) {
      error.textContent = '';
      error.classList.remove('block');
      error.classList.add('hidden');
    }
    if (wrapper) {
      wrapper.classList.remove('border-red-500');
      wrapper.classList.add('border-gray-300');
    }
  }

  window.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    if (!form) return;

    const fields = [
      { id: 'nama_lengkap',     wrapperId: 'field-nama',       errorId: 'nama_lengkap-error',     label: 'Nama Lengkap' },
      { id: 'reg_email',        wrapperId: 'field-email',      errorId: 'reg_email-error',        label: 'Email' },
      { id: 'no_hp',            wrapperId: 'field-nohp',       errorId: 'no_hp-error',            label: 'No. HP' },
      { id: 'nama_perusahaan',  wrapperId: 'field-perusahaan', errorId: 'nama_perusahaan-error',  label: 'Nama Perusahaan' },
      { id: 'domisili',         wrapperId: 'field-domisili',   errorId: 'domisili-error',         label: 'Domisili Perusahaan', isSelect: true },
      { id: 'posisi',           wrapperId: 'field-posisi',     errorId: 'posisi-error',           label: 'Posisi Pekerjaan',    isSelect: true },
      { id: 'password',         wrapperId: 'field-password',   errorId: 'password-error',         label: 'Password' },
      { id: 'confirmPassword',  wrapperId: 'field-confirm',    errorId: 'confirmPassword-error',  label: 'Konfirmasi Password' },
    ];

    // Auto-clear errors on input
    fields.forEach(f => {
      const el = document.getElementById(f.id);
      if (!el) return;

      if (f.isSelect) {
        // el-select fires 'change' when an option is picked
        el.addEventListener('change', () => clearError(f.wrapperId, f.errorId));
      } else {
        el.addEventListener('input', () => clearError(f.wrapperId, f.errorId));
      }
    });

    // Form submit
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      let isValid = true;

      fields.forEach(f => {
        const el = document.getElementById(f.id);
        if (!el) return;

        let value = '';
        if (f.isSelect) {
          value = el.value || '';
        } else {
          value = el.value.trim();
        }

        if (!value) {
          showError(f.wrapperId, f.errorId, `${f.label} tidak boleh kosong.`);
          isValid = false;
        } else {
          clearError(f.wrapperId, f.errorId);
        }
      });

      // Extra: email format
      const emailInput = document.getElementById('reg_email');
      if (emailInput && emailInput.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value.trim())) {
          showError('field-email', 'reg_email-error', 'Format email tidak valid.');
          isValid = false;
        }
      }

      // Extra: password match
      const pw = document.getElementById('password');
      const cpw = document.getElementById('confirmPassword');
      if (pw && cpw && pw.value && cpw.value && pw.value !== cpw.value) {
        showError('field-confirm', 'confirmPassword-error', 'Konfirmasi password tidak cocok.');
        isValid = false;
      }

      if (!isValid) return;

      // All valid — form would submit here
      alert('Registrasi berhasil! (dummy)');
    });
  });
})();
