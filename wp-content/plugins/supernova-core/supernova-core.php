<?php
/**
 * Plugin Name:       Supernova Core
 * Plugin URI:        https://github.com/global-supernova/wpwebsite
 * Description:        Site-specific functionality for the Global Supernova website: registers a "Project" custom post type and shortcodes. Keeping this in a plugin means it survives theme changes.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Global Supernova
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       supernova-core
 *
 * @package SupernovaCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUPERNOVA_CORE_VERSION', '1.0.0' );

/**
 * Register the "Project" custom post type.
 */
function supernova_core_register_project_cpt() {
	$labels = array(
		'name'          => __( 'Projects', 'supernova-core' ),
		'singular_name' => __( 'Project', 'supernova-core' ),
		'add_new_item'  => __( 'Add New Project', 'supernova-core' ),
		'edit_item'     => __( 'Edit Project', 'supernova-core' ),
		'all_items'     => __( 'All Projects', 'supernova-core' ),
		'menu_name'     => __( 'Projects', 'supernova-core' ),
	);

	$args = array(
		'labels'       => $labels,
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-star-filled',
		'rewrite'      => array( 'slug' => 'projects' ),
		'show_in_rest' => true,
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
	);

	register_post_type( 'project', $args );
}
add_action( 'init', 'supernova_core_register_project_cpt' );

/**
 * A simple call-to-action shortcode: [supernova_cta text="..." url="..." label="..."]
 *
 * @param array $atts Shortcode attributes.
 * @return string Rendered HTML.
 */
function supernova_core_cta_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'text'  => __( 'Ready to get started?', 'supernova-core' ),
			'url'   => home_url( '/' ),
			'label' => __( 'Get in touch', 'supernova-core' ),
		),
		$atts,
		'supernova_cta'
	);

	return sprintf(
		'<div class="supernova-cta"><p>%1$s</p><a class="button button--primary" href="%2$s">%3$s</a></div>',
		esc_html( $atts['text'] ),
		esc_url( $atts['url'] ),
		esc_html( $atts['label'] )
	);
}
add_shortcode( 'supernova_cta', 'supernova_core_cta_shortcode' );

/**
 * Flush rewrite rules on activation so custom post type URLs work immediately.
 */
function supernova_core_activate() {
	supernova_core_register_project_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'supernova_core_activate' );

/**
 * Clean up rewrite rules on deactivation.
 */
function supernova_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'supernova_core_deactivate' );
