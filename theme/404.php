<?php
if ( ! defined( 'ABSPATH' ) ) exit;
status_header( 404 );
get_header();
?>
<div class="vsa-container" style="text-align:center;padding:80px 0;">
	<h1><?php esc_html_e( 'Page not found', 'visitsaudiarab' ); ?></h1>
	<p><?php esc_html_e( "The page you're looking for doesn't exist or has moved.", 'visitsaudiarab' ); ?></p>
	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to homepage', 'visitsaudiarab' ); ?></a></p>
</div>
<?php get_footer(); ?>
