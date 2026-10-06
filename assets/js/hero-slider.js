/* global corralonPro */
(function () {
	'use strict';

	function initHeroSlider() {
		var track = document.getElementById('hero-slider-track');
		var prev  = document.querySelector('.hero-slider-prev');
		var next  = document.querySelector('.hero-slider-next');

		if (!track || !prev || !next) {
			return;
		}

		/* ---------- Configuración ---------- */
		var AUTOPLAY_MS = 4000;   // pausa entre avances: 7s
		var DURATION_MS = 2600;   // duración del desplazamiento: 2.6s
		var CLONE_COUNT = 6;      // clones a cada lado

		function easeInOutQuint(t) {
			return t < 0.5
				? 16 * t * t * t * t * t
				: 1 - Math.pow(-2 * t + 2, 5) / 2;
		}

		/* ---------- Clonado para loop infinito ---------- */
		var originalSlides = Array.prototype.slice.call(track.children);
		var total = originalSlides.length;

		if (total <= 1) {
			prev.style.display = 'none';
			next.style.display = 'none';
			return;
		}

		// Clonamos al final.
		for (var i = 0; i < CLONE_COUNT; i++) {
			track.appendChild(originalSlides[i].cloneNode(true));
		}
		// Clonamos al inicio.
		for (var j = total - 1; j >= total - CLONE_COUNT && j >= 0; j--) {
			track.insertBefore(originalSlides[j].cloneNode(true), track.firstChild);
		}

		var allSlides = Array.prototype.slice.call(track.children);

		/* ---------- Estado ---------- */
		var index = 0;
		var realIndex = CLONE_COUNT;
		var autoplayId = null;
		var animId = null;
		var isJumping = false;

		/* ---------- Helpers ---------- */
		function getStep() {
			if (allSlides.length < 2) { return allSlides[0].offsetWidth; }
			return allSlides[1].offsetLeft - allSlides[0].offsetLeft;
		}

		function setScrollLeft(pos) {
			track.scrollLeft = pos;
		}

		function jumpTo(i) {
			realIndex = i;
			index = ((realIndex - CLONE_COUNT) % total + total) % total;
			isJumping = true;
			setScrollLeft(realIndex * getStep());
			void track.offsetWidth;
			isJumping = false;
		}

		function animateScrollTo(target, duration, onDone) {
			if (animId) {
				cancelAnimationFrame(animId);
				animId = null;
			}

			var start = track.scrollLeft;
			var delta = target - start;
			if (Math.abs(delta) < 1) {
				setScrollLeft(target);
				if (onDone) { onDone(); }
				return;
			}

			var startTime = null;

			function step(timestamp) {
				if (startTime === null) { startTime = timestamp; }
				var elapsed = timestamp - startTime;
				var progress = Math.min(1, elapsed / duration);
				var eased = easeInOutQuint(progress);
				setScrollLeft(start + delta * eased);
				if (progress < 1) {
					animId = requestAnimationFrame(step);
				} else {
					animId = null;
					if (onDone) { onDone(); }
				}
			}

			animId = requestAnimationFrame(step);
		}

		function goToReal(i) {
			animateScrollTo(i * getStep(), DURATION_MS, function () {
				realIndex = i;
				index = ((realIndex - CLONE_COUNT) % total + total) % total;

				if (realIndex >= CLONE_COUNT + total) {
					jumpTo(realIndex - total);
				} else if (realIndex < CLONE_COUNT) {
					jumpTo(realIndex + total);
				}
			});
		}

		function nextSlide() {
			goToReal(realIndex + 1);
		}

		function prevSlide() {
			goToReal(realIndex - 1);
		}

		/* ---------- Autoplay ---------- */
		function startAutoplay() {
			if (autoplayId) { return; }
			autoplayId = setInterval(nextSlide, AUTOPLAY_MS);
		}
		function stopAutoplay() {
			if (autoplayId) {
				clearInterval(autoplayId);
				autoplayId = null;
			}
		}
		function restartAutoplay() {
			stopAutoplay();
			startAutoplay();
		}

		next.addEventListener('click', function () { nextSlide(); restartAutoplay(); });
		prev.addEventListener('click', function () { prevSlide(); restartAutoplay(); });

		/* ---------- Sincronizar index con scroll manual ---------- */
		var scrollTimeout;
		track.addEventListener('scroll', function () {
			if (animId || isJumping) { return; }
			clearTimeout(scrollTimeout);
			scrollTimeout = setTimeout(function () {
				var step = getStep();
				if (step > 0) {
					realIndex = Math.round(track.scrollLeft / step);
					index = ((realIndex - CLONE_COUNT) % total + total) % total;

					if (realIndex >= CLONE_COUNT + total) {
						jumpTo(realIndex - total);
					} else if (realIndex < CLONE_COUNT) {
						jumpTo(realIndex + total);
					}
				}
			}, 120);
		}, { passive: true });

		document.addEventListener('visibilitychange', function () {
			if (document.hidden) { stopAutoplay(); } else { startAutoplay(); }
		});

		/* ---------- Recalcular al redimensionar ---------- */
		var resizeTimeout;
		window.addEventListener('resize', function () {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(function () {
				setScrollLeft(realIndex * getStep());
			}, 150);
		}, { passive: true });

		/* ---------- Arranque ---------- */
		jumpTo(CLONE_COUNT);
		startAutoplay();
	}

	if (document.readyState !== 'loading') {
		initHeroSlider();
	} else {
		document.addEventListener('DOMContentLoaded', initHeroSlider);
	}
}());