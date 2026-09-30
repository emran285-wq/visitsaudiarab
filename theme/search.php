<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// NOTE: robots noindex for this template is added in mu-plugins/visitsaudiarab-core.php
get_header();
?>
<div class="vsa-container">
	<h1><?php printf( esc_html__( 'Search results for: %s', 'visitsaudiarab' ), esc_html( get_search_query() ) ); ?></h1>
	<div class="vsa-grid">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<a class="vsa-card" href="<?php the_permalink(); ?>">
				<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium' ); ?>
				<div class="vsa-card-body"><h3><?php the_title(); ?></h3></div>
			</a>
		<?php endwhile; else : ?>
			<p><?php esc_html_e( 'No results found. Try a different search.', 'visitsaudiarab' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>
