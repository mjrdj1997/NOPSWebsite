/* NOPS preview — small interactions */
(function () {
  // Mobile nav
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('open');
      toggle.classList.toggle('open');
    });
    nav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        // Snap the drawer shut with NO animation before we navigate away.
        // Mobile browsers hold the outgoing page's last painted frame until
        // the next page is ready; a slide-out mid-transition froze a half-open
        // drawer + dim backdrop on screen — the "blank overlay" flash.
        nav.style.transition = 'none';
        nav.classList.remove('open');
        toggle.classList.remove('open');
      });
    });
  }

  // Current year
  document.querySelectorAll('[data-year]').forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });

  // Reveal on scroll
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    });
  }, { threshold: 0.12 });
  document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });

  // Demo form handler (preview only — no backend)
  document.querySelectorAll('form[data-demo]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      e.preventDefault();
      var note = f.querySelector('.form-status');
      if (note) {
        note.textContent = 'Thank you — this is a design preview, so no message was actually sent. On the live WordPress site this will deliver to Kari directly.';
        note.style.color = '#2f7d4f';
      }
      f.reset();
    });
  });
})();
