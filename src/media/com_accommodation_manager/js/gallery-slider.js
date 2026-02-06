/**
 * Room Gallery Slider - Swiper initialization
 * com_accommodation_manager
 *
 * Dispatches a custom event "am-gallery-before-init" on each gallery container
 * before initialising Swiper. Template JS can listen for this event and modify
 * the Swiper options via event.detail.options.
 *
 * Example:
 *   document.addEventListener('am-gallery-before-init', function (e) {
 *       e.detail.options.effect = 'fade';
 *       e.detail.options.speed  = 600;
 *   });
 */

document.addEventListener('DOMContentLoaded', function () {
	'use strict';

	var containers = document.querySelectorAll('.am-gallery-swiper');
	if (!containers.length) {
		return;
	}

	containers.forEach(function (container) {
		var slidesPerViewMobile  = parseFloat(container.dataset.slidesPerViewMobile) || 1;
		var slidesPerViewDesktop = parseFloat(container.dataset.slidesPerViewDesktop) || 1;
		var spaceBetweenMobile   = parseInt(container.dataset.spaceBetweenMobile, 10) || 10;
		var spaceBetweenDesktop  = parseInt(container.dataset.spaceBetweenDesktop, 10) || 30;
		var autoplayDelay        = parseInt(container.dataset.autoplay, 10) || 0;
		var showNavigation       = container.dataset.navigation === '1';
		var showPagination       = container.dataset.pagination === '1';

		var options = {
			slidesPerView: slidesPerViewMobile,
			spaceBetween: spaceBetweenMobile,
			loop: true,
			breakpoints: {
				768: {
					slidesPerView: slidesPerViewDesktop,
					spaceBetween: spaceBetweenDesktop,
				},
			},
		};

		if (autoplayDelay > 0) {
			options.autoplay = {
				delay: autoplayDelay,
				disableOnInteraction: false,
			};
		}

		if (showNavigation) {
			options.navigation = {
				nextEl: container.querySelector('.swiper-button-next'),
				prevEl: container.querySelector('.swiper-button-prev'),
			};
		}

		if (showPagination) {
			options.pagination = {
				el: container.querySelector('.swiper-pagination'),
				clickable: true,
			};
		}

		// Allow template JS to modify options before Swiper initialisation
		container.dispatchEvent(new CustomEvent('am-gallery-before-init', {
			bubbles: true,
			detail: { options: options },
		}));

		new Swiper(container, options);
	});
});
