<?php
/**
 * The header for the Supernova theme.
 *
 * @package Supernova
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'supernova' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="site-header__inner">
			<div class="site-branding">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php endif; ?>

				<?php if ( is_front_page() && is_home() ) : ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php else : ?>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php endif; ?>

				<?php
				$supernova_description = get_bloginfo( 'description', 'display' );
				if ( $supernova_description || is_customize_preview() ) :
					?>
					<p class="site-description"><?php echo $supernova_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>

			<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'supernova' ); ?>">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
					<span class="menu-toggle__bar"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'supernova' ); ?></span>
				</button>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => 'supernova_primary_menu_fallback',
					)
				);
				?>
			</nav>
		</div>
	</header>

	<div id="content" class="site-content">
