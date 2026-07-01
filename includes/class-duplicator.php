<?php

namespace ElementPack\Includes;

use Elementor\Core\Files\CSS\Post as Post_CSS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if accessed directly
/**
 * Duplicator Class
 */

class BdThemes_Duplicator {

	public function __construct() {
		add_action( 'admin_action_bdt_duplicate_as_draft', [ $this, 'bdt_duplicate_as_draft' ] );
		add_action( 'admin_init', [ $this, 'register_row_action_hooks' ] );
	}

	/**
	 * Register duplicate links for all supported post types.
	 */
	public function register_row_action_hooks() {
		foreach ( $this->get_duplicatable_post_types() as $post_type ) {
			add_filter( "{$post_type}_row_actions", [ $this, 'bdt_duplicate_post_link' ], 10, 2 );
		}
	}

	/**
	 * Post types that should show the duplicate row action.
	 *
	 * @return string[]
	 */
	protected function get_duplicatable_post_types() {
		$post_types = get_post_types(
			[
				'show_ui' => true,
			],
			'names'
		);

		unset( $post_types['attachment'] );

		/**
		 * Filter duplicatable post types.
		 *
		 * @param string[] $post_types Post type slugs.
		 */
		return apply_filters( 'element_pack/duplicator/post_types', array_values( $post_types ) );
	}

	/**
	 * Whether the current user can duplicate a post.
	 *
	 * @param \WP_Post $post Post object.
	 * @return bool
	 */
	protected function user_can_duplicate_post( $post ) {
		if ( ! $post instanceof \WP_Post ) {
			return false;
		}

		return current_user_can( 'edit_post', $post->ID );
	}

	public function bdt_duplicate_as_draft() {

		if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'bdt_duplicate_as_draft' === $_REQUEST['action'] ) ) ) {
			wp_die( esc_html__( 'No post to duplicate has been supplied!', 'bdthemes-element-pack' ) );
		}

		/**
		 * Nonce verification
		 */
		if ( ! isset( $_GET['duplicate_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['duplicate_nonce'] ) ), basename( __FILE__ ) ) ) {
			wp_die( esc_html__( 'Security check failed. Please try again.', 'bdthemes-element-pack' ) );
		}

		/**
		 * get the original post id
		 */
		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : absint( wp_unslash( $_POST['post'] ) );

		if ( ! $this->user_can_duplicate_post( get_post( $post_id ) ) ) {
			wp_die( esc_html__( 'You don\'t have permission to duplicate it; please go back!', 'bdthemes-element-pack' ) );
		}

		$this->duplicate_edit_post( $post_id );
	}

	/**
	 * duplicate edit post
	 */
	public function duplicate_edit_post( $post_id ) {
		global $wpdb;
		/**
		 * and all the original post data then
		 */
		$bdt_post = get_post( $post_id );
		/**
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$bdt_current_user    = wp_get_current_user();
		$bdt_new_post_author = $bdt_current_user->ID;

		/**
		 * if post data exists, create the post duplicate
		 */
		if ( isset( $bdt_post ) && $bdt_post != null ) {
			/**
			 * new post data array
			 */
			$bdt_args = [ 
				'post_status'    => 'draft',
				/* translators: %1$s: Original post title */
				'post_title'     => sprintf( __( '%1$s - [Duplicated]', 'bdthemes-element-pack' ), $bdt_post->post_title ),
				'post_type'      => $bdt_post->post_type,
				'post_name'      => $bdt_post->post_name,
				'post_content'   => $bdt_post->post_content,
				'post_excerpt'   => $bdt_post->post_excerpt,
				'post_author'    => $bdt_new_post_author,
				'post_parent'    => $bdt_post->post_parent,
				'post_password'  => $bdt_post->post_password,
				'comment_status' => $bdt_post->comment_status,
				'ping_status'    => $bdt_post->ping_status,
				'menu_order'     => $bdt_post->menu_order,
				'to_ping'        => $bdt_post->to_ping,
			];

			/**
			 * insert the post by wp_insert_post() function
			 */
			$bdt_new_post_id = wp_insert_post( $bdt_args );

			/**
			 * get all current post terms ad set them to the new post draft
			 */
			$bdt_taxonomies = get_object_taxonomies( $bdt_post->post_type );

			/**
			 * returns array of taxonomy names for post type, ex array("category", "post_tag");
			 */

			foreach ( $bdt_taxonomies as $bdt_taxonomy ) {
				$bdt_post_terms = wp_get_object_terms( $post_id, $bdt_taxonomy, [ 'fields' => 'slugs' ] );
				wp_set_object_terms( $bdt_new_post_id, $bdt_post_terms, $bdt_taxonomy, false );
			}

			$bdt_post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : false;

			/**
			 * Duplicate all post meta just in two SQL queries
			 */

			if ( $bdt_post_id ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$bdt_post_meta_infos = $wpdb->get_results( $wpdb->prepare(
					"SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d",
					$bdt_post_id
				) );
			}

			if ( isset( $bdt_post_meta_infos ) && is_array( $bdt_post_meta_infos ) ) {
				foreach ( $bdt_post_meta_infos as $bdt_meta_info ) {
					$wpdb->insert(
						$wpdb->postmeta,
						array(
							'post_id'    => $bdt_new_post_id,
							'meta_key'   => $bdt_meta_info->meta_key,
							'meta_value' => $bdt_meta_info->meta_value,
						),
						array( '%d', '%s', '%s' )
					);
				}

				/**
				 * fix template type issues
				 */
				$source_type = get_post_meta( $post_id, '_elementor_template_type', true );
				delete_post_meta( $bdt_new_post_id, '_elementor_template_type' );
				update_post_meta( $bdt_new_post_id, '_elementor_template_type', $source_type );
			}

			if ( class_exists( Post_CSS::class ) ) {
				$css = Post_CSS::create( $bdt_new_post_id );
				$css->update();
			}

			/**
			 * Redirect back to the post type list after duplication.
			 */
			wp_safe_redirect( admin_url( 'edit.php?post_type=' . $bdt_post->post_type ) );
			exit;
		} else {
			wp_die( 'Failed. Not Found Post: ' . esc_html( $post_id ) );
		}
	}


	public function bdt_duplicate_post_link( $actions, $post ) {

		if ( ! $this->user_can_duplicate_post( $post ) ) {
			return $actions;
		}

		if ( ! in_array( $post->post_type, $this->get_duplicatable_post_types(), true ) ) {
			return $actions;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$label            = esc_html_x( 'Duplicate', 'Admin String', 'bdthemes-element-pack' );

		if ( $post_type_object && ! empty( $post_type_object->labels->singular_name ) ) {
			$label = sprintf(
				/* translators: %s: post type singular name */
				esc_html_x( 'Duplicate %s', 'Admin String', 'bdthemes-element-pack' ),
				$post_type_object->labels->singular_name
			);
		}

		$actions['duplicate'] = '<a href="' . wp_nonce_url(
			'admin.php?action=bdt_duplicate_as_draft&post=' . absint( $post->ID ),
			basename( __FILE__ ),
			'duplicate_nonce'
		) . '" title="' . esc_attr( $label ) . '" rel="permalink">' . esc_html( $label ) . '</a>';

		return $actions;
	}
}

if ( class_exists( '\ElementPack\Includes\BdThemes_Duplicator' ) ) {
	new BdThemes_Duplicator();
}
