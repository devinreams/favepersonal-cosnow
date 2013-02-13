<?php

//define('CFCT_DEBUG', true);

function fpdr_author_redirect() {
	if(is_author( 'admin' )) { 
		wp_redirect( '/author/devin/', 301 );
	}
}
add_action('template_redirect', 'fpdr_author_redirect');

// set format for status posts to exclude title
// if status post is short enough, do not include URL either
function akv3_social_broadcast_format($format, $post, $service) {
	if (get_post_format($post) == 'status') {
		$format = (strlen($post->post_content) <= $service->max_broadcast_length() ? '{content}' : '{content} {url}');
	}
	return $format;
}
add_filter('social_broadcast_format', 'akv3_social_broadcast_format', 10, 3);

// remove URL in comments if comment is short enough to be included
function akv3_social_comment_broadcast_format($format, $comment, $service) {
	return (strlen($comment->comment_content) <= $service->max_broadcast_length() ? '{content}' : '{content} {url}');
}
add_filter('social_comment_broadcast_format', 'akv3_social_comment_broadcast_format', 10, 3);

// move comment form after the comments themselves
function akv3_social_comment_block_order($order) {
	return array('comments', 'form');
}
add_filter('social_comment_block_order', 'akv3_social_comment_block_order');

// remove the CSS for Colors from the HTML (if you want to load it via child theme instead)
function favepersonal_remove_inline_color_css() {
	remove_action('wp_head', 'cfcp_color_css_min', 8); 
}
add_action('wp', 'favepersonal_remove_inline_color_css', 11);

// Show author avatar next to posts (except mine)
function fpcs_author() {
	if (get_the_author_meta('ID') != "1" ) {
		echo "<p class=\"entry-meta author\"><a href=\"" . get_author_posts_url(get_the_author_meta( 'ID' ))."\">";
		echo get_avatar( get_the_author_meta('ID'), 32 );
		echo "</a></p>";
	}
}
add_action('favepersonal_content_sidebar_after', 'fpcs_author');
add_action('favepersonal_excerpt_sidebar_after', 'fpcs_author');

// add Add to Home Screen script
function fpcs_scripts() {
	if (is_home()) {
		echo "<script type=\"text/javascript\">
		var addToHomeConfig = {
			animationIn: 'drop',
			animationOut: 'fade',
			lifespan: '10000',
			touchIcon:true,
			message:'Install Colorado Snow as an app on your <strong>%device</strong>. Tap the %icon icon and click <strong>Add to Home Screen</strong>.'
		};
		</script>";
	    wp_register_script( 'add2home', get_bloginfo('stylesheet_directory') . '/add2home.js');
	    wp_enqueue_script( 'add2home' );
    }
}
// add_action('wp_enqueue_scripts', 'fpcs_scripts');

// add Facebook ID for insights
function fpcs_fbinsights() {
	echo "<meta property=\"fb:admins\" content=\"10200068\" />\n";
}
add_action('wp_head','fpcs_fbinsights');

// add Infinite Scroll support
add_theme_support( 'infinite-scroll', array(
	'container'  => 'primary',
	'footer'     => 'footer',
	'footer_widgets' => array( 'sidebar-section-1', 'sidebar-section-2', 'sidebar-section-3' ),
	'render'	 => 'cfct_loop',
) );

// add copyright and byline to RSS feed content
function fpcs_feed_byline($content) {
	if ( !has_post_format( 'status') ) {
	   $content .= '<p><small>This article is published at <a href="http://cosnow.com/">Colorado Snow (cosnow.com)</a>. All rights reserved. Republication, in part or entirety is welcome with attribution.</small></p>';
   }
   return $content;
}
add_filter( "the_content_feed", "fpcs_feed_byline" );