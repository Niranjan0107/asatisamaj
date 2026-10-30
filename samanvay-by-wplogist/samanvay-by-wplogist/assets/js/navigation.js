(function () {
  const nav = document.getElementById('site-navigation');
  if (!nav) return;
  const button = nav.querySelector('.menu-toggle');
  const menu = nav.querySelector('ul');
  if (!button || !menu) return;
  button.addEventListener('click', function () {
    const open = nav.classList.toggle('toggled');
    button.setAttribute('aria-expanded', String(open));
  });
  document.addEventListener('click', function (event) {
    if (!nav.contains(event.target)) {
      nav.classList.remove('toggled');
      button.setAttribute('aria-expanded', 'false');
    }
  });
})();
