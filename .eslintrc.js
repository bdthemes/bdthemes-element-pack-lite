/**
 * ESLint config for the React admin app (src/admin/app).
 *
 * Scoped via the project's lint:admin script. Uses the official WordPress
 * preset shipped with @wordpress/scripts.
 */
module.exports = {
	root: true,
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	settings: {
		react: {
			version: 'detect',
		},
	},
	env: {
		browser: true,
		es2021: true,
	},
	globals: {
		elementPackAdmin: 'readonly',
		wp: 'readonly',
	},
	rules: {
		'jsdoc/check-tag-names': 'off',
		'jsdoc/require-param': 'off',
		'jsdoc/check-line-alignment': 'off',
	},
	ignorePatterns: [ 'build/**', 'assets/**', 'node_modules/**', 'src/**/!(admin)/**' ],
};
