(function() {
	function ensureKROTLoaded(callback) {
		const checkVariable = () => {
			if (window['KROT'] !== undefined) {
				clearInterval(checkInterval);
				callback();
			}
		};
		const checkInterval = setInterval(checkVariable, 200);
	}

	document.addEventListener('DOMContentLoaded', function () {
		ensureKROTLoaded(function() {
			document.querySelectorAll('[data-captchaeu-mode]').forEach(function(marker) {
				var mode = marker.dataset.captchaeuMode;
				var key = marker.dataset.captchaeuKey;
				var host = marker.dataset.captchaeuHost;
				var fieldId = marker.dataset.captchaeuFieldId;

				var input = document.getElementById(fieldId);
				if (!input) { return; }
				var form = input.closest('form');
				if (!form) { return; }
				var btn = form.querySelector('[type="submit"]');
				if (!btn) { return; }

				KROT.setup(key);
				KROT.KROT_HOST = host;

				if (mode === 'widget') {
					KROT.init();
					btn.disabled = true;

					var pollInterval = setInterval(function() {
						var sdkField = document.querySelector('input[name="captcha_at_hidden_field"]');
						if (sdkField && sdkField.value !== '') {
							input.value = sdkField.value;
							btn.disabled = false;
							clearInterval(pollInterval);
						}
					}, 200);
				} else {
					// INVISIBLE MODE (unchanged, existing behaviour)
					KROT.interceptForm(form, true);

					function btnClickHandler(e) {
						if (e.intercepted) return;
						e.preventDefault();
						btn.disabled = true;
						KROT.getSolution().then(function(sol) {
							document.getElementById(fieldId).value = JSON.stringify(sol);
							btn.disabled = false;
							btn.removeAttribute('disabled');
							e.intercepted = true;
							btn.removeEventListener('click', btnClickHandler);
							form.requestSubmit(btn);
						});
						btn.addEventListener('click', btnClickHandler);
					}
					btn.addEventListener('click', btnClickHandler);
				}
			});
		});
	});
})();