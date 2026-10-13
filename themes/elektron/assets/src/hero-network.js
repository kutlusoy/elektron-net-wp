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

( function () {
	'use strict';

	var canvas = document.querySelector( '.hero-canvas' );
	if ( ! canvas ) {
		return;
	}

	var root = document.documentElement;
	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var small = window.matchMedia && window.matchMedia( '(max-width: 720px)' ).matches;

	var renderer;
	try {
		renderer = new WebGLRenderer( { canvas: canvas, alpha: true, antialias: true, powerPreference: 'low-power' } );
	} catch ( e ) {
		return;
	}

	var NODE_COUNT = small ? 48 : 96;
	var LINK_DISTANCE = 2.6;
	var MAX_LINKS = NODE_COUNT * 6;
	var BOX = { x: 11, y: 6, z: 5 };

	var scene = new Scene();
	var camera = new PerspectiveCamera( 55, 1, 0.1, 100 );
	camera.position.z = 11;
	var group = new Group();
	scene.add( group );

	var positions = new Float32Array( NODE_COUNT * 3 );
	var velocities = new Float32Array( NODE_COUNT * 3 );
	for ( var i = 0; i < NODE_COUNT; i++ ) {
		positions[ i * 3 ] = ( Math.random() - 0.5 ) * BOX.x * 2;
		positions[ i * 3 + 1 ] = ( Math.random() - 0.5 ) * BOX.y * 2;
		positions[ i * 3 + 2 ] = ( Math.random() - 0.5 ) * BOX.z * 2;
		velocities[ i * 3 ] = ( Math.random() - 0.5 ) * 0.006;
		velocities[ i * 3 + 1 ] = ( Math.random() - 0.5 ) * 0.006;
		velocities[ i * 3 + 2 ] = ( Math.random() - 0.5 ) * 0.006;
	}

	var pointGeometry = new BufferGeometry();
	pointGeometry.setAttribute( 'position', new BufferAttribute( positions, 3 ) );
	var pointMaterial = new PointsMaterial( { size: 0.12, transparent: true, opacity: 0.9, depthWrite: false, blending: AdditiveBlending } );
	group.add( new Points( pointGeometry, pointMaterial ) );

	var linePositions = new Float32Array( MAX_LINKS * 6 );
	var lineGeometry = new BufferGeometry();
	lineGeometry.setAttribute( 'position', new BufferAttribute( linePositions, 3 ) );
	lineGeometry.setDrawRange( 0, 0 );
	var lineMaterial = new LineBasicMaterial( { transparent: true, opacity: 0.28, depthWrite: false, blending: AdditiveBlending } );
	group.add( new LineSegments( lineGeometry, lineMaterial ) );

	var chain = new Group();
	var blockEdges = new EdgesGeometry( new BoxGeometry( 1.1, 1.1, 1.1 ) );
	var blockMaterial = new LineBasicMaterial( { transparent: true, opacity: 0.7, depthWrite: false } );
	var linkMaterial = new LineBasicMaterial( { transparent: true, opacity: 0.45, depthWrite: false } );
	var BLOCKS = small ? 4 : 6;
	var blocks = [];
	for ( var b = 0; b < BLOCKS; b++ ) {
		var block = new LineSegments( blockEdges, blockMaterial );
		block.position.set( ( b - ( BLOCKS - 1 ) / 2 ) * 2.1, Math.sin( b * 0.9 ) * 0.5, 0 );
		chain.add( block );
		blocks.push( block );
		if ( b > 0 ) {
			var prev = blocks[ b - 1 ].position;
			var geometry = new BufferGeometry();
			geometry.setAttribute( 'position', new BufferAttribute( new Float32Array( [ prev.x + 0.55, prev.y, 0, block.position.x - 0.55, block.position.y, 0 ] ), 3 ) );
			chain.add( new LineSegments( geometry, linkMaterial ) );
		}
	}
	chain.position.set( small ? 0 : 4.6, small ? 3.4 : 3.3, -4 );
	chain.scale.setScalar( small ? 0.7 : 1 );
	group.add( chain );

	function readColors() {
		var style = window.getComputedStyle( root );
		var accent = new Color( style.getPropertyValue( '--e-blue' ).trim() || '#2da4fa' );
		var light = root.getAttribute( 'data-scheme' ) === 'light';
		pointMaterial.color.copy( accent );
		lineMaterial.color.copy( accent );
		blockMaterial.color.copy( accent );
		linkMaterial.color.copy( accent );
		pointMaterial.blending = lineMaterial.blending = light ? NormalBlending : AdditiveBlending;
		pointMaterial.opacity = light ? 0.75 : 0.9;
		lineMaterial.opacity = light ? 0.35 : 0.28;
		pointMaterial.needsUpdate = lineMaterial.needsUpdate = true;
	}
	readColors();
	new MutationObserver( readColors ).observe( root, { attributes: true, attributeFilter: [ 'data-scheme' ] } );

	function resize() {
		var width = canvas.clientWidth;
		var height = canvas.clientHeight;
		if ( ! width || ! height ) {
			return;
		}
		renderer.setPixelRatio( Math.min( window.devicePixelRatio || 1, 2 ) );
		renderer.setSize( width, height, false );
		camera.aspect = width / height;
		camera.updateProjectionMatrix();
	}
	resize();
	if ( window.ResizeObserver ) {
		new ResizeObserver( resize ).observe( canvas );
	} else {
		window.addEventListener( 'resize', resize );
	}

	var pointer = { x: 0, y: 0, tx: 0, ty: 0 };
	if ( ! reduceMotion ) {
		window.addEventListener( 'pointermove', function ( event ) {
			pointer.tx = ( event.clientX / window.innerWidth - 0.5 ) * 2;
			pointer.ty = ( event.clientY / window.innerHeight - 0.5 ) * 2;
		}, { passive: true } );
	}

	function updateLinks() {
		var count = 0;
		var limit = LINK_DISTANCE * LINK_DISTANCE;
		for ( var a = 0; a < NODE_COUNT && count < MAX_LINKS; a++ ) {
			for ( var c = a + 1; c < NODE_COUNT && count < MAX_LINKS; c++ ) {
				var dx = positions[ a * 3 ] - positions[ c * 3 ];
				var dy = positions[ a * 3 + 1 ] - positions[ c * 3 + 1 ];
				var dz = positions[ a * 3 + 2 ] - positions[ c * 3 + 2 ];
				if ( dx * dx + dy * dy + dz * dz < limit ) {
					linePositions.set( positions.subarray( a * 3, a * 3 + 3 ), count * 6 );
					linePositions.set( positions.subarray( c * 3, c * 3 + 3 ), count * 6 + 3 );
					count++;
				}
			}
		}
		lineGeometry.setDrawRange( 0, count * 2 );
		lineGeometry.attributes.position.needsUpdate = true;
	}

	function step( time ) {
		for ( var n = 0; n < NODE_COUNT; n++ ) {
			for ( var k = 0; k < 3; k++ ) {
				var idx = n * 3 + k;
				var bound = k === 0 ? BOX.x : k === 1 ? BOX.y : BOX.z;
				positions[ idx ] += velocities[ idx ];
				if ( positions[ idx ] > bound || positions[ idx ] < -bound ) {
					velocities[ idx ] *= -1;
				}
			}
		}
		pointGeometry.attributes.position.needsUpdate = true;
		updateLinks();

		pointer.x += ( pointer.tx - pointer.x ) * 0.04;
		pointer.y += ( pointer.ty - pointer.y ) * 0.04;
		group.rotation.y = time * 0.00006 + pointer.x * 0.18;
		group.rotation.x = pointer.y * 0.1;
		for ( var m = 0; m < blocks.length; m++ ) {
			blocks[ m ].rotation.y = time * 0.0004 + m * 0.5;
			blocks[ m ].rotation.x = time * 0.0002 + m * 0.3;
		}
	}

	function render( time ) {
		step( time );
		renderer.render( scene, camera );
	}

	if ( reduceMotion ) {
		render( 0 );
		return;
	}

	var frame = 0;
	var visible = true;

	function loop( time ) {
		frame = 0;
		if ( ! visible || document.hidden ) {
			return;
		}
		render( time );
		frame = window.requestAnimationFrame( loop );
	}

	function start() {
		if ( ! frame ) {
			frame = window.requestAnimationFrame( loop );
		}
	}

	if ( window.IntersectionObserver ) {
		new IntersectionObserver( function ( entries ) {
			visible = entries[ 0 ].isIntersecting;
			if ( visible ) {
				start();
			}
		} ).observe( canvas );
	}
	document.addEventListener( 'visibilitychange', start );
	start();
}() );
