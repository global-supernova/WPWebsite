<?php
/**
 * Supernova theme functions and definitions.
 *
 * @package Supernova
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'SUPERNOVA_VERSION', '1.0.0' );

if ( ! function_exists( 'supernova_setup' ) ) {
	/**
	 * Register theme support and features.
	 */
	function supernova_setup() {
		load_theme_textdomain( 'supernova', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		add_theme_support(
			'custom-logo',
			array(
				'height'      => 48,
				'width'       => 180,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'supernova' ),
				'footer'  => __( 'Footer Menu', 'supernova' ),
			)
		);
	}
}
add_action( 'after_setup_theme', 'supernova_setup' );

/**
 * Set the content width in pixels.
 */
function supernova_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'supernova_content_width', 1140 );
}
add_action( 'after_setup_theme', 'supernova_content_width', 0 );

/**
 * Enqueue styles and scripts.
 */
function supernova_scripts() {
	wp_enqueue_style(
		'supernova-style',
		get_stylesheet_uri(),
		array(),
		SUPERNOVA_VERSION
	);

	wp_enqueue_style(
		'supernova-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'supernova-style' ),
		SUPERNOVA_VERSION
	);

	wp_enqueue_script(
		'supernova-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		SUPERNOVA_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'supernova_scripts' );

/**
 * Register widget areas.
 */
function supernova_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'supernova' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Add widgets here to appear in your sidebar.', 'supernova' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer', 'supernova' ),
			'id'            => 'footer-1',
			'description'   => __( 'Add widgets here to appear in the footer.', 'supernova' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'supernova_widgets_init' );

/**
 * Fallback menu when no primary menu is assigned.
 */
function supernova_primary_menu_fallback() {
	echo '<ul id="primary-menu" class="menu">';
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
		)
	);
	echo '</ul>';
}

require get_template_directory() . '/inc/template-tags.php';
