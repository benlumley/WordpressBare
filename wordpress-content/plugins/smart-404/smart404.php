<?php
/*
Plugin Name: Smart 404
Plugin URI: http://atastypixel.com/blog/wordpress/plugins/smart-404/
Description: Rescue your viewers from site errors!  When content cannot be found, Smart 404 will use the current URL to attempt to find matching content, and redirect to it automatically. Smart 404 also supplies template tags which provide a list of suggestions, for use on a 404.php template page if matching content can't be immediately discovered.
Version: 0.5
Author: Michael Tyson
Author URI: http://atastypixel.com/blog/
*/

/*  Copyright 2008 Michael Tyson <mike@tyson.id.au>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Main action handler
 *
 * @package Smart404
 * @since 0.1
 *
 * Searches through posts to see if any matches the REQUEST_URI.
 * Also searches tags
 */
function smart404_redirect() {
	if ( !is_404() )
		return;
	
	// Extract any GET parameters from URL
	$get_params = "";
	if ( preg_match("@/?(\?.*)@", $_SERVER["REQUEST_URI"], $matches) ) {
	    $get_params = $matches[1];
	}
	
	// Extract search term from URL
	$patterns_array = array();
	if ( ( $patterns = trim( get_option('ignored_patterns' ) ) ) ) {
		$patterns_array = explode( '\n', $patterns );
	}
	
	$patterns_array[] = "/(trackback|feed|(comment-)?page-?[0-9]*)/?$";
	$patterns_array[] = "\.(html|php)$";
	$patterns_array[] = "/?\?.*";
	$patterns_array = array_map(create_function('$a', '$sep = (strpos($a, "@") === false ? "@" : "%"); return $sep.trim($a).$sep."i";'), $patterns_array);
	
	$search = preg_replace( $patterns_array, "", urldecode( $_SERVER["REQUEST_URI"] ) );
	$search = basename(trim($search));
	$search = str_replace("_", "-", $search);
	$search = trim(preg_replace( $patterns_array, "", $search));
	
	if ( !$search ) return;
	
	$search_words = trim(preg_replace( "@[_-]@", " ", $search));
	$GLOBALS["__smart404"]["search_words"] = explode(" ", $search_words);
    $GLOBALS["__smart404"]["suggestions"] = array();

    $search_groups = (array)get_option( 'also_search' );
    if ( !$search_groups ) $search_groups = array("posts","pages","tags","categories");
    
    // Search twice: First looking for exact title match (high priority), then for a general search
    foreach ( $search_groups as $group ) {
        switch ( $group ) {
            case "posts":
                // Search for posts with exact name, redirect if one found
        	    $posts = get_posts( array( "name" => $search, "post_type" => "post" ) );
         		if ( count( $posts ) == 1 ) {
           			wp_redirect( get_permalink( $posts[0]->ID ) . $get_params, 301 );
           			exit();
           		}
                break;
                
            case "pages":
                // Search pages
                $posts = get_posts( array( "name" => $search, "post_type" => "page" ) );
         		if ( count( $posts ) == 1 ) {
           			wp_redirect( get_permalink( $posts[0]->ID ) . $get_params, 301 );
           			exit();
           		}
        		break;
        		
        	case "tags":
        	    // Search tags
        		$tags = get_tags( array ( "name__like" => $search ) );
        		if ( count($tags) == 1) {
        			wp_redirect(get_tag_link($tags[0]->term_id) . $get_params, 301);
        			exit();
        		}
        		break;
        		
            case "categories":
                // Search categories
        		$categories = get_categories( array ( "name__like" => $search ) );
        		if ( count($categories) == 1) {
        			wp_redirect(get_category_link($categories[0]->term_id) . $get_params, 301);
        			exit();
        		}
        		break;
        }
    }
    
    // Now perform general search
    foreach ( $search_groups as $group ) {
        switch ( $group ) {
            case "posts":
                $posts = smart404_search($search, "post");
         		if ( count( $posts ) == 1 ) {
           			wp_redirect( get_permalink( $posts[0]->ID ) . $get_params, 301 );
           			exit();
           		}

           		$GLOBALS["__smart404"]["suggestions"] = array_merge ( (array)$GLOBALS["__smart404"]["suggestions"], $posts );
                break;
                
            case "pages":
                $posts = smart404_search($search, "page");
         		if ( count( $posts ) == 1 ) {
           			wp_redirect( get_permalink( $posts[0]->ID ) . $get_params, 301 );
           			exit();
           		}

           		$GLOBALS["__smart404"]["suggestions"] = array_merge ( (array)$GLOBALS["__smart404"]["suggestions"], $posts );
        		break;
        }
    }
}


/**
 * Helper function for searching
 *
 * @package Smart404
 * @since 0.5
 * @param   query   Search query
 * @param   type    Entity type (page or post)
 * @return  Array of results
 */
function smart404_search($search, $type) {
    $search_words = trim(preg_replace( "@[_-]@", " ", $search));
	$posts = get_posts( array( "s" => $search_words, "post_type" => $type ) );
	if ( count( $posts ) > 1 ) {
	    // See if search phrase exists in title, and prioritise any single match
	    $titlematches = array();
	    foreach ( $posts as $post ) {
	        if ( strpos(strtolower($post->post_title), strtolower($search_words)) !== false ) {
	            $titlematches[] = $post;
	        }
	    }
	    if ( count($titlematches) == 1 ) {
	        return $titlematches;
	    }
	}
	
	return $posts;
}
 
 
/**
 * Filter to keep the inbuilt 404 handlers at bay
 *
 * @package Smart404
 * @since 0.3
 *
 */
function smart404_redirect_canonical_filter($redirect, $request) {
	
	if ( is_404() ) {
		// 404s are our domain now - keep redirect_canonical out of it!
		return false;
	}
	
	// redirect_canonical is good to go
	return $redirect;
}

/**
 * Set up administration
 *
 * @package Smart404
 * @since 0.1
 */
function smart404_setup_admin() {
	add_options_page( 'Smart 404', 'Smart 404', 5, __FILE__, 'smart404_options_page' );
	wp_enqueue_script('jquery-ui-sortable');
}

/**
 * Options page
 *
 * @package Smart404
 * @since 0.1
 */
function smart404_options_page() {
	?>
	<div class="wrap">
	<h2>Smart 404</h2>
	
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>
	
	<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><?php _e('Search:') ?><br/><small><?php _e('(Drag up/down to change priority)') ?></small></th>
		<td>
		<ul id="also_search_group">
		    <?php foreach ( array_unique(array_merge((array)get_option('also_search'), array('posts','pages','tags','categories'))) as $group ) : ?>
			<li><input type="checkbox" name="also_search[]" value="<?php echo $group ?>" <?php echo (in_array($group, (array)get_option('also_search')) ? "checked" : ""); ?> /> <?php _e(ucwords($group)) ?></li>
		    <?php endforeach; ?>
		</ul>
		</div>
		</td>
	</tr>
	
	<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#also_search_group').sortable();
        jQuery('#also_search_group').disableSelection();
    });
	</script>
	
	<tr valign="top">
		<th scope="row"><?php _e('Ignored patterns:') ?></th>
		<td>
			<textarea name="ignored_patterns" cols="44" rows="5"><?php echo htmlspecialchars(get_option('ignored_patterns')); ?></textarea><br />
			<?php _e("One term per line to ignore while searching. Regular expressions are permitted."); ?>
		</td>
	</tr>
	
	</table>
	
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="also_search,ignored_patterns" />
	
	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
	</p>
	
	</form>
	</div>
	<?php
}

/**
 * Template tag to determine if there any suggested posts
 *
 * @package Smart404
 * @since 0.1
 *
 * @return	boolean	True if there are some suggestions, false otherwise
 */
function smart404_has_suggestions() {
	return ( isset ( $GLOBALS["__smart404"]["suggestions"] ) && is_array( $GLOBALS["__smart404"]["suggestions"] ) && count( $GLOBALS["__smart404"]["suggestions"] ) > 0 ); 
}

/**
 * Template tag to obtain suggested posts
 *
 * @package Smart404
 * @since 0.1
 *
 * @return	array	Array of posts
 */
function smart404_get_suggestions() {
	return $GLOBALS["__smart404"]["suggestions"];
}

/**
 * Template tag to render HTML list of suggestions
 *
 * @package Smart404
 * @since 0.1
 *
 * @param	format	string	How to display the items: flat (just links, separated by line-breaks), list (li items)
 * @return	boolean	True if some suggestions were rendered, false otherwise
 */
function smart404_suggestions($format = 'flat') {
	if ( !isset ( $GLOBALS["__smart404"]["suggestions"] ) || !is_array( $GLOBALS["__smart404"]["suggestions"] ) || count( $GLOBALS["__smart404"]["suggestions"] ) == 0 ) 
		return false;
	
	echo '<div id="smart404_suggestions">';
	if ( $format == 'list' )
		echo '<ul>';
		
	foreach ( (array) $GLOBALS["__smart404"]["suggestions"] as $post ) {
		if ( $format == "list" )
			echo '<li>';
			
		?>
		<a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a>
		<?php
		
		if ( $format == "list" )
			echo '</li>';
		else if ( $format == "flat" )
			echo '<br />';
	}
	
	if ( $format == 'list ')
		echo '</ul>';
		
	echo '</div>';
	
	return true;
}

/**
 * Template tag to initiate 'The Loop' with suggested posts
 *
 * @package Smart404
 * @since 0.1
 *
 * @return	boolean	True if there are some posts to loop over, false otherwise
 */
function smart404_loop() {
	if ( !isset ( $GLOBALS["__smart404"]["suggestions"] ) || !is_array( $GLOBALS["__smart404"]["suggestions"] ) || count( $GLOBALS["__smart404"]["suggestions"] ) == 0 ) {
		return false;
	}
	
	$postids = array_map(create_function('$a', 'return $a->ID;'), $GLOBALS["__smart404"]["suggestions"]);
	
	query_posts( array( "post__in" => $postids ) );
	return have_posts();
}

/**
 * Template tag to retrieve array of search terms used
 *
 * @package Smart 404
 * @since 0.4
 *
 * @return Array of search terms
 */
function smart404_get_search_terms() {
    return $GLOBALS["__smart404"]["search_words"];
}

// Set up plugin

add_action( 'template_redirect', 'smart404_redirect' );
add_filter( 'redirect_canonical', 'smart404_redirect_canonical_filter', 10, 2 );
add_action( 'admin_menu', 'smart404_setup_admin' );
add_option( 'also_search', array ( 'posts', 'pages', 'tags', 'categories' ) );
add_option( 'ignored_patterns', '' );

?>
