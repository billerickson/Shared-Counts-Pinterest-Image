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
	 */
	function init() {

		// Translations
		load_plugin_textdomain( 'shared-counts-pinterest-image', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

}
