/* global corralonPro, fetch */
(function () {
	'use strict';

	function ready(callback) {
		if (document.readyState !== 'loading') {
			callback();
		} else {
			document.addEventListener('DOMContentLoaded', callback);
		}
	}

	function isMobileNav() {
		return window.matchMedia('(max-width: 46rem)').matches;
	}

	/* =========================================================
	   FAVORITOS
	   ========================================================= */
	function getFavorites() {
		try {
			return JSON.parse(localStorage.getItem('corralonProFavorites') || '[]');
		} catch (error) {
			return [];
		}
	}

	function setFavorites(ids) {
		localStorage.setItem('corralonProFavorites', JSON.stringify(ids));
	}

	function syncWishlistButtons() {
	var ids = getFavorites().map(String);
		document.querySelectorAll('[data-wishlist-id]').forEach(function (button) {
			var saved = ids.indexOf(String(button.dataset.wishlistId)) !== -1;
			button.classList.toggle('is-saved', saved);
			button.setAttribute(
				'aria-label',
				saved ? 'Quitar de favoritos' : 'Agregar a favoritos'
			);
		});

		// Actualizar el contador del header (badge "Favoritos")
		var countEl = document.querySelector('.fav-count');
		if (countEl) {
			var total = ids.length;
			countEl.textContent = total;
			if (total > 0) {
				countEl.hidden = false;
			} else {
				countEl.hidden = true;
			}
		}
	}

	function setupWishlist() {
		document.addEventListener('click', function (event) {
			var button = event.target.closest('[data-wishlist-id]');
			if (!button) {
				return;
			}
			event.preventDefault();
			var id = String(button.dataset.wishlistId);
			var ids = getFavorites().map(String);
			var index = ids.indexOf(id);
			if (index === -1) {
				ids.push(id);
			} else {
				ids.splice(index, 1);
			}
			setFavorites(ids);
			syncWishlistButtons();
			renderFavoritesPage();
		});
		syncWishlistButtons();
	}

	function renderFavoritesPage() {
		var grid = document.getElementById('corralon-favorites-grid');
		if (!grid || !window.corralonPro || !corralonPro.ajaxUrl) {
			return;
		}
		var ids = getFavorites();
		if (!ids.length) {
			grid.innerHTML = '<p>No tenés productos guardados todavía.</p>';
			return;
		}
		var params = new URLSearchParams({
			action: 'corralon_pro_favorites',
			nonce: corralonPro.searchNonce,
			ids: ids.join(',')
		});
		fetch(corralonPro.ajaxUrl + '?' + params.toString())
			.then(function (response) { return response.json(); })
			.then(function (data) {
				grid.innerHTML = data.success && data.data.html ? data.data.html : '<p>No pudimos cargar tus favoritos.</p>';
			})
			.catch(function () {
				grid.innerHTML = '<p>No pudimos cargar tus favoritos.</p>';
			});
	}

	/* =========================================================
	   MENÚS Y SUBMENÚS
	   ========================================================= */
	function setupMenus() {

		var nav = document.querySelector('.main-navigation');
		if (!nav) { return; }

		var dropdown = nav.querySelector('.categories-dropdown');
		var catToggle = nav.querySelector('.categories-toggle');

		function openDropdown() {
			if (!dropdown || !catToggle) { return; }
			dropdown.classList.add('is-open');
			catToggle.setAttribute('aria-expanded', 'true');
		}
		function closeDropdown() {
			if (!dropdown || !catToggle) { return; }
			dropdown.classList.remove('is-open');
			catToggle.setAttribute('aria-expanded', 'false');
		}

		var hoverCloseTimer = null;
		function scheduleClose() {
			if (hoverCloseTimer) { clearTimeout(hoverCloseTimer); }
			hoverCloseTimer = setTimeout(closeDropdown, 150);
		}
		function cancelClose() {
			if (hoverCloseTimer) {
				clearTimeout(hoverCloseTimer);
				hoverCloseTimer = null;
			}
		}

		if (catToggle && dropdown) {
			var isTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);

			if (!isTouch) {
				catToggle.addEventListener('mouseenter', function () {
					cancelClose();
					openDropdown();
				});
				catToggle.addEventListener('mouseleave', function () {
					scheduleClose();
				});

				dropdown.addEventListener('mouseenter', function () {
					cancelClose();
					openDropdown();
				});
				dropdown.addEventListener('mouseleave', function () {
					scheduleClose();
				});
			}

			catToggle.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var isOpen = dropdown.classList.contains('is-open');
				if (isOpen) {
					closeDropdown();
				} else {
					openDropdown();
				}
			});
		}

		var mobileToggle = nav.querySelector('.mobile-menu-toggle');
		var primaryMenu = document.getElementById('primary-menu');
		if (mobileToggle && primaryMenu) {
			mobileToggle.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var expanded = mobileToggle.getAttribute('aria-expanded') === 'true';
				mobileToggle.setAttribute('aria-expanded', String(!expanded));
				primaryMenu.classList.toggle('is-open', !expanded);
			});
		}

		nav.querySelectorAll('.menu-item-has-children > a').forEach(function (link) {
			var li = link.parentNode;

			if (li.querySelector(':scope > .submenu-toggle')) {
				return;
			}

			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'submenu-toggle';
			btn.setAttribute('aria-expanded', 'false');
			btn.setAttribute('aria-label', 'Abrir submenú');
			btn.innerHTML = '<span aria-hidden="true">▾</span>';
			li.insertBefore(btn, link.nextSibling);

			function syncButtonVisibility() {
				btn.style.display = isMobileNav() ? '' : 'none';
			}
			syncButtonVisibility();
			window.addEventListener('resize', syncButtonVisibility, { passive: true });

			btn.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();

				var isOpen = li.classList.contains('is-open');
				var parentList = li.parentNode;
				parentList.querySelectorAll(':scope > .menu-item-has-children.is-open').forEach(function (sib) {
					if (sib !== li) {
						sib.classList.remove('is-open');
						var sibBtn = sib.querySelector(':scope > .submenu-toggle');
						if (sibBtn) {
							sibBtn.setAttribute('aria-expanded', 'false');
						}
					}
				});

				li.classList.toggle('is-open', !isOpen);
				btn.setAttribute('aria-expanded', String(!isOpen));
			});

			li.addEventListener('keyup', function (e) {
				if (e.key === 'Escape') {
					li.classList.remove('is-open');
					btn.setAttribute('aria-expanded', 'false');
				}
			});
		});

		document.addEventListener('click', function (e) {
			if (!nav.contains(e.target)) {
				if (dropdown) {
					dropdown.classList.remove('is-open');
				}
				if (catToggle) {
					catToggle.setAttribute('aria-expanded', 'false');
				}
				if (mobileToggle) {
					mobileToggle.setAttribute('aria-expanded', 'false');
				}
				if (primaryMenu) {
					primaryMenu.classList.remove('is-open');
				}
				nav.querySelectorAll('.menu-item-has-children.is-open').forEach(function (el) {
					el.classList.remove('is-open');
					var b = el.querySelector(':scope > .submenu-toggle');
					if (b) {
						b.setAttribute('aria-expanded', 'false');
					}
				});
			}
		});

		if (dropdown) {
			dropdown.addEventListener('click', function (e) {
				var link = e.target.closest('a');
				if (link && isMobileNav()) {
					closeDropdown();
				}
			});
		}
	}

	/* =========================================================
	   MEGAMENÚ DE CATEGORÍAS: activar bloque al hover/click
	   ========================================================= */
	function setupCategoriesMegamenu() {
		var titles = document.querySelectorAll('.categories-megamenu-title');
		var panels = document.querySelectorAll('.categories-megamenu-panel');

		if (!titles.length || !panels.length) {
			return;
		}

		function activate(catId) {
			titles.forEach(function (t) {
				t.classList.toggle('is-active', t.dataset.catId === catId);
			});
			panels.forEach(function (p) {
				p.classList.toggle('is-active', p.dataset.catId === catId);
			});
		}

		titles.forEach(function (title) {
			title.addEventListener('mouseenter', function () {
				activate(title.dataset.catId);
			});
			title.addEventListener('focus', function () {
				activate(title.dataset.catId);
			});
			title.addEventListener('click', function (e) {
				if (isMobileNav() && !title.classList.contains('is-active')) {
					e.preventDefault();
					activate(title.dataset.catId);
				}
			});
		});

		if (titles[0]) {
			activate(titles[0].dataset.catId);
		}
	}

	/* =========================================================
	   BUSCADOR CON SUGERENCIAS
	   ========================================================= */
	function setupSearch() {
		var searchInput = document.querySelector('.search-form input[type="search"]');
		var suggestions = document.querySelector('.search-suggestions');
		if (!searchInput || !suggestions || !window.corralonPro || !corralonPro.ajaxUrl) {
			return;
		}
		var timeout;
		searchInput.addEventListener('input', function () {
			clearTimeout(timeout);
			var term = searchInput.value.trim();
			if (term.length < 2) {
				suggestions.classList.remove('is-visible');
				suggestions.innerHTML = '';
				return;
			}
			timeout = setTimeout(function () {
				var params = new URLSearchParams({
					action: 'corralon_pro_search',
					nonce: corralonPro.searchNonce,
					term: term
				});
				fetch(corralonPro.ajaxUrl + '?' + params.toString())
					.then(function (response) { return response.json(); })
					.then(function (data) {
						var products = data.success && data.data.products ? data.data.products : [];
						if (!products.length) {
							suggestions.innerHTML = '<div class="search-suggestion"><span>' + corralonPro.noResults + '</span></div>';
						} else {
							suggestions.innerHTML = products.map(function (product) {
								return '<a class="search-suggestion" href="' + product.url + '"><img src="' + product.image + '" alt=""><span>' + product.name + '<small>' + (product.sku || '') + '</small></span><span class="search-suggestion-price">' + (product.priceHtml || '') + '</span></a>';
							}).join('') + '<a class="search-suggestion" href="?s=' + encodeURIComponent(term) + '&post_type=product"><strong>' + corralonPro.viewAll + '</strong></a>';
						}
						suggestions.classList.add('is-visible');
					});
			}, 220);
		});
		document.addEventListener('click', function (event) {
			if (!event.target.closest('.search-form')) {
				suggestions.classList.remove('is-visible');
			}
		});
	}

	/* =========================================================
	   BOTÓN VOLVER ARRIBA
	   ========================================================= */
	function setupBackToTop() {
		var btn = document.querySelector('.back-to-top');
		if (!btn) {
			return;
		}

		var SHOW_AFTER = 400;

		function toggle() {
			var y = window.scrollY || document.documentElement.scrollTop;
			if (y > SHOW_AFTER) {
				btn.hidden = false;
				btn.classList.add('is-visible');
			} else {
				btn.classList.remove('is-visible');
				setTimeout(function () {
					if (!btn.classList.contains('is-visible')) {
						btn.hidden = true;
					}
				}, 220);
			}
		}

		btn.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});

		window.addEventListener('scroll', toggle, { passive: true });
		toggle();
	}

	/* =========================================================
	   INIT
	   ========================================================= */
	ready(function () {
		setupMenus();
		setupWishlist();
		setupSearch();
		renderFavoritesPage();
		setupBackToTop();
		setupCategoriesMegamenu();
	});
}());