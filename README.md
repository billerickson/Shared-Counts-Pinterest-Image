# [Shared Counts - Pinterest Image](https://wordpress.org/plugins/shared-counts-pinterest-image/) #

![Plugin Version](https://img.shields.io/wordpress/plugin/v/shared-counts-pinterest-image.svg?style=flat-square) ![Total Downloads](https://img.shields.io/wordpress/plugin/dt/shared-counts-pinterest-image.svg?style=flat-square) ![Plugin Rating](https://img.shields.io/wordpress/plugin/r/shared-counts-pinterest-image.svg?style=flat-square) ![WordPress Compatibility](https://img.shields.io/wordpress/v/shared-counts-pinterest-image.svg?style=flat-square) ![License](https://img.shields.io/badge/license-GPL--2.0%2B-red.svg?style=flat-square)

**Contributors:** billerickson  
**Tags:** pinterest, image, sharing, social sharing, share buttons, social buttons, share counts, social  
**Requires at least:** 4.6  
**Tested up to:** 4.9  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

This add-on for [Shared Counts](https://wordpress.org/plugins/shared-counts) allows you to share a different image on Pinterest.

Shared Counts uses the post's featured image for sharing across all platforms. But given the unique display of images on Pinterest, it's useful to specify a separate, Pinterest-only image that's formatted for that service.

This plugin will only work if [Shared Counts](https://wordpress.org/plugins/shared-counts) is active. It does not work with any other social sharing plugins.

## Screenshot ##

<img width="306" alt="screenshot" src="https://user-images.githubusercontent.com/685131/40438104-cebfb6e6-5e7c-11e8-8d30-bc11e268ebf7.png">

## Installation ##
1. Download the plugin [from GitHub.](https://github.com/billerickson/Shared-Counts-Pinterest-Image/archive/master.zip) or from [WordPress.org](https://wordpress.org/plugins/shared-counts-pinterest-image/).
2. Activate plugin.
3. When editing a post, use the "Pinterest Sharing Image" metabox in the sidebar (see screenshot).

## Customization ##

By default the Pinterest Sharing Image box is only added to posts, but you can use a filter to add support for other post types.

The following code will add the box to pages. Add it to your theme's functions.php file or a [Core Functionality plugin](https://www.billerickson.net/core-functionality-plugin/).

```php
/**
 * Pinterest Sharing Image on pages
 *
 * @author Bill Erickson
 * @see https://github.com/billerickson/Shared-Counts-Pinterest-Image
 *
 * @param array $post_types
 * @return array
 */
function be_pinterest_image_on_pages( $post_types ) {
	$post_types[] = 'page';
	return $post_types;
}
add_filter( 'shared_counts_pinterest_image_post_types', 'be_pinterest_image_on_pages' );
```

## Bugs ##
If you find an bug or problem, please let us know by [creating an issue](https://github.com/billerickson/Shared-Counts-Pinterest-Image/issues?state=open).
