<?php
/** Packages — edit prices here. Placeholders based on the LA market, 2026. */
$type = $args['type'] ?? 'family';
$sets = [
	'family' => [ 'head' => 'Pick the one that fits<br>the day you have in mind.', 'items' => [
		[ 'Short', '450', '45 minutes · one location', [ '15 retouched photos', 'Outfit guide', 'Private gallery in 14 days' ], false ],
		[ 'Session', '850', '90 minutes · studio or location', [ '35 retouched photos', 'Concept call and outfit guide', 'Two outfit changes', 'Private gallery in 14 days' ], true ],
		[ 'Big one', '1,400', '2–3 hours · two locations', [ '60+ retouched photos', 'Everything in Session', 'Hardcover album', 'Grandparents welcome' ], false ],
	] ],
	'kids' => [ 'head' => 'Portraits with a bit<br>of attitude.', 'items' => [
		[ 'Short', '390', '45 minutes · studio', [ '12 retouched photos', 'One look', 'Outfit guide' ], false ],
		[ 'Session', '750', '90 minutes · studio or location', [ '30 retouched photos', 'Two looks', 'Concept and outfit guide', 'Comp-card crops for agencies on request' ], true ],
		[ 'Siblings', '1,100', '2 hours · up to 3 kids', [ '45 retouched photos', 'Individual and group portraits', 'Everything in Session' ], false ],
	] ],
	'parties' => [ 'head' => 'Pick the one that fits<br>the length of the party.', 'items' => [
		[ 'Short', '450', '1 hour of coverage', [ 'Around 40 edited photos', 'Private gallery in 7 days' ], false ],
		[ 'Full party', '750', '2 hours of coverage', [ 'Around 90 edited photos', 'Setup shots before guests arrive', 'Private gallery in 7 days' ], true ],
		[ 'Big one', '1,050', '3+ hours of coverage', [ 'Around 150 edited photos', 'Setup shots and family portraits', 'Private gallery in 7 days' ], false ],
	] ],
	'weddings' => [ 'head' => 'Coverage that fits<br>the shape of your day.', 'items' => [
		[ 'Elopement', '1,800', 'Up to 3 hours', [ '150+ edited photos', 'City hall, estate or beach', 'Gallery in 3 weeks' ], false ],
		[ 'Half day', '3,200', 'Up to 6 hours', [ '400+ edited photos', 'Getting ready through first dance', 'Timeline help', 'Gallery in 4 weeks' ], true ],
		[ 'Full day', '4,800', 'Up to 10 hours', [ '700+ edited photos', 'Second shooter', 'Engagement session', 'Hardcover album' ], false ],
	] ],
	'editorial' => [ 'head' => 'For agencies, magazines<br>and families who want the full production.', 'items' => [
		[ 'Agency test', '550', '60 minutes · clean studio', [ '10 retouched photos', 'Digitals included', 'Comp-card-ready crops' ], false ],
		[ 'Editorial story', '1,200', 'Half day · location, 3 looks', [ '25 retouched photos', 'Concept, mood board, styling', 'Art direction on set' ], true ],
		[ 'Full production', 'from 2,500', 'Full day · team on set', [ 'Stylist and hair/makeup coordinated', '40+ retouched photos', 'Portfolio and press usage rights' ], false ],
	] ],
];
$set = $sets[ $type ] ?? $sets['family'];
?>
<div class="head reveal"><span class="eyebrow">Packages</span><h2><?php echo wp_kses( $set['head'], [ 'br' => [] ] ); ?></h2></div>
<div class="packs reveal">
	<?php foreach ( $set['items'] as [ $name, $price, $sub, $list, $pick ] ) : ?>
	<div class="pack<?php echo $pick ? ' pick' : ''; ?>">
		<?php if ( $pick ) echo '<span class="badge">Most booked</span>'; ?>
		<h3><?php echo esc_html( $name ); ?></h3>
		<span class="price">$<?php echo esc_html( $price ); ?></span>
		<span class="sub"><?php echo esc_html( $sub ); ?></span>
		<ul><?php foreach ( $list as $li ) echo '<li>' . esc_html( $li ) . '</li>'; ?></ul>
		<a class="btn" href="<?php echo esc_url( saturdayla_book_url() ); ?>">Check my date</a>
	</div>
	<?php endforeach; ?>
</div>
<p class="body reveal" style="margin-top:28px;font-size:14px">Travel within 30 miles of Los Angeles is included. A 30% deposit holds your date; move it once, free, if plans change.</p>
