import './bootstrap';

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'overlayscrollbars/js/OverlayScrollbars.min.js';
import 'admin-lte';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
	$('[data-widget="tooltip"]').tooltip();

	const body = document.querySelector('body');
	if (body && body.classList.contains('sidebar-mini')) {
		$('[data-widget="pushmenu"]').on('click', () => {
			body.classList.toggle('sidebar-open');
		});
	}
});
