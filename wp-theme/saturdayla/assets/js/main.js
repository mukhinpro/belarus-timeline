(function () {
  // Mobile nav
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      document.body.style.overflow = open ? 'hidden' : '';
    });
  }

  // Scroll reveal
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var rv = document.querySelectorAll('.reveal');
  if (reduce || !('IntersectionObserver' in window)) { rv.forEach(function (el) { el.classList.add('in'); }); }
  else {
    var io = new IntersectionObserver(function (es) { es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } }); }, { threshold: 0.1, rootMargin: '0px 0px -6% 0px' });
    rv.forEach(function (el) { io.observe(el); });
  }

  // Portfolio filter (client-side, for the contact sheet)
  var filters = document.querySelectorAll('[data-filter]');
  var items = document.querySelectorAll('.sheet__item[data-type]');
  filters.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      if (btn.tagName === 'A') e.preventDefault();
      var type = btn.getAttribute('data-filter');
      filters.forEach(function (b) { b.classList.toggle('is-active', b === btn); });
      items.forEach(function (it) {
        var show = type === 'all' || it.getAttribute('data-type').split(' ').indexOf(type) !== -1;
        it.style.display = show ? '' : 'none';
      });
    });
  });

  // Lightbox
  var links = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox]'));
  if (!links.length) return;
  var box = document.createElement('div');
  box.className = 'lightbox';
  box.setAttribute('role', 'dialog');
  box.setAttribute('aria-modal', 'true');
  box.innerHTML =
    '<button class="lightbox__btn lightbox__close" aria-label="Close">×</button>' +
    '<button class="lightbox__btn lightbox__prev" aria-label="Previous">‹</button>' +
    '<img alt="">' +
    '<button class="lightbox__btn lightbox__next" aria-label="Next">›</button>' +
    '<div class="lightbox__cap"></div>';
  document.body.appendChild(box);
  var img = box.querySelector('img');
  var cap = box.querySelector('.lightbox__cap');
  var idx = 0;

  function show(i) {
    idx = (i + links.length) % links.length;
    var a = links[idx];
    img.src = a.getAttribute('href');
    img.alt = a.getAttribute('data-alt') || '';
    cap.textContent = a.getAttribute('data-caption') || '';
    box.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    // preload neighbours
    [idx + 1, idx - 1].forEach(function (n) {
      var p = new Image(); p.src = links[(n + links.length) % links.length].getAttribute('href');
    });
  }
  function close() { box.classList.remove('is-open'); document.body.style.overflow = ''; }

  links.forEach(function (a, i) {
    a.addEventListener('click', function (e) { e.preventDefault(); show(i); });
  });
  box.querySelector('.lightbox__close').addEventListener('click', close);
  box.querySelector('.lightbox__prev').addEventListener('click', function () { show(idx - 1); });
  box.querySelector('.lightbox__next').addEventListener('click', function () { show(idx + 1); });
  box.addEventListener('click', function (e) { if (e.target === box) close(); });
  document.addEventListener('keydown', function (e) {
    if (!box.classList.contains('is-open')) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') show(idx - 1);
    if (e.key === 'ArrowRight') show(idx + 1);
  });
  // touch swipe
  var sx = 0;
  box.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
  box.addEventListener('touchend', function (e) {
    var dx = e.changedTouches[0].clientX - sx;
    if (Math.abs(dx) > 50) show(dx < 0 ? idx + 1 : idx - 1);
  });
})();
