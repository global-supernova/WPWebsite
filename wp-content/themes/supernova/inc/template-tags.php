<?php
/**
 * Custom template tags for the Supernova theme.
 *
 * @package Supernova
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'supernova_posted_on' ) ) {
	/**
	 * Print HTML with meta information for the current post date/time.
	 */
	function supernova_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);

		echo '<span class="posted-on">' . $time_string . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'supernova_entry_footer' ) ) {
	/**
	 * Print HTML with meta information for categories and tags.
	 */
	function supernova_entry_footer() {
		if ( 'post' !== get_post_type() ) {
			return;
		}

		$categories_list = get_the_category_list( ', ' );
		if ( $categories_list ) {
			echo '<span class="cat-links">' . wp_kses_post( $categories_list ) . '</span>';
		}

		$tags_list = get_the_tag_list( '', ', ' );
		if ( $tags_list ) {
			echo '<span class="tags-links">' . wp_kses_post( $tags_list ) . '</span>';
		}
	}
}
