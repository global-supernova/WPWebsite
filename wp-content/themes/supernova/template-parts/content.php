<?php
/**
 * Template part for displaying posts in a list/archive context.
 *
 * @package Supernova
 */

?>
<article <?php post_class( 'post-summary' ); ?>>
	<header class="entry-header">
		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>
		<div class="entry-meta">
			<span class="posted-on"><?php echo esc_html( get_the_date() ); ?></span>
			<span class="byline"><?php echo esc_html( get_the_author() ); ?></span>
		</div>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<a class="entry-thumbnail" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'medium_large' ); ?>
		</a>
	<?php endif; ?>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>

	<footer class="entry-footer">
		<a class="read-more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Continue reading', 'supernova' ); ?> &rarr;
		</a>
	</footer>
</article>
