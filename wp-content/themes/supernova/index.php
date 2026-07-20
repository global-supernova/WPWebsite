<?php
/**
 * The main template file.
 *
 * @package Supernova
 */

get_header();
?>

<main class="container content-area">
	<div class="primary" role="main">
		<?php if ( have_posts() ) : ?>

			<?php if ( is_home() && ! is_front_page() ) : ?>
				<header class="page-header">
					<h1 class="page-title"><?php single_post_title(); ?></h1>
				</header>
			<?php endif; ?>

			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			endwhile;

			the_posts_pagination(
				array(
					'prev_text' => __( '&larr; Older posts', 'supernova' ),
					'next_text' => __( 'Newer posts &rarr;', 'supernova' ),
				)
			);

		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</div>

	<?php get_sidebar(); ?>
</main>

<?php
get_footer();
