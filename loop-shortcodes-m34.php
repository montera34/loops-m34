<?php
/*
Plugin Name: Loop shortcodes m34
Description: This plugin allows you to include posts lists in your posts and pages via shortcodes.
Version: 0.1
Author: montera34
Author URI: http://montera34.com
License: GPLv2+
*/

/* Load JavaScript and styles */
add_action( 'wp_enqueue_scripts', 'm34loops_scripts_styles' );

// Register styles and scripts
function m34loops_scripts_styles() {
	wp_enqueue_style( 'm34loops-css',plugins_url( 'style/loop-shortcode-m34.css' , __FILE__) );
} // END register scripts and styles

/* Loops shortcode */
add_shortcode('m34loop', 'm34loops');
function m34loops( $loop_args ) {
	extract(shortcode_atts(
		array(
			'post_type' => 'post',
			'order' => 'DESC',
			'orderby' => 'date',
			'posts_per_page' => '12'
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
	$loop_count = 0;
	foreach ( $loop_items as $item ) {
		$loop_count++;
		$item_class = "m34loop-item-" .$loop_count;
		if ( $loop_count == 4 ) {  $loop_count = 0; }
		setup_postdata($item);
		$item_cats = get_the_terms($item->ID,'category');
		if ( is_array($item_cats) ) {
			$item_cats_array = array();
			$item_cats_out = "<div class='m34loop-terms'>";
			foreach ( $item_cats as $cat ) {
				$cat_perma = get_term_link($cat);
				$item_cats_array[] = "<a href='" .$cat_perma. "'>" .$cat->name. "</a>";
			}
			$item_cats_out .= implode(', ', $item_cats_array);
			$item_cats_out .= "</div>";
		} else { $item_cats = ""; }
		$item_date_human = get_the_date('j F, Y',$item->ID);
		$item_date = get_the_date('Y-m-d',$item->ID);
		$item_date_out = "<time class='m34loop-date' datetime='$item_date'>" .$item_date_human. "</time>";
		$item_tit = get_the_title($item->ID);
		$item_perma = get_permalink($item->ID);
		$item_desc = get_the_excerpt();
		if ( has_post_thumbnail($item->ID) ) {
			$item_img_out = "<figure><a href='" .$item_perma. "' title='" .$item_tit. "' rel='bookmark'>" .get_the_post_thumbnail($item->ID,'medium'). "</a></figure>";
		} else { $item_img_out = ""; }
		$loop_out .= "<section class='m34loop-item " .$item_class. "'>
			" .$item_img_out. "
			<header><h4 class='m34loop-tit'><a href='" .$item_perma. "' title='" .$item_tit. "' rel='bookmark'>" .$item_tit. "</a></h4>" .$item_date_out. "</header>
			<div class='m34loop-desc'>" .$item_desc. "</div>
			<footer>" .$item_cats_out. "</footer>
		</section>";

	} // END foreach $loop_items
	$loop_out .= "<div class='m34loop-more'><a href='/actualites'>Plus d'actualités</a></div>";
	return $loop_out;

} /* END loops shortcode */

?>
