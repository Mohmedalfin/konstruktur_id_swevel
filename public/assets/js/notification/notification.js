// Dummy credentials for login validation
const DUMMY_USERS = [
  { email: 'admin@kontraktor.id', password: 'admin123' },
  { email: 'user@kontraktor.id', password: 'user123' },
];

// ─── Inline Validation Helpers ───────────────────────────────────────────────

function showFieldError(inputId, errorId, message) {
  const input = document.getElementById(inputId);
  const error = document.getElementById(errorId);
  if (!input || !error) return;

  // Show error message
  error.textContent = message;
  error.classList.remove('hidden');
  error.classList.add('block');

  // Add red border to input
  input.classList.remove('border-gray-300', 'focus:border-brand-dark');
  input.classList.add('border-red-500', 'focus:border-red-500');
}

function clearFieldError(inputId, errorId) {
  const input = document.getElementById(inputId);
  const error = document.getElementById(errorId);
  if (!input || !error) return;

  // Hide error message
  error.textContent = '';
  error.classList.remove('block');
  error.classList.add('hidden');

  // Restore normal border
  input.classList.remove('border-red-500', 'focus:border-red-500');
  input.classList.add('border-gray-300', 'focus:border-brand-dark');
}

// ─── Toast Notification (for wrong credentials only) ─────────────────────────

function showToast(type, title, message) {
  const isSuccess = type === 'success';

  const successIcon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
  const errorIcon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';

  const borderColor = isSuccess ? '#bbf7d0' : '#fecaca';
  const bgColor = isSuccess ? '#f0fdf4' : '#fef2f2';
  const titleColor = isSuccess ? '#166534' : '#991b1b';
  const msgColor = isSuccess ? '#15803d' : '#b91c1c';
  const icon = isSuccess ? successIcon : errorIcon;
  const closeColor = isSuccess ? '#16a34a' : '#dc2626';

  const toastNode = document.createElement('div');
  toastNode.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 320px;
    max-width: 400px;
    background: ${bgColor} !important;
    background-image: none !important;
    border: 1px solid ${borderColor};
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1), 0 4px 10px rgba(0,0,0,0.05);
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: 'Inter', sans-serif;
  `;

  toastNode.innerHTML = `
    <div style="flex-shrink:0; margin-top:2px;">${icon}</div>
    <div style="flex:1; min-width:0;">
      <div style="font-weight:600; font-size:14px; color:${titleColor}; margin-bottom:4px;">${title}</div>
      <div style="font-size:13px; color:${msgColor}; line-height:1.4;">${message}</div>
    </div>
    <button onclick="this.parentElement.style.opacity='0';this.parentElement.style.transform='translateX(100%)';setTimeout(()=>this.parentElement.remove(),400)" style="flex-shrink:0; background:none; border:none; cursor:pointer; padding:2px; color:${closeColor}; opacity:0.6; transition:opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    </button>
  `;

  document.body.appendChild(toastNode);

  // Animate in
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      toastNode.style.opacity = '1';
      toastNode.style.transform = 'translateX(0)';
    });
  });

  // Auto dismiss
  setTimeout(() => {
    toastNode.style.opacity = '0';
    toastNode.style.transform = 'translateX(100%)';
    setTimeout(() => toastNode.remove(), 400);
  }, 4000);
}

// ─── Form Validation & Submit Handler ────────────────────────────────────────

window.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.querySelector('form');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');

  if (!loginForm) return;

  // Auto-clear inline errors on typing
  if (emailInput) {
    emailInput.addEventListener('input', () => {
      clearFieldError('email', 'email-error');
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener('input', () => {
      clearFieldError('password', 'password-error');
    });
  }

  // Form submit handler
  loginForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const email = emailInput ? emailInput.value.trim() : '';
    const password = passwordInput ? passwordInput.value : '';
    let isValid = true;

    if (!email) {
      showFieldError('email', 'email-error', 'Email tidak boleh kosong.');
      isValid = false;
    } else {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        showFieldError('email', 'email-error', 'Format email tidak valid.');
        isValid = false;
      }
    }

    if (!password) {
      showFieldError('password', 'password-error', 'Password tidak boleh kosong.');
      isValid = false;
    }

    if (!isValid) return;

    const user = DUMMY_USERS.find(u => u.email === email);

    if (!user) {
      showToast('error', 'Login Gagal', 'Email tidak terdaftar. Silakan periksa kembali email Anda.');
      return;
    }

    if (user.password !== password) {
      showToast('error', 'Login Gagal', 'Password salah. Silakan coba lagi.');
      return;
    }

    showToast('success', 'Login Berhasil', 'Selamat datang kembali! Anda akan dialihkan...');
  });
});
