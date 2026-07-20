<?php
/**
 * Template part for displaying a single post's content.
 *
 * @package Supernova
 */

?>
<article <?php post_class( 'post-single' ); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<div class="entry-meta">
			<span class="posted-on"><?php echo esc_html( get_the_date() ); ?></span>
			<span class="byline"><?php echo esc_html( get_the_author() ); ?></span>
			<?php
			$supernova_categories = get_the_category_list( ', ' );
			if ( $supernova_categories ) {
				echo '<span class="cat-links">' . wp_kses_post( $supernova_categories ) . '</span>';
			}
			?>
		</div>
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

	<footer class="entry-footer">
		<?php
		$supernova_tags = get_the_tag_list( '<ul class="post-tags"><li>', '</li><li>', '</li></ul>' );
		if ( $supernova_tags ) {
			echo wp_kses_post( $supernova_tags );
		}
		?>
	</footer>
</article>
