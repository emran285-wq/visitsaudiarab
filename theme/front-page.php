<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

/**
 * Plan Your Trip links.
 *
 * These are manually-curated landing pages. If a page does not exist yet,
 * create it in wp-admin and keep this slug stable. The slugs match the
 * front-end URL paths exactly.
 */
$plan_links = array(
	array( __( 'Things to Do', 'visitsaudiarab' ),  home_url( '/things-to-do/' ) ),
	array( __( 'Itineraries', 'visitsaudiarab' ),   home_url( '/itineraries/' ) ),
	array( __( 'Places to Stay', 'visitsaudiarab' ), home_url( '/places-to-stay/' ) ),
	array( __( 'Food & Cafés', 'visitsaudiarab' ),  home_url( '/food-and-cafes/' ) ),
	array( __( 'Events', 'visitsaudiarab' ),        home_url( '/events/' ) ),
	array( __( 'Travel Tips', 'visitsaudiarab' ),   home_url( '/travel-tips/' ) ),
);
?>

<section class="vsa-hero">
	<div class="vsa-container">
		<h1><?php esc_html_e( 'Explore Saudi Arabia', 'visitsaudiarab' ); ?></h1>
		<p><?php esc_html_e( 'Independent travel guides, itineraries, attractions and local recommendations from across the Kingdom.', 'visitsaudiarab' ); ?></p>
		<form class="vsa-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'Where do you want to go?', 'visitsaudiarab' ); ?>">
			<button type="submit"><?php esc_html_e( 'Search', 'visitsaudiarab' ); ?></button>
		</form>
	</div>
</section>

<section class="vsa-section vsa-container">
	<h2><?php esc_html_e( 'Trending Destinations', 'visitsaudiarab' ); ?></h2>
	<div class="vsa-grid">
		<?php
		$destinations = new WP_Query( array(
			'post_type'      => 'destination',
			'posts_per_page' => 6,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		) );
		while ( $destinations->have_posts() ) : $destinations->the_post(); ?>
			<a class="vsa-card" href="<?php the_permalink(); ?>">
				<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium' ); ?>
				<div class="vsa-card-body">
					<h3><?php the_title(); ?></h3>
					<span><?php esc_html_e( 'Destination guide', 'visitsaudiarab' ); ?></span>
				</div>
			</a>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
</section>

<section class="vsa-section vsa-container">
	<h2><?php esc_html_e( 'Plan Your Trip', 'visitsaudiarab' ); ?></h2>
	<div class="vsa-grid">
		<?php foreach ( $plan_links as $l ) : ?>
			<a class="vsa-card" href="<?php echo esc_url( $l[1] ); ?>">
				<div class="vsa-card-body"><h3><?php echo esc_html( $l[0] ); ?></h3></div>
			</a>
		<?php endforeach; ?>
	</div>
</section>

<section class="vsa-section vsa-container">
	<h2><?php esc_html_e( 'Latest Verified Guides', 'visitsaudiarab' ); ?></h2>
	<div class="vsa-grid">
		<?php
		$latest = new WP_Query( array( 'posts_per_page' => 4 ) );
		while ( $latest->have_posts() ) : $latest->the_post(); ?>
			<a class="vsa-card" href="<?php the_permalink(); ?>">
				<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium' ); ?>
				<div class="vsa-card-body">
					<h3><?php the_title(); ?></h3>
					<?php $verified = get_post_meta( get_the_ID(), 'vsa_last_reviewed', true ); ?>
					<?php if ( $verified ) : ?>
						<span><?php printf( esc_html__( 'Verified: %s', 'visitsaudiarab' ), esc_html( $verified ) ); ?></span>
					<?php endif; ?>
				</div>
			</a>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
</section>

<section class="vsa-section vsa-container" style="background:var(--vsa-sand);border-radius:var(--vsa-radius);padding:32px;">
	<h2><?php esc_html_e( 'Discover something new in Saudi every week.', 'visitsaudiarab' ); ?></h2>
	<?php echo do_shortcode( '[newsletter-form]' ); /* Replace with your ESP's shortcode/embed */ ?>
</section>

<?php get_footer(); ?>
