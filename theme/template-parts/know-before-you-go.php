<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$id = get_the_ID();
$rows = array(
	__( 'Best months', 'visitsaudiarab' )        => get_post_meta( $id, 'vsa_best_months', true ),
	__( 'Typical visit duration', 'visitsaudiarab' ) => get_post_meta( $id, 'vsa_avg_trip_length', true ),
	__( 'Typical budget', 'visitsaudiarab' )      => get_post_meta( $id, 'vsa_typical_budget', true ),
	__( 'Nearest airport', 'visitsaudiarab' )     => get_post_meta( $id, 'vsa_airport', true ),
	__( 'Transport difficulty', 'visitsaudiarab' ) => get_post_meta( $id, 'vsa_transport_difficulty', true ),
	__( 'Family suitability', 'visitsaudiarab' )  => get_post_meta( $id, 'vsa_family_suitability', true ),
	__( 'Last verified', 'visitsaudiarab' )       => get_post_meta( $id, 'vsa_last_reviewed', true ),
);
$rows = array_filter( $rows );
if ( empty( $rows ) ) return;
?>
<div class="vsa-kbyg">
	<h2><?php esc_html_e( 'Know Before You Go', 'visitsaudiarab' ); ?></h2>
	<dl>
		<?php foreach ( $rows as $label => $value ) : ?>
			<dt><?php echo esc_html( $label ); ?></dt>
			<dd><?php echo esc_html( $value ); ?></dd>
		<?php endforeach; ?>
	</dl>
</div>
