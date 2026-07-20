<?php
/**
 * The template for displaying search results.
 *
 * @package Supernova
 */

get_header();
?>

<main class="container content-area">
	<div class="primary" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="page-header">
				<h1 class="page-title">
					<?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Search results for: %s', 'supernova' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
					?>
				</h1>
			</header>

			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'search' );
			endwhile;

			the_posts_pagination();

		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</div>
</main>

<?php
get_footer();
