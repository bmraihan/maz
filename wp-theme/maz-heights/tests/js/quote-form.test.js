const { isValidUkPhone, isValidUkPostcode, validateQuoteForm } = require('../../assets/js/quote-form.js');

const messages = {
	required: 'Please fill in this field.',
	invalidPhone: 'Enter a valid UK phone number.',
	invalidPost: 'Enter a valid UK postcode.'
};

describe('isValidUkPhone', () => {
	test.each([
		['07123 456789', true],
		['+44 7123 456789', true],
		['02476 123456', true],
		['0247 6123456', true],
		['+447123456789', true],
	])('accepts %s', (value, expected) => {
		expect(isValidUkPhone(value)).toBe(expected);
	});

	test.each([
		['', false],
		['abcdefg', false],
		['123', false],
		['12345678901234567890', false],
		['91234567890', false], // doesn't start with 0 or 44
	])('rejects %s', (value, expected) => {
		expect(isValidUkPhone(value)).toBe(expected);
	});
});

describe('isValidUkPostcode', () => {
	test.each([
		'CV1 2AB',
		'CV12AB',
		'cv1 2ab',
		'B1 1AA',
		'EC1A 1BB',
		'W1A 0AX',
	])('accepts %s', (value) => {
		expect(isValidUkPostcode(value)).toBe(true);
	});

	test.each([
		'',
		'not a postcode',
		'12345',
		'CV1',
	])('rejects %s', (value) => {
		expect(isValidUkPostcode(value)).toBe(false);
	});
});

describe('validateQuoteForm', () => {
	const validValues = {
		name: 'Sarah Tom',
		phone: '07123 456789',
		project_type: 'Extension',
		postcode: 'CV5 6AB'
	};

	test('passes with all valid fields', () => {
		const result = validateQuoteForm(validValues, messages);
		expect(result.valid).toBe(true);
		expect(result.errors).toEqual({});
	});

	test('flags every field as required when the form is empty', () => {
		const result = validateQuoteForm({}, messages);
		expect(result.valid).toBe(false);
		expect(result.errors.name).toBe(messages.required);
		expect(result.errors.phone).toBe(messages.required);
		expect(result.errors.project_type).toBe(messages.required);
		expect(result.errors.postcode).toBe(messages.required);
	});

	test('flags an invalid phone distinctly from a missing one', () => {
		const result = validateQuoteForm({ ...validValues, phone: '123' }, messages);
		expect(result.valid).toBe(false);
		expect(result.errors.phone).toBe(messages.invalidPhone);
	});

	test('flags an invalid postcode distinctly from a missing one', () => {
		const result = validateQuoteForm({ ...validValues, postcode: 'nope' }, messages);
		expect(result.valid).toBe(false);
		expect(result.errors.postcode).toBe(messages.invalidPost);
	});

	test('does not require a budget (optional field)', () => {
		const result = validateQuoteForm({ ...validValues, budget: '' }, messages);
		expect(result.valid).toBe(true);
	});

	test('treats a whitespace-only name as missing', () => {
		const result = validateQuoteForm({ ...validValues, name: '   ' }, messages);
		expect(result.valid).toBe(false);
		expect(result.errors.name).toBe(messages.required);
	});
});
