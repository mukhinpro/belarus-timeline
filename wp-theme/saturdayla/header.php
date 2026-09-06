<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="sr-only" href="#main">Skip to content</a>
<header class="masthead">
	<div class="row">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo esc_html( get_theme_mod( 'saturdayla_owner', 'SaturdayLa' ) ); ?>
			<small><?php echo esc_html( get_theme_mod( 'saturdayla_tagline', 'Kids · Family · Parties · Los Angeles' ) ); ?></small>
		</a>
		<button class="nav-toggle" aria-label="Menu" aria-expanded="false" aria-controls="nav"><span></span><span></span><span></span></button>
		<nav class="nav" id="nav" aria-label="Primary">
			<?php
			$cta = '<li><a class="btn" href="' . esc_url( saturdayla_book_url() ) . '">Check my date</a></li>';
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '<ul>%3$s' . $cta . '</ul>',
				'fallback_cb'    => function () use ( $cta ) {
					echo '<ul>';
					foreach ( [ 'family-photography-los-angeles' => 'Family', 'kids-photographer-los-angeles' => 'Kids', 'kids-party-photography-los-angeles' => 'Parties', 'wedding-photography-los-angeles' => 'Weddings', 'portfolio' => 'Portfolio', 'pricing' => 'Pricing', 'about' => 'About' ] as $slug => $label ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/' . $slug . '/' ) ), $label );
					}
					echo $cta . '</ul>';
				},
			] );
			?>
		</nav>
	</div>
</header>
<main id="main">
