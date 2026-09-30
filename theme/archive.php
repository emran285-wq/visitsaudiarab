<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<div class="vsa-container">
	<?php vsa_breadcrumbs(); ?>
	<h1><?php the_archive_title(); ?></h1>
	<div class="vsa-grid">
		<?php while ( have_posts() ) : the_post(); ?>
			<a class="vsa-card" href="<?php the_permalink(); ?>">
				<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium' ); ?>
				<div class="vsa-card-body"><h3><?php the_title(); ?></h3></div>
			</a>
		<?php endwhile; ?>
	</div>
	<?php the_posts_pagination(); ?>
</div>
<?php get_footer(); ?>
