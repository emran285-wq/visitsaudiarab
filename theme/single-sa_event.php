<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
	$id = get_the_ID();
	$end = get_post_meta( $id, 'vsa_end_date', true );
	$is_past = $end && strtotime( $end ) < time();
?>
<article class="vsa-container">
	<?php vsa_breadcrumbs(); ?>
	<h1><?php the_title(); ?></h1>
	<?php if ( $is_past ) : ?>
		<p style="background:#fdecea;padding:10px 16px;border-radius:8px;">
			<?php esc_html_e( 'This edition has ended. See below for the current edition if available.', 'visitsaudiarab' ); ?>
		</p>
	<?php endif; ?>
	<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'large' ); ?>
	<div class="vsa-content"><?php the_content(); ?></div>

	<div class="vsa-kbyg">
		<dl>
			<dt><?php esc_html_e( 'Venue', 'visitsaudiarab' ); ?></dt>
			<dd><?php echo esc_html( get_post_meta( $id, 'vsa_venue', true ) ); ?></dd>
			<dt><?php esc_html_e( 'Dates', 'visitsaudiarab' ); ?></dt>
			<dd><?php echo esc_html( get_post_meta( $id, 'vsa_start_date', true ) . ' – ' . $end ); ?></dd>
			<dt><?php esc_html_e( 'Price range', 'visitsaudiarab' ); ?></dt>
			<dd><?php echo esc_html( get_post_meta( $id, 'vsa_price_range', true ) ); ?></dd>
		</dl>
		<?php $ticket = get_post_meta( $id, 'vsa_ticket_url', true ); ?>
		<?php if ( $ticket ) : ?>
			<p><a href="<?php echo esc_url( $ticket ); ?>" rel="nofollow sponsored"><?php esc_html_e( 'Get tickets', 'visitsaudiarab' ); ?></a></p>
		<?php endif; ?>
	</div>
</article>
<?php endwhile; get_footer(); ?>
