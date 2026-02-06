/**
 * Categories Slider - Swiper initialization
 * com_accommodation_manager
 *
 * Dispatches a custom event "am-slider-before-init" on the container element
 * before initialising Swiper. Template JS can listen for this event and modify
 * the Swiper options via event.detail.options.
 *
 * Example:
 *   document.addEventListener('am-slider-before-init', function (e) {
 *       e.detail.options.effect = 'fade';
 *       e.detail.options.speed  = 800;
 *   });
 */

document.addEventListener('DOMContentLoaded', function () {
	'use strict';

	var container = document.querySelector('.am-categories-swiper');
	if (!container) {
		return;
	}

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
			nextEl: '.am-categories-swiper .swiper-button-next',
			prevEl: '.am-categories-swiper .swiper-button-prev',
		};
	}

	if (showPagination) {
		options.pagination = {
			el: '.am-categories-swiper .swiper-pagination',
			clickable: true,
		};
	}

	// Allow template JS to modify options before Swiper initialisation
	container.dispatchEvent(new CustomEvent('am-slider-before-init', {
		bubbles: true,
		detail: { options: options },
	}));

	new Swiper('.am-categories-swiper', options);
});
