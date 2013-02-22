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

return; //not in use
class TCPDynamicOptionsCustomFieldsMetabox {

	function __construct() {
		//add_action( 'admin_init', array( $this, 'register_metabox' ), 99 );
	}

	function register_metabox() {
		add_meta_box( 'tcp-dyn-option-custom-fields', __( 'Option Custom Fields', 'tcp-do' ), array( &$this, 'showCustomFields' ), TCP_DYNAMIC_OPTIONS_POST_TYPE, 'normal', 'high' );
		remove_meta_box( 'tcp-product-custom-fields', TCP_PRODUCT_POST_TYPE, 'normal' );
		add_action( 'save_post', array( $this, 'saveCustomFields' ), 1, 2 );
		add_action( 'delete_post', array( $this, 'deleteCustomFields' ) );
	}

	function showCustomFields() {
		global $post;
		if ( $post->post_type != TCP_DYNAMIC_OPTIONS_POST_TYPE ) return;
		$post_id = tcp_get_default_id( $post->ID, TCP_DYNAMIC_OPTIONS_POST_TYPE );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$lang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$source_lang = isset( $_REQUEST['source_lang'] ) ? $_REQUEST['source_lang'] : '';
		$is_translation = $lang != $source_lang;
		$parent_id = tcp_get_parent_from_dynamic_option( $post_id ); ?>
		<ul class="subsubsub">
			<li><a href="post.php?action=edit&post=<?php echo $parent_id; ?>"><?php printf( __( 'return to %s', 'tcp-do' ), get_the_title( $parent_id ) ); ?></a></li>
			<li>|</li>
			<?php $admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/'; ?>
			<li><a href="<?php echo $admin_path; ?>DynamicOptionsList.php&post_id=<?php echo $parent_id; ?>" title="<?php echo __( 'return to options list', 'tcp-do' ); ?>"><?php echo __( 'Options list', 'tcp-do' ); ?></a></li>
			<?php do_action( 'tcp_dynamic_option_custom_metabox_toolbar', $post_id ); ?>
		</ul>

		<div class="form-wrap">
			<?php wp_nonce_field( 'tcp-dynamic-option-custom-fields', 'tcp-dynamic-option-custom-fields_wpnonce', false, true ); ?>
			<table class="form-table"><tbody>
			<tr valign="top">
				<th scope="row"><label for="tcp_price"><?php _e( 'Price', 'tcp-do' ); ?>:</label></th>
				<td><input type="text" min="0" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_price" id="tcp_price" value="<?php echo tcp_number_format( tcp_get_the_price( $post_id ) ); ?>" class="regular-text" style="width:12em">&nbsp;<?php tcp_the_currency(); ?>
				<p class="description"><?php _e( 'This price will be added to the price of the parent.', 'tcp-do' ); ?></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_weight"><?php _e( 'Weight', 'tcp-do' ); ?>:</label></th>
				<td><input type="text" min="0" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_weight" id="tcp_weight" value="<?php echo tcp_number_format( tcp_get_the_weight( $post_id ) ); ?>" class="regular-text" style="width:12em" />&nbsp;<?php tcp_the_unit_weight(); ?>
				<p class="description"><?php _e( 'If value is zero then the weight will be the weight of the parent. This weight will not be added to the weight of the parent anyway.', 'tcp-do' ); ?></p></td>
			</tr>
			<!--<tr valign="top">
				<th scope="row"><label for="tcp_order"><?php _e( 'Order', 'tcp-do' ); ?>:</label></th>
				<td><input name="tcp_order" id="tcp_order" value="<?php echo htmlspecialchars( tcp_get_the_order( $post_id ) ); ?>" class="regular-text" type="text" min="0" style="width:4em" /></td>
			</tr>-->
			<tr valign="top">
				<th scope="row"><label for="tcp_sku"><?php _e( 'Sku', 'tcp-do' ); ?>:</label></th>
				<td><input name="tcp_sku" id="tcp_sku" value="<?php echo htmlspecialchars( tcp_get_the_sku( $post_id ) ); ?>" class="regular-text" type="text" style="width:12em" /></td>
			</tr>
			<?php do_action( 'tcp_dynamic_options_metabox_custom_fields', $post_id ); ?>
			</tbody></table>
		</div> <!-- form-wrap -->
		<?php
	}

	function saveCustomFields( $post_id, $post ) {
		if ( $post->post_type != TCP_DYNAMIC_OPTIONS_POST_TYPE ) return array( $post_id, $post );
		if ( ! isset( $_POST[ 'tcp-dynamic-option-custom-fields_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ 'tcp-dynamic-option-custom-fields_wpnonce' ], 'tcp-dynamic-option-custom-fields' ) ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		$post_id	= tcp_get_default_id( $post_id, TCP_DYNAMIC_OPTIONS_POST_TYPE );
		$price		= isset( $_POST['tcp_price'] )  ? tcp_input_number( $_POST['tcp_price'] ) : 0;
		$weight		= isset( $_POST['tcp_weight'] )  ? tcp_input_number( $_POST['tcp_weight'] ) : 0;
		//$order	= isset( $_POST['tcp_order'] )  ? $_POST['tcp_order'] : '';
		$sku		= isset( $_POST['tcp_sku'] ) ? $_POST['tcp_sku'] : '';
		update_post_meta( $post_id, 'tcp_price', $price );
		update_post_meta( $post_id, 'tcp_weight', $weight );
		//update_post_meta( $post_id, 'tcp_order', $order );
		update_post_meta( $post_id, 'tcp_sku', $sku );
		do_action( 'tcp_dynamic_options_metabox_save_custom_fields', $post_id );
	}

	function deleteCustomFields( $post_id ) {
		$post_id = tcp_get_default_id( $post_id, TCP_DYNAMIC_OPTIONS_POST_TYPE );
		//if ( ! isset( $_POST[ 'tcp-option-custom-fields_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ 'tcp-option-custom-fields_wpnonce' ], 'tcp-option-custom-fields' ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
		$post = get_post( $post_id );
		if ( $post->post_type != TCP_DYNAMIC_OPTIONS_POST_TYPE ) return $post_id;
		delete_post_meta( $post_id, 'tcp_price' );
		delete_post_meta( $post_id, 'tcp_weight' );
		delete_post_meta( $post_id, 'tcp_order' );
		delete_post_meta( $post_id, 'tcp_sku' );
		do_action( 'tcp_dynamic_options_metabox_delete_custom_fields', $post_id );
	}
}

//new TCPDynamicOptionsCustomFieldsMetabox();
?>
