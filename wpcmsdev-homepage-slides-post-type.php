<?php
/*
Plugin Name: wpCMSdev Homepage Slides Post Type
Plugin URI:  http://wpcmsdev.com/plugins/homepage-slides-post-type/
Description: Registers a "Homepage Slides" custom post type.
Author:      wpCMSdev
Author URI:  http://wpcmsdev.com
Version:     1.0
Text Domain: wpcmsdev-homepage-slides-post-type
Domain Path: /languages
License:     GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Copyright (C) 2014  wpCMSdev

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/**
 * Registers the "homepage_slide" post type.
 */
function wpcmsdev_homepage_slides_post_type_register() {

	$labels = array(
		'name'               => __( 'Homepage Slides',                    'wpcmsdev-homepage-slides-post-type' ),
		'menu_name'          => __( 'Home Slides',                        'wpcmsdev-homepage-slides-post-type' ),
		'singular_name'      => __( 'Homepage Slide',                     'wpcmsdev-homepage-slides-post-type' ),
		'all_items'          => __( 'All Homepage Slides',                'wpcmsdev-homepage-slides-post-type' ),
		'add_new'            => _x( 'Add New', 'homepage slide',          'wpcmsdev-homepage-slides-post-type' ),
		'add_new_item'       => __( 'Add New Homepage Slide',             'wpcmsdev-homepage-slides-post-type' ),
		'edit_item'          => __( 'Edit Homepage Slide',                'wpcmsdev-homepage-slides-post-type' ),
		'new_item'           => __( 'New Homepage Slide',                 'wpcmsdev-homepage-slides-post-type' ),
		'view_item'          => __( 'View Homepage Slide',                'wpcmsdev-homepage-slides-post-type' ),
		'search_items'       => __( 'Search Homepage Slides',             'wpcmsdev-homepage-slides-post-type' ),
		'not_found'          => __( 'No homepage slides found.',          'wpcmsdev-homepage-slides-post-type' ),
		'not_found_in_trash' => __( 'No homepage slides found in Trash.', 'wpcmsdev-homepage-slides-post-type' ),
	);

	$args = array(
		'labels'        => $labels,
		'menu_icon'     => 'dashicons-desktop',
		'menu_position' => 5,
		'public'        => false,
		'show_ui'       => true,
		'supports'      => array(
			'author',
			'custom-fields',
			'editor',
			'page-attributes',
			'revisions',
			'thumbnail',
			'title',
		),
	);

	$args = apply_filters( 'wpcmsdev_homepage_slides_post_type_args', $args );

	register_post_type( 'homepage_slide', $args );

}
add_action( 'init', 'wpcmsdev_homepage_slides_post_type_register' );


/**
 * Loads the translation files.
 */
function wpcmsdev_homepage_slides_load_translations() {

	load_plugin_textdomain( 'wpcmsdev-homepage-slides-post-type', false, dirname( plugin_basename( __FILE__ ) ) ) . '/languages/';
}
add_action( 'plugins_loaded', 'wpcmsdev_homepage_slides_load_translations' );


/**
 * Initializes additional functionality when used with a theme that declares support for the plugin.
 */
function wpmcsdev_homepage_slides_additional_functionality_init() {

	if ( current_theme_supports( 'wpcmsdev-homepage-slides-post-type' ) ) {
		add_action( 'admin_enqueue_scripts',                     'wpcmsdev_homepage_slides_manage_posts_css' );
		add_action( 'manage_homepage_slide_posts_custom_column', 'wpcmsdev_homepage_slides_manage_posts_columm_content' );
		add_filter( 'cmb2_meta_boxes',                           'wpcmsdev_homepage_slides_meta_box' );
		add_filter( 'manage_edit-homepage_slide_columns',        'wpcmsdev_homepage_slides_manage_posts_columns' );
	}
}
add_action( 'after_setup_theme', 'wpmcsdev_homepage_slides_additional_functionality_init', 11 );


/**
 * Registers custom columns for the Manage Homepage Slides admin page.
 */
function wpcmsdev_homepage_slides_manage_posts_columns( $columns ) {

	$column_order     = array( 'order'       => __( 'Order', 'wpcmsdev-homepage-slides-post-type' ) );
	$column_thumbnail = array( 'thumbnail'   => __( 'Image', 'wpcmsdev-homepage-slides-post-type' ) );

	$columns = array_slice( $columns, 0, 2, true ) + $column_thumbnail + $column_order + array_slice( $columns, 2, null, true );

	return $columns;
}


/**
 * Outputs the custom column content for the Manage Homepage Slides admin page.
 */
function wpcmsdev_homepage_slides_manage_posts_columm_content( $column ) {

	global $post;

	switch( $column ) {

		case 'order':
			$order = $post->menu_order;
			if ( 0 === $order ) {
				echo '<span class="default-value">' . $order . '</span>';
			} else {
				echo $order;
			}
			break;

		case 'thumbnail':
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'thumbnail' );
			} else {
				echo '&#8212;';
			}
			break;
	}
}


/**
 * Outputs the custom columns CSS used on the Manage Homepage Slides admin page.
 */
function wpcmsdev_homepage_slides_manage_posts_css() {

	global $pagenow, $typenow;
	if ( ! ( 'edit.php' == $pagenow && 'homepage_slide' == $typenow ) ) {
		return;
	}

?>
<style>
	.edit-php .posts .column-order,
	.edit-php .posts .column-thumbnail {
		width: 10%;
	}
	.edit-php .posts .column-thumbnail img {
		width: 50px;
		height: auto;
	}
	.edit-php .posts .column-order .default-value {
		color: #bbb;
	}
</style>
<?php
}


/**
 * Creates the Slide Settings meta box and fields.
 */
function wpcmsdev_homepage_slides_meta_box( $meta_boxes ) {

	$meta_boxes['homepage-slide-settings'] = array(
		'id'           => 'homepage-slide-settings',
		'title'        => __( 'Slide Settings', 'wpcmsdev-homepage-slides-post-type' ),
		'object_types' => array( 'homepage_slide' ),
		'fields'       => array(
			array(
				'name' => __( 'Target URL', 'wpcmsdev-homepage-slides-post-type' ),
				'id'   => 'target_url',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'Button text', 'wpcmsdev-homepage-slides-post-type' ),
				'id'   => 'button_text',
				'type' => 'text',
			),
		),
	);

	return $meta_boxes;
}
