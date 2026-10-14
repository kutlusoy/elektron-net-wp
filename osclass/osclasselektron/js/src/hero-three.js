import {
  AdditiveBlending,
  BoxGeometry,
  BufferAttribute,
  BufferGeometry,
  CanvasTexture,
  Color,
  InstancedBufferAttribute,
  InstancedBufferGeometry,
  Mesh,
  PlaneGeometry,
  ShaderMaterial,
  EdgesGeometry,
  Group,
  LineBasicMaterial,
  LineSegments,
  NormalBlending,
  PerspectiveCamera,
  Points,
  PointsMaterial,
  Scene,
  Sprite,
  SpriteMaterial,
  SRGBColorSpace,
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
  var prefersReduced = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  var alwaysAnimate = root.getAttribute('data-motion') !== 'respect';
  // Devices that ask for less motion still get the scene, at half speed, unless the site owner chose to respect the setting
  var reduceMotion = prefersReduced && !alwaysAnimate;
  var speed = prefersReduced ? 0.5 : 1;
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
  var GLOW = {
    off:    { strength: 0,   boost: 1,    halo: 0 },
    subtle: { strength: 0.3, boost: 0.8,  halo: 0.5 },
    medium: { strength: 0.5, boost: 1,    halo: 1 },
    strong: { strength: 0.85, boost: 1.3, halo: 2 }
  };
  var glow = GLOW[root.getAttribute('data-glow')] || GLOW.medium;

  var composer = new EffectComposer(renderer);
  composer.addPass(new RenderPass(scene, camera));
  composer.addPass(new UnrealBloomPass(new Vector2(256, 256), glow.strength, 0.35, 0.32));
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
  var nodePoints = new Points(pointGeometry, pointMaterial);
  group.add(nodePoints);

  var linePositions = new Float32Array(MAX_LINKS * 6);
  var lineGeometry = new BufferGeometry();
  lineGeometry.setAttribute('position', new BufferAttribute(linePositions, 3));
  lineGeometry.setDrawRange(0, 0);
  var lineMaterial = new LineBasicMaterial({ transparent: true, opacity: 0.28, depthWrite: false, blending: AdditiveBlending });
  var nodeLines = new LineSegments(lineGeometry, lineMaterial);
  group.add(nodeLines);

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


  // Terminal palette: fast streams of terminal text flow upward in perspective.
  // Most characters are pale, a few glow and spell hidden words, like a cipher.
  var ATLAS_COLS = 16;
  var ATLAS_ROWS = 6;
  var GLYPHS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz$#%&*+=<>/\\|{}[]();:_-~@!?^';
  var WORDS = ['ELEKTRON', 'ESCROW', 'TRUSTLESS', '2 OF 2', 'SIGN', 'VERIFY', 'NO KEYS', 'BLOCKS', 'MINING', 'LOCKTIME', 'PSBT', 'HASH'];
  var RAIN_COLS = small ? 14 : 34;
  var RAIN_ROWS = small ? 24 : 34;
  var RAIN_ROW_H = 0.62;
  var RAIN_H = RAIN_ROWS * RAIN_ROW_H;

  function glyphAtlas() {
    var cell = 64;
    var c = document.createElement('canvas');
    c.width = ATLAS_COLS * cell;
    c.height = ATLAS_ROWS * cell;
    var g = c.getContext('2d');
    g.fillStyle = '#fff';
    g.textAlign = 'center';
    g.textBaseline = 'middle';
    g.font = '700 46px "Share Tech Mono", ui-monospace, Menlo, Consolas, "Courier New", monospace';
    for (var i = 0; i < GLYPHS.length; i++) {
      g.fillText(GLYPHS.charAt(i), (i % ATLAS_COLS + 0.5) * cell, (Math.floor(i / ATLAS_COLS) + 0.5) * cell + 3);
    }
    return c;
  }

  var atlasTexture = new CanvasTexture(glyphAtlas());
  atlasTexture.colorSpace = SRGBColorSpace;
  if (document.fonts && document.fonts.load) {
    document.fonts.load('700 46px "Share Tech Mono"').then(function() {
      atlasTexture.image = glyphAtlas();
      atlasTexture.needsUpdate = true;
    }).catch(function() {});
  }

  var rainCount = RAIN_COLS * RAIN_ROWS;
  var rainBase = new Float32Array(rainCount * 3);
  var rainGlyph = new Float32Array(rainCount);
  var rainBright = new Float32Array(rainCount);
  var rainSeed = new Float32Array(rainCount);
  var rainSpeed = new Float32Array(rainCount);

  function randomGlyph() {
    return Math.floor(Math.random() * GLYPHS.length);
  }

  for (var rc = 0; rc < RAIN_COLS; rc++) {
    var word = WORDS[Math.floor(Math.random() * WORDS.length)];
    var stride = 2 + Math.floor(Math.random() * 3);
    var wordStart = Math.floor(Math.random() * 6);
    var colX = (rc - (RAIN_COLS - 1) / 2) * 1.0 + (Math.random() - 0.5) * 0.25;
    var colZ = (Math.random() - 0.5) * 6;
    var colSpeed = 3.2 + Math.random() * 7;
    for (var rr = 0; rr < RAIN_ROWS; rr++) {
      var idx = rc * RAIN_ROWS + rr;
      rainBase[idx * 3] = colX;
      rainBase[idx * 3 + 1] = (rr - RAIN_ROWS / 2) * RAIN_ROW_H;
      rainBase[idx * 3 + 2] = colZ;
      rainSeed[idx] = Math.random();
      rainSpeed[idx] = colSpeed;
      rainGlyph[idx] = randomGlyph();
      rainBright[idx] = Math.random() < 0.025 ? 0.55 : 0;
      var k = rr - wordStart;
      if (k >= 0 && k % stride === 0 && k / stride < word.length) {
        var letter = word.charAt(k / stride);
        var pos = GLYPHS.indexOf(letter);
        if (pos >= 0) {
          rainGlyph[idx] = pos;
          rainBright[idx] = 1;
        }
      }
    }
  }

  var rainGeometry = new InstancedBufferGeometry();
  var quad = new PlaneGeometry(1, 1);
  rainGeometry.index = quad.index;
  rainGeometry.setAttribute('position', quad.getAttribute('position'));
  rainGeometry.setAttribute('uv', quad.getAttribute('uv'));
  rainGeometry.setAttribute('aBase', new InstancedBufferAttribute(rainBase, 3));
  var glyphAttribute = new InstancedBufferAttribute(rainGlyph, 1);
  rainGeometry.setAttribute('aGlyph', glyphAttribute);
  rainGeometry.setAttribute('aBright', new InstancedBufferAttribute(rainBright, 1));
  rainGeometry.setAttribute('aSeed', new InstancedBufferAttribute(rainSeed, 1));
  rainGeometry.setAttribute('aSpeed', new InstancedBufferAttribute(rainSpeed, 1));
  rainGeometry.instanceCount = rainCount;

  var rainMaterial = new ShaderMaterial({
    transparent: true,
    depthWrite: false,
    blending: AdditiveBlending,
    uniforms: {
      uTime: { value: 0 },
      uH: { value: RAIN_H },
      uMaxX: { value: 18 },
      uAtlas: { value: atlasTexture },
      uDim: { value: new Color('#8b979b') },
      uBright: { value: new Color('#5af78e') },
      uDimAlpha: { value: 0.2 },
      uBoost: { value: 1 }
    },
    vertexShader: [
      'attribute vec3 aBase; attribute float aGlyph; attribute float aBright; attribute float aSeed; attribute float aSpeed;',
      'uniform float uTime; uniform float uH; uniform float uMaxX;',
      'varying vec2 vUv; varying float vBright; varying float vFade; varying float vSeed;',
      'void main() {',
      '  float y = mod(aBase.y + uTime * aSpeed + uH * 0.5, uH) - uH * 0.5;',
      '  vec3 pos = vec3(aBase.x, y, aBase.z) + vec3(position.x * 0.5, position.y * 0.62, 0.0);',
      '  if (abs(aBase.x) > uMaxX) { pos = vec3(0.0, 0.0, 1000.0); }',
      '  vFade = smoothstep(-uH * 0.5, -uH * 0.5 + 3.0, y) * (1.0 - smoothstep(uH * 0.5 - 8.0, uH * 0.5, y));',
      '  float col = mod(aGlyph, 16.0);',
      '  float row = floor(aGlyph / 16.0);',
      '  vUv = vec2((col + uv.x) / 16.0, 1.0 - (row + 1.0) / 6.0 + uv.y / 6.0);',
      '  vBright = aBright; vSeed = aSeed;',
      '  gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);',
      '}'
    ].join('\n'),
    fragmentShader: [
      'uniform sampler2D uAtlas; uniform vec3 uDim; uniform vec3 uBright; uniform float uDimAlpha; uniform float uBoost; uniform float uTime;',
      'varying vec2 vUv; varying float vBright; varying float vFade; varying float vSeed;',
      'void main() {',
      '  float a = texture2D(uAtlas, vUv).a;',
      '  float pulse = vBright > 0.5 ? 0.78 + 0.22 * sin(uTime * 5.0 + vSeed * 40.0) : 1.0;',
      '  vec3 col = mix(uDim, uBright * uBoost, vBright);',
      '  float alpha = a * vFade * mix(uDimAlpha, 1.0, vBright) * pulse;',
      '  gl_FragColor = vec4(col, alpha);',
      '  #include <colorspace_fragment>',
      '}'
    ].join('\n')
  });

  var rainMesh = new Mesh(rainGeometry, rainMaterial);
  rainMesh.frustumCulled = false;
  var rain = new Group();
  rain.add(rainMesh);
  rain.position.set(0, -0.5, -7);
  rain.rotation.x = -0.65;
  rain.visible = false;
  scene.add(rain);
  var terminalMode = false;
  var lastMutation = 0;

  function mutateRain() {
    for (var m = 0; m < 4; m++) {
      var i = Math.floor(Math.random() * rainCount);
      if (rainBright[i] === 0) {
        rainGlyph[i] = randomGlyph();
      }
    }
    glyphAttribute.needsUpdate = true;
  }

  // Padlock icons are drawn on a canvas as flat, rounded icons, or taken from the system emoji font
  var useEmoji = root.getAttribute('data-lock') === 'emoji';
  var lockTextures = {};

  function roundRect(g, x, y, w, h, r) {
    g.beginPath();
    g.moveTo(x + r, y);
    g.arcTo(x + w, y, x + w, y + h, r);
    g.arcTo(x + w, y + h, x, y + h, r);
    g.arcTo(x, y + h, x, y, r);
    g.arcTo(x, y, x + w, y, r);
    g.closePath();
  }

  function emojiTexture(open) {
    var size = 256;
    var c = document.createElement('canvas');
    c.width = c.height = size;
    var g = c.getContext('2d');
    g.textAlign = 'center';
    g.textBaseline = 'middle';
    g.font = '190px "Apple Color Emoji","Segoe UI Emoji","Noto Color Emoji",sans-serif';
    g.fillText(open ? '🔓' : '🔒', size / 2, size / 2 + 10);
    var data = g.getImageData(0, 0, size, size).data;
    var colorful = 0;
    for (var i = 0; i < data.length; i += 16) {
      if (data[i + 3] > 128 && (Math.abs(data[i] - data[i + 1]) > 24 || Math.abs(data[i + 1] - data[i + 2]) > 24)) {
        colorful++;
      }
    }
    return colorful > 120 ? c : null;
  }

  function iconCanvas(open, palette) {
    var size = 256;
    var c = document.createElement('canvas');
    c.width = c.height = size;
    var g = c.getContext('2d');
    g.lineCap = 'round';
    g.lineJoin = 'round';

    // shackle
    g.strokeStyle = palette.metal;
    g.lineWidth = 22;
    g.beginPath();
    g.moveTo(88, 120);
    g.lineTo(88, 88);
    g.arc(128, 88, 40, Math.PI, 0);
    g.lineTo(168, open ? 52 : 120);
    g.stroke();
    g.strokeStyle = 'rgba(255,255,255,0.35)';
    g.lineWidth = 6;
    g.beginPath();
    g.moveTo(80, 112);
    g.lineTo(80, 88);
    g.arc(128, 88, 48, Math.PI, Math.PI * 1.55);
    g.stroke();

    // body
    var body = g.createLinearGradient(0, 112, 0, 232);
    body.addColorStop(0, palette.light);
    body.addColorStop(1, palette.base);
    g.fillStyle = body;
    roundRect(g, 46, 112, 164, 120, 30);
    g.fill();
    g.strokeStyle = palette.dark;
    g.lineWidth = 5;
    g.stroke();

    // highlight
    g.fillStyle = 'rgba(255,255,255,0.22)';
    roundRect(g, 58, 120, 140, 30, 15);
    g.fill();

    // keyhole
    g.fillStyle = palette.dark;
    g.beginPath();
    g.arc(128, 168, 15, 0, Math.PI * 2);
    g.fill();
    g.beginPath();
    g.moveTo(120, 174);
    g.lineTo(136, 174);
    g.lineTo(140, 204);
    g.lineTo(116, 204);
    g.closePath();
    g.fill();
    return c;
  }

  var PALETTES = {
    red:         { base: '#ff2d4a', light: '#ff8a9a', dark: '#8e0c22', metal: '#ffd3d9' },
    green:       { base: '#1fd76a', light: '#8dffb9', dark: '#0a6a31', metal: '#d6ffe6' },
    redLight:    { base: '#d11a36', light: '#ee5b72', dark: '#750a1c', metal: '#c8485f' },
    greenLight:  { base: '#0f9d4a', light: '#4fd286', dark: '#06592a', metal: '#2f9a63' }
  };

  function lockTexture(open, light) {
    var key = (open ? 'o' : 'c') + (light ? 'l' : 'd') + (useEmoji ? 'e' : 'i');
    if (!lockTextures[key]) {
      var canvasSource = useEmoji ? emojiTexture(open) : null;
      if (!canvasSource) {
        canvasSource = iconCanvas(open, PALETTES[(open ? 'green' : 'red') + (light ? 'Light' : '')]);
      }
      var texture = new CanvasTexture(canvasSource);
      texture.colorSpace = SRGBColorSpace;
      lockTextures[key] = texture;
    }
    return lockTextures[key];
  }

  var lockLight = false;
  var lockSprites = [];
  var lockedBlocks = [];
  var lockGlowMaterials = [];

  function makeLockedBlock(open) {
    var item = new Group();
    var lineMaterial = open ? greenLine : redLine;
    var cube = new LineSegments(blockEdges, lineMaterial);
    item.add(cube);

    var sprite = new Sprite(new SpriteMaterial({ map: lockTexture(open, false), transparent: true, depthWrite: false }));
    sprite.scale.set(1.0, 1.0, 1);
    sprite.position.z = 0.1;
    lockSprites.push(sprite);
    item.add(sprite);

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
    var bloom = !light && glow.strength > 0;
    terminalMode = root.getAttribute('data-palette') === 'terminal';
    rain.visible = terminalMode;
    nodePoints.visible = nodeLines.visible = !terminalMode;
    rainMaterial.uniforms.uBright.value.set(light ? (style.getPropertyValue('--pal-l-link').trim() || '#0a6b32') : (style.getPropertyValue('--pal-accent').trim() || '#5af78e'));
    rainMaterial.uniforms.uDim.value.set(light ? (style.getPropertyValue('--pal-l-muted').trim() || '#4c5a54') : (style.getPropertyValue('--pal-muted').trim() || '#8b979b'));
    rainMaterial.uniforms.uDimAlpha.value = light ? 0.34 : 0.2;
    rainMaterial.uniforms.uBoost.value = bloom ? Math.max(1, 1.7 * glow.boost) : 1;
    rainMaterial.blending = light ? NormalBlending : AdditiveBlending;
    rainMaterial.needsUpdate = true;
    setBloom(bloom);
    function tint(material, color, boost, opacity) {
      material.color.copy(color);
      if (bloom) {
        material.color.multiplyScalar(Math.max(1, boost * glow.boost));
      }
      material.opacity = opacity;
    }
    tint(pointMaterial, accent, 1.4, light ? 0.75 : 1);
    tint(lineMaterial, accent, 1.18, light ? 0.35 : 0.6);
    tint(partyMaterial, accent, 1.8, 1);
    tint(pathMaterial, accent, 1.3, light ? 0.5 : 0.85);
    tint(pulseMaterial, second, 2.1, 1);
    tint(accent2Line, second, 1.3, light ? 0.7 : 1);
    tint(accent2Soft, second, 1.15, light ? 0.45 : 0.8);
    tint(redLine, new Color(light ? RED_LIGHT : RED), 2.0, 1);
    tint(greenLine, new Color(light ? GREEN_LIGHT : GREEN), 1.6, 1);
    lockLight = light;
    for (var si = 0; si < lockSprites.length; si++) {
      var spriteMaterial = lockSprites[si].material;
      spriteMaterial.map = lockTexture(lockedBlocks[si].userData.open, light);
      spriteMaterial.color.setScalar(bloom ? Math.max(1, 1.25 * glow.boost) : 1);
      spriteMaterial.needsUpdate = true;
    }
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
    rainMaterial.uniforms.uMaxX.value = Math.min(18, 10 * camera.aspect + 1);
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

  function step(rawTime) {
    var time = rawTime * speed;
    for (var n = 0; n < NODE_COUNT; n++) {
      for (var k = 0; k < 3; k++) {
        var idx = n * 3 + k;
        var bound = k === 0 ? BOX.x : k === 1 ? BOX.y : BOX.z;
        positions[idx] += velocities[idx] * speed;
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
    if (terminalMode) {
      rainMaterial.uniforms.uTime.value = time * 0.001;
      rain.rotation.y = pointer.x * 0.12;
      rain.rotation.x = -0.65 + pointer.y * 0.06;
      if (Math.floor(time / 90) !== lastMutation) {
        lastMutation = Math.floor(time / 90);
        mutateRain();
      }
    }
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
      lb.userData.glow.opacity = lockLight ? 0.28 + pulse * 0.3 : (0.045 + pulse * 0.11) * glow.halo;
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
