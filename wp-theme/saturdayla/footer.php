</main>
<footer class="site-footer"><div class="wrap"><div class="row">
	<span>© <?php echo date( 'Y' ); ?> <?php echo esc_html( get_theme_mod( 'saturdayla_owner', 'SaturdayLa' ) ); ?> · Family, kids &amp; party photography · <?php echo esc_html( get_theme_mod( 'saturdayla_city', 'Los Angeles' ) ); ?>, CA</span>
	<span>
		<a href="tel:<?php echo esc_attr( preg_replace( '/\D/', '', saturdayla_phone() ) ); ?>"><?php echo esc_html( saturdayla_phone() ); ?></a> ·
		<a href="mailto:<?php echo esc_attr( saturdayla_email() ); ?>">Email</a> ·
		<a href="<?php echo esc_url( saturdayla_instagram() ); ?>" rel="me noopener" target="_blank">Instagram</a> ·
		<a href="<?php echo esc_url( home_url( '/journal/' ) ); ?>">Journal</a>
	</span>
</div>
<?php wp_nav_menu( [ 'theme_location' => 'footer', 'container' => false, 'fallback_cb' => false, 'items_wrap' => '<ul style="display:flex;gap:18px;list-style:none;margin:16px 0 0;padding:0">%3$s</ul>' ] ); ?>
</div></footer>
<?php wp_footer(); ?>
</body>
</html>
