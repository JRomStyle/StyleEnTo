document.addEventListener('DOMContentLoaded', () => {
  const rows = document.querySelectorAll('tr[data-days]');
  rows.forEach(r => {
    const d = parseInt(r.getAttribute('data-days') || '0', 10);
    if (d <= 7 && d >= 0) {
      r.classList.add('ring-1', 'ring-red-700');
    }
  });
});

