<?php
//* this will bring in the Genesis Parent files needed:
include_once( get_template_directory() . '/lib/init.php' );

//* We tell the name of our child theme
define( 'Child_Theme_Name', __( 'Gray Life', 'genesischild' ) );
//* We tell the web address of our child theme (More info & demo)
define( 'Child_Theme_Url', 'http://gsquaredstudios.com' );
//* We tell the version of our child theme
define( 'CHILD_THEME_VERSION', '1.0' );

//* Add HTML5 markup structure from Genesis
add_theme_support( 'html5' );

//* Add HTML5 responsive recognition
add_theme_support( 'genesis-responsive-viewport' );

// Removes site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );


//* IMPORTS --------------------------------

add_action( 'wp_enqueue_scripts', 'add_font_awesome_icons' );
function add_font_awesome_icons() {
    wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
}

//* Enqueue Lato Google font
add_action( 'wp_enqueue_scripts', 'sp_load_google_fonts' );
function sp_load_google_fonts() {
	wp_enqueue_style( 'google-font-lato', '//fonts.googleapis.com/css?family=Lato:300,700', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'google-font-amatic-SC', '//fonts.googleapis.com/css?family=Amatic+SC:400,700', array() );
}

//* Activate the use of Dashicons
 add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );
function load_dashicons_front_end() {
wp_enqueue_style( 'dashicons' );
}
//* Enqueue scripts for Responsive menu
add_action( 'wp_enqueue_scripts', 'enqueue_responsive_menu_script' );
function enqueue_responsive_menu_script() {

	wp_enqueue_script( 'my-responsive-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );

}

//* Add new image sizes
add_image_size('grid-thumbnail', 500, 500, TRUE);


// Customize the Post Info Conditionally on Home Page and Single Post in Genesis Sample Theme
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'genesis_post_info', 5 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
add_action( 'genesis_entry_content', 'genesis_do_post_title', 2 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );


add_filter( 'genesis_post_info', 'sp_post_info_filter' );
function sp_post_info_filter($post_info) {
	$post_info = '[post_date]   |   [post_categories before="Posted in: "] ';
	return $post_info;
}


//Change search form text

function themeprefix_search_button_text( $text ) {
return ( 'SEARCH...');
}
add_filter( 'genesis_search_text', 'themeprefix_search_button_text' );

/** Customize Read More Text */
add_filter( 'excerpt_more', 'child_read_more_link' );
add_filter( 'get_the_content_more_link', 'child_read_more_link' );
add_filter( 'the_content_more_link', 'child_read_more_link' );
function child_read_more_link() {

return '<a href="' . get_permalink() . '" rel="nofollow" class="more-link">READ MORE...</a>';
}


/* Header
---------------------------------------------------------------------------------------------------- */
/* Remove Header Widget Area for centered logo
--------------------------------------------- */
unregister_sidebar( 'header-right' );


// Add in custom header support.
// Parameters - https://codex.wordpress.org/Custom_Headers
// Need to add in flex-height and flex-width to be able to skip cropping in the Customizer.
add_theme_support( 'custom-header', array(
	'width'            => 1200,
	'height'           => 176,
	'flex-height'      => true,
	'flex-width'       => true,
	'header-text'      => false,
) );

//// Remove Genesis header style so we can use the customiser and header function genesischild_swap_header to add our header logo.
remove_action( 'wp_head', 'genesis_custom_header_style' );


/**
 * Add an image tag inline in the site title element for the main logo
 *
 * The header logo is then added via the Customiser
 *
 * @param string $title All the mark up title.
 * @param string $inside Mark up inside the title.
 * @param string $wrap Mark up on the title.
 * @author @_AlphaBlossom
 * @author @_neilgee
 */
function genesischild_swap_header( $title, $inside, $wrap ) {
	// Set what goes inside the wrapping tags.
	if ( get_header_image() ) :
		$logo = '<img  src="' . get_header_image() . '" width="' . esc_attr( get_custom_header()->width ) . '" height="' . esc_attr( get_custom_header()->height ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
	else :
		$logo = get_bloginfo( 'name' );
	endif;
		 $inside = sprintf( '<a href="%s" title="%s">%s</a>', trailingslashit( home_url() ), esc_attr( get_bloginfo( 'name' ) ), $logo );
		 // Determine which wrapping tags to use - changed is_home to is_front_page to fix Genesis bug.
		 $wrap = is_front_page() && 'title' === genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : 'p';
		 // A little fallback, in case an SEO plugin is active - changed is_home to is_front_page to fix Genesis bug.
		 $wrap = is_front_page() && ! genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : $wrap;
		 // And finally, $wrap in h1 if HTML5 & semantic headings enabled.
		 $wrap = genesis_html5() && genesis_get_seo_option( 'semantic_headings' ) ? 'h1' : $wrap;
		 $title = sprintf( '<%1$s %2$s>%3$s</%1$s>', $wrap, genesis_attr( 'site-title' ), $inside );
		 return $title;
}
add_filter( 'genesis_seo_title','genesischild_swap_header', 10, 3 );


/**
 * Add class for screen readers to site description.
 * This will keep the site description mark up but will not have any visual presence on the page
 * This runs if their is a header image set in the Customiser.
 *
 * @param string $attributes Add screen reader class.
 * @author @_AlphaBlossom
 * @author @_neilgee
 */
 function genesischild_add_site_description_class( $attributes ) {
		if ( get_header_image() ) :
			$attributes['class'] .= ' screen-reader-text';
			return $attributes;
		endif;
			return $attributes;
 }
 add_filter( 'genesis_attr_site-description', 'genesischild_add_site_description_class' );





//* NAVIGATION -----------------------------

//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_before', 'genesis_do_nav');

//* Adding back navigation extras
add_filter( 'wp_nav_menu_items', 'theme_menu_extras', 10, 2 );
/**
 * Filter menu items, appending either a search form or today's date.
 *
 * @param string   $menu HTML string of list items.
 * @param stdClass $args Menu arguments.
 *
 * @return string Amended HTML string of list items.
 */

function theme_menu_extras( $menu, $args ) {
	//* Change 'primary' to 'secondary' to add extras to the secondary navigation menu
	if ( 'primary' !== $args->theme_location )
		return $menu;
	ob_start();
	get_search_form();
	$search = ob_get_clean();
	$menu  .= '<li class="right-search">' . $search . '</li>';

	return $menu;
}

// Add previous and next post links after entry.
add_action( 'genesis_entry_footer', 'genesis_prev_next_post_nav' );

// Customize next page link in entry navigation.
add_filter ( 'genesis_next_link_text' , 'bg_next_page_link' );
function bg_next_page_link ( $text ) {
	return 'Next Page &raquo;';
}

// Customize previous page link in entry navigation.
add_filter ( 'genesis_prev_link_text' , 'bg_previous_page_link' );
function bg_previous_page_link ( $text ) {

	return '&laquo; Previous Page';

}

//* FOOTER --------------------------------

//* Change the footer text
add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');
function sp_footer_creds_filter( $creds ) {
	$creds = '<p class="attribution"> Theme designed & Developed by me, <a href="http://abigailthomas.com">Abigail Thomas</a> ';
	return $creds;
}


//*  WIDGETS AREA -------------------------

// Slider on Homepage
genesis_register_sidebar( array(
	'id'		=> 'home_slider',
	'name'		=> __( 'Homepage Slider', 'theme-name' ),
	'description'	=> __( 'This is the widget area the slider on the homepage.', 'theme-name' ),
) );

add_action( 'genesis_after_header', 'nabm_add_home_slider' );
function nabm_add_home_slider() {
	if ( is_home() )
	genesis_widget_area ('home_slider', array(
        'before' => '<div class="home_slider"><div class="wrap">',
        'after' => '</div></div>',
	) );
}

// Homepage Features @ bottom of page
genesis_register_sidebar( array(
	'id'		=> 'home_features',
	'name'		=> __( 'Homepage Features', 'theme-name' ),
	'description'	=> __( 'This is the widget area the features on the homepage.', 'theme-name' ),
) );

add_action( 'genesis_before_footer', 'nabm_add_home_features' );
function nabm_add_home_features() {
	if ( is_home() )
	genesis_widget_area ('home_features', array(
        'before' => '<div class="home_features"><div class="wrap">',
        'after' => '</div></div>',
	) );
}


// Post Categories at end of Post
genesis_register_sidebar( array(
	'id'		=> 'post_categories',
	'name'		=> __( 'End of Post Categories', 'theme-name' ),
	'description'	=> __( 'This is the widget area the post categories at the end of a post.', 'theme-name' ),
) );

add_action( 'genesis_entry_footer', 'nabm_add_post_content' );
function nabm_add_post_content() {
 	if ( is_single() )
	genesis_widget_area ('post_categories', array(
        'before' => '<div class="post_categories"><div class="wrap">',
        'after' => '</div></div>',
	) );
}

// Instagram Widget

// Register Instagram widget area.
genesis_register_sidebar( array(
	'id'          => 'instagram',
	'name'        => __( 'Instagram', 'theme-name' ),
	'description' => __( 'This is the instagram widget area.', 'theme-name' ),
) );

// Add Instagram widget after .
add_action( 'genesis_after_header', 'sp_instagram_feed_widget' );
function sp_instagram_feed_widget() {
	genesis_widget_area( 'instagram', array(
		'before' => '<div class="instagram"><div class="wrap">',
		'after'  => '</div></div>',
	) );
}

// //* UNDER HEADER --------------------------------

// // #GrayLifeFitLife Widget

// // Register Instagram widget area.
// genesis_register_sidebar( array(
// 	'id'          => 'insta-hashtag',
// 	'name'        => __( 'Instagram Hashtag', 'theme-name' ),
// 	'description' => __( 'This is the instagram hashtag widget area.', 'theme-name' ),
// ) );

// // Add Instagram widget before footer.
// add_action( 'genesis_after_header', 'sp_instagram_hashtag_feed_widget' );
// function sp_instagram_hashtag_feed_widget() {
// 	genesis_widget_area( 'insta-hashtag', array(
// 		'before' => '<div class="instagram"><div class="wrap">',
// 		'after'  => '</div></div>',
// 	) );
// }

// // //* RELATED POSTS --------------------------------

// // Adds custom image size for images in Related Posts section.
// add_image_size( 'related', 400, 222, true );

// add_action( 'genesis_after_loop', 'sk_related_posts', 12 );
// /**
//  * Outputs related posts with thumbnail.
//  *
//  * @author Nick the Geek
//  * @url http://designsbynickthegeek.com/tutorials/related-posts-genesis
//  * @global object $post
//  */
// function sk_related_posts() {
//     global $do_not_duplicate;

//     // If we are not on a single post page, abort.
//     if ( ! is_singular( 'post' ) ) {
//         return;
//     }

//     global $count;
//     $count = 0;

//     $related = '';

//     $do_not_duplicate = array();

//     // Get the tags for the current post.
//     $tags = get_the_terms( get_the_ID(), 'post_tag' );

//     // Get the categories for the current post.
//     $cats = get_the_terms( get_the_ID(), 'category' );

//     // If we have some tags, run the tag query.
//     if ( $tags ) {
//         $query    = sk_related_tax_query( $tags, $count, 'tag' );
//         $related .= $query['related'];
//         $count    = $query['count'];
//     }

//     // If we have some categories and less than 3 posts, run the cat query.
//     if ( $cats && $count <= 2 ) {
//         $query    = sk_related_tax_query( $cats, $count, 'category' );
//         $related .= $query['related'];
//         $count    = $query['count'];
//     }

//     // End here if we don't have any related posts.
//     if ( ! $related ) {
//         return;
//     }

//     // Display the related posts section.
//     echo '<div class="related">';
//         echo '<h3 class="related-title">Other posts you might like!</h3>';
//         echo '<div class="related-posts">' . $related . '</div>';
//     echo '</div>';
// }

// /**
//  * The taxonomy query.
//  *
//  * @since  1.0.0
//  *
//  * @param  array  $terms Array of the taxonomy's objects.
//  * @param  int    $count The number of posts.
//  * @param  string $type  The type of taxonomy, e.g: `tag` or `category`.
//  *
//  * @return string
//  */
// function sk_related_tax_query( $terms, $count, $type ) {
//     global $do_not_duplicate;

//     // If the current post does not have any terms of the specified taxonomy, abort.
//     if ( ! $terms ) {
//         return;
//     }

//     // Array variable to store the IDs of the posts.
//     // Stores the current post ID to begin with.
//     $post_ids = array_merge( array( get_the_ID() ), $do_not_duplicate );

//     $term_ids = array();

//     // Array variable to store the IDs of the specified taxonomy terms.
//     foreach ( $terms as $term ) {
//         $term_ids[] = $term->term_id;
//     }

//     $tax_query = array(
//         array(
//             'taxonomy'  => 'post_format',
//             'field'     => 'slug',
//             'terms'     => array(
//                 'post-format-link',
//                 'post-format-status',
//                 'post-format-aside',
//                 'post-format-quote',
//             ),
//             'operator' => 'NOT IN',
//         ),
//     );

//     $showposts = 3 - $count;

//     $args = array(
//         $type . '__in'        => $term_ids,
//         'post__not_in'        => $post_ids,
//         'showposts'           => $showposts,
//         'ignore_sticky_posts' => 1,
//         'tax_query'           => $tax_query,
//     );

//     $related  = '';

//     $tax_query = new WP_Query($args);

//     if ( $tax_query->have_posts() ) {
//         while ( $tax_query->have_posts() ) {
//             $tax_query->the_post();

//             $do_not_duplicate[] = get_the_ID();

//             $count++;

//             $title = get_the_title();

//             $related .= '<div class="related-post">';

//             $related .= '<a href="' . get_permalink() . '" rel="bookmark" title="Permanent Link to ' . $title . '">' . genesis_get_image(array( 'size' => 'related', 'attr' => array( 'class' => 'related-post-image' ) )) . '</a>';

//             $related .= '<div class="related-post-info"><a class="related-post-title" href="' . get_permalink() . '" rel="bookmark" title="Permanent Link to ' . $title . '">' . $title . '</a>';

//             $related .= '<div class="related-post-date">' . do_shortcode( '[post_date]' ) . '</div>';

//             $related .= '<div class="related-post-tags">' . do_shortcode( '[post_tags before="Tags: "]' ) . '</div>';

//             $related .= '<div class="related-post-categories">' . do_shortcode( '[post_categories before="Categories: "]' ) . '</div></div>';

//             $related .= '</div>';
//         }
//     }

//     wp_reset_postdata();

//     $output = array(
//         'related' => $related,
//         'count'   => $count,
//     );

//     return $output;
// }
