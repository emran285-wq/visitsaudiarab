<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
?>
<article class="vsa-container">
	<?php vsa_breadcrumbs(); ?>
	<h1><?php the_title(); ?></h1>
	<div class="vsa-content"><?php the_content(); ?></div>
</article>
<?php endwhile; get_footer(); ?>
