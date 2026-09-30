<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ( ! is_singular() ) : ?>
<link rel="canonical" href="<?php echo esc_url( home_url( add_query_arg( array() ) ) ); ?>" />
<?php endif; ?>
<?php vsa_favicon_links(); ?>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="vsa-header">
	<div class="vsa-container vsa-header-inner">
		<a class="vsa-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">VisitSaudiArab</a>

		<nav class="vsa-nav" aria-label="Primary">
			<?php wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => false,
			) ); ?>
		</nav>

		<div class="vsa-lang-switch">
			<?php if ( is_singular() ) : ?>
				<a href="<?php echo esc_url( vsa_get_translation_url() ); ?>"
				   hreflang="<?php echo vsa_get_lang() === 'en' ? 'ar-SA' : 'en'; ?>">
					<?php echo vsa_get_lang() === 'en' ? 'العربية' : 'English'; ?>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/ar/' ) ); ?>" hreflang="ar-SA">العربية</a>
			<?php endif; ?>
		</div>
	</div>
</header>
