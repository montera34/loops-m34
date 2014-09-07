<?php
/*
Plugin Name: Loop shortcodes m34
Description: This plugin allows you to include posts lists in your posts and pages via shortcodes.
Version: 0.1
Author: montera34
Author URI: http://montera34.com
License: GPLv2+
*/

/* EDIT THIS VARS TO CONFIG THE PLUGIN */
$cpt = "glossary"; // custom post type name and slug for permalinks
$tax1 = "letter"; // letters taxonomy name and slug for permalinks
$tax2 = "group"; // groups taxonomy name and slug for permalinks
/* STOP EDIT */

if (!defined('M34GLOSSARY_CPT')) define('M34GLOSSARY_CPT', $cpt);
if (!defined('M34GLOSSARY_TAX_LETTER')) define('M34GLOSSARY_TAX_LETTER', $tax1);
if (!defined('M34GLOSSARY_TAX_GROUP')) define('M34GLOSSARY_TAX_GROUP', $tax2);

/* Load JavaScript and styles */
add_action( 'wp_enqueue_scripts', 'm34loops_scripts_styles' );

// Register styles and scripts
function m34loops_scripts_styles() {
	wp_enqueue_style( 'm34loops-css',plugins_url( 'style/loop-shortcodes-m34.css' , __FILE__) );
} // END register scripts and styles

/* Loops shortcode */
add_shortcode('m34loop', 'm34loops');
function m34loops( $loop_args ) {
	extract(shortcode_atts(
		array(
			'post_type' => 'post',
			'order' => 'DESC',
			'orderby' => 'date',
			'posts_per_page' => '10'
		),
		$loop_args,
		'm34loop'
	));
	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => $posts_per_page,
		'orderby' => $orderby,
		'order' => $order
	);
	$loop_items = get_posts($args);

	$loop_out = "";
	foreach ( $loop_items as $item ) {
		setup_postdata($item);
		$item_cats = get_the_terms($item->ID,'category');
		if ( is_array($item_cats) ) {
			$item_cats_out = "<div class='m34loop-terms'>";
			foreach ( $item_cats as $cat ) {
				$cat_perma = get_term_link($cat);
				$item_cats_out .= "<a href='" .$cat_perma. "'>" .$cat->name. "</a>";
			}
			$item_cats_out .= "</div>";
		} else { $item_cats = ""; }
		$item_date = get_the_date();
		$item_date_out = "<div class='m34loop-date'>" .$item_date. "</div>";
		$item_tit = get_the_title($item->ID);
		$item_perma = get_permalink($item->ID);
		$item_desc = get_the_excerpt();
		if ( has_post_thumbnail($item->ID) ) {
			$item_img_out = "<figure><a href='" .$item_perma. "'>" .get_the_post_thumbnail($item->ID,'thumbnail'). "</a></figure>";
		} else { $item_img_out = ""; }
		$loop_out .= "<section class='m34loop-item'>
			" .$item_img_out. "
			<header><h4 class='m34loop-tit'><a href='" .$item_perma. "' title='" .$item_tit. "' rel='bookmark'>" .$item_tit. "</a></h4>
			<div class='m34loop-desc'>" .$item_desc. "</div>
			<footer>" .$item_date_out. "</footer>
		</section>";
	} // END foreach $loop_items
	return $loop_out;

} /* END loops shortcode */

?>
