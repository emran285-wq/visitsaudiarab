<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
?>
<article class="vsa-container">
	<?php vsa_breadcrumbs(); ?>
	<h1><?php the_title(); ?></h1>
	<p style="color:#6b756e;font-size:.9rem;">
		<?php printf(
			esc_html__( 'By %1$s — Published %2$s', 'visitsaudiarab' ),
			esc_html( get_the_author() ),
			esc_html( get_the_date() )
		); ?>
		<?php $reviewed = get_post_meta( get_the_ID(), 'vsa_last_reviewed', true ); ?>
		<?php if ( $reviewed ) : ?>
			&middot; <?php printf( esc_html__( 'Information verified: %s', 'visitsaudiarab' ), esc_html( $reviewed ) ); ?>
		<?php endif; ?>
	</p>
	<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'large' ); ?>
	<div class="vsa-content"><?php the_content(); ?></div>
</article>
<?php endwhile; get_footer(); ?>
