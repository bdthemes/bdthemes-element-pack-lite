<?php
namespace ElementPack\Includes\TemplateLibrary\Editor\Manager;

use Elementor\TemplateLibrary\Source_Base;

defined( 'ABSPATH' ) || exit;
class Library_Source extends Source_Base {

	/**
	 * Template library data cache
	 */
	const LIBRARY_CACHE_KEY = 'elementpack_library_cache';

	public function get_id() {
		return 'elementpack-library';
	}

	public function get_title() {
		return esc_html__( 'Element Pack Library', 'bdthemes-element-pack-lite' );
	}

	public function register_data() {}

	public function save_item( $template_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot save template to a Element Pack Library' );
	}

	public function update_item( $new_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot update template to a Element Pack Library' );
	}

	public function delete_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot delete template from a Element Pack Library' );
	}

	public function export_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot export template from a Element Pack Library' );
	}

	public function get_items( $args = [] ) {
		$library_data = self::get_library_data();

		$templates = [];

		if ( ! empty( $library_data['templates'] ) ) {
			foreach ( $library_data['templates'] as $template_data ) {
				$templates[] = $this->prepare_template( $template_data );
			}
		}

		return $templates;
	}

	public function get_tags() {
		$library_data = self::get_library_data();

		return ( ! empty( $library_data['tags'] ) ? $library_data['tags'] : [] );
	}

	/**
	 * Prepare template items to match model
	 *
	 * @param array $template_data
	 * @return array
	 */
	private function prepare_template( array $template_data ) {
		return [
			'template_id' => $template_data['id'],
			'title'       => $template_data['title'],
			'type'        => $template_data['type'],
			'thumbnail'   => $template_data['thumbnail'],
			'date'        => $template_data['created_at'],
			'tags'        => $template_data['tags'],
			'isPro'       => $template_data['is_pro'],
			'url'         => $template_data['url'],
		];
	}

	/**
	 * Get library data from remote source and cache
	 *
	 * @param boolean $force_update
	 * @return array
	 */
	private static function request_library_data( $force_update = false ) {
		$data = get_option( self::LIBRARY_CACHE_KEY );

		if ( $force_update || false === $data ) {
			$timeout = ( $force_update ) ? 25 : 8;

			$response = wp_remote_get( self::API_TEMPLATES_INFO_URL, [
				'timeout' => $timeout,
			] );

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				update_option( self::LIBRARY_CACHE_KEY, [] );
				return false;
			}

			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( empty( $data ) || ! is_array( $data ) ) {
				update_option( self::LIBRARY_CACHE_KEY, [] );
				return false;
			}

			update_option( self::LIBRARY_CACHE_KEY, $data, 'no' );
		}

		return $data;
	}

	/**
	 * Get library data
	 *
	 * @param boolean $force_update
	 * @return array
	 */
	public static function get_library_data( $force_update = false ) {
		self::request_library_data( $force_update );

		$data = get_option( self::LIBRARY_CACHE_KEY );

		if ( empty( $data ) ) {
			return [];
		}

		return $data;
	}

	/**
	 * Get remote template.
	 *
	 * Retrieve a single remote template from Elementor.com servers.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return array Remote template.
	 */
	public function get_item( $template_id ) {
		$templates = $this->get_items();

		return $templates[ $template_id ];
	}

	public function request_template_data( $request_url ) {
		if ( empty( $request_url ) ) {
			return;
		}
		
		$response = wp_remote_get( $request_url,
			array(
				'timeout'     => 120,
				'httpversion' => '1.1',
			)
		);

		$body = wp_remote_retrieve_body( $response );

		// Detect reCAPTCHA HTML
		if ( strpos( $body, 'recaptcha/api.js' ) !== false || 
			strpos( $body, 'Verifying that you are not a robot') !== false ) {
			throw new \Exception('Blocked by reCAPTCHA instead of JSON response');
		}

		return $body;
	}

	/**
	 * Get remote template data.
	 *
	 * Retrieve the data of a single remote template from Elementor.com servers.
	 *
	 * @return array|\WP_Error Remote Template data.
	 */
	public function get_data( array $args, $context = 'display' ) {
		$progress = new Import_Progress( isset( $args['import_id'] ) ? $args['import_id'] : '' );

		try {
			$progress->update( 'fetching', 2 );

			$data = $this->request_template_data( $args['demo_json'] );

			$data = json_decode( $data, true );

			if ( empty( $data ) || empty( $data['content'] ) ) {
				throw new \Exception( esc_html__( 'Template does not have any content', 'bdthemes-element-pack-lite' ) );
			}

			$progress->update( 'preparing', 8 );

			$data['content'] = $this->replace_elements_ids( $data['content'] );
			$data['content'] = $this->import_content_with_progress( $data['content'], $progress );

			$progress->update( 'finalizing', 96 );

			$post_id = $args['editor_post_id'];
			$document = \Elementor\Plugin::instance()->documents->get( $post_id );

			if ( $document ) {
				$data['content'] = $document->get_elements_raw_data( $data['content'], true );
			}

			$progress->update( 'complete', 100 );

			return $data;
		} catch ( \Exception $e ) {
			// The editor stops polling as soon as the import request itself
			// fails, so the state is of no further use to anyone.
			$progress->clear();

			throw $e;
		}
	}

	/**
	 * Element-for-element equivalent of Source_Base::process_export_import_content()
	 * for the `on_import` method, reporting progress as it walks the tree.
	 *
	 * Element count is the only dependable progress signal available here. The
	 * obvious alternative — counting downloaded images via the
	 * `elementor/template_library/import_images/new_attachment` filter — stalls
	 * badly, because that filter is skipped for any image already present in the
	 * media library and for any image whose download fails. Image downloads
	 * happen inside this loop anyway, so element progress still tracks the part
	 * that actually takes the time.
	 *
	 * @param array            $content  A set of elements.
	 * @param Import_Progress  $progress Progress reporter.
	 *
	 * @return mixed Processed content data.
	 */
	private function import_content_with_progress( $content, Import_Progress $progress ) {
		$total  = max( 1, $this->count_elements( $content ) );
		$done   = 0;
		$images = 0;

		$count_image = function( $post_id ) use ( &$images ) {
			$images++;

			return $post_id;
		};

		add_filter( 'elementor/template_library/import_images/new_attachment', $count_image );

		try {
			$content = \Elementor\Plugin::$instance->db->iterate_data(
				$content, function( $element_data ) use ( &$done, &$images, $total, $progress ) {
					$element = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

					$done++;

					// A null element is one whose widget isn't registered, e.g. a
					// disabled module. Matches Source_Base: drop it and move on.
					$processed = $element ? $this->process_element_export_import_content( $element, 'on_import' ) : null;

					$progress->update(
						'importing',
						10 + (int) floor( 85 * $done / $total ),
						[
							'done'   => $done,
							'total'  => $total,
							'images' => $images,
						]
					);

					return $processed;
				}
			);
		} finally {
			remove_filter( 'elementor/template_library/import_images/new_attachment', $count_image );
		}

		return $content;
	}

	/**
	 * Count the elements in a content tree, so progress has a denominator.
	 *
	 * @param mixed $content
	 *
	 * @return int
	 */
	private function count_elements( $content ) {
		if ( ! is_array( $content ) ) {
			return 0;
		}

		$count = 0;

		if ( isset( $content['elType'] ) ) {
			$count++;

			if ( ! empty( $content['elements'] ) ) {
				$count += $this->count_elements( $content['elements'] );
			}

			return $count;
		}

		foreach ( $content as $child ) {
			$count += $this->count_elements( $child );
		}

		return $count;
	}
}
