<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<div class="vsa-container">
	<div class="vsa-grid">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<a class="vsa-card" href="<?php the_permalink(); ?>">
				<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium' ); ?>
				<div class="vsa-card-body"><h3><?php the_title(); ?></h3></div>
			</a>
		<?php endwhile; else : ?>
			<p><?php esc_html_e( 'Nothing found.', 'visitsaudiarab' ); ?></p>
		<?php endif; ?>
	</div>
	<?php the_posts_pagination(); ?>
</div>
<?php get_footer(); ?>
