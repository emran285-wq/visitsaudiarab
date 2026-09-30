<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<footer class="vsa-footer">
	<div class="vsa-container">
		<div class="vsa-grid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));">
			<div>
				<h4><?php esc_html_e( 'Explore', 'visitsaudiarab' ); ?></h4>
				<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'fallback_cb' => false ) ); ?>
			</div>
			<div>
				<h4><?php esc_html_e( 'About', 'visitsaudiarab' ); ?></h4>
				<ul style="list-style:none;padding:0;margin:0;">
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'visitsaudiarab' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/editorial-policy/' ) ); ?>"><?php esc_html_e( 'Editorial Policy', 'visitsaudiarab' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/how-we-review/' ) ); ?>"><?php esc_html_e( 'How We Review', 'visitsaudiarab' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/authors/' ) ); ?>"><?php esc_html_e( 'Authors', 'visitsaudiarab' ); ?></a></li>
				</ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Legal', 'visitsaudiarab' ); ?></h4>
				<ul style="list-style:none;padding:0;margin:0;">
					<li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'visitsaudiarab' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/cookies/' ) ); ?>"><?php esc_html_e( 'Cookie Policy', 'visitsaudiarab' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/affiliate-disclosure/' ) ); ?>"><?php esc_html_e( 'Affiliate Disclosure', 'visitsaudiarab' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'visitsaudiarab' ); ?></a></li>
				</ul>
			</div>
		</div>

		<p class="vsa-footer-disclaimer">
			<?php esc_html_e( 'VisitSaudiArab.com is an independent travel publication and is not affiliated with the Saudi Tourism Authority, Visit Saudi, or any government organization.', 'visitsaudiarab' ); ?>
			&copy; <?php echo esc_html( date( 'Y' ) ); ?> VisitSaudiArab.com
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
