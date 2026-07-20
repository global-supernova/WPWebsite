<?php
/**
 * The front page template.
 *
 * Shown on the site's homepage. If a static page is not assigned, it still
 * renders the hero and falls back to the latest posts below.
 *
 * @package Supernova
 */

get_header();
?>

<section class="hero">
	<div class="container hero__inner">
		<p class="hero__eyebrow"><?php esc_html_e( 'Global Supernova', 'supernova' ); ?></p>
		<h1 class="hero__title">
			<?php
			if ( is_page() && get_the_title() ) {
				the_title();
			} else {
				esc_html_e( 'Building brilliant things, at cosmic scale.', 'supernova' );
			}
			?>
		</h1>
		<p class="hero__subtitle">
			<?php esc_html_e( 'A modern WordPress site powered by the Supernova theme. Fast, accessible, and ready to customize.', 'supernova' ); ?>
		</p>
		<p class="hero__actions">
			<a class="button button--primary" href="<?php echo esc_url( home_url( '/?page_id=2' ) ); ?>"><?php esc_html_e( 'Learn more', 'supernova' ); ?></a>
			<a class="button button--ghost" href="#latest"><?php esc_html_e( 'Read the blog', 'supernova' ); ?></a>
		</p>
	</div>
</section>

<section class="features">
	<div class="container features__grid">
		<article class="feature-card">
			<h2 class="feature-card__title"><?php esc_html_e( 'Fast by default', 'supernova' ); ?></h2>
			<p><?php esc_html_e( 'Minimal CSS and JavaScript keep pages loading quickly on every device.', 'supernova' ); ?></p>
		</article>
		<article class="feature-card">
			<h2 class="feature-card__title"><?php esc_html_e( 'Accessible', 'supernova' ); ?></h2>
			<p><?php esc_html_e( 'Semantic markup, skip links, and keyboard-friendly navigation out of the box.', 'supernova' ); ?></p>
		</article>
		<article class="feature-card">
			<h2 class="feature-card__title"><?php esc_html_e( 'Block ready', 'supernova' ); ?></h2>
			<p><?php esc_html_e( 'Supports wide alignment, block styles, and the WordPress editor.', 'supernova' ); ?></p>
		</article>
	</div>
</section>

<?php if ( is_page() ) : ?>
	<section class="container content-area">
		<div class="primary" role="main">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</section>
<?php endif; ?>

<section id="latest" class="latest-posts">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'Latest from the blog', 'supernova' ); ?></h2>
		<?php
		$supernova_recent = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'ignore_sticky_posts' => true,
			)
		);

		if ( $supernova_recent->have_posts() ) :
			?>
			<div class="post-grid">
				<?php
				while ( $supernova_recent->have_posts() ) :
					$supernova_recent->the_post();
					?>
					<article <?php post_class( 'post-grid__item' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="post-grid__thumb" href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'medium_large' ); ?>
							</a>
						<?php endif; ?>
						<h3 class="post-grid__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<p class="post-grid__meta"><?php echo esc_html( get_the_date() ); ?></p>
						<p class="post-grid__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
					</article>
					<?php
				endwhile;
				?>
			</div>
			<?php
			wp_reset_postdata();
		else :
			?>
			<p><?php esc_html_e( 'No posts yet. Create your first post from the WordPress dashboard.', 'supernova' ); ?></p>
			<?php
		endif;
		?>
	</div>
</section>

<?php
get_footer();
