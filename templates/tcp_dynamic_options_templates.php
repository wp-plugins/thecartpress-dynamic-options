<?php
/**
 * This file is part of TheCartPress.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Returns true if the dynamic option exists
 * 
 * @param $args array('title' => 'ss', 'parent_id' => 9, 'price' => 99.9, 'terms' => array( 'taxonomy' => 'term_slug', ...) )
 */
function tcp_exists_dynamic_option( $args ) {
	$post_arg = array(
		'post_type'			=> TCP_DYNAMIC_OPTIONS_POST_TYPE,
		'post_parent'		=> $args['parent_id'],
	);
	if ( isset( $args['ID'] ) ) $post_arg['exclude'] = $args['ID'];
	$post_arg['tax_query'] = array( 'relation' => 'AND' );
	if ( isset( $args['terms'] ) ) {
		foreach( $args['terms'] as $taxonomy => $term ) {
			$tax_query = array(
				'taxonomy'	=> $taxonomy,
				'field'		=> 'slug',
			);
			if ( $term == NULL ) {
				$tax_query['terms'] = tcp_get_terms_slugs( $taxonomy );
				$tax_query['operator'] = 'NOT IN';
			} else {
				$tax_query['terms'] = $term;
			}
			$post_arg['tax_query'][] = $tax_query;
		}
	}
	$posts = get_posts( $post_arg );
	return count( $posts ) > 0;
}

/**
 * Inserts a dynamic product option
 * 
 * @param $args array( 'title' => 'ss', 'parent_id' => 9, 'price' => 99.9, 'terms' => array( 'taxonomy' => 'term', ...),  )
 * @since 1.0.0
 */
function tcp_insert_dynamic_option( $args ) {
	$post = array(
		'comment_status'	=> 'closed',
		'post_content'		=> '',
		'post_status'		=> 'publish',
		'post_type'			=> TCP_DYNAMIC_OPTIONS_POST_TYPE,
		'post_title'		=> $args['title'],
		'post_parent'		=> $args['parent_id'],
	);
	$post_id = wp_insert_post( $post );
	if ( isset( $args['terms'] ) && is_array( $args['terms'] ) ) {
		$terms = $args['terms'];
		foreach( $terms as $taxonomy => $term )
			$ids = wp_set_object_terms( $post_id, $term, $taxonomy );
	}
	update_post_meta( $post_id, 'tcp_price', $args['price'] );
	do_action( 'tcp_insert_option', $post_id, $args );
	return $post_id; 
}

/**
 * Updates a dynamic product option
 *
 * @param $args array( 'ID' => option_id, 'title' => 'ss', 'parent_id' => 9, 'price' => 99.9, 'terms' => array( 'taxonomy' => 'term', ...) )
 * @since 1.0.0
 */
function tcp_update_dynamic_option( $args ) {
	$post_id = $args['ID'];
	wp_delete_object_term_relationships( $post_id, get_taxonomies() );
	if ( isset( $args['terms'] ) && is_array( $args['terms'] ) ) {
		$terms = $args['terms'];
		foreach( $terms as $taxonomy => $term )
			wp_set_object_terms( $post_id, $term, $taxonomy, true );
	}
	$post = array(
		'ID'			=> $post_id, 
		'post_title'	=> $args['title'],
	);
	wp_update_post( $post );
	update_post_meta( $post_id, 'tcp_price', $args['price'] );
	do_action( 'tcp_update_option', $post_id, $args );
	return $post_id;
}

/**
 * Deletes a dynamic product option
 *
 * @param $args array( 'ID' => option_id, 'title' => 'ss', 'parent_id' => 9, 'price' => 99.9, 'terms' => array( 'taxonomy' => 'term', ...) )
 * @since 1.0.0
 */
function tcp_delete_dynamic_option( $post_id ) {
	wp_delete_post( $post_id, true );
	delete_post_meta( $post_id, 'tcp_price' );
	do_action( 'tcp_delete_option', $post_id );
}

/**
 * Returns the dynamic product options
 * @param $post_id
 * @since 1.0.0 
 */
function tcp_get_dynamic_options( $post_id ) {
	return get_posts( array( 'post_type' => TCP_DYNAMIC_OPTIONS_POST_TYPE, 'post_parent' => $post_id, 'numberposts' => -1 ) );
}

/**
 * Returns the dynamic product parent from a dynamic option
 * @param $post_id
 */
function tcp_get_parent_from_dynamic_option( $post_id ) {
	$post = get_post( $post_id );
	return $post->post_parent;
	//$ancestors = get_post_ancestors( $post_id );
	//if ( is_array( $ancestors ) && count( $ancestors ) > 0 ) return $ancestors[0];
	//else return false;
}

/**
 * Returns the number of options
 * @param $post_id
 * @since 1.0.0
 */
function tcp_count_dynamic_options( $post_id ) {
	return count( tcp_get_dynamic_options( $post_id ) );
}

/**
 * 
 * Returns true if the product has options
 * @param $post_id
 * @since 1.0.0
 */
function tcp_has_dynamic_options( $post_id ) {
	return count( tcp_get_dynamic_options( $post_id ) ) > 0;
}

/**
 * Returns the attributes defined in the ecommerce
 * @param $type names (by default) or objects
 * @since 1.0.0
 */
function tcp_get_attributes( $type = 'names' ) {
	return get_object_taxonomies( TCP_DYNAMIC_OPTIONS_POST_TYPE, $type );
}

/**
 * Returns the atributes assigned to a product
 * @param $post_id product id
 * @return Array of objects
 * @since 1.0.0
 */ 
function tcp_get_attributes_by_product( $post_id ) {
	$attributes = array();
	$product_attribute_sets = get_post_meta( $post_id, 'tcp_attribute_sets', true );
	if ( is_array( $product_attribute_sets )  && count( $product_attribute_sets ) > 0 ) {
		$attribute_sets = get_option( 'tcp_attribute_sets', array() );
		foreach( $product_attribute_sets as $id ) {
			if ( isset( $attribute_sets[$id] ) ) {
				foreach( $attribute_sets[$id]['taxonomies'] as $taxonomy ) {
					$attributes[] = get_taxonomy( $taxonomy );
				}
			}
		}
	}
	return $attributes;
}

/**
 * Returns the term slugs defined for a taxonomy
 * @param $taxonomy
 * @since 1.0.0
 */
function tcp_get_terms_slugs( $taxonomy ) {
	$slugs = array();
	$terms = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
	foreach ( $terms as $term ) {
		$slugs[] = $term->slug;
	}
	return $slugs;
}

/**
 * Display the dynmic product options
 * @param $product id
 * @since 1.0.0
 */
function tcp_the_buy_button_dyamic_options( $product_id, $echo = true ) {
	global $tcp_dynamic_options;
	if ( isset( $tcp_dynamic_options ) ) {
		$html = $tcp_dynamic_options->tcp_the_add_to_cart_unit_field( '', $product_id );
		if ( $echo ) echo $html;
		else return $html;
	}
}
?>
