<?php
/**
 * Template part shown when no content is found.
 *
 * @package Supernova
 */

?>
<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing here', 'supernova' ); ?></h1>
	</header>

	<div class="page-content">
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'Sorry, nothing matched your search. Please try again with different keywords.', 'supernova' ); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'It looks like nothing was found here. Maybe try a search?', 'supernova' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>
