<?php
/*
Plugin Name: Loop shortcodes m34
Description: This plugin allows you to include posts lists in your posts and pages via shortcodes.
Version: 0.1
Author: Montera34
Author URI: https://montera34.com
License: GPLv2+
*/

/* Load JavaScript and styles */
add_action( 'wp_enqueue_scripts', 'm34loops_scripts_styles' );

// Register styles and scripts
function m34loops_scripts_styles() {
	wp_enqueue_style( 'm34loops-css',plugins_url( 'style/loop-shortcode-m34.css' , __FILE__) );
} // END register scripts and styles

/* Loops shortcode
   Parameters:
	+ To build the loop. This group of parameters works the same way as WP_Query args
	  post_type
	  order
	  orderby
	  posts_per_page
	  taxonomy
	  terms
	  meta_key
	  meta_value
	  meta_value_num
	  meta_compare
	+ To build each loop item (which fields and how to order them):
	  fields: comma separated fields. Options: featured image, title, date, excerpt, a taxnomony slug
	+ Placeholders
	  %today% in meta_value or meta_value_num values today's date with the format Y-m-d (usefull to make a loop of current events)
	+ Style output
	  colums: from 1 to 4
	  image_size: thumbnail, medium, large, full or any other registered size
 */
add_shortcode('m34loop', 'm34loops');
function m34loops( $loop_args ) {
	extract(shortcode_atts(
		array(
			'post_type' => 'post',
			'order' => 'DESC',
			'orderby' => 'date',
			'posts_per_page' => '12',
			'taxonomy' => '',
			'terms' => '',
			'meta_key' => '',
			'meta_value' => '',
			'meta_value_num' => '',
			'meta_compare' => '',
			'fields' => 'featured image,title,date,excerpt',
			'colums' => '1',
			'image_size' => 'large',
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
	if ( taxonomy_exists($taxonomy) ) {
		$terms = explode(',',$terms);
		$args['tax_query'] = array(
			array(
				'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $terms
			)
		);
	}
	if ( $meta_key != '' || $meta_value != '' || $meta_value_num != '' ) {
		$meta_query = array();
		if ( $meta_key != '' ) { $meta_query['key'] = $meta_key; }
		if ( $meta_value == '%today%' || $meta_value_num == '%today%' ) { $meta_query['value'] = date('Y-m-d'); }
		elseif ( $meta_value != '' ) { $meta_query['value'] = $meta_value; }
		elseif ( $meta_value_num != '' ) { $meta_query['value'] = $meta_value_num; $meta_query['type'] = "numeric"; }
		if ( $meta_compare != '' ) {
			$meta_compare = str_replace("-","<",$meta_compare);
			$meta_compare = str_replace("+",">",$meta_compare);
			$meta_query['compare'] = $meta_compare;
		}
		$args['meta_query'] = array($meta_query);
	}

	$loop_items = get_posts($args);

	// fields to include in each item
	$fields = explode(',',$fields);

	$loop_out = "<div class='m34loop-container'>";
	$loop_count = 0;
	foreach ( $loop_items as $item ) {
		$loop_count++;
		$item_class = "m34loop-item-" .$loop_count;
		setup_postdata($item);

		// fixed fields
		$item_perma = get_permalink($item->ID);
		$item_tit = get_the_title($item->ID);

		// optional fields
		$fields_out = "";
		foreach ( $fields as $f ) {
			switch ( trim($f)) {
			
				case 'featured image' : // featured image
					if ( has_post_thumbnail($item->ID) ) {
						$fields_out .= "<figure><a href='" .$item_perma. "' title='" .$item_tit. "' rel='bookmark'>" .get_the_post_thumbnail($item->ID,$image_size). "</a></figure>";
					}
					continue 2;
		
				case 'title' : // title
					$fields_out .= "<header><h4 class='m34loop-tit'><a href='" .$item_perma. "' title='" .$item_tit. "' rel='bookmark'>" .$item_tit. "</a></h4></header>";
					continue 2;
		
				case 'date' : // date
					$item_date_human = get_the_date('j F, Y',$item->ID);
					$item_date = get_the_date('Y-m-d',$item->ID);
					$fields_out .= "<time class='m34loop-date' datetime='$item_date'>" .$item_date_human. "</time>";
					continue 2;
	
				case 'excerpt' : // excerpt
					$item_desc = get_the_excerpt();
					$fields_out .= "<div class='m34loop-desc'>" .$item_desc. "</div>";
					continue 2;

				default : // terms_list
					if ( taxonomy_exists($f) == TRUE ) {
						$item_cats = get_the_terms($item->ID,$terms_list);
						if ( is_array($item_cats) ) {
							$item_cats_array = array();
							$fields_out .= "<div class='m34loop-terms'>";
							foreach ( $item_cats as $cat ) {
								$cat_perma = get_term_link($cat);
								$item_cats_array[] = "<a href='" .$cat_perma. "'>" .$cat->name. "</a>";
							}
							$fields_out .= implode(', ', $item_cats_array);
							$fields_out .= "</div>";
						}
					}
					continue 2;

			}
		} // end switcher to include fields

		$loop_out .= "<article class='m34loop-item m34loop-item-".$colums."-col " .$item_class. "'>
			" .$fields_out. "
		</article>";

		// aux divs for responsive cols
		switch ( trim($colums)) {
			case '4' :
				if ( $loop_count == 4 ) { $loop_out .= "<div class='clearfix visible-large'></div>"; }
				elseif ( $loop_count == 3 ) { $loop_out .= "<div class='clearfix visible-medium'></div>"; }
				elseif ( $loop_count == 2 ) { $loop_out .= "<div class='clearfix visible-small'></div>"; }
				break;

			case '3' :
				if ( $loop_count == 3 ) { $loop_out .= "<div class='clearfix visible-large visible-medium'></div>"; }
				elseif ( $loop_count == 2 ) { $loop_out .= "<div class='clearfix visible-small'></div>"; }
				break;

			case '2' :
				if ( $loop_count == 2 ) { $loop_out .= "<div class='clearfix visible-large visible-medium visible-small'></div>"; }
				break;

			case '1' :
				break;

		} // end switcher
		if ( $loop_count == $colums ) {  $loop_count = 0; }

	} // END foreach $loop_items
	$loop_out .= "</div><!-- .m34loop-container -->";
	return $loop_out;

} /* END loops shortcode */

?>
