/**
 * Element Pack admin SPA entry point.
 *
 * Registers the data store and mounts the React 18 root into the PHP-rendered
 * #ep-admin-app node. Uses createRoot from @wordpress/element (React 18 API).
 */
import { createRoot } from '@wordpress/element';
import { registerStore } from './store';
import App from './app';
import './styles/index.scss';

registerStore();

domReady();

function domReady() {
	const node = document.getElementById( 'ep-admin-app' );
	if ( ! node ) {
		return;
	}
	createRoot( node ).render( <App /> );
}
