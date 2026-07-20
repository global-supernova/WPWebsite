<?php
/**
 * The template for displaying a single post.
 *
 * @package Supernova
 */

get_header();
?>

<main class="container content-area">
	<div class="primary" role="main">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'single' );

			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'supernova' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'supernova' ) . '</span> <span class="nav-title">%title</span>',
				)
			);

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}

		endwhile;
		?>
	</div>

	<?php get_sidebar(); ?>
</main>

<?php
get_footer();
