<?php
/**
 * The template for displaying archive pages.
 *
 * @package Supernova
 */

get_header();
?>

<main class="container content-area">
	<div class="primary" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="page-header">
				<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header>

			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			endwhile;

			the_posts_pagination();

		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</div>

	<?php get_sidebar(); ?>
</main>

<?php
get_footer();
