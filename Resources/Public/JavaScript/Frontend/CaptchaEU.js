(function() {
	'use strict';

	function ensureKROTLoaded(callback) {
		var checkInterval = setInterval(function () {
			if (window['KROT'] !== undefined) {
				clearInterval(checkInterval);
				callback();
			}
		}, 200);
	}

	function initInvisibleField(input, form, btn) {
		function btnClickHandler(e) {
			// return if already intercepted
			if (e.intercepted) {
				return;
			}

			e.preventDefault();
			btn.disabled = true;

			// RUN captcha
			KROT.getSolution().then(function (sol) {
				// write solution to hidden field
				input.value = JSON.stringify(sol);

				btn.disabled = false;
				btn.removeAttribute('disabled');
				e.intercepted = true;

				btn.removeEventListener('click', btnClickHandler);

				// submit the form
				form.requestSubmit(btn);
			});

			btn.addEventListener('click', btnClickHandler);
		}

		btn.addEventListener('click', btnClickHandler);
	}

	function initWidgetField(input, form, btn) {
		btn.disabled = true;

		KROT.init();

		var pollInterval = setInterval(function () {
			var sdkField = document.querySelector('input[name="captcha_at_hidden_field"]');
			if (sdkField && sdkField.value !== '') {
				input.value = sdkField.value;
				btn.disabled = false;
				clearInterval(pollInterval);
			}
		}, 200);
	}

	function initField(input) {
		var publicKey = input.getAttribute('data-captchaeu-public-key');
		var host = input.getAttribute('data-captchaeu-host');
		var mode = input.getAttribute('data-captchaeu-mode') || 'invisible';

		// KROT setup
		KROT.setup(publicKey);
		KROT.KROT_HOST = host;

		var form = input.closest('form');
		if (!form) {
			return;
		}

		var btn = form.querySelector('.form-navigation .next [type="submit"], .form-navigation .submit [type="submit"], button.btn-primary[type="submit"]');
		if (!btn) {
			btn = form.querySelector('[type="submit"]');
		}

		if (!btn) {
			return;
		}

		if (mode === 'widget') {
			initWidgetField(input, form, btn);
		} else {
			initInvisibleField(input, form, btn);
		}
	}

	// wait for dom content to load
	document.addEventListener('DOMContentLoaded', function () {
		var fields = document.querySelectorAll('[data-captchaeu-field]');
		if (!fields.length) {
			return;
		}

		ensureKROTLoaded(function () {
			fields.forEach(initField);
		});
	});
})();