module.exports = {
	env: {
		browser: true,
		es2021: true,
		node: true,
	},
	extends: [
		'eslint:recommended',
		'plugin:react/recommended',
		'plugin:@typescript-eslint/recommended',
		'plugin:@tanstack/eslint-plugin-query/recommended',
	],
	overrides: [],
	parser: '@typescript-eslint/parser',
	parserOptions: {
		ecmaVersion: 'latest',
		sourceType: 'module',
	},
	plugins: ['react', '@typescript-eslint', '@tanstack/query'],
	rules: {
		indent: ['off'],
		/* 		'linebreak-style': ['error', 'windows'], */
		quotes: ['error', 'single'],
		semi: ['error', 'always'],
		'@typescript-eslint/no-explicit-any': 'off',
		'@typescript-eslint/no-empty-function': 'warn',
		'no-mixed-spaces-and-tabs': 'off',
		'no-extra-boolean-cast': 'off',
		'@tanstack/query/exhaustive-deps': 'error',
		'@tanstack/query/prefer-query-object-syntax': 'error',
	},
};
