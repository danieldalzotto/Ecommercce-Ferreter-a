/**
 * Filtrado AJAX de productos por categoría + paginación.
 *
 * - En tienda / home / categorías: filtra vía AJAX sin recargar.
 * - En ficha de producto (single-product): deja que el navegador
 *   navegue normalmente a la categoría (recarga de página).
 */
(function ($) {
	'use strict';

	$(document).ready(function () {

		var $sidebar = $('.shop-sidebar');
		var $content = $('.shop-content');

		if (!$content.length) {
			return;
		}

		// Si estamos en single-product, NO interceptamos.
		// Detectamos por la clase que WooCommerce agrega al <body>.
		var isSingleProduct = $('body').hasClass('single-product');

		if (isSingleProduct) {
			// En la ficha del producto, los enlaces del sidebar navegan normal.
			// No hacemos nada: dejamos que el navegador siga el href.
			return;
		}

		if (typeof corralonProFilter === 'undefined') {
			return;
		}

		/* =========================================================
		   Cargar productos vía AJAX
		   ========================================================= */
		function loadProducts(slug, paged, $link) {
			$content.addClass('is-loading');

			$.post(
				corralonProFilter.ajaxUrl,
				{
					action: 'corralon_pro_filter',
					nonce: corralonProFilter.nonce,
					cat: slug,
					paged: paged || 1
				},
				function (response) {
					if (response && response.success) {
						$content.find('.product-grid, .no-products-found').first().replaceWith(response.data.html);
						$content.find('.shop-pagination').remove();

						var newUrl = window.location.pathname;
						var params = [];
						if (slug) params.push('product_cat=' + slug);
						if (paged > 1) params.push('paged=' + paged);
						if (params.length) newUrl += '?' + params.join('&');

						window.history.pushState({}, '', newUrl);

						if ($link && $link.length) {
							var catName = $link.find('span').first().text().trim();
							var $title  = $content.find('.section-heading h2');
							var $sub    = $content.find('.section-heading p');

							if ($title.length) {
								$title.text(slug ? 'Productos en ' + catName : 'Nuestros productos');
							}
							if ($sub.length) {
								$sub.text(slug
									? 'Mostrando productos de ' + catName + '.'
									: 'Explorá todo nuestro catálogo o filtrá por categoría.'
								);
							}
						}

						$('html, body').animate({
							scrollTop: $content.offset().top - 100
						}, 300);
					}
					$content.removeClass('is-loading');
				}
			).fail(function () {
				$content.removeClass('is-loading');
			});
		}

		/* =========================================================
		   SOLO interceptar clics en el sidebar
		   ========================================================= */
		if ($sidebar.length) {
			$sidebar.on('click', '.category-filter-list a', function (e) {
				e.preventDefault();
				var $link = $(this);
				var slug  = $link.data('cat') || '';
				$sidebar.find('.category-filter-list li').removeClass('current-cat is-active');
				$link.closest('li').addClass('current-cat is-active');
				loadProducts(slug, 1, $link);
			});
		}

		/* =========================================================
		   SOLO interceptar clics en la paginación
		   ========================================================= */
		$content.on('click', '.shop-pagination a', function (e) {
			e.preventDefault();
			var $link = $(this);
			var href  = $link.attr('href');
			var paged = 1;
			var match = href.match(/paged[=\/](\d+)/);
			if (match) {
				paged = parseInt(match[1], 10);
			}
			var slug = $sidebar.find('.category-filter-list li.current-cat a').data('cat') || '';
			loadProducts(slug, paged, null);
		});

		window.addEventListener('popstate', function () {
			window.location.reload();
		});
	});
})(jQuery);