<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
?>
<article class="vsa-container">
	<?php vsa_breadcrumbs(); ?>
	<h1><?php the_title(); ?></h1>
	<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'large' ); ?>

	<div class="vsa-content"><?php the_content(); ?></div>

	<?php get_template_part( 'template-parts/know-before-you-go' ); ?>

	<h2><?php esc_html_e( 'Related Guides', 'visitsaudiarab' ); ?></h2>
	<div class="vsa-grid">
		<?php
		$terms = get_the_terms( get_the_ID(), 'region' );
		if ( $terms && ! is_wp_error( $terms ) ) :
			$related = new WP_Query( array(
				'post_type'      => array( 'attraction', 'itinerary', 'post' ),
				'posts_per_page' => 6,
				'tax_query'      => array( array(
					'taxonomy' => 'region',
					'field'    => 'term_id',
					'terms'    => wp_list_pluck( $terms, 'term_id' ),
				) ),
			) );
			while ( $related->have_posts() ) : $related->the_post(); ?>
				<a class="vsa-card" href="<?php the_permalink(); ?>">
					<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium' ); ?>
					<div class="vsa-card-body"><h3><?php the_title(); ?></h3></div>
				</a>
			<?php endwhile; wp_reset_postdata();
		endif;
		?>
	</div>
</article>
<?php endwhile; get_footer(); ?>
