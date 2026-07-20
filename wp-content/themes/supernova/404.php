<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Supernova
 */

get_header();
?>

<main class="container content-area">
	<div class="primary" role="main">
		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( '404', 'supernova' ); ?></h1>
			</header>
			<div class="page-content">
				<p><?php esc_html_e( 'The page you are looking for could not be found. It may have moved, or never existed.', 'supernova' ); ?></p>
				<?php get_search_form(); ?>
				<p><a class="button button--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'supernova' ); ?></a></p>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
