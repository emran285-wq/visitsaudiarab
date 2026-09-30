<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
	$id = get_the_ID();
?>
<article class="vsa-container">
	<?php vsa_breadcrumbs(); ?>
	<h1><?php the_title(); ?></h1>
	<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'large' ); ?>

	<div class="vsa-content"><?php the_content(); ?></div>

	<div class="vsa-kbyg">
		<h2><?php esc_html_e( 'Visitor Information', 'visitsaudiarab' ); ?></h2>
		<dl>
			<?php
			$fields = array(
				__( 'Address', 'visitsaudiarab' )    => get_post_meta( $id, 'vsa_address', true ),
				__( 'Price', 'visitsaudiarab' )       => get_post_meta( $id, 'vsa_price', true ),
				__( 'Opening hours', 'visitsaudiarab' ) => get_post_meta( $id, 'vsa_opening_hours', true ),
				__( 'Recommended duration', 'visitsaudiarab' ) => get_post_meta( $id, 'vsa_duration', true ),
				__( 'Indoor / outdoor', 'visitsaudiarab' ) => get_post_meta( $id, 'vsa_indoor_outdoor', true ),
				__( 'Family friendly', 'visitsaudiarab' ) => get_post_meta( $id, 'vsa_family_friendly', true ) ? __( 'Yes', 'visitsaudiarab' ) : '',
				__( 'Last verified', 'visitsaudiarab' ) => get_post_meta( $id, 'vsa_last_verified', true ),
			);
			foreach ( array_filter( $fields ) as $label => $value ) : ?>
				<dt><?php echo esc_html( $label ); ?></dt>
				<dd><?php echo esc_html( $value ); ?></dd>
			<?php endforeach; ?>
		</dl>
		<?php $booking = get_post_meta( $id, 'vsa_booking_url', true ); ?>
		<?php if ( $booking ) : ?>
			<p><a href="<?php echo esc_url( $booking ); ?>" rel="nofollow sponsored"><?php esc_html_e( 'Check availability / book', 'visitsaudiarab' ); ?></a></p>
		<?php endif; ?>
		<?php $official = get_post_meta( $id, 'vsa_official_url', true ); ?>
		<?php if ( $official ) : ?>
			<p><a href="<?php echo esc_url( $official ); ?>"><?php esc_html_e( 'Official website', 'visitsaudiarab' ); ?></a></p>
		<?php endif; ?>
	</div>

	<?php
	$lat = get_post_meta( $id, 'vsa_lat', true );
	$lng = get_post_meta( $id, 'vsa_lng', true );
	if ( $lat && $lng ) :
	?>
		<div class="vsa-map-placeholder" data-lat="<?php echo esc_attr( $lat ); ?>" data-lng="<?php echo esc_attr( $lng ); ?>">
			<!-- Static map image here; load interactive map JS only on click (see PRD #36) -->
			<img loading="lazy" alt="<?php the_title_attribute(); ?> map"
				 src="https://staticmap.example/render?lat=<?php echo esc_attr( $lat ); ?>&lng=<?php echo esc_attr( $lng ); ?>">
		</div>
	<?php endif; ?>
</article>
<?php endwhile; get_footer(); ?>
