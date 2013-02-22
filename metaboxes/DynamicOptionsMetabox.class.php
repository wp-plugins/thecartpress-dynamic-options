<?php
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


class TCPDynamicOptionsMetabox {
	function register_metabox() {
		$saleable_post_types = tcp_get_product_post_types(); //tcp_get_saleable_post_types();
		if ( is_array( $saleable_post_types ) && count( $saleable_post_types ) )
			foreach( $saleable_post_types as $post_type )
				add_meta_box( 'tcp-product-dynamic-options', __( 'Product Dynamic options', 'tcp-do' ), array( $this, 'show' ), $post_type, 'normal', 'core' );
	}

	function show() {
		global $post;
		$post_id = tcp_get_default_id( $post->ID, $post->post_type );
		$attributes	= tcp_get_attributes_by_product( $post_id );
		?>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>DynamicOptionsList.php&post_id=<?php echo $post_id; ?>" title="<?php echo __( 'manage options list', 'tcp-do' ); ?>"><?php echo __( 'Manage Options list', 'tcp-do' ); ?></a></li>
	<?php do_action( 'tcp_dynamic_option_metabox_toolbar', $post_id ); ?>
</ul>
<table class="widefat fixed">
<thead>
<?php ob_start(); ?>
<tr>
	<th>&nbsp;</th>
<?php foreach( $attributes as $attribute ) : ?>
	<th scope="col" class="manage-column">
		<a href="wp-admin/edit-tags.php?taxonomy=<?php echo $attribute->name; ?>&post_type=<?php echo TCP_DYNAMIC_OPTIONS_POST_TYPE; ?>" title="<?php _e( 'Edit attribute', 'tcp-do' ); ?>"><?php echo $attribute->labels->name; ?></a>
	</th>
<?php endforeach; ?>
	<th scope="col" class="manage-column">
		<?php _e( 'Price', 'tcp-do' ); ?>
	</th>
	<?php do_action( 'tcp_dynamic_options_metabox_column_headers', $post ); ?>
</tr>
<?php $thead = ob_get_clean(); ?>
<?php echo $thead; ?>
</thead>
<tfoot>
<?php echo $thead; ?>
</tfoot>
<tbody>
<?php $children = tcp_get_dynamic_options( $post_id, array(), false );
if ( is_array( $children ) && count( $children > 0 ) ) 
	foreach( $children as $id ) : $child = get_post( $id ); ?>
	<tr>
		<td>
			<div class="tcp_dynamic_options_edit_option">
			<?php $image = tcp_get_the_thumbnail( $child->ID, 0, 0, 32 );
			if ( $image == '' ) $image = '<img src="' . plugins_url() . '/thecartpress/images/tcp_icon_gray.png" />';
			echo $image; ?>
			<a href="post.php?action=edit&post=<?php echo $child->ID;?>"><?php _e( 'edit', 'tcp-do' );?></a>
			<?php do_action( 'tcp_dynamic_options_edit_option', $child->ID ); ?>
			</div>
		</td>
		<?php foreach( $attributes as $attribute ) : 
		$child_terms = wp_get_object_terms( $child->ID, $attribute->name );
		$child_term = isset( $child_terms[0]->name ) ? $child_terms[0]->name : ''; ?>
		<td class="tcp_do_child_term">
			<?php echo $child_term; ?>
		</td>
		<?php endforeach; ?>
		<td class="tcp_do_price">
			<?php echo tcp_format_the_price( tcp_get_the_price( $child->ID ) ); ?>
		</td>
		<?php do_action( 'tcp_dynamic_options_metabox_value_rows', $child->ID ); ?>
	</tr>
<?php endforeach; ?>
</tbody>
</table><?php
	}

	function __construct() {
		add_action( 'admin_init', array( $this, 'register_metabox' ) );
	}
}

new TCPDynamicOptionsMetabox();
?>
