(function() {
  'use strict';

  var canvas = document.querySelector('.hero-canvas');
  if (!canvas || !canvas.getContext) {
    return;
  }

  var ctx = canvas.getContext('2d');
  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var root = document.documentElement;
  var hero = canvas.parentNode;
  var nodes = [];
  var width = 0;
  var height = 0;
  var ratio = Math.min(window.devicePixelRatio || 1, 2);
  var raf = 0;
  var accent = '#2da4fa';
  var accent2 = '#22d3ee';

  function readColors() {
    var style = window.getComputedStyle(root);
    accent = style.getPropertyValue('--e-ui').trim() || accent;
    accent2 = style.getPropertyValue('--pal-accent-2').trim() || accent2;
  }

  function resize() {
    width = hero.clientWidth;
    height = hero.clientHeight;
    canvas.width = width * ratio;
    canvas.height = height * ratio;
    canvas.style.width = width + 'px';
    canvas.style.height = height + 'px';
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

    var count = Math.max(24, Math.min(90, Math.round(width * height / 14000)));
    nodes = [];
    for (var i = 0; i < count; i++) {
      nodes.push({
        x: Math.random() * width,
        y: Math.random() * height,
        vx: (Math.random() - 0.5) * 0.28,
        vy: (Math.random() - 0.5) * 0.28,
        r: 1 + Math.random() * 1.8,
        alt: Math.random() < 0.25
      });
    }
    draw();
  }

  function draw() {
    ctx.clearRect(0, 0, width, height);
    var limit = Math.min(150, width / 6);
    var i, j, a, b, dx, dy, d;

    ctx.lineWidth = 1;
    for (i = 0; i < nodes.length; i++) {
      a = nodes[i];
      for (j = i + 1; j < nodes.length; j++) {
        b = nodes[j];
        dx = a.x - b.x;
        dy = a.y - b.y;
        d = Math.sqrt(dx * dx + dy * dy);
        if (d < limit) {
          ctx.globalAlpha = (1 - d / limit) * 0.35;
          ctx.strokeStyle = accent;
          ctx.beginPath();
          ctx.moveTo(a.x, a.y);
          ctx.lineTo(b.x, b.y);
          ctx.stroke();
        }
      }
    }

    ctx.globalAlpha = 0.9;
    for (i = 0; i < nodes.length; i++) {
      a = nodes[i];
      ctx.fillStyle = a.alt ? accent2 : accent;
      ctx.beginPath();
      ctx.arc(a.x, a.y, a.r, 0, Math.PI * 2);
      ctx.fill();
    }
    ctx.globalAlpha = 1;
  }

  function step() {
    for (var i = 0; i < nodes.length; i++) {
      var n = nodes[i];
      n.x += n.vx;
      n.y += n.vy;
      if (n.x < 0 || n.x > width) {
        n.vx *= -1;
      }
      if (n.y < 0 || n.y > height) {
        n.vy *= -1;
      }
    }
    draw();
    raf = window.requestAnimationFrame(step);
  }

  function start() {
    if (!raf && !reduced) {
      raf = window.requestAnimationFrame(step);
    }
  }

  function stop() {
    window.cancelAnimationFrame(raf);
    raf = 0;
  }

  readColors();
  resize();
  start();

  window.addEventListener('resize', resize);
  document.addEventListener('elektron:palette', function() {
    readColors();
    draw();
  });
  document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
      stop();
    } else {
      start();
    }
  });

  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function(entries) {
      if (entries[0].isIntersecting) {
        start();
      } else {
        stop();
      }
    }).observe(hero);
  }
}());
