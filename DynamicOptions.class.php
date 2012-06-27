<?php
/*
Plugin Name: TheCartPress Dynamic Options
Plugin URI: http://extend.thecartpress.com/ecommerce-plugins/dynamic-options/
Description: Adds Dynamic Options to TheCartPress
Version: 1.0.5
Author: TheCartPress team
Author URI: http://thecartpress.com
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

define( 'TCP_DYNAMIC_OPTIONS_FOLDER'			, dirname( __FILE__) . '/' );
define( 'TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER'		, TCP_DYNAMIC_OPTIONS_FOLDER . 'admin/' );
define( 'TCP_DYNAMIC_OPTIONS_CLASSES_FOLDER'	, TCP_DYNAMIC_OPTIONS_FOLDER . 'classes/' );
define( 'TCP_DYNAMIC_OPTIONS_TEMPLATES_FOLDER'	, TCP_DYNAMIC_OPTIONS_FOLDER . 'templates/' );
define( 'TCP_DYNAMIC_OPTIONS_POST_TYPE_FOLDER'	, TCP_DYNAMIC_OPTIONS_FOLDER . 'customposttypes/' );
define( 'TCP_DYNAMIC_OPTIONS_METABOXES_FOLDER'	, TCP_DYNAMIC_OPTIONS_FOLDER . 'metaboxes/' );

define( 'TCP_DYNAMIC_OPTIONS_POST_TYPE'			, 'tcp_dynamic_options' );
define( 'TCP_DYNAMIC_OPTIONS_ADMIN_PATH'		, 'admin.php?page=' . plugin_basename( TCP_DYNAMIC_OPTIONS_FOLDER ) . '/admin/' );

require_once( TCP_DYNAMIC_OPTIONS_TEMPLATES_FOLDER . 'tcp_dynamic_options_templates.php' );

class TCPDynamicOptions {

	function init() {
		if ( ! function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'thecartpress/TheCartPress.class.php' ) )  {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		} else {
			require_once( TCP_DYNAMIC_OPTIONS_METABOXES_FOLDER . 'DynamicOptionsCustomFieldsMetabox.class.php' );
			require_once( TCP_DYNAMIC_OPTIONS_METABOXES_FOLDER . 'DynamicOptionsMetabox.class.php' );
		}
	}

	function admin_notices() { ?>
		<div class="updated"><p><?php _e( '<strong>Dynamic Options for TheCartPress</strong> requires TheCartPress plugin activated.', 'tcp-do' ); ?></p></div>
	<?php }

	function activate_plugin() {
		if ( ! function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'thecartpress/TheCartPress.class.php' ) ) exit( __( '<strong>Dynamic Options for TheCartPress</strong> requires TheCartPress plugin', 'tcp-do' ) );
		TCPDynamicOptionPostType::create_default_custom_post_type_and_taxonomies();
		$ids = get_posts( array(
			'post_type'		=> TCP_DYNAMIC_OPTIONS_POST_TYPE,
			'numberposts'	=> -1,
			'fields'		=> 'ids',
		) );
		foreach( $ids as $id ) {
			$order = get_post_meta( $id, 'tcp_order', true );
			if ( $order == '' ) update_post_meta( $id, 'tcp_order', 0 );
		}//Deprecated
	}

	function admin_init() {
		$file = TCP_ADMIN_FOLDER . 'Settings.class.php';
		add_settings_field( 'dynamic_options_type', __( 'Dynamic options type', 'tcp-do' ), array( $this, 'show_dynamic_options_type' ), $file, 'tcp_theme_compatibility_section' );
		add_settings_field( 'dynamic_options_order_by', __( 'Dynamic options order by', 'tcp-do' ), array( $this, 'show_dynamic_options_order_by' ), $file, 'tcp_theme_compatibility_section' );
		add_settings_field( 'dynamic_options_order_asc', __( 'Dynamic options order', 'tcp-do' ), array( $this, 'show_dynamic_options_order' ), $file, 'tcp_theme_compatibility_section' );
	}

	function admin_menu() {
		$base = 'edit.php?post_type=' . TCP_PRODUCT_POST_TYPE;
		add_submenu_page( $base, __( 'Attribute Sets', 'tcp-do' ), __( 'Attributes Sets', 'tcp-do' ), 'tcp_edit_products', TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER . 'AttributeSetsList.php' );
		add_submenu_page( $base, __( 'Attributes', 'tcp-do' ), __( 'Attributes', 'tcp-do' ), 'tcp_edit_products', TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER . 'AttributeList.php' );

		add_submenu_page( 'tcpatt', __( 'Option list', 'tcp-do' ), __( 'Option list', 'tcp-do' ), 'tcp_edit_products', TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER . 'AttributeSetEdit.php' );
		add_submenu_page( 'tcpatt', __( 'Option list', 'tcp-do' ), __( 'Option list', 'tcp-do' ), 'tcp_edit_products', TCP_DYNAMIC_OPTIONS_ADMIN_FOLDER . 'DynamicOptionsList.php' );
	}

	function show_dynamic_options_type() {
		global $thecartpress;
		if ( ! isset( $thecartpress ) ) return;
		$dynamic_options_type = $thecartpress->get_setting( 'dynamic_options_type', 'radio' ); ?>
		<select id="dynamic_options_type" name="tcp_settings[dynamic_options_type]">
			<option value="list" <?php selected( $dynamic_options_type, 'list' ); ?>><?php _e( 'List', 'tcp-do' ); ?></option>
			<option value="single" <?php selected( $dynamic_options_type, 'single' ); ?>><?php _e( 'Single', 'tcp-do' ); ?></option>
			<option value="double" <?php selected( $dynamic_options_type, 'double' ); ?>><?php _e( 'Multiple', 'tcp-do' ); ?></option>
		</select><?php
	}

	function show_dynamic_options_order_by() {
		global $thecartpress;
		if ( ! isset( $thecartpress ) ) return;
		$dynamic_options_order_by = $thecartpress->get_setting( 'dynamic_options_order_by', 'title' ); ?>
		<input type="radio" id="dynamic_options_order_by_order" name="tcp_settings[dynamic_options_order_by]" value="order" <?php checked( 'order', $dynamic_options_order_by ); ?> /> <?php _e( 'Order field', 'tcp-do' ); ?><br/>
		<input type="radio" id="dynamic_options_order_by_title" name="tcp_settings[dynamic_options_order_by]" value="title" <?php checked( 'title', $dynamic_options_order_by ); ?> /> <?php _e( 'Title', 'tcp-do' ); ?><br/>
		<input type="radio" id="dynamic_options_order_by_price" name="tcp_settings[dynamic_options_order_by]" value="price" <?php checked( 'price', $dynamic_options_order_by ); ?> /> <?php _e( 'Price', 'tcp-do' ); ?>
		<?php
	}

	function show_dynamic_options_order() {
		global $thecartpress;
		if ( ! isset( $thecartpress ) ) return;
		$dynamic_options_order = $thecartpress->get_setting( 'dynamic_options_order', 'asc' ); ?>
		<input type="radio" id="dynamic_options_order_asc" name="tcp_settings[dynamic_options_order]" value="asc" <?php checked( 'asc', $dynamic_options_order ); ?> /> <?php _e( 'Ascending', 'tcp-do' ); ?><br/>
		<input type="radio" id="dynamic_options_order_desc" name="tcp_settings[dynamic_options_order]" value="desc" <?php checked( 'desc', $dynamic_options_order ); ?> /> <?php _e( 'Descending', 'tcp-do' ); ?>
		<?php
	}

	function tcp_product_metabox_toolbar( $post_id ) {
		if ( tcp_get_the_product_type( $post_id ) == 'SIMPLE' && current_user_can( 'tcp_edit_products' ) ) {
			echo '<li>|</li>';
			$count = tcp_count_dynamic_options( $post_id );
			$count = ( $count > 0 ) ? ' (' . $count . ')' : '';
			$admin_path = 'admin.php?page=' . plugin_basename( TCP_DYNAMIC_OPTIONS_FOLDER ) . '/admin/';
			echo '<li><a href="', $admin_path, 'DynamicOptionsList.php&post_id=', $post_id, '">', __( 'dynamic options', 'tcp-do' ), $count, '</a></li>';
		}
	}

	function tcp_product_metabox_custom_fields( $post_id ) { 
		$tcp_attribute_sets = get_post_meta( $post_id, 'tcp_attribute_sets', true ); ?>
		<tr valign="top">
			<th scope="row"><label for="tcp_attribute_sets"><?php _e( 'Attribute Sets', 'tcp-do' ); ?>:</label></th>
			<td>
				<select name="tcp_attribute_sets[]" id="tcp_attribute_sets">
					<option value=""><?php _e( 'none', 'tcp-do' ); ?></option>
				<?php $attribute_sets = get_option( 'tcp_attribute_sets', array() );
				foreach( $attribute_sets as $id => $attribute_set ) : ?>
					<option value="<?php echo $id; ?>" <?php tcp_selected_multiple( $tcp_attribute_sets, $id ); ?>><?php echo $attribute_set['title']; ?></option>
				<?php endforeach; ?>
				</select>
				<a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>AttributeSetsList.php"><?php _e( 'Manage Attribute Sets', 'tcp-do' ); ?></a>
			</td>
		</tr><?php
	}

	function tcp_product_metabox_save_custom_fields( $post_id ) {
		$tcp_attribute_set = isset( $_POST['tcp_attribute_sets'] ) ? $_POST['tcp_attribute_sets'] : '';
		update_post_meta( $post_id, 'tcp_attribute_sets', $tcp_attribute_set );
	}

	function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, 'tcp_attribute_sets' );
	}

	function product_row_actions( $actions ) {
		global $post;
		if ( $post->post_type == 'tcp_product' && tcp_get_the_product_type( $post->ID ) == 'SIMPLE' && current_user_can( 'tcp_edit_products' ) ) {
			$admin_path = 'admin.php?page=' . plugin_basename( TCP_DYNAMIC_OPTIONS_FOLDER ) . '/admin/';
			$count = tcp_count_dynamic_options( $post->ID );
			$count = ( $count > 0 ) ? ' (' . $count . ')' : '';
			$actions['tcp_dynamic_options'] = '<a href="' . $admin_path . 'DynamicOptionsList.php&post_id=' . $post->ID . '" title="' . esc_attr( __( 'dynamic options', 'tcp_op' ) ) . '">' . __( 'dynamic options', 'tcp-do' ) . $count . '</a>';
		}
		return $actions;
	}

	function tcp_the_add_to_cart_unit_field( $out, $post_id ) {
		if ( tcp_exists_dynamic_option( array( 'parent_id' => $post_id ) ) ) {
			ob_start();
			global $thecartpress;
			if ( ! isset( $thecartpress ) ) return;
			$dynamic_options_type	= $thecartpress->get_setting( 'dynamic_options_type', 'list' );
			$attributes				= tcp_get_attributes_by_product( $post_id );
			$options				= tcp_get_dynamic_options( $post_id, true );
			$product_price			= tcp_get_the_price( $post_id );
			if ( 'list' == $dynamic_options_type ) {
				if ( isset( $_REQUEST['tcp_dynamic_option'] ) ) {
					$option_id = $_REQUEST['tcp_dynamic_option'][0];
					$_REQUEST['tcp_dynamic_option'] = array_shift( $_REQUEST['tcp_dynamic_option'] );
				} else {
					$option_id = 0;
				}
				foreach( $options as $id ) {
					if ( $option_id == 0 ) $option_id = $id; ?>
					<div class="tcp_dynamic_option_panel">
					<input type="radio" name="tcp_dynamic_option_<?php echo $post_id; ?>[]" id="tcp_dynamic_option_<?php echo $id; ?>" value="<?php echo $id; ?>"
					onclick="tcp_set_price_<?php echo $id; ?>();jQuery('.tcp_thumbnail_<?php echo $post_id; ?>').hide();jQuery('.tcp_thumbnail_option_<?php echo $id; ?>').show();" />
					<label for="tcp_dynamic_option_<?php echo $id; ?>" class="tcp_dynamic_option_label">
					<?php echo tcp_get_the_thumbnail( $id ); ?>
					<?php foreach( $attributes as $attribute ) {
						$terms = wp_get_object_terms( $id, $attribute->name );
						if ( is_array( $terms) && count( $terms ) > 0 ) {
							$terms = $terms[0];
							echo $attribute->labels->name, ': ', $terms->name, ' ';
						} else {
							break;
						}
					}
					$price = tcp_get_the_price( $id ); ?>
					</label>
					<script>
					function tcp_set_price_<?php echo $id; ?>() {
						jQuery('#tcp_unit_price_<?php echo $post_id; ?>').html('<?php echo tcp_get_the_price_label( $post_id, $product_price + $price ); ?>');
					}
					</script>
					</div>
				<?php } ?>
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#tcp_dynamic_option_<?php echo $option_id; ?>').click();
				});
				</script><?php
			} elseif ( 'single' == $dynamic_options_type ) { ?>
				<div class="tcp_dynamic_option_panel">
				<label><?php _e( 'Options', 'tcp-do' ); ?>
				<select name="tcp_dynamic_option_<?php echo $post_id; ?>[]" id="tcp_dynamic_option_<?php echo $post_id; ?>" onchange="tcp_set_price_<?php echo $post_id; ?>(this);">
				<?php if ( isset( $_REQUEST['tcp_dynamic_option'] ) ) {
					$option_id = $_REQUEST['tcp_dynamic_option'][0];
					$_REQUEST['tcp_dynamic_option'] = array_shift( $_REQUEST['tcp_dynamic_option'] );
				} else {
					$option_id = false;
				}
				$set_price = '';
				foreach( $options as $id ) : ?>
					<option value="<?php echo $id; ?>" <?php selected( $option_id, $id ); ?>>
					<?php foreach( $attributes as $attribute ) :
						$terms = wp_get_object_terms( $id, $attribute->name );
						if ( is_array( $terms) && count( $terms ) > 0 ) {
							$terms = $terms[0];
							echo $attribute->labels->name, ': ', $terms->name, ' ';
						} else {
							break;
						}
					endforeach; ?>
					<?php $price = tcp_get_the_price( $id );
					$set_price .= 'if (id == ' . $id . ') { jQuery(\'#tcp_unit_price_' . $post_id . '\').html(\'' . tcp_get_the_price_label( $post_id, $product_price + $price ) . '\'); }' . "\n";
				endforeach; ?>
				</option>
				</select>
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#tcp_dynamic_option_<?php echo $post_id; ?>').trigger('change');
				});
				function tcp_set_price_<?php echo $post_id; ?>(e) {
					var id = jQuery('#tcp_dynamic_option_<?php echo $post_id; ?>').val();
					<?php echo $set_price; ?>
					if (jQuery('.tcp_thumbnail_option_' + jQuery(e).val()).length) { 
						jQuery('.tcp_thumbnail_<?php echo $post_id; ?>').hide();
						jQuery('.tcp_thumbnail_option_' + jQuery(e).val()).show();
					}
				}
				</script>
				</label>
				</div>
			<?php } elseif ( 'double' == $dynamic_options_type ) { ?>
<script>
function set_not_valid_<?php echo $post_id; ?>() {
	jQuery('#tcp_unit_price_<?php echo $post_id; ?>').html('<?php _e( 'Combination not valid', 'tcp-do' ); ?>');
	jQuery('#tcp_add_product_<?php echo $post_id; ?>').hide();
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
function tcp_set_price_<?php echo $id; ?>(e) {
	<?php $price = tcp_get_the_price( $id ); ?>
	jQuery('#tcp_unit_price_<?php echo $post_id; ?>').html('<?php echo tcp_get_the_price_label( $post_id, $product_price + $price ); ?>');
	jQuery('#tcp_add_product_<?php echo $post_id; ?>').show();
	jQuery('#tcp_dynamic_option_<?php echo $post_id; ?>').val(<?php echo $id; ?>);
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
	jQuery('.tcp_dynamic_options_<?php echo $post_id; ?>').trigger('change');//with only one would be enough TODO
});
</script>
				<input type="hidden" name="tcp_dynamic_option_<?php echo $post_id; ?>[]" id="tcp_dynamic_option_<?php echo $post_id; ?>"/>
				<?php foreach( $attributes as $attribute ) : ?>
					<div class="tcp_dynamic_option_panel">
					<label><?php echo $attribute->labels->name; ?>:
					<?php $terms = array();
					foreach( $options as $id ) {
						//$terms = array_merge( wp_get_object_terms( $id, $attribute->name ), $terms );
						$terms_to_add = wp_get_object_terms( $id, $attribute->name );
						foreach( $terms_to_add as $term_to_add ) {
							$term_id = $term_to_add->term_id;
							if ( ! isset( $terms[$term_id] ) ) $terms[$term_id] = $term_to_add;
						}
					} ?>
					<select name="tcp_dynamic_option_<?php echo $post_id; ?>_<?php echo $attribute->name; ?>[]" id="tcp_dynamic_option_<?php echo $attribute->name; ?>_<?php echo $post_id; ?>" class="tcp_dynamic_options_<?php echo $post_id; ?>">
					<?php foreach( $terms as $term ) : ?>
						<option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
					<?php endforeach; ?>
					</select>
					</label>
					</div>
				<?php endforeach;
			}
			$out .= ob_get_clean();
		}
		return $out;
	}

	function tcp_the_add_to_cart_items_in_the_cart( $out, $post_id ) {
		if ( strlen( $out ) == 0 ) {
			$total = 0;
			$shopingCart = TheCartPress::getShoppingCart();
			$options = tcp_get_dynamic_options( $post_id, true );
			foreach( $options as $id ) {
				$item = $shopingCart->getItem( $id );
				if ( $item ) $total += $item->getUnits();
			}
			if ( $total > 0 )
				$out = '<span class="tcp_added_product_title">' . sprintf ( __( '%s unit(s) <a href="%s">in your cart</a>', 'tcp' ), $total, tcp_get_the_shopping_cart_url() ) . '</span>';
		}
		return $out;
	}

	function tcp_get_the_tax_id( $tax_id, $post_id ) {
		if ( get_post_type( $post_id ) == TCP_DYNAMIC_OPTIONS_POST_TYPE ) {
			$post = get_post( $post_id );
			return tcp_get_the_tax_id( $post->post_parent );
		}
		return $tax_id;
	}

	function tcp_add_item_shopping_cart( $args ) { 
		extract( $args ); //$i, $post_id, $count, $unit_price, $unit_weight
		if ( isset( $_REQUEST['tcp_dynamic_option_' . $post_id][$i] ) ) {
			$dynamic_option_id	= $_REQUEST['tcp_dynamic_option_' . $post_id][$i];
			$unit_price 		+= tcp_get_the_price( $dynamic_option_id );
			$dynamic_weight	= tcp_get_the_weight( $dynamic_option_id );
			if ( $dynamic_weight > 0 ) $unit_weight = $dynamic_weight;//not add to the original weight
			$post_id			= $dynamic_option_id;
		}
		$args = compact( 'i', 'post_id', 'count', 'unit_price', 'unit_weight' );
		return $args;
	}

	function tcp_get_discount_by_product( $discounts_by_product, $discounts, $product_id ) {
		if ( get_post_type( $product_id ) == TCP_DYNAMIC_OPTIONS_POST_TYPE ) {
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

	function tcp_get_the_title( $title, $post_id, $html, $show_parent ) {
		if ( get_post_type( $post_id ) == TCP_DYNAMIC_OPTIONS_POST_TYPE ) {
			$parent_id = tcp_get_parent_from_dynamic_option( $post_id );
			$title = get_the_title( $parent_id );
			$attributes = tcp_get_attributes( 'objects' );
			foreach( $attributes as $attribute ) {
				$terms = wp_get_object_terms( $post_id, $attribute->name ); 
				$term = isset( $terms[0]->name ) ? $terms[0]->name : false;
				if ( $term !== false ) $title .= ', ' . $attribute->labels->name . ': ' . $term;
				//else break;
			}
			if ( $html ) $title = '<span class="tcp_nested_title">' . $title . '</span>';
		}
		return $title;
	}

	function tcp_get_permalink( $url, $post_id ) {
		if ( get_post_type( $post_id ) == TCP_DYNAMIC_OPTIONS_POST_TYPE ) {
			$parent_id = tcp_get_parent_from_dynamic_option( $post_id );
			$url = get_permalink( $parent_id );
		}
		return $url;
	}

	function tcp_get_the_thumbnail( $image, $post_id, $size ) {
		if ( strlen( $image ) == 0 && get_post_type( $post_id ) == TCP_DYNAMIC_OPTIONS_POST_TYPE ) {
			$parent_id = tcp_get_parent_from_dynamic_option( $post_id );
			return tcp_get_the_thumbnail( $image, $parent_id, $size );
		}
		return $image;
	}

	function tcp_get_image_in_grouped( $image, $post_id, $args = false  ) {
		global $thecartpress;
		$args['size'] = $thecartpress->get_setting( 'image_size_grouped_by_button', 'thumbnail' );
		return $this->tcp_get_image_in_content( $image, $post_id, $args );
	}
	
	function tcp_get_image_in_content( $image, $post_id, $args = false  ) {
		$options = tcp_get_dynamic_options( $post_id, true );
		if ( is_array( $options ) && count( $options ) > 0 ) {
			$image = '';
			foreach( $options as $id ) {
				if ( has_post_thumbnail( $id ) ) {
					$ima = $this->get_thumbnail_link( $id, $args, $post_id, $id );
					$image .= $ima;
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
		$image = tcp_get_the_thumbnail_with_permalink( $post_id, $args, false );
		$link = '<span '. $class . ' style="display:none;">' . $image . '</span>';
		return $link;

	}
	
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		if ( is_admin() ) {
			register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );			
			add_action( 'admin_init', array( $this, 'admin_init' ), 90 );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
			add_action( 'tcp_product_metabox_toolbar', array( $this, 'tcp_product_metabox_toolbar' ) );
			add_action( 'tcp_product_metabox_custom_fields', array( $this, 'tcp_product_metabox_custom_fields' ) );
			add_action( 'tcp_product_metabox_save_custom_fields', array( $this, 'tcp_product_metabox_save_custom_fields' ) );
			add_action( 'tcp_product_metabox_delete_custom_fields', array( $this, 'tcp_product_metabox_delete_custom_fields' ) );
			add_filter( 'tcp_product_row_actions', array( $this, 'product_row_actions' ) );
		} else {
			add_filter( 'tcp_the_add_to_cart_items_in_the_cart', array( $this, 'tcp_the_add_to_cart_items_in_the_cart' ), 10, 2 );
			add_filter( 'tcp_get_the_tax_id', array( $this, 'tcp_get_the_tax_id' ), 10, 2 );
		}
		add_filter( 'tcp_add_item_shopping_cart', array( $this, 'tcp_add_item_shopping_cart' ) );
		add_filter( 'tcp_get_discount_by_product', array( $this, 'tcp_get_discount_by_product' ), 10, 3 );
		add_filter( 'tcp_get_the_title', array( $this, 'tcp_get_the_title' ), 10, 4 );
		add_filter( 'tcp_get_the_thumbnail', array( $this, 'tcp_get_the_thumbnail'), 10, 3 );
		add_filter( 'tcp_get_permalink', array( $this, 'tcp_get_permalink'), 10, 2 );
		add_filter( 'tcp_get_image_in_content', array( $this, 'tcp_get_image_in_content' ), 10, 3 );
		add_filter( 'tcp_get_image_in_excerpt', array( $this, 'tcp_get_image_in_content' ), 10, 3 );
		add_filter( 'tcp_get_image_in_grouped_buy_button', array( $this, 'tcp_get_image_in_grouped' ), 10, 3 );
	}
}

$tcp_dynamic_options = new TCPDynamicOptions();

require_once( TCP_DYNAMIC_OPTIONS_POST_TYPE_FOLDER . 'DynamicOptionPostType.class.php' );
?>
