(function () {
  'use strict';
  var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('[data-work-carousel]').forEach(function (carousel) {
    var track = carousel.querySelector('[data-carousel-track]');
    var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-carousel-slide]'));
    var dots = carousel.querySelector('[data-carousel-dots]');
    var toggle = carousel.querySelector('[data-carousel-toggle]');
    if (!track || slides.length < 2 || !dots || !toggle) {
      if (toggle) toggle.hidden = true;
      if (dots) dots.hidden = true;
      return;
    }
    var index = 0, timer = null, userPaused = reducedMotion, interactionPaused = false;
    var buttons = [];
    slides.forEach(function (slide, i) {
      var b = document.createElement('button'); b.type = 'button'; b.className = 'work-carousel__dot';
      b.setAttribute('aria-label', 'Show featured project ' + (i + 1));
      b.addEventListener('click', function () { show(i); stopTimer(); if (!userPaused) startTimer(); });
      dots.appendChild(b); buttons.push(b);
    });
    function show(next) {
      index = (next + slides.length) % slides.length;
      track.style.transform = 'translateX(-' + slides[index].offsetLeft + 'px)';
      slides.forEach(function (slide, i) { slide.setAttribute('aria-hidden', i === index ? 'false' : 'true'); slide.inert = i !== index; });
      buttons.forEach(function (b, i) { if (i === index) { b.setAttribute('aria-current', 'true'); } else { b.removeAttribute('aria-current'); } });
    }
    function stopTimer() { if (timer !== null) window.clearInterval(timer); timer = null; }
    function startTimer() { stopTimer(); if (!userPaused && !interactionPaused && !document.hidden) timer = window.setInterval(function () { show(index + 1); }, 5500); }
    function updateToggle() { toggle.setAttribute('aria-pressed', userPaused ? 'true' : 'false'); toggle.innerHTML = userPaused ? '<i class="fa-solid fa-play" aria-hidden="true"></i> <span>Play</span>' : '<i class="fa-solid fa-pause" aria-hidden="true"></i> <span>Pause</span>'; }
    toggle.addEventListener('click', function () { userPaused = !userPaused; updateToggle(); if (userPaused) stopTimer(); else startTimer(); });
    ['mouseenter', 'focusin', 'touchstart', 'pointerdown'].forEach(function (name) { carousel.addEventListener(name, function () { interactionPaused = true; stopTimer(); }, { passive: true }); });
    ['mouseleave', 'focusout', 'touchend', 'touchcancel', 'pointerup', 'pointercancel'].forEach(function (name) { carousel.addEventListener(name, function () { interactionPaused = false; startTimer(); }, { passive: true }); });
    document.addEventListener('visibilitychange', function () { if (document.hidden) stopTimer(); else startTimer(); });
    show(0); updateToggle(); if (reducedMotion) toggle.setAttribute('aria-label', 'Autoplay disabled because reduced motion is enabled'); startTimer();
  });
}());
