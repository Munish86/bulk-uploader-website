<?php
/**
 * Draws the blog cover images (content/covers/*.png, 1600x900) and the social share image
 * (theme assets/images/og-image.png, 1200x630) in the brand colours. Needs PHP GD with FreeType and the
 * Segoe UI fonts that come with Windows.
 *
 * Usage: php tools/make-images.php
 */

$root  = dirname( __DIR__ );
$bold  = 'C:/Windows/Fonts/segoeuib.ttf';
$semi  = 'C:/Windows/Fonts/seguisb.ttf';
$plain = 'C:/Windows/Fonts/segoeui.ttf';

function hex( $im, $hex, $alpha = 0 ) {
	return imagecolorallocatealpha( $im, hexdec( substr( $hex, 1, 2 ) ), hexdec( substr( $hex, 3, 2 ) ), hexdec( substr( $hex, 5, 2 ) ), $alpha );
}

// Soft glow: many transparent circles, largest first.
function glow( $im, $cx, $cy, $r, $hex, $strength ) {
	for ( $i = 0; $i < 40; $i++ ) {
		$rr = (int) ( $r * ( 1 - $i / 40 ) );
		imagefilledellipse( $im, $cx, $cy, $rr * 2, $rr * 2, hex( $im, $hex, 127 - $strength ) );
	}
}

// Logo mark (as logo-mark.svg: indigo rounded square, white sheet grid, amber circle with an up arrow) at x,y, size s.
function mark( $im, $x, $y, $s ) {
	$k  = $s / 32;
	$px = function ( $v ) use ( $k ) {
		return (int) round( $v * $k );
	};
	$brand = hex( $im, '#3A4AD6' );
	imagefilledrectangle( $im, $x + $px( 8 ), $y, $x + $px( 24 ), $y + $s, $brand );
	imagefilledrectangle( $im, $x, $y + $px( 8 ), $x + $s, $y + $px( 24 ), $brand );
	foreach ( array( array( 8, 8 ), array( 24, 8 ), array( 8, 24 ), array( 24, 24 ) ) as $c ) {
		imagefilledellipse( $im, $x + $px( $c[0] ), $y + $px( $c[1] ), $px( 16 ), $px( 16 ), $brand );
	}
	$white = hex( $im, '#FFFFFF' );
	imagesetthickness( $im, max( 2, $px( 2 ) ) );
	imagerectangle( $im, $x + $px( 6.5 ), $y + $px( 7.5 ), $x + $px( 20.5 ), $y + $px( 24.5 ), $white );
	imagesetthickness( $im, max( 1, $px( 1.5 ) ) );
	imageline( $im, $x + $px( 6.5 ), $y + $px( 13.2 ), $x + $px( 20.5 ), $y + $px( 13.2 ), $white );
	imageline( $im, $x + $px( 6.5 ), $y + $px( 18.8 ), $x + $px( 20.5 ), $y + $px( 18.8 ), $white );
	imageline( $im, $x + $px( 12.5 ), $y + $px( 7.5 ), $x + $px( 12.5 ), $y + $px( 24.5 ), $white );
	imagefilledellipse( $im, $x + $px( 22.5 ), $y + $px( 21.5 ), $px( 13 ), $px( 13 ), hex( $im, '#F5A524' ) );
	$ink = hex( $im, '#0D1230' );
	imagesetthickness( $im, max( 2, $px( 2 ) ) );
	imageline( $im, $x + $px( 22.5 ), $y + $px( 24.6 ), $x + $px( 22.5 ), $y + $px( 18.4 ), $ink );
	imageline( $im, $x + $px( 19.8 ), $y + $px( 21 ), $x + $px( 22.5 ), $y + $px( 18.3 ), $ink );
	imageline( $im, $x + $px( 25.2 ), $y + $px( 21 ), $x + $px( 22.5 ), $y + $px( 18.3 ), $ink );
	imagesetthickness( $im, 1 );
}

// Wraps text to a width; returns lines.
function wrap( $text, $font, $size, $width ) {
	$lines = array();
	$line  = '';
	foreach ( explode( ' ', $text ) as $word ) {
		$try = '' === $line ? $word : "$line $word";
		$box = imagettfbbox( $size, 0, $font, $try );
		if ( $box[2] - $box[0] > $width && '' !== $line ) {
			$lines[] = $line;
			$line    = $word;
		} else {
			$line = $try;
		}
	}
	$lines[] = $line;
	return $lines;
}

function canvas( $w, $h ) {
	$im = imagecreatetruecolor( $w, $h );
	imagealphablending( $im, true );
	imagefilledrectangle( $im, 0, 0, $w, $h, hex( $im, '#232E8F' ) );
	glow( $im, (int) ( $w * 1.0 ), 0, (int) ( $w * 0.55 ), '#F5A524', 5 );
	glow( $im, 0, $h, (int) ( $w * 0.45 ), '#8C9BFF', 6 );
	// Faint grid, like a stock sheet
	$line = hex( $im, '#FFFFFF', 120 );
	for ( $x = 0; $x < $w; $x += 80 ) {
		imageline( $im, $x, 0, $x, $h, $line );
	}
	for ( $y = 0; $y < $h; $y += 80 ) {
		imageline( $im, 0, $y, $w, $y, $line );
	}
	return $im;
}

$covers = array(
	'cover-excel.png'      => array( 'GUIDE', 'How to bulk upload products to Shopify from Excel' ),
	'cover-images.png'     => array( 'IMAGES', 'Upload hundreds of product images at once' ),
	'cover-mistakes.png'   => array( 'CHECKLIST', '7 product import mistakes to avoid' ),
	'cover-metafields.png' => array( 'METAFIELDS', 'Fill Shopify metafields from a spreadsheet' ),
);
@mkdir( "$root/content/covers", 0777, true );
foreach ( $covers as $file => list( $label, $title ) ) {
	$w  = 1600;
	$h  = 900;
	$im = canvas( $w, $h );
	imagettftext( $im, 26, 0, 120, 210, hex( $im, '#FFD48A' ), $semi, $label );
	$y = 310;
	foreach ( wrap( $title, $bold, 72, 1250 ) as $line ) {
		imagettftext( $im, 72, 0, 116, $y, hex( $im, '#FFFFFF' ), $bold, $line );
		$y += 108;
	}
	mark( $im, 120, 730, 56 );
	imagettftext( $im, 30, 0, 196, 772, hex( $im, '#FFFFFF' ), $bold, 'Bulk Product Uploader' );
	imagepng( $im, "$root/content/covers/$file", 9 );
	echo "content/covers/$file\n";
}

// Social share image
$im = canvas( 1200, 630 );
mark( $im, 90, 90, 72 );
imagettftext( $im, 34, 0, 186, 140, hex( $im, '#FFFFFF' ), $bold, 'Bulk Product Uploader' );
$y = 300;
foreach ( wrap( 'Update your whole catalogue from one spreadsheet', $bold, 60, 1000 ) as $line ) {
	imagettftext( $im, 60, 0, 86, $y, hex( $im, '#FFFFFF' ), $bold, $line );
	$y += 88;
}
imagettftext( $im, 26, 0, 90, $y + 30, hex( $im, '#C9CFF7' ), $plain, 'Excel, CSV and Google Sheets to Shopify, with preview and undo' );
imagepng( $im, "$root/theme/bulk-uploader/assets/images/og-image.png", 9 );
echo "theme/bulk-uploader/assets/images/og-image.png\n";
