<?php
/**
 * The template for displaying pages.
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
			?>
			<article <?php post_class( 'page-single' ); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="entry-thumbnail">
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
				<?php endif; ?>

				<div class="entry-content">
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'supernova' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>
			</article>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}

		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
