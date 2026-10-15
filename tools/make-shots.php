<?php
/**
 * Cuts the app screenshots used on the website out of the uploader's browser-test screenshots.
 *
 * The source PNGs come from shopify-bulk-uploader-app's browser test (test/browser/run.mjs) run at
 * deviceScaleFactor 2 (see docs/PROGRESS.md for the command). Crop boxes below are in 1x page pixels.
 *
 * Usage: php tools/make-shots.php <folder with 1-loaded.png ... 8-restored.png>
 */

$src = rtrim( $argv[1] ?? '', '/\\' );
if ( '' === $src || ! is_file( "$src/4-results.png" ) ) {
	fwrite( STDERR, "Usage: php tools/make-shots.php <folder with the 2x browser-test screenshots>\n" );
	exit( 1 );
}
$out   = dirname( __DIR__ ) . '/theme/bulk-uploader/assets/images';
$scale = 2;
$x     = 150;
$w     = 980;

// name => [source file, top, bottom] (1x pixels)
$crops = array(
	'app-sheet'    => array( '4-results.png', 50, 562 ),
	'app-columns'  => array( '4-results.png', 562, 1160 ),
	'app-images'   => array( '4-results.png', 1878, 2276 ),
	'app-run'      => array( '3-running.png', 2612, 3100 ),
	'app-results'  => array( '4-results.png', 3032, 3490 ),
	'app-backups'  => array( '6-backups.png', 50, 720 ),
	'app-restored' => array( '8-restored.png', 776, 1096 ),
);

foreach ( $crops as $name => list( $file, $top, $bottom ) ) {
	$im = imagecreatefrompng( "$src/$file" );
	$cw = $w * $scale;
	$ch = ( $bottom - $top ) * $scale;
	// Same light grey as the app's page background behind the cards
	$cut = imagecreatetruecolor( $cw, $ch );
	imagecopy( $cut, $im, 0, 0, $x * $scale, $top * $scale, $cw, $ch );
	// Websites show these at most ~640px wide: 1600px is sharp on 2x screens and keeps files small
	$tw    = min( 1600, $cw );
	$th    = (int) round( $ch * $tw / $cw );
	$small = imagecreatetruecolor( $tw, $th );
	imagecopyresampled( $small, $cut, 0, 0, 0, 0, $tw, $th, $cw, $ch );
	imagewebp( $small, "$out/$name.webp", 86 );
	printf( "%s.webp %dx%d %d KB\n", $name, $tw, $th, filesize( "$out/$name.webp" ) / 1024 );
}
