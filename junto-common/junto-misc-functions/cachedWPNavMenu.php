<?php
/**
 * Wrapper function around wp_nav_menu() that will cache the wp_nav_menu for all tag/category
 * pages used in the nav menus
 * @see http://lookup.hitchhackerguide.com/wp_nav_menu for $args
 * @author tott
 */
function hh_cached_nav_menu( $args = array(), $prime_cache = false ) {
	global $wp_query;

	$queried_object_id = empty( $wp_query->queried_object_id ) ? 0 : (int) $wp_query->queried_object_id;

	// If design of navigation menus differs per queried object use the key below
	// $nav_menu_key = md5( serialize( $args ) . '-' . serialize( get_queried_object() ) );

	// Otherwise
	$nav_menu_key = md5( serialize( $args ) );

	$my_args = wp_parse_args( $args );
	$my_args = apply_filters( 'wp_nav_menu_args', $my_args );
	$my_args = (object) $my_args;

	if ( ( isset( $my_args->echo ) && true === $my_args->echo ) || !isset( $my_args->echo ) ) {
		$echo = true;
	} else {
		$echo = false;
	}

	$skip_cache = false;
	$use_cache = ( true === $prime_cache ) ? false : true;

	// If design of navigation menus differs per queried object comment out this section
	//*
	if ( is_singular() ) {
		$skip_cache = true;
	} else if ( !in_array( $queried_object_id, hh_get_nav_menu_cache_objects( $use_cache ) ) ) {
		$skip_cache = true;
	}
	//*/

	if ( true === $skip_cache || true === $prime_cache || false === ( $nav_menu = get_transient( $nav_menu_key ) ) ) {
		if ( false === $echo ) {
			$nav_menu = wp_nav_menu( $args );
		} else {
			ob_start();
			wp_nav_menu( $args );
			$nav_menu = ob_get_clean();
		}
		if ( false === $skip_cache )
			set_transient( $nav_menu_key, $nav_menu );
	}
	if ( true === $echo )
		echo $nav_menu;
	else
		return $nav_menu;
}

/**
 * Invalidate navigation menu when an update occurs
 */
function hh_update_nav_menu_objects( $menu_id = null, $menu_data = null ) {
	hh_cached_nav_menu( array( 'echo' => false ), $prime_cache = true );
}
add_action( 'wp_update_nav_menu', 'hh_update_nav_menu_objects' );

/**
 * Helper function that returns the object_ids we'd like to cache
 */
function hh_get_nav_menu_cache_objects( $use_cache = true ) {
	$object_ids = get_transient( 'hh_nav_menu_cache_object_ids' );
	if ( true === $use_cache && !empty( $object_ids ) ) {
		return $object_ids;
	}

	$object_ids = $objects = array();

	$menus = wp_get_nav_menus();
	foreach ( $menus as $menu_maybe ) {
		if ( $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id ) ) {
			foreach( $menu_items as $menu_item ) {
				if ( preg_match( "#.*/category/([^/]+)/?$#", $menu_item->url, $match ) )
					$objects['category'][] = $match[1];
				if ( preg_match( "#.*/tag/([^/]+)/?$#", $menu_item->url, $match ) )
					$objects['post_tag'][] = $match[1];
			}
		}
	}
	if ( !empty( $objects ) ) {
		foreach( $objects as $taxonomy => $term_names ) {
			foreach( $term_names as $term_name ) {
				$term = get_term_by( 'slug', $term_name, $taxonomy );
				if ( $term )
					$object_ids[] = $term->term_id;
			}
		}
	}

	$object_ids[] = 0; // that's for the homepage

	set_transient( 'hh_nav_menu_cache_object_ids', $object_ids );
	return $object_ids;
}