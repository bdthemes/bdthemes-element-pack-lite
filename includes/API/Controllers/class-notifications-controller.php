<?php
/**
 * Notifications + changelog endpoints for the dashboard bell and Activity card.
 *
 * GET /element-pack/v1/notifications  Release entry for the installed version.
 * GET /element-pack/v1/changelog      The newest releases, parsed from readme.txt.
 *
 * Everything is read from the plugin's own readme.txt. No remote notification
 * feed is contacted: Element Pack Lite no longer renders one.
 *
 * @package ElementPack\API\Controllers
 */

namespace ElementPack\API\Controllers;

use ElementPack\API\REST_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Notifications_Controller extends REST_Controller {

	protected $rest_base = 'notifications';

	/**
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_notifications' ],
					'permission_callback' => [ $this, 'permissions_check' ],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/changelog',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_changelog' ],
					'permission_callback' => [ $this, 'permissions_check' ],
				],
			]
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function get_changelog( $request ) {
		return $this->success( [ 'releases' => $this->parse_changelog( 5 ) ] );
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function get_notifications( $request ) {
		return $this->success( [ 'items' => $this->changelog_item() ] );
	}

	/**
	 * Newest releases from readme.txt.
	 *
	 * Headings look like `= 10.0.0 [8th October 2026] =` and entries like
	 * `* Improved: …`; the word before the colon becomes the section name.
	 *
	 * @param int $limit
	 * @return array<int,array<string,mixed>>
	 */
	private function parse_changelog( $limit = 5 ) {
		$changelog = $this->read_changelog_text();

		if ( '' === $changelog ) {
			return [];
		}

		preg_match_all(
			'/^=\s*([0-9][0-9.]*)\s*\[([^\]]+)\]\s*=\s*$(.*?)(?=^=\s*[0-9]|\z)/ms',
			$changelog,
			$blocks,
			PREG_SET_ORDER
		);

		$releases = [];

		foreach ( array_slice( $blocks, 0, $limit ) as $block ) {
			$version   = $block[1];
			$date      = trim( $block[2] );
			$timestamp = strtotime( preg_replace( '/(\d+)(st|nd|rd|th)/', '$1', $date ) );

			$sections   = [];
			$highlights = [];

			preg_match_all( '/^\*\s+(?:([A-Za-z]+):\s+)?(.+)$/m', $block[3], $entries, PREG_SET_ORDER );

			foreach ( $entries as $entry ) {
				$name = '' !== $entry[1] ? strtolower( $entry[1] ) : 'updated';

				$sections[ $name ] = isset( $sections[ $name ] ) ? $sections[ $name ] + 1 : 1;

				if ( count( $highlights ) < 2 ) {
					$highlights[] = wp_strip_all_tags( trim( $entry[2] ) );
				}
			}

			$releases[] = [
				'version'    => $version,
				'date'       => $timestamp ? date_i18n( get_option( 'date_format' ), $timestamp ) : $date,
				'timestamp'  => $timestamp ? $timestamp : 0,
				'sections'   => $sections,
				'highlights' => $highlights,
				'is_current' => defined( 'BDTEP_VER' ) && BDTEP_VER === $version,
			];
		}

		return $releases;
	}

	/**
	 * The changelog part of readme.txt (top 64 KB is plenty for the newest releases).
	 *
	 * @return string
	 */
	private function read_changelog_text() {
		$file = defined( 'BDTEP_PATH' ) ? BDTEP_PATH . 'readme.txt' : '';

		if ( ! $file || ! is_readable( $file ) ) {
			return '';
		}

		$contents = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reads this plugin's own readme.txt.

		if ( ! $contents ) {
			return '';
		}

		$start = strpos( $contents, '== Changelog ==' );

		if ( false === $start ) {
			return '';
		}

		return substr( $contents, $start + strlen( '== Changelog ==' ), 65536 );
	}

	/**
	 * A single "What's new" entry for the installed (newest) release.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function changelog_item() {
		$releases = $this->parse_changelog( 1 );

		if ( empty( $releases ) ) {
			return [];
		}

		$release = $releases[0];

		return [
			[
				'id'        => 'changelog-' . $release['version'],
				'type'      => 'release',
				/* translators: %s: plugin version */
				'title'     => sprintf( __( "What's new in v%s", 'bdthemes-element-pack-lite' ), $release['version'] ),
				'text'      => __( 'See everything added, fixed and improved in this release.', 'bdthemes-element-pack-lite' ),
				'url'       => 'https://feedback.elementpack.pro/announcements/',
				'time'      => $release['date'],
				'timestamp' => $release['timestamp'],
			],
		];
	}
}
