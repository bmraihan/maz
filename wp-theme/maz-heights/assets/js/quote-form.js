/**
 * Pure validation logic for the "Get a fixed price" quote form.
 *
 * Deliberately has zero DOM/WordPress dependency so it can be unit
 * tested directly (see tests/js/quote-form.test.js) and reused by
 * assets/js/main.js, which is the only file that touches the DOM.
 */
(function (root, factory) {
	if (typeof module === 'object' && module.exports) {
		module.exports = factory();
	} else {
		root.MazQuoteForm = factory();
	}
}(typeof self !== 'undefined' ? self : this, function () {
	'use strict';

	/**
	 * A UK phone number is considered valid if, once non-digits are
	 * stripped, it starts with a national (0) or international (44)
	 * prefix and has a plausible total digit count. This is
	 * intentionally permissive (covers mobiles and landlines, with or
	 * without spacing) — Formspree/the office doing the actual
	 * callback is the real validation; this just catches obvious typos.
	 *
	 * @param {string} value
	 * @returns {boolean}
	 */
	function isValidUkPhone(value) {
		var digits = String(value || '').replace(/\D/g, '');

		if (digits.length < 9 || digits.length > 12) {
			return false;
		}

		return /^(0|44)/.test(digits);
	}

	/**
	 * Standard UK postcode pattern (outward + inward code), case-insensitive,
	 * with or without the internal space.
	 *
	 * @param {string} value
	 * @returns {boolean}
	 */
	function isValidUkPostcode(value) {
		return /^[A-Z]{1,2}\d[A-Z\d]?\s?\d[A-Z]{2}$/i.test(String(value || '').trim());
	}

	/**
	 * Validate the full quote form.
	 *
	 * @param {{name?:string, phone?:string, project_type?:string, postcode?:string}} values
	 * @param {{required:string, invalidPhone:string, invalidPost:string}} messages
	 * @returns {{valid:boolean, errors:Object<string,string>}}
	 */
	function validateQuoteForm(values, messages) {
		values = values || {};
		messages = messages || { required: 'Required', invalidPhone: 'Invalid phone', invalidPost: 'Invalid postcode' };

		var errors = {};

		if (!values.name || !String(values.name).trim()) {
			errors.name = messages.required;
		}

		if (!values.phone || !String(values.phone).trim()) {
			errors.phone = messages.required;
		} else if (!isValidUkPhone(values.phone)) {
			errors.phone = messages.invalidPhone;
		}

		if (!values.project_type || !String(values.project_type).trim()) {
			errors.project_type = messages.required;
		}

		if (!values.postcode || !String(values.postcode).trim()) {
			errors.postcode = messages.required;
		} else if (!isValidUkPostcode(values.postcode)) {
			errors.postcode = messages.invalidPost;
		}

		return { valid: Object.keys(errors).length === 0, errors: errors };
	}

	return {
		isValidUkPhone: isValidUkPhone,
		isValidUkPostcode: isValidUkPostcode,
		validateQuoteForm: validateQuoteForm
	};
}));
