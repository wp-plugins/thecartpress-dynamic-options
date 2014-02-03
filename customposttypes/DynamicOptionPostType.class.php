<?php
/**
 * This file is part of TheCartPress-Dynamic options.
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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPDynamicOptionPostType' ) ) :

class TCPDynamicOptionPostType {

	static function create_default_custom_post_type_and_taxonomies() {
		$def = array(
			'name'					=> __( 'Dynamic Options', 'tcp-do' ),
			'desc'					=> __( 'Options for TheCartPress Dynamic Options', 'tcp-do' ),
			'activate'				=> true,
			'singular_name'			=> __( 'Option', 'tcp-do' ),
			'add_new'				=> __( 'Add New', 'tcp-do' ),
			'add_new_item'			=> __( 'Add New', 'tcp-do' ),
			'edit_item'				=> __( 'Edit Option', 'tcp-do' ),
			'new_item'				=> __( 'New Option', 'tcp-do' ),
			'view_item'				=> __( 'View Option', 'tcp-do' ),
			'search_items'			=> __( 'Search Options', 'tcp-do' ),
			'not_found'				=> __( 'No options found', 'tcp-do' ),
			'not_found_in_trash'	=> __( 'No options found in Trash', 'tcp-do' ),
			'public'				=> false,
			'show_ui'				=> true,
			'show_in_menu'			=> false,
			'can_export'			=> true,
			'show_in_nav_menus'		=> false,
			'query_var'				=> true,
			'supports'				=> array( 'title', 'excerpt', 'editor', 'thumbnail' ),// 'comments' ),
			'rewrite'				=> 'options',
			'has_archive'			=> 'options',
			'is_saleable'			=> true,
		);
		tcp_create_custom_post_type( TCP_DYNAMIC_OPTIONS_POST_TYPE, $def );
	}
}
endif; // class_exists check