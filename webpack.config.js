/**
 * Webpack configuration for the Element Pack Lite React admin SPA.
 *
 * Extends the default @wordpress/scripts config (Babel, SCSS, JSX, dependency
 * extraction, fast-refresh) and only overrides the entry/output so the bundle
 * is emitted to /build/admin. This build tool is scoped to src/admin/app
 * exclusively; the plugin's frontend assets in assets/ are not built here.
 */

const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( __dirname, 'src/admin/app/index.js' ),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'build/admin' ),
	},
};
