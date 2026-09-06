/**
 * DOM wiring for the quote form: client-side validation (using
 * MazQuoteForm from quote-form.js), then a progressively-enhanced AJAX
 * submit to the Formspree endpoint with inline success/error state.
 *
 * The form itself is plain HTML with a real `action`/`method`, so it
 * still works with JS disabled or before this script has loaded.
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var form = document.getElementById('quote-form');
		if (!form || typeof window.MazQuoteForm === 'undefined') {
			return;
		}

		var settings = window.mazHeightsQuoteForm || {};
		var strings = settings.strings || {};
		var statusEl = form.querySelector('.quote-form__status');
		var submitBtn = form.querySelector('.quote-form__submit');
		var submitLabel = submitBtn ? submitBtn.querySelector('.quote-form__submit-label') : null;
		var defaultSubmitText = submitLabel ? submitLabel.textContent : '';

		function setFieldError(fieldName, message) {
			var input = form.querySelector('[name="' + fieldName + '"]');
			if (!input) {
				return;
			}
			var wrapper = input.closest('.quote-form__field') || input.parentElement;
			var errorEl = form.querySelector('[data-error-for="' + input.id + '"]');

			if (message) {
				if (wrapper) {
					wrapper.classList.add('has-error');
				}
				if (errorEl) {
					errorEl.textContent = message;
				}
			} else {
				if (wrapper) {
					wrapper.classList.remove('has-error');
				}
				if (errorEl) {
					errorEl.textContent = '';
				}
			}
		}

		function clearErrors() {
			['name', 'phone', 'project_type', 'postcode'].forEach(function (field) {
				setFieldError(field, '');
			});
		}

		function setStatus(message, state) {
			if (!statusEl) {
				return;
			}
			statusEl.textContent = message;
			statusEl.hidden = !message;
			if (state) {
				statusEl.setAttribute('data-state', state);
			} else {
				statusEl.removeAttribute('data-state');
			}
		}

		function getValues() {
			return {
				name: form.querySelector('[name="name"]').value,
				phone: form.querySelector('[name="phone"]').value,
				project_type: form.querySelector('[name="project_type"]').value,
				postcode: form.querySelector('[name="postcode"]').value
			};
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			clearErrors();
			setStatus('', null);

			if (form.dataset.configured !== 'true') {
				setStatus(strings.notConfigured || 'This form isn’t connected yet.', 'error');
				return;
			}

			// Honeypot: if a bot filled the hidden field, silently pretend success.
			var honeypot = form.querySelector('[name="_gotcha"]');
			if (honeypot && honeypot.value) {
				setStatus(strings.success || 'Thanks.', 'success');
				form.reset();
				return;
			}

			var result = window.MazQuoteForm.validateQuoteForm(getValues(), strings);

			if (!result.valid) {
				Object.keys(result.errors).forEach(function (field) {
					setFieldError(field, result.errors[field]);
				});
				return;
			}

			if (submitBtn) {
				submitBtn.disabled = true;
			}
			if (submitLabel) {
				submitLabel.textContent = strings.sending || 'Sending…';
			}

			fetch(form.action, {
				method: 'POST',
				body: new FormData(form),
				headers: { Accept: 'application/json' }
			})
				.then(function (response) {
					if (response.ok) {
						setStatus(strings.success || 'Thanks — we’ll be in touch.', 'success');
						form.reset();
					} else {
						setStatus(strings.error || 'Something went wrong.', 'error');
					}
				})
				.catch(function () {
					setStatus(strings.error || 'Something went wrong.', 'error');
				})
				.finally(function () {
					if (submitBtn) {
						submitBtn.disabled = false;
					}
					if (submitLabel) {
						submitLabel.textContent = defaultSubmitText;
					}
				});
		});
	});
}());
