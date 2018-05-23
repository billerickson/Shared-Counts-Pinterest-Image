<?php
/**
 * Plugin Name: Shared Counts - Pinterest Image
 * Plugin URI:  https://wordpress.org/plugins/shared-counts-pinterest-image/
 * Description: Use a separate image for sharing on Pinterest
 * Author:      Bill Erickson & Jared Atchison
 * Version:     1.0.0
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
 * @author     Bill Erickson & Jared Atchison
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
	private $plugin_version = '1.0.0';

	/**
	 * Meta Key
	 *
	 * @since 1.0.0
	 */
	private $meta_key = 'shared_counts_pinterest_image';

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

		// Translations
		load_plugin_textdomain( 'shared-counts-pinterest-image', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		add_action( 'admin_enqueue_scripts',	array( $this, 'scripts'          )         );
		add_action( 'add_meta_boxes',			array( $this, 'metabox_register' )         );
		add_action( 'save_post',				array( $this, 'metabox_save'     ),  1, 2  );

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
			add_meta_box( 'shared-counts-pinterest-image', 'Shared Counts - Pinterest Image', array( $this, 'metabox_render' ), $post_type, 'normal', 'high' );
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


		echo '<div class="shared-counts-pinterest-image-setting">';
		printf( '<h3><label for="' . $this->meta_key . '">%s</label></h3>', __( 'Pinterest Sharing Image', 'shared-counts-pinterest-image' ) );
		printf( '<p>%s</p>', __( 'If blank, the post\'s featured image is used.', 'shared-counts-pinterest-image' ) );

		echo '<span class="shared-counts-pinterest-image-setting-field">
				<img src="">
				<input type="text" name="' . $this->meta_key . '" value="">
				<button class="button">Upload Image</button>
			</span>';

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

		update_post_meta( $post_id, $this->meta_key, esc_url_raw( $_POST[ $this->meta_key] ) );
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

}
new Shared_Counts_Pinterest_Image;
