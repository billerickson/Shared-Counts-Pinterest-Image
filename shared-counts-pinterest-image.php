<?php
/**
 * Plugin Name: Shared Counts - Pinterest Image
 * Plugin URI:  https://wordpress.org/plugins/shared-counts-pinterest-image/
 * Description: Use a separate image for sharing on Pinterest
 * Author:      Bill Erickson
 * Author URI:  https://www.billerickson.net
 * Version:     1.4.1
 *
 * Shared Counts - Pinterest Image is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Shared Counts - Pinterest Image is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Shared Counts. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    SharedCountsPinterestImage
 * @author     Bill Erickson
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2017
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shared_Counts_Pinterest_Image {

	/**
	 * Plugin Veresion
	 *
	 * @since 1.0.0
	 */
	private $plugin_version = '1.4.1';

	/**
	 * Nonce Value
	 *
	 * @since 1.0.0
	 */
	private $nonce = 'shared_counts_pinterest_image_nonce';

	/**
	 * Primary constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 */
	function init() {

		// Metabox
		add_action( 'admin_enqueue_scripts',	array( $this, 'scripts'          )         );
		add_action( 'add_meta_boxes',			array( $this, 'metabox_register' )         );
		add_action( 'save_post',				array( $this, 'metabox_save'     ),  1, 2  );

		// Shared Counts integration
		add_filter( 'shared_counts_single_image', array( $this, 'pinterest_image'  ), 10, 3  );
		add_filter( 'shared_counts_link',         array( $this, 'pinterest_desc'   ), 10, 3 );

		// Include hidden image in content, for pinterest browser extensions
		add_filter( 'the_content', 				array( $this, 'pinterest_image_content' ) );

	}

	/**
	 * Register Scripts
	 *
	 * @since 1.0.0
	 */
	function scripts() {

		wp_register_script( 'shared-counts-pinterest-image', plugins_url( 'assets/js/shared-counts-pinterest-image.js', __FILE__ ), array( 'jquery' ), $this->plugin_version, true );
	}

	/**
	 * Register Metabox
	 *
	 * @since 1.0.0
	 */
	function metabox_register() {

		// Make sure Shared Counts is active
		if( ! function_exists( 'shared_counts' ) )
			return;

		$post_types = apply_filters( 'shared_counts_pinterest_image_post_types', array( 'post' ) );
		foreach( $post_types as $post_type ) {
			add_meta_box( 'shared-counts-pinterest-image', 'Pinterest Sharing Image', array( $this, 'metabox_render' ), $post_type, 'side', 'low' );
		}
	}

	/**
	 * Render Metabox
	 *
	 * @since 1.0.0
	 */
	function metabox_render() {

		// Load assets
		wp_enqueue_script( 'shared-counts-pinterest-image' );

		// Security nonce
		wp_nonce_field( plugin_basename( __FILE__ ), $this->nonce );

		// Image
		$image_url = apply_filters( 'shared_counts_pinterest_image_url', esc_url_raw( get_post_meta( get_the_ID(), $this->meta_key( 'image' ), true ) ) );
		$image = !empty( $image_url ) ? '<img src="' . $image_url . '" style="max-width: 100%; height: auto;" />' : '';

		// Links
		$link_format = empty( $image ) ? '<button class="button">%s</button><a href="#" style="display: none;">%s</a>' : '<button class="button" style="display: none;">%s</button><a href="#">%s</a>';

		echo '<div class="shared-counts-pinterest-image-setting">';

			echo '<div class="shared-counts-pinterest-image-setting-field" style="overflow: hidden; width: 100%;">';
				echo $image;
				echo '<input type="text" class="sc-pinterest-image" name="' . $this->meta_key( 'image' ) . '" value="' . $image_url . '" style="display: none;">';

				printf(
					$link_format,
					__( 'Select Image', 'shared-counts-pinterest-image' ),
					__( 'Remove Image', 'shared-counts-pinterest-image' )
				);

				echo '<p><label for="' . $this->meta_key( 'desc' ) . '">Description</label><br /><input class="widefat" type="text" name="' . $this->meta_key( 'desc' ) . '" class="sc-pinterest-description" value="' . get_post_meta( get_the_ID(), $this->meta_key( 'desc' ), true ) . '" />';

			echo '</div>';

		echo '</div>';

	}

	/**
	 * Save Metabox
	 *
	 * @since 1.0.0
	 */
	function metabox_save( $post_id, $post ) {

		if( ! $this->user_can_save( $post_id, $this->nonce ) )
			return;

		update_post_meta( $post_id, $this->meta_key( 'image' ), esc_url_raw( $_POST[ $this->meta_key( 'image' ) ] ) );
		update_post_meta( $post_id, $this->meta_key( 'desc' ), esc_html( $_POST[ $this->meta_key( 'desc' ) ] ) );
	}

	/**
	 * User can save metabox
	 *
	 * @since 1.0.0
	 */
	function user_can_save( $post_id, $nonce ) {

		// Security check
		if ( ! isset( $_POST[ $nonce ] ) || ! wp_verify_nonce( $_POST[ $nonce ], plugin_basename( __FILE__ ) ) ) {
			return false;
		}

		// Bail out if running an autosave, ajax, cron.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return false;
		}

		// Bail out if the user doesn't have the correct permissions to edit the post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		// Good to go!
		return true;
	}

	/**
	 * Pinterest Image
	 *
	 * @since 1.0.0
	 */
	function pinterest_image( $image_url, $id, $link ) {

		if( 'pinterest' != $link['type'] )
			return $image_url;

		$pinterest_image = apply_filters( 'shared_counts_pinterest_image_url', get_post_meta( $id, $this->meta_key( 'image' ), true ) );
		if( !empty( $pinterest_image ) )
			$image_url = $pinterest_image;

		return $image_url;
	}

	/**
	 * Pinterest Description
	 *
	 * @since 1.1.0
	 *
	 */
	function pinterest_desc( $link, $id, $style ) {
		if( 'pinterest' !== $link['type'] )
			return $link;

		$description = get_post_meta( $id, $this->meta_key( 'desc' ), true );
		if( empty( $description ) )
			return $link;

		$url = explode( 'description=', $link['link'] );
		$link['link'] = $url[0] . 'description=' . rawurlencode( wp_strip_all_tags( $description ) );
		return $link;

	}

	/**
	 * Pinterest image in content
	 *
	 * @since 1.2.0
	 *
	 * @param string $content
	 * @return string
	 */
	function pinterest_image_content( $content ) {

		if( ! ( is_singular() && function_exists( 'shared_counts' ) ) )
			return $content;

		$options = shared_counts()->admin->options();
		if( empty( $options['post_type'] ) || ! in_array( get_post_type(), $options['post_type'] ) )
			return $content;

		if( ! apply_filters( 'shared_counts_pinterest_image_content', true ) )
			return $content;

		$image_url = false;
		$pinterest_image = get_post_meta( get_the_ID(), $this->meta_key( 'image' ), true );
		if( !empty( $pinterest_image ) ) {
			$image_url = $pinterest_image;
		} elseif( has_post_thumbnail() ) {
			$image_url = wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' );
		} else {
			$image = apply_filters( 'shared_counts_default_image', '', get_the_ID(), array( 'type' => 'pinterest' ) );
			if( !empty( $image ) )
				$image_url = wp_get_attachment_image_url( intval( $image ), 'full' );
		}
		$image_url = apply_filters( 'shared_counts_pinterest_image_url', $image_url );

		if( empty( $image_url ) )
			return $content;

		$description = get_post_meta( get_the_ID(), $this->meta_key( 'desc' ), true );
		if( empty( $description ) )
			$description = wp_strip_all_tags( get_the_title() );

		$hidden_image = '<div class="shared-counts-hidden-image-container" style="display:none;"><img src="' . $image_url . '" class="shared-counts-hidden-image" data-pin-description="' . $description . '" data-pin-media="' . $image_url . '" alt="" /></div>';

		return $hidden_image . $content;

	}

	/**
	 * Meta Key
	 *
	 */
	function meta_key( $type = 'image' ) {
		$key = false;
		if( 'image' === $type ) {
			$key = 'shared_counts_pinterest_image';
		} elseif( 'desc' === $type ) {
			$key = 'shared_counts_pinterest_image_description';
		}

		return apply_filters( 'shared_counts_pinterest_image_meta_key', $key, $type );
	}

}
new Shared_Counts_Pinterest_Image;
