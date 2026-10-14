import {
  AdditiveBlending,
  BoxGeometry,
  BufferAttribute,
  BufferGeometry,
  CanvasTexture,
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
  Vector2,
  WebGLRenderer,
} from 'three';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';

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

  // Bloom makes every bright line, point and padlock glow in its own color (dark scheme only)
  var composer = new EffectComposer(renderer);
  composer.addPass(new RenderPass(scene, camera));
  composer.addPass(new UnrealBloomPass(new Vector2(256, 256), 0.7, 0.4, 0.22));
  composer.addPass(new OutputPass());
  var bloomOn = false;

  function setBloom(on) {
    bloomOn = on;
    renderer.setClearColor(0x000000, on ? 1 : 0);
    canvas.setAttribute('data-bloom', on ? '1' : '0');
  }
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


  // Locked and released blocks: padlocks in red (locked) and green (released) with a pulsing glow
  var RED = '#ff2d4a';
  var GREEN = '#2dff7a';
  var RED_LIGHT = '#d11a36';
  var GREEN_LIGHT = '#0f9d4a';

  function glowTexture() {
    var size = 128;
    var c = document.createElement('canvas');
    c.width = c.height = size;
    var g = c.getContext('2d');
    var gradient = g.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);
    gradient.addColorStop(0, 'rgba(255,255,255,1)');
    gradient.addColorStop(0.35, 'rgba(255,255,255,0.35)');
    gradient.addColorStop(1, 'rgba(255,255,255,0)');
    g.fillStyle = gradient;
    g.fillRect(0, 0, size, size);
    return new CanvasTexture(c);
  }
  var glowMap = glowTexture();

  var redLine = new LineBasicMaterial({ transparent: true, opacity: 0.95, depthWrite: false });
  var greenLine = new LineBasicMaterial({ transparent: true, opacity: 0.95, depthWrite: false });
  var lockBodyEdges = new EdgesGeometry(new BoxGeometry(0.6, 0.44, 0.16));

  function arcGeometry(radius, from, to, steps) {
    var pts = [];
    for (var a = 0; a < steps; a++) {
      var t0 = from + (to - from) * a / steps;
      var t1 = from + (to - from) * (a + 1) / steps;
      pts.push(Math.cos(t0) * radius, Math.sin(t0) * radius, 0, Math.cos(t1) * radius, Math.sin(t1) * radius, 0);
    }
    var geo = new BufferGeometry();
    geo.setAttribute('position', new BufferAttribute(new Float32Array(pts), 3));
    return geo;
  }
  var shackleGeometry = arcGeometry(0.17, 0, Math.PI, 14);

  function makeLock(lineMaterial, open) {
    var lock = new Group();
    var body = new LineSegments(lockBodyEdges, lineMaterial);
    body.position.y = -0.1;
    lock.add(body);

    var shackle = new Group();
    shackle.add(new LineSegments(shackleGeometry, lineMaterial));
    var legs = new BufferGeometry();
    legs.setAttribute('position', new BufferAttribute(new Float32Array([-0.17, 0, 0, -0.17, open ? 0.05 : 0.0, 0, 0.17, 0, 0, 0.17, -0.12, 0]), 3));
    shackle.add(new LineSegments(legs, lineMaterial));
    shackle.position.set(0, 0.12, 0);
    if (open) {
      shackle.position.set(-0.17, 0.2, 0);
      shackle.rotation.z = 0.7;
      shackle.position.x = 0.0;
    }
    lock.add(shackle);

    var keyhole = new BufferGeometry();
    keyhole.setAttribute('position', new BufferAttribute(new Float32Array([0, -0.06, 0.09, 0, -0.18, 0.09]), 3));
    lock.add(new LineSegments(keyhole, lineMaterial));
    return lock;
  }

  var lockLight = false;
  var lockedBlocks = [];
  var lockGlowMaterials = [];

  function makeLockedBlock(open) {
    var item = new Group();
    var lineMaterial = open ? greenLine : redLine;
    var cube = new LineSegments(blockEdges, lineMaterial);
    item.add(cube);

    var lock = makeLock(lineMaterial, open);
    lock.position.z = 0.62;
    item.add(lock);

    var glowGeometry = new BufferGeometry();
    glowGeometry.setAttribute('position', new BufferAttribute(new Float32Array([0, 0, 0.3]), 3));
    var glow = new PointsMaterial({ size: 3.4, map: glowMap, transparent: true, opacity: 0.5, depthWrite: false, blending: AdditiveBlending });
    glow.userData = { open: open };
    lockGlowMaterials.push(glow);
    item.add(new Points(glowGeometry, glow));

    item.userData = { phase: Math.random() * Math.PI * 2, open: open, glow: glow };
    lockedBlocks.push(item);
    group.add(item);
    return item;
  }

  // Spots: x, y, z and whether the lock is open. The first four also serve the narrow layout.
  var LOCK_SPOTS = {
    wide: [
      [-11.2, -2.6, -2.5, false],
      [-7.6, -4.6, -3, true],
      [9.8, 3.4, -3, true],
      [-11.4, 0.6, -4, false],
      [7.2, 5.2, -3.5, false],
      [11.6, 0.2, -4, true]
    ],
    narrow: [
      [-3.0, 6.0, -3],
      [3.0, 5.2, -3],
      [-3.0, -6.1, -3],
      [3.0, -5.3, -3]
    ]
  };
  for (var q = 0; q < LOCK_SPOTS.wide.length; q++) {
    makeLockedBlock(LOCK_SPOTS.wide[q][3]);
  }

  function placeLocks(spots, scale) {
    for (var i = 0; i < lockedBlocks.length; i++) {
      var spot = spots[i];
      var item = lockedBlocks[i];
      item.visible = !!spot;
      if (!spot) {
        continue;
      }
      item.position.set(spot[0], spot[1], spot[2]);
      item.scale.setScalar(scale);
      item.userData.baseY = spot[1];
    }
  }

  function layout(aspect) {
    if (aspect >= 1.25) {
      escrow.position.set(-8.4, 3.3, -3);
      escrow.scale.setScalar(1.3);
      chain.position.set(10.4, -3.0, -3);
      chain.scale.setScalar(0.66);
      placeLocks(LOCK_SPOTS.wide, 1.5);
    } else {
      placeLocks(LOCK_SPOTS.narrow, 0.8);
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
    var bloom = !light;
    setBloom(bloom);
    function tint(material, color, boost, opacity) {
      material.color.copy(color);
      if (bloom) {
        material.color.multiplyScalar(boost);
      }
      material.opacity = opacity;
    }
    tint(pointMaterial, accent, 1.6, light ? 0.75 : 1);
    tint(lineMaterial, accent, 1.35, light ? 0.35 : 0.6);
    tint(partyMaterial, accent, 2.0, 1);
    tint(pathMaterial, accent, 1.5, light ? 0.5 : 0.85);
    tint(pulseMaterial, second, 2.4, 1);
    tint(accent2Line, second, 1.5, light ? 0.7 : 1);
    tint(accent2Soft, second, 1.3, light ? 0.45 : 0.8);
    tint(redLine, new Color(light ? RED_LIGHT : RED), 1.7, 1);
    tint(greenLine, new Color(light ? GREEN_LIGHT : GREEN), 1.7, 1);
    lockLight = light;
    var blend = light ? NormalBlending : AdditiveBlending;
    for (var gi = 0; gi < lockGlowMaterials.length; gi++) {
      lockGlowMaterials[gi].blending = blend;
      lockGlowMaterials[gi].needsUpdate = true;
    }
    pointMaterial.blending = lineMaterial.blending = partyMaterial.blending = pulseMaterial.blending = blend;
    pointMaterial.needsUpdate = lineMaterial.needsUpdate = partyMaterial.needsUpdate = pulseMaterial.needsUpdate = true;
  }
  readColors();
  placeLocks(LOCK_SPOTS.wide, 1.5);
  new MutationObserver(function() {
    readColors();
    if (reduceMotion) {
      render(0);
    }
  }).observe(root, { attributes: true, attributeFilter: ['data-scheme', 'data-palette'] });

  function resize() {
    var width = canvas.clientWidth;
    var height = canvas.clientHeight;
    if (!width || !height) {
      return;
    }
    var ratio = Math.min(window.devicePixelRatio || 1, small ? 1 : 1.5);
    renderer.setPixelRatio(ratio);
    renderer.setSize(width, height, false);
    composer.setPixelRatio(ratio);
    composer.setSize(width, height);
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
    for (var li = 0; li < lockedBlocks.length; li++) {
      var lb = lockedBlocks[li];
      var ph = lb.userData.phase;
      lb.rotation.y = Math.sin(time * 0.0005 + ph) * 0.7;
      lb.rotation.x = Math.sin(time * 0.0004 + ph * 1.3) * 0.25;
      lb.position.y = lb.userData.baseY + Math.sin(time * 0.0007 + ph) * 0.25;
      var pulse = 0.5 + 0.5 * Math.sin(time * (lb.userData.open ? 0.0016 : 0.0027) + ph);
      lb.userData.glow.opacity = (lockLight ? 0.28 : 0.06) + pulse * (lockLight ? 0.3 : 0.14);
    }
    vaultOuter.rotation.y = time * 0.0005;
    vaultOuter.rotation.x = time * 0.0003;
    vaultInner.rotation.y = -time * 0.0009;
    vaultInner.rotation.z = time * 0.0006;
  }

  function render(time) {
    step(time);
    if (bloomOn) {
      composer.render();
    } else {
      renderer.render(scene, camera);
    }
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
