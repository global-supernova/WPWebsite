<?php
/**
 * The footer for the Supernova theme.
 *
 * @package Supernova
 */

?>
	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="site-footer__inner">
			<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
				<div class="footer-widgets">
					<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>
			<?php endif; ?>

			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'menu_class'     => 'footer-menu',
						'container'      => 'nav',
						'depth'          => 1,
					)
				);
			}
			?>

			<div class="site-info">
				<p>
					&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>.
					<?php esc_html_e( 'All rights reserved.', 'supernova' ); ?>
				</p>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
