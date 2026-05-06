<?php

namespace ElementPack\Modules\Search;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}


	public function get_name() {
		return 'search';
	}

	public function get_widgets() {

		$widgets = [ 
			'Search',
		];

		return $widgets;
	}

	/**
	 * @param array $term_ids
	 * @return array
	 */
	private function mapGroupControlQuery( $term_ids = [] ) {
		$terms = get_terms(
			[ 
				'term_taxonomy_id' => $term_ids,
				'hide_empty'       => false,
			]
		);

		$tax_terms_map = [];

		foreach ( $terms as $term ) {
			$taxonomy                   = $term->taxonomy;
			$tax_terms_map[ $taxonomy ][] = $term->term_id;
		}

		return $tax_terms_map;
	}

	/**
	 * Normalize string for title/search comparison (trim, collapse spaces, lowercase).
	 *
	 * @param string $text Raw text.
	 * @return string
	 */
	private function normalize_search_compare_string( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$text = trim( preg_replace( '/\s+/u', ' ', $text ) );
		if ( function_exists( 'mb_strtolower' ) ) {
			return mb_strtolower( $text, 'UTF-8' );
		}
		return strtolower( $text );
	}

	/**
	 * Normalize for title ranking: ignore trailing sentence punctuation so
	 * "Some title." matches a stored title without the period.
	 *
	 * @param string $text Already lowercased normalized text.
	 * @return string
	 */
	private function normalize_search_for_title_ranking( $text ) {
		return preg_replace( '/[\s\.!?…:]+$/u', '', $text );
	}

	/**
	 * Lower rank = earlier in results (exact/prefix matches first).
	 *
	 * @param \WP_Post $post               Post object.
	 * @param string   $search_normalized Output of normalize_search_compare_string() for the query.
	 * @return int 0–3
	 */
	private function get_ajax_search_title_rank( $post, $needle_norm, $needle_rank ) {
		if ( '' === $needle_norm ) {
			return 3;
		}
		$title         = $this->normalize_search_compare_string( $post->post_title );
		$title_for_cmp = $this->normalize_search_for_title_ranking( $title );

		if ( $title === $needle_norm || $title_for_cmp === $needle_rank ) {
			return 0;
		}
		if ( function_exists( 'mb_strpos' ) ) {
			if ( mb_strpos( $title, $needle_norm, 0, 'UTF-8' ) === 0 ) {
				return 1;
			}
			if ( mb_strpos( $title_for_cmp, $needle_rank, 0, 'UTF-8' ) === 0 ) {
				return 1;
			}
			if ( false !== mb_strpos( $title, $needle_norm, 0, 'UTF-8' ) ) {
				return 2;
			}
			if ( '' !== $needle_rank && false !== mb_strpos( $title_for_cmp, $needle_rank, 0, 'UTF-8' ) ) {
				return 2;
			}
		} else {
			if ( 0 === strpos( $title, $needle_norm ) ) {
				return 1;
			}
			if ( '' !== $needle_rank && 0 === strpos( $title_for_cmp, $needle_rank ) ) {
				return 1;
			}
			if ( false !== strpos( $title, $needle_norm ) ) {
				return 2;
			}
			if ( '' !== $needle_rank && false !== strpos( $title_for_cmp, $needle_rank ) ) {
				return 2;
			}
		}
		return 3;
	}

	public function element_pack_ajax_search() {
		global $post;

		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['nonce'] ), 'element-pack-site' ) ) {
			die( json_encode( array( 'results' => array() ) ) );
		}

		$result       = array( 'results' => array() );
		$search_input = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
		$settings     = isset( $_POST['settings'] ) ? array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['settings'] ) ) : [];

		if ( strlen( $search_input ) >= 3 ) {

			$allowed_post_types = get_post_types( array( 'public' => true ) );
			$requested_type = isset( $settings['post_type'] ) ? $settings['post_type'] : 'post';
			$post_type = isset( $allowed_post_types[ $requested_type ] ) ? $requested_type : 'post';

			$response_limit = isset( $settings['per_page'] ) ? absint( $settings['per_page'] ) : 5;
			if ( $response_limit < 1 ) {
				$response_limit = 5;
			}
			if ( $response_limit > 50 ) {
				$response_limit = 50;
			}

			/*
			 * Fetch many more matches than we return: core search orders by date/relevance and
			 * can omit an exact title match from the first N rows. We rank by title client-side
			 * and slice — full-site search shows more rows so the same post appears "on top" there.
			 */
			$fetch_pool = min( 200, max( 80, $response_limit * 25 ) );

			$query_args = [
				'post_type'      => $post_type,
				's'              => sanitize_text_field( $search_input ),
				'posts_per_page' => $fetch_pool,
				'post_status'    => 'publish',
			];

			/**
			 * Set Authors
			 */
			$include_users = [];
			$exclude_users = [];
			if ( ! empty( $settings['include_author_ids'] ) ) {
				if ( in_array( 'authors', $settings['include_by'] ) ) {
					$include_users = wp_parse_id_list( $settings['include_author_ids'] );
				}
			}
			if ( ! empty( $settings['exclude_author_ids'] ) ) {
				if ( in_array( 'authors', $settings['exclude_by'] ) ) {
					$exclude_users = wp_parse_id_list( $settings['exclude_author_ids'] );
					$include_users = array_diff( $include_users, $exclude_users );
				}
			}
			if ( ! empty( $include_users ) ) {
				$query_args['author__in'] = $include_users;
			}

			if ( ! empty( $exclude_users ) ) {
				$query_args['author__not_in'] = $exclude_users;
			}

			/**
			 * Set Taxonomy
			 */

			$include_terms = [];
			$exclude_terms = [];
			$terms_query   = [];

			if ( ! empty( $settings['include_term_ids'] ) ) {
				if ( in_array( 'terms', $settings['include_by'] ) ) {
					$include_terms = wp_parse_id_list( $settings['include_term_ids'] );
				}
			}
			if ( ! empty( $settings['exclude_term_ids'] ) ) {
				if ( in_array( 'terms', $settings['exclude_by'] ) ) {
					$exclude_terms = wp_parse_id_list( $settings['exclude_term_ids'] );
					$include_terms = array_diff( $include_terms, $exclude_terms );
				}
			}

			if ( ! empty( $include_terms ) ) {
				$tax_terms_map = $this->mapGroupControlQuery( $include_terms );
				foreach ( $tax_terms_map as $tax => $terms ) {
					$terms_query[] = [ 
						'taxonomy' => $tax,
						'field'    => 'term_id',
						'terms'    => $terms,
						'operator' => 'IN',
					];
				}
			}

			if ( ! empty( $exclude_terms ) ) {
				$tax_terms_map = $this->mapGroupControlQuery( $exclude_terms );
				foreach ( $tax_terms_map as $tax => $terms ) {
					$terms_query[] = [ 
						'taxonomy' => $tax,
						'field'    => 'term_id',
						'terms'    => $terms,
						'operator' => 'NOT IN',
					];
				}
			}

			if ( ! empty( $terms_query ) ) {
				$query_args['tax_query']             = $terms_query;
				$query_args['tax_query']['relation'] = 'AND';
			}

			$query_posts = get_posts( $query_args );
			if ( ! empty( $query_posts ) ) {
				$needle_norm = $this->normalize_search_compare_string( $search_input );
				$needle_rank = $this->normalize_search_for_title_ranking( $needle_norm );

				usort(
					$query_posts,
					function ( $a, $b ) use ( $needle_norm, $needle_rank ) {
						$ra = $this->get_ajax_search_title_rank( $a, $needle_norm, $needle_rank );
						$rb = $this->get_ajax_search_title_rank( $b, $needle_norm, $needle_rank );
						return $ra <=> $rb;
					}
				);

				$query_posts = array_slice( $query_posts, 0, $response_limit );

				foreach ( $query_posts as $post ) {
					$content = ! empty( $post->post_excerpt ) ? strip_tags( strip_shortcodes( $post->post_excerpt ) ) : strip_tags( strip_shortcodes( $post->post_content ) );
					if ( strlen( $content ) > 180 ) {
						$content = substr( $content, 0, 179 ) . '...';
					}
					$result['results'][] = array(
						'title' => get_the_title(),
						'text'  => $content,
						'url'   => get_permalink( $post->ID ),
					);
				}
			}
		}

		die( json_encode( $result ) );
	}


	protected function add_actions() {

		// TODO AJAX SEARCH
		add_action( 'wp_ajax_element_pack_search', [ $this, 'element_pack_ajax_search' ] );
		add_action( 'wp_ajax_nopriv_element_pack_search', [ $this, 'element_pack_ajax_search' ] );
	}
}
