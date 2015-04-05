<?php
/*
Plugin Name: TheCartPress Dynamic Options
Plugin URI: http://extend.thecartpress.com/ecommerce-plugins/dynamic-options/
Description: Adds Dynamic Options to TheCartPress
Version: 1.3.3
Author: TheCartPress team
Author URI: http://thecartpress.com
Text Domain: tcp-do
Domain Path: /languages/
License: GPL
Parent: thecartpress
*/

/**
 * This file is part of TheCartPress-DynamicOptions.
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

if ( ! class_exists( 'TCPDynamicOptions' ) ) :

define( 'TCP_DYNAMIC_OPTIONS_FOLDER'					, dirname( __FILE__ ) . '/' );
define( 'TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER'				, TCP_DYNAMIC_OPTIONS_FOLDER . 'admin/' );
define( 'TCP_DYNAMIC_OPTIONS_CLASSES_FOLDER'			, TCP_DYNAMIC_OPTIONS_FOLDER . 'classes/' );
define( 'TCP_DYNAMIC_OPTIONS_POST_TYPE_FOLDER'			, TCP_DYNAMIC_OPTIONS_FOLDER . 'customposttypes/' );
define( 'TCP_DYNAMIC_OPTIONS_TEMPLATES_FOLDER'			, TCP_DYNAMIC_OPTIONS_FOLDER . 'templates/' );
define( 'TCP_DYNAMIC_OPTIONS_THEMES_TEMPLATES_FOLDER'	, TCP_DYNAMIC_OPTIONS_FOLDER . 'themes-templates/' );
define( 'TCP_DYNAMIC_OPTIONS_METABOXES_FOLDER'			, TCP_DYNAMIC_OPTIONS_FOLDER . 'metaboxes/' );

define( 'TCP_DYNAMIC_OPTIONS_POST_TYPE'		, 'tcp_dynamic_options' );
define( 'TCP_DYNAMIC_OPTIONS_ADMIN_PATH'	, 'admin.php?page=' . plugin_basename( TCP_DYNAMIC_OPTIONS_FOLDER ) . '/admin/' );

require_once( TCP_DYNAMIC_OPTIONS_TEMPLATES_FOLDER . 'tcp_dynamic_options_templates.php' );

class TCPDynamicOptions {

	function __construct() {
		add_action( 'tcp_init'									, array( $this, 'tcp_init' ) );
		add_filter( 'tcp_the_add_to_cart_items_in_the_cart'		, array( $this, 'tcp_the_add_to_cart_items_in_the_cart' ), 10, 2 );
		add_filter( 'tcp_get_the_tax_id'						, array( $this, 'tcp_get_the_tax_id' ), 10, 2 );
		add_filter( 'tcp_add_item_shopping_cart'				, array( $this, 'tcp_add_item_shopping_cart' ) );
		//add_filter( 'tcp_add_item_wish_list'					, array( $this, 'tcp_add_item_shopping_cart' ) );
		add_filter( 'tcp_add_wish_list'							, array( $this, 'tcp_add_wish_list' ), 10, 2 );

		add_filter( 'tcp_get_discount_by_product'				, array( $this, 'tcp_get_discount_by_product' ), 10, 3 );
		add_filter( 'tcp_get_discount_by_coupon_by_product'		, array( $this, 'tcp_get_discount_by_coupon_by_product' ), 10, 3 );

		add_filter( 'tcp_get_the_title'							, array( $this, 'tcp_get_the_title' ), 10, 4 );
		add_filter( 'tcp_get_the_sku'							, array( $this, 'tcp_get_the_sku' ), 10, 2 );

		add_filter( 'tcp_get_the_thumbnail'						, array( $this, 'tcp_get_the_thumbnail'), 10, 3 );
		add_filter( 'tcp_has_post_thumbnail'					, array( $this, 'tcp_has_post_thumbnail'), 10, 2 );

		add_filter( 'tcp_get_permalink'							, array( $this, 'tcp_get_permalink'), 10, 2 );
		add_filter( 'post_type_link'							, array( $this, 'post_type_link' ), 10, 4 );

		add_filter( 'tcp_get_image_in_content'					, array( $this, 'tcp_get_image_in_content' ), 10, 3 );
		add_filter( 'tcp_get_image_in_excerpt'					, array( $this, 'tcp_get_image_in_content' ), 10, 3 );
		add_filter( 'tcp_get_image_in_grouped_buy_button'		, array( $this, 'tcp_get_image_in_grouped' ), 10, 3 );

		//add_filter( 'tcp_get_the_product_price'				, array( $this, 'tcp_get_the_product_price' ), 10, 2 );
		add_filter( 'tcp_get_the_price'							, array( $this, 'tcp_get_the_product_price' ), 10, 2 );

		add_filter( 'tcp_get_product_types'						, array( $this, 'tcp_get_product_types' ) );
		//add_filter( 'tcp_exclude_from_order_discount', array( $this, 'tcp_exclude_from_order_discount' ), 10, 2 );
		add_filter( 'tcp_get_the_price_label'					, array( $this, 'tcp_get_the_price_label' ), 10, 3 );
		add_filter( 'tcp_get_product_post_types'				, array( $this, 'tcp_get_product_post_types' ) );
		add_filter( 'tcp_the_tier_price'						, array( $this, 'tcp_the_tier_price' ), 10, 2 );
		add_filter( 'tcp_api_update_product_post'				, array( $this, 'tcp_api_update_product_post' ), 10, 2 );
		add_action( 'tcp_api_update_product'					, array( $this, 'tcp_api_update_product' ), 10, 2 );
		add_filter( 'tcp_get_the_total_sales'					, array( $this, 'tcp_get_the_total_sales' ), 10, 2 );
		add_filter( 'tcp_get_the_stock'							, array( $this, 'tcp_get_the_stock' ), 10, 2 );
		//Layered Navigation
		add_filter( 'tcp_the_layered_navigation_get_posts_query', array( $this, 'tcp_the_layered_navigation_get_posts_query' ), 10, 4 );
		add_filter( 'tcp_the_layered_navigation_link'			, array( $this, 'tcp_the_layered_navigation_link' ), 10, 4 );
		add_filter( 'tcp_filter_navigation_get_filters_request'	, array( $this, 'tcp_filter_navigation_get_filters_request' ) );
		add_filter( 'tcp_filter_navigation_get_filter'			, array( $this, 'tcp_filter_navigation_get_filter' ), 10, 2 );

		if ( is_admin() ) {
			add_action( 'admin_init'							, array( $this, 'admin_init' ) );
			add_action( 'admin_menu'							, array( $this, 'admin_menu' ), 20 );
			register_activation_hook( __FILE__					, array( $this, 'activate_plugin' ) );
		}
	}

	function tcp_get_the_stock( $stock, $post_id ) {
		$args = array(
			'post_type'		=> TCP_DYNAMIC_OPTIONS_POST_TYPE,
			'post_parent'	=> $post_id,
			'numberposts'	=> -1,
			'fields'		=> 'ids',
		);
		$posts = get_posts( $args );
		if ( is_array( $posts ) && count( $posts ) > 0 ) {
			$stock = 0;
			foreach( $posts as $post_id ) {
				$current_stock = (int)tcp_get_the_stock( $post_id );
				if ( $current_stock == -1 ) return -1;
				else $stock += $current_stock;
			}
		}
		return $stock;
	}

	function tcp_get_the_total_sales( $total_sales, $parent_id ) {
		$args = array(
			'post_type'		=> TCP_DYNAMIC_OPTIONS_POST_TYPE,
			'post_parent'	=> $parent_id,
			'numberposts'	=> -1,
			'fields'		=> 'ids',
		);
		$posts = get_posts( $args );
		if ( is_array( $posts ) && count( $posts ) > 0 ) {
			require_once( TCP_DAOS_FOLDER . 'OrdersDetails.class.php' );
			foreach( $posts as $post_id ) {
				$post_id = tcp_get_default_id( $post_id );
				$total_sales += OrdersDetails::get_product_total_sales( $post_id );
			}
		}
		return $total_sales;
	}

	function tcp_api_update_product_post( $post, $params ) {
		if ( isset( $params['parent_sku'] ) && strlen( $params['parent_sku'] ) > 0 ) {
			$parent = tcp_get_product_by_sku( $params['parent_sku'] );
			if ( $parent > 0 ) $post['post_parent'] = $parent;
		}
		return $post;
	}

	function tcp_api_update_product( $post_id, $params ) {
		if ( isset( $params['attribute_sets'] ) && strlen( $params['attribute_sets'] ) > 0 ) {
			update_post_meta( $post_id, 'tcp_attribute_sets', array( $params['attribute_sets'] ) );
		}
	}

	function tcp_the_tier_price( $out, $post_id ) {
		$options = tcp_get_dynamic_options( $post_id );
		if ( is_array( $options ) && count( $options ) > 0 ) {
			ob_start();
			$located = locate_template( 'tcp_dynamic_tier_price.php' );
			if ( strlen( $located ) == 0 ) $located = TCP_DYNAMIC_OPTIONS_THEMES_TEMPLATES_FOLDER . 'tcp_dynamic_tier_price.php';
			require( $located );
			$out .= ob_get_clean();
		}
		return $out;
	}

	function init() {
		if ( ! function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'thecartpress/TheCartPress.class.php' ) )
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	function tcp_init( $thecartpress ) {
		if ( function_exists( 'load_plugin_textdomain' ) ) {
			load_plugin_textdomain( 'tcp-do', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		require_once( TCP_DYNAMIC_OPTIONS_METABOXES_FOLDER . 'DynamicOptionsCustomFieldsMetabox.class.php' );
		require_once( TCP_DYNAMIC_OPTIONS_METABOXES_FOLDER . 'DynamicOptionsMetabox.class.php' );

		wp_register_script( 'tcp_do_scripts', plugins_url( 'js/tcp_dinamic_options.js', __FILE__ ) );
		wp_enqueue_script( 'tcp_do_scripts' );

		//$thecartpress->register_saleable_post_type( TCP_DYNAMIC_OPTIONS_POST_TYPE );

		$version = (int)get_option( 'tcp_dynamic_version' );
		if ( $version < 113 ) {//move to activate_plugin
			$args = array(
				'post_type'		=> TCP_DYNAMIC_OPTIONS_POST_TYPE,
				'numberposts'	=> -1,
				'fields'		=> 'ids',
			);
			$posts = get_posts( $args );
			foreach( $posts as $post_id ) {
				update_post_meta( $post_id, 'tcp_is_visible', true );
				$stock = get_post_meta( $post_id, 'tcp_stock', true );
				if ( $stock == '') update_post_meta( $post_id, 'tcp_stock', -1 );
			}
			update_option( 'tcp_dynamic_version', 124 );
		}
	}

	function admin_init() {
		//add_action( 'tcp_product_metabox_toolbar'					, array( $this, 'tcp_product_metabox_toolbar' ) );//before 1.3.2
		add_filter( 'tcp_product_custom_fields_links'				, array( $this, 'tcp_product_custom_fields_links' ), 10, 3 );//since 1.3.2

		add_action( 'tcp_product_metabox_custom_fields'				, array( $this, 'tcp_product_metabox_custom_fields' ) );
		add_action( 'tcp_product_metabox_save_custom_fields'		, array( $this, 'tcp_product_metabox_save_custom_fields' ) );
		add_action( 'tcp_product_metabox_delete_custom_fields'		, array( $this, 'tcp_product_metabox_delete_custom_fields' ) );
		add_filter( 'tcp_product_row_actions'						, array( $this, 'tcp_product_row_actions' ) );
		add_filter( 'tcp_theme_compatibility_unset_settings_action'	, array( $this, 'tcp_theme_compatibility_unset_settings_action' ), 10, 2 );
		add_filter( 'tcp_theme_compatibility_settings_action'		, array( $this, 'tcp_theme_compatibility_settings_action' ), 10, 2 );
		add_action( 'tcp_theme_compatibility_settings_page'			, array( $this, 'tcp_theme_compatibility_settings_page' ), 50 );

		//CSV Loader hooks
		add_filter( 'tcp_csvl_option_columns'						, array( $this, 'tcp_csvl_option_columns' ), 10, 2 );
		add_filter( 'tcp_csv_loader_new_post'						, array( $this, 'tcp_csv_loader_new_post' ), 10, 2 );
		add_action( 'tcp_csv_loader_row'							, array( $this, 'tcp_csv_loader_row' ), 10, 2 );
	}

	function admin_notices() { ?>
		<div class="error"><p><?php _e( '<strong>Dynamic Options for TheCartPress</strong> requires TheCartPress plugin activated.', 'tcp-do' ); ?></p></div>
	<?php }

	function activate_plugin() {
		if ( ! function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'thecartpress/TheCartPress.class.php' ) ) {
			exit( __( '<strong>Dynamic Options for TheCartPress</strong> requires TheCartPress plugin', 'tcp-do' ) );
		}
		require_once( TCP_DYNAMIC_OPTIONS_POST_TYPE_FOLDER . 'DynamicOptionPostType.class.php' );
		TCPDynamicOptionPostType::create_default_custom_post_type_and_taxonomies();
		// $ids = get_posts( array(
		// 	'post_type'		=> TCP_DYNAMIC_OPTIONS_POST_TYPE,
		// 	'numberposts'	=> -1,
		// 	'fields'		=> 'ids',
		// ) );
		// foreach( $ids as $id ) {
		// 	$order = get_post_meta( $id, 'tcp_order', true );
		// 	if ( $order == '' ) update_post_meta( $id, 'tcp_order', 0 );
		// }//Deprecated
	}

	function admin_menu() {
		if ( ! defined( 'TCP_PRODUCT_POST_TYPE' ) ) return;
		$base = 'edit.php?post_type=' . TCP_PRODUCT_POST_TYPE;
		add_submenu_page( $base, __( 'Attribute Sets', 'tcp-do' ), __( 'Attributes Sets', 'tcp-do' ), 'tcp_edit_product', TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER . 'AttributeSetsList.php' );
		add_submenu_page( $base, __( 'Attributes', 'tcp-do' ), __( 'Attributes', 'tcp-do' ), 'tcp_edit_product', TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER . 'AttributeList.php' );
		add_submenu_page( 'tcpatt', __( 'Option list', 'tcp-do' ), __( 'Option list', 'tcp-do' ), 'tcp_edit_product', TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER . 'AttributeSetEdit.php' );
		add_submenu_page( 'tcpatt', __( 'Option list', 'tcp-do' ), __( 'Option list', 'tcp-do' ), 'tcp_edit_product', TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER . 'DynamicOptionsList.php' );
	}

	function tcp_theme_compatibility_settings_page( $suffix ) {
		global $thecartpress;
		if ( ! isset( $thecartpress ) ) return;
		$dynamic_options_type			 = $thecartpress->get_setting( 'dynamic_options_type' . $suffix, 'single' );
		$dynamic_options_order_by		 = $thecartpress->get_setting( 'dynamic_options_order_by' . $suffix, 'title' );
		$dynamic_options_order			 = $thecartpress->get_setting( 'dynamic_options_order' . $suffix, 'ASC' );
		$dynamic_options_calculate_price = $thecartpress->get_setting( 'dynamic_options_calculate_price' . $suffix, 'complex' ); ?>
<a name="dynamic_options_settings"></a>
<h3><?php _e( 'Dynamic Option Settings', 'tcp-do'); ?></h3>

<div class="postbox">
<div class="inside">
<table class="form-table">
<tbody>
	<tr valign="top">
		<th scope="row">
		<label for="dynamic_options_type"><?php _e( 'Dynamic options type', 'tcp-do' ); ?></label>
		</th>
		<td>
			<select id="dynamic_options_type" name="dynamic_options_type">
				<option value="list" <?php selected( $dynamic_options_type, 'list' ); ?>><?php _e( 'List', 'tcp-do' ); ?></option>
				<option value="single" <?php selected( $dynamic_options_type, 'single' ); ?>><?php _e( 'Single', 'tcp-do' ); ?></option>
				<option value="double" <?php selected( $dynamic_options_type, 'double' ); ?>><?php _e( 'Multiple', 'tcp-do' ); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
		<label for="dynamic_options_order_by_order"><?php _e( 'Dynamic options order by', 'tcp-do' ); ?></label>
		</th>
		<td>
			<label><input type="radio" id="dynamic_options_order_by_order" name="dynamic_options_order_by" value="order" <?php checked( 'order', $dynamic_options_order_by ); ?> /> <?php _e( 'Order field', 'tcp-do' ); ?></label><br/>
			<label><input type="radio" id="dynamic_options_order_by_title" name="dynamic_options_order_by" value="title" <?php checked( 'title', $dynamic_options_order_by ); ?> /> <?php _e( 'Title', 'tcp-do' ); ?></label><br/>
			<label><input type="radio" id="dynamic_options_order_by_price" name="dynamic_options_order_by" value="price" <?php checked( 'price', $dynamic_options_order_by ); ?> /> <?php _e( 'Price', 'tcp-do' ); ?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
		<label for="dynamic_options_order_asc"><?php _e( 'Dynamic options order', 'tcp-do' ); ?></label>
		</th>
		<td>
			<label><input type="radio" id="dynamic_options_order_asc" name="dynamic_options_order" value="ASC" <?php checked( 'ASC', $dynamic_options_order ); ?> /> <?php _e( 'Ascending', 'tcp-do' ); ?></label><br/>
			<label><input type="radio" id="dynamic_options_order_desc" name="dynamic_options_order" value="DESC" <?php checked( 'DESC', $dynamic_options_order ); ?> /> <?php _e( 'Descending', 'tcp-do' ); ?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
		<label for="dynamic_options_calculate_price_individual"><?php _e( 'Dynamic options calculate price', 'tcp-do' ); ?></label>
		</th>
		<td>
			<label><input type="radio" id="dynamic_options_calculate_price_individual" name="dynamic_options_calculate_price" value="individual" <?php checked( 'individual', $dynamic_options_calculate_price ); ?> /> <?php _e( 'Individual', 'tcp-do' ); ?></label><br/>
			<label><input type="radio" id="dynamic_options_calculate_price_complex" name="dynamic_options_calculate_price" value="complex" <?php checked( 'complex', $dynamic_options_calculate_price ); ?> /> <?php _e( 'Complex', 'tcp-do' ); ?></label>
			<p class="description"><?php _e( '"Individual" value will return as the price of an option the price of the Product. "Complex" means that prices will be added.', 'tcp-do' ); ?></p>
		</td>
	</tr>
</tbody>
</table>
</div>
</div><?php
	}

	function tcp_csvl_option_columns( $options, $col ) {
		$options[] = array( 'tcp_do_parent', strtoupper( $col ) == 'PARENT', 'DYN: ' . __( 'Parent', 'tcp-do' ) );
		$options[] = array( 'tcp_do_att_set', strtoupper( $col ) == 'ATTRIBUTE SET', 'DYN: ' .__( 'Attribute set', 'tcp-do' ) );
		foreach( get_object_taxonomies( TCP_DYNAMIC_OPTIONS_POST_TYPE ) as $taxmy ) {
			$tax = get_taxonomy( $taxmy );
			$options[] = array( 'tcp_do_tax_' . $taxmy, $col == $tax->labels->name, 'DYN: ' . 'Taxonomy: ' . $tax->labels->name ); //. ' (' . $tax->labels->desc . ')' );
		}
		return $options;
	}

	function tcp_csv_loader_new_post( $post, $cols ) {
		foreach( $cols as $i => $col ) {
			$col_names = isset( $_REQUEST['col_' . $i] ) ? $_REQUEST['col_' . $i] : array();
			if ( is_array( $col_names ) && count( $col_names ) > 0 ) {
				foreach( $col_names as $col_name ) {
					if ( 'tcp_do_parent' == $col_name ) {
						$post['post_parent'] = tcp_get_product_by_sku( $col );
					}
				}
			}
		}
		return $post;
	}

	function tcp_csv_loader_row( $post_id, $cols ) {
		$taxonomies = get_object_taxonomies( TCP_DYNAMIC_OPTIONS_POST_TYPE );
		foreach( $cols as $i => $col ) {
			$col_names = isset( $_REQUEST['col_' . $i] ) ? $_REQUEST['col_' . $i] : array();
			if ( is_array( $col_names ) && count( $col_names ) > 0 ) {
				foreach( $col_names as $col_name ) {
					if ( 'tcp_do_att_set' == $col_name ) {
						$tcp_attribute_set = $col;
					}
					if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
						foreach( $taxonomies as $taxmy ) {
							if ( $col_name == 'tcp_do_tax_' . $taxmy ) {
								$taxo_values[$taxmy] = explode( ',', $col );
							}
						}
					}
				}
			}
		}
		if ( isset( $tcp_attribute_set ) ) {
			$id = tcp_att_set_get_id( $tcp_attribute_set );
			tcp_update_attribute_set( $id, $tcp_attribute_set );
			update_post_meta( $post_id, 'tcp_attribute_sets', $id );
		}
		//Taxonomies & Terms
		if ( isset( $taxo_values ) ) {
			foreach( $taxo_values as $tax => $terms ) {
				if ( is_array( $terms ) && count( $terms ) > 0 ) {
					foreach( $terms as $term ) {
						$new_term = term_exists( $term, $tax );
						if ( ! is_array( $new_term ) ) {
							$new_term = wp_insert_term(	$term, $tax, array( 'slug' => esc_html( $term ) ) );
						} else {
							wp_set_object_terms( $post_id, (int)$new_term['term_id'], $tax, true );
						}
					}
				}
			}
		}
	}

	function tcp_theme_compatibility_settings_action( $settings, $suffix ) {
		$settings['dynamic_options_type' . $suffix]		= isset( $_POST['dynamic_options_type'] ) ? $_POST['dynamic_options_type'] : 'single';
		$settings['dynamic_options_order_by' . $suffix]	= isset( $_POST['dynamic_options_order_by'] ) ? $_POST['dynamic_options_order_by'] : 'title';
		$settings['dynamic_options_order' . $suffix]	= isset( $_POST['dynamic_options_order'] ) ? $_POST['dynamic_options_order'] : 'ASC';
		$settings['dynamic_options_calculate_price' . $suffix] = isset( $_POST['dynamic_options_calculate_price'] ) ? $_POST['dynamic_options_calculate_price'] : 'complex';
		return $settings;
	}

	function tcp_theme_compatibility_unset_settings_action( $settings, $suffix ) {
		unset( $settings['dynamic_options_type' . $suffix] );
		unset( $settings['dynamic_options_order_by' . $suffix] );
		unset( $settings['dynamic_options_order' . $suffix] );
		unset( $settings['dynamic_options_calculate_price' . $suffix] );
		return $settings;
	}

	/*function tcp_product_metabox_toolbar( $post_id ) {
		if ( ! current_user_can( 'tcp_edit_product' ) ) return;
		$post_type = get_post_type( $post_id );
		if ( $post_type == TCP_DYNAMIC_OPTIONS_POST_TYPE ) {
			$post = get_post( $post_id );
			$post = get_post( $post->post_parent );
			echo '<li>|</li>', '<li>', __( 'Parent: ', 'tcp-do' ), '<a href="', get_edit_post_link( $post->ID ), '">', $post->post_title, '</a>';
		} else {
			$type = tcp_get_the_product_type( $post_id );
			if ( strlen( $type ) == 0 ) return;
			$types = tcp_get_product_types();
			$type = $types[$type];
			if ( isset( $type['tcp_dynamic_options_supported'] ) && $type['tcp_dynamic_options_supported'] ) {
				echo '<li>|</li>';
				$count = tcp_count_dynamic_options( $post_id, false );
				$count = ( $count > 0 ) ? ' (' . $count . ')' : '';
				$admin_path = 'admin.php?page=' . plugin_basename( TCP_DYNAMIC_OPTIONS_FOLDER ) . '/admin/';
				echo '<li><a href="', $admin_path, 'DynamicOptionsList.php&post_id=', $post_id, '">', __( 'dynamic options', 'tcp-do' ), $count, '</a></li>';
			}
		}
	}*/

	function tcp_product_custom_fields_links( $links, $post_id, $post ) {
		if ( ! current_user_can( 'tcp_edit_product' ) ) return;
		$post_type = get_post_type( $post_id );
		if ( $post_type == TCP_DYNAMIC_OPTIONS_POST_TYPE ) {
			$post = get_post( $post->post_parent );
			$links[] = array(
				'url'	=> get_edit_post_link( $post->ID ),
				'title'	=> '',
				'label'	=> __( 'Parent', 'tcp-do' ) .': ' . $post->post_title
			);
		} else {
			$type = tcp_get_the_product_type( $post_id );
			if ( strlen( $type ) == 0 ) return;
			$types = tcp_get_product_types();
			$type = $types[$type];
			if ( isset( $type['tcp_dynamic_options_supported'] ) && $type['tcp_dynamic_options_supported'] ) {
				$count = tcp_count_dynamic_options( $post_id, false );
				$count = ( $count > 0 ) ? ' (' . $count . ')' : '';
				$admin_path = 'admin.php?page=' . plugin_basename( TCP_DYNAMIC_OPTIONS_FOLDER ) . '/admin/';
				$links[] = array(
					'url'	=> $admin_path . 'DynamicOptionsList.php&post_id=' . $post_id,
					'title'	=> '',
					'label'	=> __( 'dynamic options', 'tcp-do' ) . $count
				);
			}
		}
		return $links;
	}

	function tcp_product_metabox_custom_fields( $post_id ) {
		$tcp_attribute_sets = get_post_meta( $post_id, 'tcp_attribute_sets', true );
		$post_type = get_post_type( $post_id );
		if ( $post_type == TCP_DYNAMIC_OPTIONS_POST_TYPE ) return; ?>
		<tr valign="top">
			<th scope="row">
				<label for="tcp_attribute_sets"><?php _e( 'Attribute Sets', 'tcp-do' ); ?>:</label>
			</th>
			<td>
				<?php $attribute_sets = get_option( 'tcp_attribute_sets', array() ); ?>
				<select name="tcp_attribute_sets[]" id="tcp_attribute_sets">
					<option value=""><?php _e( 'none', 'tcp-do' ); ?></option>
				<?php foreach( $attribute_sets as $id => $attribute_set ) : ?>
					<option value="<?php echo $id; ?>" <?php tcp_selected_multiple( $tcp_attribute_sets, $id ); ?>><?php echo $attribute_set['title']; ?></option>
				<?php endforeach; ?>
				</select>
				<a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>AttributeSetsList.php"><?php _e( 'Manage Attribute Sets', 'tcp-do' ); ?></a>
				<p class="description"><?php _e( 'This field helps us to join the product with its Dynamic Options', 'tcp-do' ); ?></p>
			</td>
		</tr><?php
	}

	function tcp_product_metabox_save_custom_fields( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type == TCP_DYNAMIC_OPTIONS_POST_TYPE ) return;
		$tcp_attribute_set = isset( $_POST['tcp_attribute_sets'] ) ? $_POST['tcp_attribute_sets'] : '';
		update_post_meta( $post_id, 'tcp_attribute_sets', $tcp_attribute_set );
	}

	function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, 'tcp_attribute_sets' );
	}

	function tcp_product_row_actions( $actions ) {
		global $post;
		if ( ! current_user_can( 'tcp_edit_product' ) ) return $actions;
		$type = tcp_get_the_product_type( $post->ID );
		$types = tcp_get_product_types();
		if ( ! isset( $types[$type] ) ) return $actions;
		$type = $types[$type];
		if ( isset( $type['tcp_dynamic_options_supported'] ) && $type['tcp_dynamic_options_supported'] ) {
			$admin_path = 'admin.php?page=' . plugin_basename( TCP_DYNAMIC_OPTIONS_FOLDER ) . '/admin/';
			$count = tcp_count_dynamic_options( $post->ID );
			$count = ( $count > 0 ) ? ' (' . $count . ')' : '';
			$actions['tcp_dynamic_options'] = '<a href="' . $admin_path . 'DynamicOptionsList.php&post_id=' . $post->ID . '" title="' . esc_attr( __( 'Dynamic options', 'tcp_op' ) ) . '">' . __( 'Dynamic options', 'tcp-do' ) . $count . '</a>';
		}
		return $actions;
	}

	function tcp_the_add_to_cart_unit_field( $out, $post_id ) {
		$post_id = tcp_get_default_id ( $post_id );

		remove_filter( 'tcp_get_the_price'		, array( $this, 'tcp_get_the_product_price' ), 10, 2 );
		remove_filter( 'tcp_get_the_price_label', array( $this, 'tcp_get_the_price_label' ), 10, 2 );
		if ( tcp_exists_dynamic_option( array( 'parent_id' => $post_id ) ) ) {
			global $thecartpress;
			if ( ! isset( $thecartpress ) ) return;

			//get attributes and options
			$attributes	= tcp_get_attributes_by_product( $post_id );
			$options	= tcp_get_dynamic_options( $post_id );

			if ( $thecartpress->get_setting( 'dynamic_options_calculate_price', 'complex' ) == 'complex' ) {
				$product_price = tcp_get_the_price( $post_id );
			} else {
				$product_price = 0;
			}

			ob_start();
			$dynamic_options_type = $thecartpress->get_setting( 'dynamic_options_type', 'single' );
			if ( 'list' == $dynamic_options_type ) :
				if ( isset( $_REQUEST['tcp_dynamic_option'] ) ) {
					$option_id = $_REQUEST['tcp_dynamic_option'][0];
					$_REQUEST['tcp_dynamic_option'] = array_shift( $_REQUEST['tcp_dynamic_option'] );
				} else {
					$option_id = 0;
				}
				foreach( $options as $id ) :
					if ( $option_id == 0 ) $option_id = $id; ?>
<div class="tcp_dynamic_option_panel">
	
	<div class="checkbox">
        <label for="tcp_dynamic_option_<?php echo $id; ?>" class="tcp_dynamic_option_label">
          <input type="radio" name="tcp_dynamic_option_<?php echo $post_id; ?>[]" id="tcp_dynamic_option_<?php echo $id; ?>"
			value="<?php echo $id; ?>"
			onclick="tcp_set_price_<?php echo $id; ?>(this);jQuery('.tcp_thumbnail_<?php echo $post_id; ?>').hide();jQuery('.tcp_thumbnail_option_<?php echo $id; ?>').show();"
		/>
        <?php //echo tcp_get_the_thumbnail( $id ); ?>
		<?php foreach( $attributes as $attribute ) {
			$terms = wp_get_object_terms( $id, $attribute->name );

			if ( is_array( $terms) && count( $terms ) > 0 ) {
				$term = $terms[0];
				$term = get_term( tcp_get_current_id( $term->term_id, $term->taxonomy ), $term->taxonomy );
				echo $attribute->labels->name, ': ', $term->name, ' ';
			} else {
				break;
			}
		}
		$price = tcp_get_the_price( $id ); ?>
		</label>
    </div><!-- .checkbox -->
	<script>
	function tcp_set_price_<?php echo $id; ?>( e ) {
		var	form = jQuery( e ).closest( 'form' );
		form.find( '#tcp_unit_price_<?php echo $post_id; ?>' ).html('<?php echo tcp_get_the_price_label( $post_id, $product_price + $price, true ); ?>' );
		//jQuery('#tcp_unit_price_<?php echo $post_id; ?>').html('<?php echo tcp_get_the_price_label( $post_id, $product_price + $price ); ?>');
	}
	</script>
</div><!-- .tcp_dynamic_option_panel -->
<?php endforeach; ?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#tcp_dynamic_option_<?php echo $option_id; ?>').click();
});
</script>
			<?php elseif ( 'single' == $dynamic_options_type ) : ?>
<div class="tcp_dynamic_option_panel">
	<div class="form-group">
		<?php if ( isset( $_REQUEST['tcp_dynamic_option'] ) ) {
			$option_id = $_REQUEST['tcp_dynamic_option'][0];
			$_REQUEST['tcp_dynamic_option'] = array_shift( $_REQUEST['tcp_dynamic_option'] );
		} else {
			$option_id = false;
		}
		$set_price = ''; ?>
		<select name="tcp_dynamic_option_<?php echo $post_id; ?>[]" id="tcp_dynamic_option_<?php echo $post_id; ?>" class="tcp_dynamic_options_select tcp_dynamic_options_<?php echo $post_id; ?> form-control" onchange="tcp_set_price_<?php echo $post_id; ?>(this);">
			<?php foreach( $options as $id ) : ?>
			<option value="<?php echo $id; ?>" <?php selected( $option_id, $id ); ?>>
			<?php foreach( $attributes as $attribute ) :
				$terms = wp_get_object_terms( $id, $attribute->name );

				if ( is_array( $terms) && count( $terms ) > 0 ) {
					$term = $terms[0];
					$term = get_term( tcp_get_current_id( $term->term_id, $term->taxonomy ), $term->taxonomy );
					echo $attribute->labels->name, ': ', $term->name, ' ';
				} else {
					break;
				}
			endforeach; ?>
			<?php $price = tcp_get_the_price( $id );
			$set_price .= 'if (id == ' . $id . ') { jQuery(\'#tcp_unit_price_' . $post_id . '\').html(\'' . tcp_get_the_price_label( $post_id, $product_price + $price, true ) . '\'); }' . "\n";
			endforeach; ?>
			</option>
		</select>
	</div><!-- .form-group -->
</div><!-- .tcp_dynamic_option_panel -->
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#tcp_dynamic_option_<?php echo $post_id; ?>').trigger('change');
});
function tcp_set_price_<?php echo $post_id; ?>(e) {
	//var id = jQuery('#tcp_dynamic_option_<?php echo $post_id; ?>').val();
	var	form = jQuery(e).closest('form');
	var id = form.find('#tcp_dynamic_option_<?php echo $post_id; ?>').val();
	<?php echo $set_price; ?>
	if (jQuery('.tcp_thumbnail_option_' + jQuery(e).val()).length) { 
		jQuery('.tcp_thumbnail_<?php echo $post_id; ?>').hide();
		jQuery('.tcp_thumbnail_option_' + jQuery(e).val()).show();
	}
}
</script>

			<?php elseif ( 'double' == $dynamic_options_type ) : ?>
<script>
function set_not_valid_<?php echo $post_id; ?>() {
	jQuery('#tcp_unit_price_<?php echo $post_id; ?>').html('<?php _e( 'Combination not valid', 'tcp-do' ); ?>');
	jQuery('#tcp_add_product_<?php echo $post_id; ?>').hide(); // Old version, now is "tcp_add_to_shopping_cart_""
	jQuery('#tcp_add_to_shopping_cart_<?php echo $post_id; ?>').hide();
	jQuery('#tcp_dynamic_option_<?php echo $post_id; ?>').val(0);
}

var valid_values_<?php echo $post_id; ?> = new Array();
		<?php foreach( $options as $i => $id ) :
			$valid = '';
			foreach( $attributes as $attribute ) {
				$terms = wp_get_object_terms( $id, $attribute->name );
				foreach( $terms as $term ) {
					 $valid .= $term->slug . ',';
				}
			} ?>
valid_values_<?php echo $post_id; ?>.push('<?php echo $id, ':', $valid; ?>');

			<?php $price = tcp_get_the_price( $id ); ?>

function tcp_set_price_<?php echo $id; ?>(e) {
	var	form = jQuery(e).closest('form');
	form.find('#tcp_unit_price_<?php echo $post_id; ?>').html('<?php echo tcp_get_the_price_label( $post_id, $product_price + $price, true ); ?>');
	form.find('#tcp_add_product_<?php echo $post_id; ?>').show(); // Old version, now is "tcp_add_to_shopping_cart_""
	form.find('#tcp_add_to_shopping_cart_<?php echo $post_id; ?>').show();
	form.find('#tcp_dynamic_option_<?php echo $post_id; ?>').val(<?php echo $id; ?>);
	jQuery('.tcp_thumbnail_<?php echo $post_id; ?>').hide();
	jQuery('.tcp_thumbnail_option_<?php echo $id; ?>').show();
}
		<?php endforeach; ?>

function tcp_is_valid_value_<?php echo $post_id; ?>(value) {
	for(var i in valid_values_<?php echo $post_id; ?>) {
		var item = valid_values_<?php echo $post_id; ?>[i].split(':');
		if (item[1] == value ) return item[0];
	}
	return -1;
}

jQuery(document).ready(function() {
	jQuery('.tcp_dynamic_options_<?php echo $post_id; ?>').change(function () {
		var value = '';
<?php foreach( $attributes as $attribute ) : ?>
		var id = '#tcp_dynamic_option_<?php echo $attribute->name; ?>_<?php echo $post_id; ?>';
		value += jQuery(id).val() + ',';
<?php endforeach; ?>
		var option_id = tcp_is_valid_value_<?php echo $post_id; ?>(value);
		if ( option_id > -1 ) {
			eval('tcp_set_price_' + option_id + '(this);');
		} else {
			set_not_valid_<?php echo $post_id; ?>();
		}
	});
	jQuery('.tcp_dynamic_options_<?php echo $post_id; ?>').trigger('change');//TODO with only one would be enough
});
</script>
<input type="hidden" name="tcp_dynamic_option_<?php echo $post_id; ?>[]" id="tcp_dynamic_option_<?php echo $post_id; ?>"/>
<?php foreach( $attributes as $attribute ) : ?>
	<div class="tcp_dynamic_option_panel">
		<div class="form-group">
		<label><?php echo $attribute->labels->name; ?>:
		<?php $terms = array();
		foreach( $options as $id ) {
			//$terms = array_merge( wp_get_object_terms( $id, $attribute->name ), $terms );
			$terms_to_add = wp_get_object_terms( $id, $attribute->name );
			foreach( $terms_to_add as $term_to_add ) {
				$term_id = tcp_get_current_id( $term_to_add->term_id, $attribute->name );
				if ( $term_id != $term_to_add->term_id ) {
					$translated_term_to_add = get_term( $term_id, $attribute->name );
					$term_to_add->name = $translated_term_to_add->name;
				}
				if ( ! isset( $terms[$term_to_add->term_id] ) ) $terms[$term_to_add->term_id] = $term_to_add;
			}
		} 
		$default_post_id = tcp_get_default_id( $post_id ); ?>
		</label>
		<select name="tcp_dynamic_option_<?php echo $default_post_id; ?>_<?php echo $attribute->name; ?>[]"
			id="tcp_dynamic_option_<?php echo $attribute->name; ?>_<?php echo $default_post_id; ?>"
			class="tcp_dynamic_options_select tcp_dynamic_options_<?php echo $default_post_id; ?> form-control form-control">
		<?php foreach( $terms as $term ) : ?>
			<option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
		<?php endforeach; ?>
		</select>
		</div><!-- .form-group -->
	</div><!-- .tcp_dynamic_option_panel -->
<?php endforeach;
endif;
	global $thecartpress;
	$suffix = '';
	$dynamic_options_order_by = $thecartpress->get_setting( 'dynamic_options_order_by' . $suffix, 'title' );
	if ( $dynamic_options_order_by == 'title' ) { 
		$dynamic_options_order = $thecartpress->get_setting( 'dynamic_options_order' . $suffix, 'ASC' ); ?>
<script>
jQuery( function() {
	<?php if ( $dynamic_options_order == 'ASC' ) { ?>
		tcp_sortDropDownListByTextAsc();
	<?php } else { ?>
		tcp_sortDropDownListByTextDsc();
	<?php } ?>
} );
</script>
			<?php }
			$out .= ob_get_clean();
		}
		add_filter( 'tcp_get_the_price_label'	, array( $this, 'tcp_get_the_price_label' ), 10, 2 );
		add_filter( 'tcp_get_the_price'			, array( $this, 'tcp_get_the_product_price' ), 10, 2 );
		return $out;
	}

	function tcp_the_add_to_cart_items_in_the_cart( $out, $post_id ) {
		$options = tcp_get_dynamic_options( $post_id );
		if ( is_array( $options ) && count ( $options ) > 0 ) {
			$total = 0;
			$shopingCart = TheCartPress::getShoppingCart();
			foreach( $options as $id ) {
				$item = $shopingCart->getItem( $id );
				if ( $item ) $total += $item->getUnits();
			}
			if ( $total > 0 ) { 
				//$out = '<span class="tcp_added_product_title tcp_added_product_title_' . $post_id . '">' . sprintf ( __( '%s unit(s) <a href="%s">in your cart</a>', 'tcp-do' ), $total, tcp_get_the_shopping_cart_url() ) . '</span>';
				ob_start();?>
				<div class="tcp_added_product_title tcp_added_product_title_<?php echo $post_id; ?> alert alert-success alert-dismissable">
					<?php printf ( __( '<span class="tcp_units">%s</span> <a href="%s" class="alert-link">in your cart</a>', 'tcp-do' ), $total, tcp_get_the_shopping_cart_url() ); ?>
				</div>
				<?php $out = ob_get_clean();
			}
		}
		return $out;
	}

	function tcp_get_the_tax_id( $tax_id, $post_id ) {
		if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $post_id ) ) {
			$post = get_post( $post_id );
			return tcp_get_the_tax_id( $post->post_parent );
		}
		return $tax_id;
	}

	function tcp_add_item_shopping_cart( $args ) {
		extract( $args ); //$i, $post_id, $count, $unit_price, $unit_weight
		remove_filter( 'tcp_get_the_price', array( $this, 'tcp_get_the_product_price' ), 10, 2 );
		
		if ( !isset( $_REQUEST['tcp_dynamic_option_' . $post_id][$i] ) ) {
			if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $post_id ) ) {
				$dynamic_option_id	= $post_id;
				$post_id		= tcp_get_parent_from_dynamic_option( $post_id );
				$unit_price		= tcp_get_the_price( $post_id );
				$unit_weight	= tcp_get_the_weight( $post_id );
			} else {
				return $args;
			}
		} else {
			$dynamic_option_id = $_REQUEST['tcp_dynamic_option_' . $post_id][$i];
		}

		global $thecartpress;
		if ( 'complex' == $thecartpress->get_setting( 'dynamic_options_calculate_price', 'complex' ) ) {
			$unit_price	+= tcp_get_the_price( $dynamic_option_id );
		} else {
			$unit_price	= tcp_get_the_price( $dynamic_option_id );
		}
		$dynamic_weight	= tcp_get_the_weight( $dynamic_option_id );
		if ( $dynamic_weight > 0 ) $unit_weight = $dynamic_weight;//not add to the original weight
		$post_id = $dynamic_option_id;
		$args = compact( 'i', 'post_id', 'count', 'unit_price', 'unit_weight' );
		add_filter( 'tcp_get_the_price', array( $this, 'tcp_get_the_product_price' ), 10, 2 );
		return $args;
	}

	/**
	 * If a product with dynamic options is added to the wish list,
	 * added the dynamic option, not the product
	 */
	function tcp_add_wish_list( $post_id, $i ) {
		if ( isset( $_REQUEST['tcp_dynamic_option_' . $post_id][$i] ) ) {
			$post_id = $_REQUEST['tcp_dynamic_option_' . $post_id][$i];
			return tcp_get_default_id( $post_id, get_post_type( $post_id ) );
		} else {
			return $post_id;
		}
	}

	function tcp_get_discount_by_product( $discounts_by_product, $discounts, $product_id ) {
		if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $product_id ) ) {
			$post_id = tcp_get_parent_from_dynamic_option( $product_id );
			if ( $post_id !== false && is_array( $discounts ) && count( $discounts ) > 0 ) {
				foreach( $discounts as $discount_item ) {
					$active = isset( $discount_item['active'] ) ? $discount_item['active'] : false;
					if ( $active && $discount_item['product_id'] == $post_id ) {
						$discounts_by_product[] = $discount_item;
					}
				}
			}
		}
		return $discounts_by_product;
	}

	function tcp_get_discount_by_coupon_by_product( $discounts, $coupon, $product_id ) {
		if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $product_id ) ) {
			$post_id = tcp_get_parent_from_dynamic_option( $product_id );
			if ( $coupon['product_id'] == $post_id ) {
				$discounts[] = array(
					'type'	=> $coupon['coupon_type'],
					'value'	=> $coupon['coupon_value'],
				);
			}
		}
		return $discounts;
	}

	function tcp_get_the_title( $title, $post_id, $html, $show_parent ) {
		if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $post_id ) ) {
			$parent_id	= tcp_get_parent_from_dynamic_option( $post_id );
			$title		= get_the_title( $parent_id );
			$attributes	= tcp_get_attributes( 'objects' );
			foreach( $attributes as $attribute ) {
				$terms	= wp_get_object_terms( $post_id, $attribute->name ); 
				$term	= isset( $terms[0]->name ) ? $terms[0]->name : false;
				if ( $term !== false ) $title .= ', ' . $attribute->labels->name . ': ' . $term;
				//else break;
			}
			if ( $html ) $title = '<span class="tcp_nested_title">' . $title . '</span>';
		}
		return $title;
	}

	function tcp_get_the_sku( $sku, $post_id ) {
		if ( $sku == '' && get_post_type( $post_id ) == TCP_DYNAMIC_OPTIONS_POST_TYPE )
			return tcp_get_the_sku( tcp_get_parent_from_dynamic_option( $post_id ) );
		return $sku;
	}

	function tcp_get_permalink( $url, $post_id ) {
		if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $post_id ) ) {
			$parent_id = tcp_get_parent_from_dynamic_option( $post_id );
			$url = get_permalink( $parent_id );
		}
		return $url;
	}

	function post_type_link( $post_link, $post, $leavename, $sample ) {
		return $this->tcp_get_permalink( $post_link, $post->ID );
	}

	function tcp_get_the_thumbnail( $image, $post_id, $size ) {
		if ( strlen( $image ) == 0 ) {
			if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $post_id ) ) {
				$parent_id	= tcp_get_parent_from_dynamic_option( $post_id );
				//$image	= tcp_get_the_thumbnail( $image, $parent_id, $size );
				$image		= tcp_get_the_thumbnail( $parent_id, 0, 0, $size );
				//$image	= str_replace( 'tcp_image_' . $parent_id, 'tcp_image_' . $post_id, $image );
				return $image;
			}
		}
		return $image;
	}

	/**
	 * Check if post has an image attached.
	 *
	 * @since 1.3.4
	 *
	 * @param int $post_id Optional. Post ID.
	 * @return bool Whether post has an image attached.
	 *
	 * @uses has_post_thumbnail, apply_filters (tcp_has_post_thumbnail)
	 */
	function tcp_has_post_thumbnail( $has, $post_id = null ) {
		if ( !$has ) {
			$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
			$parent_id = tcp_get_parent_from_dynamic_option( $post_id );
			$has = has_post_thumbnail( $parent_id );
		}
		return $has;
	}

	function tcp_get_image_in_grouped( $image, $post_id, $args = false  ) {
		global $thecartpress;
		$args['size'] = $thecartpress->get_setting( 'image_size_grouped_by_button', 'thumbnail' );
		return $this->tcp_get_image_in_content( $image, $post_id, $args );
	}
	
	function tcp_get_image_in_content( $image, $post_id, $args = false  ) {
		$options = tcp_get_dynamic_options( $post_id );
		if ( is_array( $options ) && count( $options ) > 0 ) {
			$image = '';
			foreach( $options as $id ) {
				if ( has_post_thumbnail( $id ) ) {
					$image .= $this->get_thumbnail_link( $id, $args, $post_id, $id );
				} else {
					$option_id = tcp_get_default_id( $id, TCP_DYNAMIC_OPTIONS_POST_TYPE );
					if ( has_post_thumbnail( $option_id ) ) {
						$image .= $this->get_thumbnail_link( $option_id, $args, $post_id, $id );
					} elseif ( has_post_thumbnail( $post_id ) ) {
						$image .= $this->get_thumbnail_link( $post_id, $args, $post_id, $id );
					} else {
						$default_post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
						$image .= $this->get_thumbnail_link( $default_post_id, $args, $post_id, $id );
					}
				}
			}
		}
		return $image;
	}

	private function get_thumbnail_link( $post_id, $args, $parent_id, $option_id ) {
		/*$class = 'class="tcp_thumbnail_' . $parent_id . ' tcp_thumbnail_option_' . $option_id . '"';
		$image = tcp_get_the_thumbnail_image( $post_id, $args );
		$link = '<a '. $class . ' style="display:none;">' . $image . '</a>';
		return $link;*/
		
		$class = 'class="tcp_thumbnail_' . $parent_id . ' tcp_thumbnail_option_' . $option_id . '"';
		//$image = tcp_get_the_thumbnail_with_permalink( $post_id, $args, false );

		$image = tcp_get_the_thumbnail_image( $post_id, $args, false );
		if ( $post_id == $parent_id ) {
			$image = str_replace( 'tcp_image_' . $parent_id, 'tcp_image_' . $option_id, $image );
		}
		$link = '<span '. $class . ' style="display:none;">' . $image . '</span>';
		return $link;

	}

	function tcp_get_the_product_price( $price, $post_id ) {
//var_dump($post_id);
//var_dump(get_post_type( $post_id ));
		if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $post_id ) ) {
			$parent_post_id = tcp_get_parent_from_dynamic_option( $post_id );
			return $price + tcp_get_the_price( $parent_post_id );
		} else {
			return $price;
		}
	}

	function tcp_exclude_from_order_discount( $discount_exclude, $post_id ) {
		if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $post_id ) ) {
			$parent_post_id = tcp_get_parent_from_dynamic_option( $post_id );
			return tcp_exclude_from_order_discount( $parent_post_id );
		} else {
			return $discount_exclude;
		}
	}
	/*function tcp_get_the_product_weight( $weight, $post_id ) {
		if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $post_id ) ) {
			$dynamic_option_id	= $post_id;
			$post_id = tcp_get_parent_from_dynamic_option( $dynamic_option_id );
			return $weight + tcp_get_the_weight( $post_id );
		}
	}*/
	function tcp_get_product_post_types( $post_types ) {
		if ( !is_array( $post_types ) ) $post_types = array( $post_types );
		$i = array_search( TCP_DYNAMIC_OPTIONS_POST_TYPE, $post_types );
		if ( $i !== false ) unset( $post_types[$i] );
		return $post_types;
	}

	function tcp_get_the_price_label( $label, $post_id, $price = false ) {
		remove_filter( 'tcp_get_the_price', array( $this, 'tcp_get_the_product_price' ), 10, 2 );
		if ( TCP_DYNAMIC_OPTIONS_POST_TYPE == get_post_type( $post_id ) ) {
			$parent_id = tcp_get_parent_from_dynamic_option( $post_id );

			global $thecartpress;
			if ( 'complex' == $thecartpress->get_setting( 'dynamic_options_calculate_price', 'complex' ) ) {
				if ( $price === false ) {
					$price = tcp_get_the_price( $post_id );
					$price += tcp_get_the_price( $parent_id );
				}
			}
			add_filter( 'tcp_get_the_price', array( $this, 'tcp_get_the_product_price' ), 10, 2 );
			return tcp_get_the_price_label( $parent_id, $price, false );
		}

		if ( 'SIMPLE' != tcp_get_the_product_type( $post_id ) ) return $label;

		$options = tcp_get_dynamic_options( $post_id );
		if ( is_array( $options ) && count( $options ) ) {
			$min_price = 999999999;
			$equal = null;
			foreach( $options as $option ) {
				$price = tcp_get_the_price( $option );
				if ( $equal == null ) {
					$equal = $price;
				} elseif ( $equal !== false ) {
					if ( $equal != $price ) $equal = false;
				}
				if ( $price < $min_price ) $min_price = $price;
			}
			global $thecartpress;
			if ( 'complex' == $thecartpress->get_setting( 'dynamic_options_calculate_price', 'complex' ) ) {
				$price = $min_price + tcp_get_the_price( $post_id );
			} else {
				$price = $min_price;
			}
		} else {
			return $label;
			//$price = false;
			//$equal = true;
		}
		$price = tcp_get_the_price_to_show( $post_id, $price );

		remove_filter( 'tcp_get_the_price_label', array( $this, 'tcp_get_the_price_label' ), 10, 2 );
		$label = tcp_get_the_price_label( '', $price );
		add_filter( 'tcp_get_the_price_label', array( $this, 'tcp_get_the_price_label' ), 10, 2 );

		add_filter( 'tcp_get_the_price', array( $this, 'tcp_get_the_product_price' ), 10, 2 );

		if ( $equal !== false ) {
			return $label;
		} else {
			return sprintf( __( 'From %s', 'tcp-do' ), $label );
		}
	}

	function tcp_get_product_types( $types ) {
		$types['SIMPLE']['tcp_dynamic_options_supported'] = true;
		return $types;
	}
	
	//Layered Navigation
	function tcp_the_layered_navigation_get_posts_query( $query, $post_type, $taxonomy, $term_slug ) {
		if ( $post_type == 'tcp_dynamic_options' ) {
			$posts_ids = array( );
			unset( $query[$taxonomy] );
			unset( $query['paged'] );
			$products = get_posts( $query );
			$args = array(
				'post_type' => 'tcp_dynamic_options',
				'posts_per_page' => -1,
				'fields' => 'ids',
				$taxonomy => $term_slug,
			);
			foreach( $products as $product_id ) {
				$args['post_parent'] = $product_id;
				$posts = get_posts( $args );
				if ( count( $posts ) > 0 ) $posts_ids[] = $product_id;
			}
			$query['post__in'] = $posts_ids;
		}
		return $query;
	}

	function tcp_the_layered_navigation_link( $link, $post_type, $taxonomy, $term_slug ) {
		if ( $post_type == 'tcp_dynamic_options' ) {
			$link = remove_query_arg( 'tcp_filter_' . $taxonomy, $link );
			$link = add_query_arg( 'tcp_dynamic_filter_' . $taxonomy, $term_slug, $link );
		}
		return $link;
	}

	function tcp_filter_navigation_get_filters_request( $filters ) {
		foreach( $_REQUEST as $key => $value )
			if ( $pos = strpos( $key, 'tcp_dynamic_filter' ) === 0 ) {
				$taxonomy = substr( $key, $pos + 18 );
				$args = array(
					'post_type' => 'tcp_dynamic_options',
					'posts_per_page' => -1,
					'fields' => 'ids',
					$taxonomy => $value
				);
				$posts = get_posts( $args );
				global $wpdb;
				$sql = 'select post_parent from ' . $wpdb->posts . ' where ID in ( ';
				$sql .= $wpdb->prepare( str_repeat( '%d, ', count( $posts ) -1 ) . '%d )' , $posts );
				//$sql .= $wpdb->prepare( ' id in (' . implode( ',', $posts ) . ')' );
				$res = $wpdb->get_results( $sql );
				if ( is_array( $res ) && count( $res ) ) {
					$filter = array(
						'type' => 'dynamic_options',
						'taxonomy' => $taxonomy,
						'term' => $value,
						'post__in' => array(),
					);
					foreach( $res as $row ) $filter['post__in'][] = $row->post_parent;
					$filters[] = $filter;
				}
			}
		return $filters;
	}
	
	function tcp_filter_navigation_get_filter( $filter, $f ) {
		if ( isset( $f['type'] ) && 'dynamic_options' == $f['type'] ) { //dynamic options
			$filter['layered']['dynamic_options'][] = array(
				'type' => 'dynamic_options',
				'taxonomy' => $f['taxonomy'],
				'term' => $f['term'],
				'post__in' => $f['post__in'],
			);
			return $filter;
		}
	}
}

$tcp_dynamic_options = new TCPDynamicOptions();
endif; // class_exists check
