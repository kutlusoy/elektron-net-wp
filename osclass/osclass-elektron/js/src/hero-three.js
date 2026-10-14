import {
  AdditiveBlending,
  BoxGeometry,
  BufferAttribute,
  BufferGeometry,
  Color,
  EdgesGeometry,
  Group,
  LineBasicMaterial,
  LineSegments,
  NormalBlending,
  PerspectiveCamera,
  Points,
  PointsMaterial,
  Scene,
  WebGLRenderer,
} from 'three';

function init() {
  'use strict';

  var canvas = document.querySelector('.hero-canvas');
  if (!canvas) {
    return;
  }

  var root = document.documentElement;
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var small = window.matchMedia && window.matchMedia('(max-width: 720px)').matches;

  var renderer;
  try {
    renderer = new WebGLRenderer({ canvas: canvas, alpha: true, antialias: true, powerPreference: 'low-power' });
  } catch (e) {
    window.console && console.warn('Elektron hero: WebGL unavailable', e);
    return;
  }
  canvas.setAttribute('data-engine', 'three');

  var NODE_COUNT = small ? 44 : 90;
  var LINK_DISTANCE = 2.6;
  var MAX_LINKS = NODE_COUNT * 6;
  var BOX = { x: 11, y: 6, z: 5 };

  var scene = new Scene();
  var camera = new PerspectiveCamera(55, 1, 0.1, 100);
  camera.position.z = 11;
  var group = new Group();
  scene.add(group);

  // Drifting node network
  var positions = new Float32Array(NODE_COUNT * 3);
  var velocities = new Float32Array(NODE_COUNT * 3);
  for (var i = 0; i < NODE_COUNT; i++) {
    positions[i * 3] = (Math.random() - 0.5) * BOX.x * 2;
    positions[i * 3 + 1] = (Math.random() - 0.5) * BOX.y * 2;
    positions[i * 3 + 2] = (Math.random() - 0.5) * BOX.z * 2;
    velocities[i * 3] = (Math.random() - 0.5) * 0.006;
    velocities[i * 3 + 1] = (Math.random() - 0.5) * 0.006;
    velocities[i * 3 + 2] = (Math.random() - 0.5) * 0.006;
  }

  var pointGeometry = new BufferGeometry();
  pointGeometry.setAttribute('position', new BufferAttribute(positions, 3));
  var pointMaterial = new PointsMaterial({ size: 0.12, transparent: true, opacity: 0.9, depthWrite: false, blending: AdditiveBlending });
  group.add(new Points(pointGeometry, pointMaterial));

  var linePositions = new Float32Array(MAX_LINKS * 6);
  var lineGeometry = new BufferGeometry();
  lineGeometry.setAttribute('position', new BufferAttribute(linePositions, 3));
  lineGeometry.setDrawRange(0, 0);
  var lineMaterial = new LineBasicMaterial({ transparent: true, opacity: 0.28, depthWrite: false, blending: AdditiveBlending });
  group.add(new LineSegments(lineGeometry, lineMaterial));

  // Blockchain: linked wireframe blocks
  var accent2Line = new LineBasicMaterial({ transparent: true, opacity: 0.7, depthWrite: false });
  var accent2Soft = new LineBasicMaterial({ transparent: true, opacity: 0.45, depthWrite: false });
  var chain = new Group();
  var blockEdges = new EdgesGeometry(new BoxGeometry(1.1, 1.1, 1.1));
  var BLOCKS = small ? 4 : 6;
  var blocks = [];
  for (var b = 0; b < BLOCKS; b++) {
    var block = new LineSegments(blockEdges, accent2Line);
    block.position.set((b - (BLOCKS - 1) / 2) * 2.1, Math.sin(b * 0.9) * 0.5, 0);
    chain.add(block);
    blocks.push(block);
    if (b > 0) {
      var prev = blocks[b - 1].position;
      var geometry = new BufferGeometry();
      geometry.setAttribute('position', new BufferAttribute(new Float32Array([prev.x + 0.55, prev.y, 0, block.position.x - 0.55, block.position.y, 0]), 3));
      chain.add(new LineSegments(geometry, accent2Soft));
    }
  }
  group.add(chain);

  // Escrow: buyer and seller both lock funds in a shared vault, payments travel to it
  var escrow = new Group();
  var buyer = [-2.7, 0.2, 0];
  var seller = [2.7, 0.2, 0];
  var vaultOuter = new LineSegments(new EdgesGeometry(new BoxGeometry(1.5, 1.5, 1.5)), accent2Line);
  var vaultInner = new LineSegments(new EdgesGeometry(new BoxGeometry(0.7, 0.7, 0.7)), accent2Soft);
  var vault = new Group();
  vault.add(vaultOuter);
  vault.add(vaultInner);
  escrow.add(vault);

  var partyGeometry = new BufferGeometry();
  partyGeometry.setAttribute('position', new BufferAttribute(new Float32Array(buyer.concat(seller)), 3));
  var partyMaterial = new PointsMaterial({ size: 0.42, transparent: true, opacity: 0.95, depthWrite: false, blending: AdditiveBlending });
  escrow.add(new Points(partyGeometry, partyMaterial));

  var pathGeometry = new BufferGeometry();
  pathGeometry.setAttribute('position', new BufferAttribute(new Float32Array([buyer[0], buyer[1], 0, -0.85, 0, 0, seller[0], seller[1], 0, 0.85, 0, 0]), 3));
  var pathMaterial = new LineBasicMaterial({ transparent: true, opacity: 0.5, depthWrite: false });
  escrow.add(new LineSegments(pathGeometry, pathMaterial));

  var PULSES = 6;
  var pulsePositions = new Float32Array(PULSES * 3);
  var pulseGeometry = new BufferGeometry();
  pulseGeometry.setAttribute('position', new BufferAttribute(pulsePositions, 3));
  var pulseMaterial = new PointsMaterial({ size: 0.26, transparent: true, opacity: 1, depthWrite: false, blending: AdditiveBlending });
  escrow.add(new Points(pulseGeometry, pulseMaterial));
  group.add(escrow);

  function layout(aspect) {
    if (aspect >= 1.25) {
      escrow.position.set(-8.4, 3.3, -3);
      escrow.scale.setScalar(1.3);
      chain.position.set(10.4, -3.0, -3);
      chain.scale.setScalar(0.66);
    } else {
      escrow.position.set(0, 6.1, -3);
      escrow.scale.setScalar(0.55);
      chain.position.set(0, -6.2, -3);
      chain.scale.setScalar(0.5);
    }
  }

  function readColors() {
    var style = window.getComputedStyle(root);
    var accent = new Color(style.getPropertyValue('--pal-accent').trim() || '#2da4fa');
    var second = new Color(style.getPropertyValue('--pal-accent-2').trim() || '#22d3ee');
    var light = root.getAttribute('data-scheme') === 'light' || (!root.getAttribute('data-scheme') && root.getAttribute('data-default-scheme') === 'light');
    if (light) {
      accent = new Color(style.getPropertyValue('--pal-l-link').trim() || '#0a67b4');
    }
    pointMaterial.color.copy(accent);
    lineMaterial.color.copy(accent);
    partyMaterial.color.copy(accent);
    pathMaterial.color.copy(accent);
    pulseMaterial.color.copy(second);
    accent2Line.color.copy(second);
    accent2Soft.color.copy(second);
    var blend = light ? NormalBlending : AdditiveBlending;
    pointMaterial.blending = lineMaterial.blending = partyMaterial.blending = pulseMaterial.blending = blend;
    pointMaterial.opacity = light ? 0.75 : 0.9;
    lineMaterial.opacity = light ? 0.35 : 0.28;
    pointMaterial.needsUpdate = lineMaterial.needsUpdate = partyMaterial.needsUpdate = pulseMaterial.needsUpdate = true;
  }
  readColors();
  new MutationObserver(readColors).observe(root, { attributes: true, attributeFilter: ['data-scheme', 'data-palette'] });

  function resize() {
    var width = canvas.clientWidth;
    var height = canvas.clientHeight;
    if (!width || !height) {
      return;
    }
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(width, height, false);
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    layout(camera.aspect);
  }
  resize();
  if (window.ResizeObserver) {
    new ResizeObserver(resize).observe(canvas);
  } else {
    window.addEventListener('resize', resize);
  }

  var pointer = { x: 0, y: 0, tx: 0, ty: 0 };
  if (!reduceMotion) {
    window.addEventListener('pointermove', function(event) {
      pointer.tx = (event.clientX / window.innerWidth - 0.5) * 2;
      pointer.ty = (event.clientY / window.innerHeight - 0.5) * 2;
    }, { passive: true });
  }

  function updateLinks() {
    var count = 0;
    var limit = LINK_DISTANCE * LINK_DISTANCE;
    for (var a = 0; a < NODE_COUNT && count < MAX_LINKS; a++) {
      for (var c = a + 1; c < NODE_COUNT && count < MAX_LINKS; c++) {
        var dx = positions[a * 3] - positions[c * 3];
        var dy = positions[a * 3 + 1] - positions[c * 3 + 1];
        var dz = positions[a * 3 + 2] - positions[c * 3 + 2];
        if (dx * dx + dy * dy + dz * dz < limit) {
          linePositions.set(positions.subarray(a * 3, a * 3 + 3), count * 6);
          linePositions.set(positions.subarray(c * 3, c * 3 + 3), count * 6 + 3);
          count++;
        }
      }
    }
    lineGeometry.setDrawRange(0, count * 2);
    lineGeometry.attributes.position.needsUpdate = true;
  }

  function updatePulses(time) {
    for (var p = 0; p < PULSES; p++) {
      var fromBuyer = p % 2 === 0;
      var t = ((time * 0.00035) + p / PULSES * 1.7) % 1;
      var sx = fromBuyer ? buyer[0] : seller[0];
      var ex = fromBuyer ? -0.85 : 0.85;
      pulsePositions[p * 3] = sx + (ex - sx) * t;
      pulsePositions[p * 3 + 1] = buyer[1] + (0 - buyer[1]) * t;
      pulsePositions[p * 3 + 2] = 0;
    }
    pulseGeometry.attributes.position.needsUpdate = true;
  }

  function step(time) {
    for (var n = 0; n < NODE_COUNT; n++) {
      for (var k = 0; k < 3; k++) {
        var idx = n * 3 + k;
        var bound = k === 0 ? BOX.x : k === 1 ? BOX.y : BOX.z;
        positions[idx] += velocities[idx];
        if (positions[idx] > bound || positions[idx] < -bound) {
          velocities[idx] *= -1;
        }
      }
    }
    pointGeometry.attributes.position.needsUpdate = true;
    updateLinks();
    updatePulses(time);

    pointer.x += (pointer.tx - pointer.x) * 0.04;
    pointer.y += (pointer.ty - pointer.y) * 0.04;
    group.rotation.y = time * 0.00006 + pointer.x * 0.18;
    group.rotation.x = pointer.y * 0.1;
    for (var m = 0; m < blocks.length; m++) {
      blocks[m].rotation.y = time * 0.0004 + m * 0.5;
      blocks[m].rotation.x = time * 0.0002 + m * 0.3;
    }
    vaultOuter.rotation.y = time * 0.0005;
    vaultOuter.rotation.x = time * 0.0003;
    vaultInner.rotation.y = -time * 0.0009;
    vaultInner.rotation.z = time * 0.0006;
  }

  function render(time) {
    step(time);
    renderer.render(scene, camera);
  }

  if (reduceMotion) {
    render(0);
    return;
  }

  var frame = 0;
  var visible = true;

  function loop(time) {
    frame = 0;
    if (!visible || document.hidden) {
      return;
    }
    render(time);
    frame = window.requestAnimationFrame(loop);
  }

  function start() {
    if (!frame) {
      frame = window.requestAnimationFrame(loop);
    }
  }

  if (window.IntersectionObserver) {
    new IntersectionObserver(function(entries) {
      visible = entries[0].isIntersecting;
      if (visible) {
        start();
      }
    }).observe(canvas);
  }
  document.addEventListener('visibilitychange', start);
  start();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
