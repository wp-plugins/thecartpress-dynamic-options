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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

$original_post_id = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : 0;
$post_id = tcp_get_default_id( $original_post_id );
$post = get_post( $post_id );
$attributes = tcp_get_attributes_by_product( $post_id );
if ( count( $attributes ) == 0 ) { ?>
	<div id="message" class="error">
	<p><?php _e( 'To create options, you need, first, to assign a Set of Attributes to the product. If you have assigned a set, ensure the set has attributes assigned.', 'tcp-do' ); ?></p>
	<p><a href="post.php?action=edit&post=<?php echo $post->ID;?>"><?php printf( __( 'return to %s', 'tcp-do' ), $post->post_title ); ?></a></p>
	</div>
	<?php //exit;
}

if ( isset( $_REQUEST['tcp_delete_options'] ) ) {
	foreach( $_REQUEST['tcp_delete'] as $post_id_to_delete ) {
		tcp_delete_dynamic_option( $post_id_to_delete );
	}
} else if ( isset( $_REQUEST['tcp_add_term'] ) ) {
	foreach( $_REQUEST['tcp_add_term'] as $taxonomy => $value ) {
		$term = trim( $_REQUEST['tcp_term'][$taxonomy] );
		if ( strlen( $term ) > 0 ) {
			wp_insert_term( $term, $taxonomy ); ?>
		<div id="message" class="updated"><p>
		<?php _e( 'The term has been inserted', 'tcp-do' ); ?>
		</p></div>
	<?php }
	}
} elseif ( isset( $_REQUEST['tcp_insert'] ) || isset( $_REQUEST['tcp_save'] ) ) {
	$options = array();
	$prices = $_REQUEST['tcp_price'];
	$terms = array();
	foreach( $prices as $id => $price ) {
		$tcp_post_ids = $_REQUEST['tcp_post_id'];
		$tcp_post_id = $tcp_post_ids[$id];
		$price = tcp_input_number( $price );
		$title = $post->post_title;
		foreach( $attributes as $attribute ) if ( $attribute ) {
			$attribute_name = 'tcp_attributes_' . $attribute->name;
			if ( strlen( $_REQUEST[$attribute_name][$id] ) > 0 ) {
				$terms[$attribute->name] = $_REQUEST[$attribute_name][$id];
			} else {
				break;
			}
			$term = get_term_by( 'slug', $_REQUEST[$attribute_name][$id], $attribute->name );
			$title .= ': ' . $attribute->labels->name . ': ' . $term->name;
		}
		if ( count( $terms ) > 0 ) {
			$option = array (
				'ID'		=> $tcp_post_id,
				'title'		=> $title,
				'parent_id'	=> $post_id,
				'price'		=> $price,
				'terms'		=> $terms,
				'order'		=> $_REQUEST['tcp_order'][$id],
				'delete'	=> in_array( $tcp_post_id, isset( $_REQUEST['tcp_delete'] ) ? (array)$_REQUEST['tcp_delete'] : array() ),
			);
			$option = apply_filters( 'tcp_dynamic_options_option_to_save', $option, $id, $_REQUEST );
			$options[] = $option;
		}
	}
	if ( isset( $_REQUEST['tcp_save'] ) ) {
		if ( count( $options ) > 0 ) {
			foreach( $options as $option ) {
				if ( $option['delete'] ) {
					tcp_delete_dynamic_option( $option['ID'] );
				} else { //if ( ! tcp_exists_dynamic_option( $option ) ) {
					tcp_update_dynamic_option( $option );
				}
			}
		} ?>
		<div id="message" class="updated"><p>
		<?php _e( 'The options have been modified', 'tcp-do' ); ?>
		</p></div>
	<?php } elseif ( count( $options ) > 0 ) {
		$option = $options[0];
		if ( ! tcp_exists_dynamic_option( $option ) ) {
			tcp_insert_dynamic_option( $option ); ?>
			<div id="message" class="updated"><p>
			<?php _e( 'The option has been inserted', 'tcp-do' ); ?>
			</p></div><?php
		} else { ?>
			<div id="message" class="error"><p>
			<?php _e( 'The option has not been inserted. The option already exists.', 'tcp-do' ); ?>
			</p></div><?php
		}
	} else { ?>
		<div id="message" class="error"><p>
		<?php _e( 'The option has not been inserted.', 'tcp-do' ); ?>
		</p></div><?php
	}
//} elseif ( isset( $_REQUEST['tcp_delete'] ) ) {
//	$option_id = $_REQUEST['option_id'];
//	tcp_delete_dynamic_option( $option_id );
} elseif( isset( $_REQUEST['tcp_create_all'] ) ) {
	$variations = array();
	foreach( $attributes as $attribute ) if ( $attribute ) {
		$terms = get_terms( $attribute->name, array( 'hide_empty' => false, 'fields' => 'ids' ) );
		$variations = tcp_do_mix_variations( $variations, $terms );//, $attribute->name );
	}
	tcp_do_add_variations( $variations, $post_id, $attributes );
}

function tcp_do_mix_variations( $variations, $values ) {
	$new_variations = array();
	if ( is_array( $variations ) && count( $variations ) > 0 ) {
		foreach( $variations as $variation ) {
			foreach( $values as $value ) {
				if ( is_array( $variation ) ) $new_variation = $variation;
				else $new_variation = array( $variation );
				$new_variation[] = $value;
				$new_variations[] = $new_variation;
			}
		}
	} else {
		$new_variations = $values;
	}
	return $new_variations;
}

function tcp_do_add_variations( $variations, $post_id, $attributes ) {
	$post = get_post( $post_id );
	$options = array();
	foreach( $variations as $variation ) {
		if ( ! is_array( $variation ) ) $variation = array( $variation );
		$terms = array();
		$title = $post->post_title;
		foreach( $attributes as $id => $attribute ) if ( $attribute ) {
			$term = get_term_by( 'id', $variation[$id], $attribute->name );
			//$terms[$attribute->name] = $term;
			$terms[$attribute->name] = $term->slug;
			$title .= ': ' . $attribute->labels->name . ': ' . $term->name;
		}
		$option = array(
			'title' => $title,
			'parent_id'	=> $post_id,
			'price' => 0,
			'order'	=> '',
			'terms' => $terms,
			'order' => 0,
		);
		$option = apply_filters( 'tcp_dynamic_options_option_to_save', $option, $id, $_REQUEST );
		$options[] = $option;
	}
	foreach( $options as $option )
		if ( ! tcp_exists_dynamic_option( $option ) )
			tcp_insert_dynamic_option( $option );
} ?>
<div class="wrap">
<h2><?php printf( __( 'Options for %s', 'tcp-do' ), $post->post_title ); ?></h2>
<ul class="subsubsub">
	<li><a href="post.php?action=edit&post=<?php echo $post->ID;?>"><?php printf( __( 'Return to %s', 'tcp-do' ), $post->post_title ); ?></a></li>
	<li>|</li>
	<li><a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>AttributeSetsList.php"><?php _e( 'Attribute Sets', 'tcp-do' ); ?></a></li>
	<li>|</li>
	<li><a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>AttributeList.php"><?php _e( 'Manage Attributes', 'tcp-do' ); ?></a></li>
</ul>
<div class="clear"></div>

<form method="post"><!-- New item -->
<table class="widefat fixed">
<thead>
<tr>
	<th>&nbsp;</th>
	<?php if ( is_array( $attributes ) && count( $attributes ) > 0 ) foreach( $attributes as $attribute ) : 
		if ( $attribute ) :
		$taxonomy = get_taxonomy( $attribute->name ); ?>
	<th scope="col" class="manage-column">
		<a href="edit-tags.php?taxonomy=<?php echo $attribute->name; ?>&post_type=<?php echo TCP_DYNAMIC_OPTIONS_POST_TYPE; ?>" title="<?php _e( 'Edit attribute', 'tcp-do' ); ?>"><?php echo $attribute->labels->name; ?></a>
	</th>
		<?php endif;
	endforeach; ?>
	<th scope="col" class="manage-column">
		<?php _e( 'Price', 'tcp-do' ); ?>
	</th>
	<?php do_action( 'tcp_dynamic_options_lists_header_new', $post ); ?>
	<th scope="col" class="manage-column">
		<?php _e( 'Sorting', 'tcp-do' ); ?>
	</th>
	<th scope="col">&nbsp;</th>
</tr>
</thead>
<tbody>
<tr>
<td><?php _e( 'New Option', 'tcp-do'); ?></td>
<?php foreach( $attributes as $attribute ) : 
	if ( $attribute ) : ?>
	<td>
		<select name="tcp_attributes_<?php echo $attribute->name; ?>[]" class="tcp_attributes tcp_attributes_<?php echo $attribute->name; ?>">
			<option value=""><?php _e( 'No one', 'tcp-do' ); ?></option>
		<?php $args = array( 'hide_empty' => false );
		$terms = get_terms( $attribute->name, $args );
		if ( is_array( $terms ) && count( $terms ) > 0 ) :
		foreach( $terms as $term ) : ?>
			<option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
		<?php endforeach; ?>
		<?php endif; ?>
		</select>
	</td>
	<?php endif;
endforeach; ?>
	<td>
		<input type="text" min="0" step="any" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_price[]" class="tcp_price" size="5" maxlength="9"/>&nbsp;<?php tcp_the_currency();?>
		<input type="hidden" name="tcp_post_id[]" value="0"/>
	</td>
	<?php do_action( 'tcp_dynamic_options_lists_row_new', $post ); ?>
	<td>
		<input type="text" name="tcp_order[]" class="tcp_order" size="3" maxlength="9"/>
	</td>
	<td>
	<input type="submit" name="tcp_insert" value="<?php _e( 'insert', 'tcp-do' ); ?>" class="button-primary"/>
	<input type="submit" name="tcp_create_all" value="<?php _e( 'create all', 'tcp-do' ); ?>" title="<?php _e( 'Create all attribute variations', 'tcp-do' ); ?>" class="button-secondary"/>
	</td>
</tr>

<tr>
	<td><?php _e( 'New terms', 'tcp-do' ); ?></td>
	<?php foreach( $attributes as $attribute ) : 
		if ( $attribute ) :
		$taxonomy = get_taxonomy( $attribute->name ); ?>
		<td>
			<input type="text" name="tcp_term[<?php echo $attribute->name; ?>]" size="10" maxlength="50" /><input type="submit" name="tcp_add_term[<?php echo $attribute->name; ?>]" value="<?php _e( 'insert', 'tcp-do' ); ?>" class="button-secondary"/>
		</td>
		<?php endif;
	endforeach; ?>
	<td>&nbsp;</td>
</tr>

</tbody>
</table>
</form>

<br/>

<form method="post">

<div class="tablenav top">

<?php foreach( $attributes as $attribute ) : if ( ! $attribute ) continue; ?>

	<div class="alignleft actions">

		<?php $name = 'tcp_filter_' . $attribute->name; ?>
		<label><?php echo $attribute->labels->name; ?>:&nbsp;
		
		<select name="<?php echo $name; ?>" class="tcp_attributes tcp_attributes_<?php echo $attribute->name; ?>">

			<option value=""><?php _e( 'No one', 'tcp-do' ); ?></option>

		<?php $args = array( 'hide_empty' => false );
		$terms = get_terms( $attribute->name, $args );
		if ( is_array( $terms ) && count( $terms ) > 0 ) foreach( $terms as $term ) : ?>

			<option value="<?php echo $term->slug; ?>" <?php selected( $term->slug, $_REQUEST[$name] ); ?>><?php echo $term->name; ?></option>

		<?php endforeach; ?>

		</select>
		</label>

	</div>

<?php endforeach; ?>

	<div class="alignleft actions">
		<input type="submit" name="tcp_filter" value="<?php _e( 'Filter', 'tcp-do' ); ?>" class="button-secondary"/>
	</div>

	<br class="clear"/>

</div><!-- .tablenav .top -->

<table class="widefat fixed">
<thead>
<tr>
	<th>&nbsp;</th>
<?php foreach( $attributes as $attribute ) : if ( !$attribute ) continue; ?>
	<th scope="col" class="manage-column">

		<a href="<?php echo 'edit-tags.php?taxonomy=' . $attribute->name . '&post_type=' . TCP_DYNAMIC_OPTIONS_POST_TYPE; ?>" title="<?php _e( 'Edit attribute', 'tcp-do' ); ?>"><?php echo $attribute->labels->name; ?></a>

	</th>
<?php endforeach; ?>
	<th scope="col" class="manage-column">
		<?php _e( 'Price', 'tcp-do' ); ?>
	</th>
	<?php do_action( 'tcp_dynamic_options_lists_header', $post ); ?>
	<th scope="col" class="manage-column">
		<?php _e( 'Order', 'tcp-do' ); ?>
		<?php global $thecartpress;
		if ( $thecartpress && 'order' != $thecartpress->get_setting( 'dynamic_options_order_by', 'title' ) ) echo '*'; ?>
	</th>
	<th scope="col" class="manage-column">
		<input type="checkbox" class="tcp_select_delete_all" onclick="if (jQuery('.tcp_select_delete_all').attr('checked') ) jQuery('.tcp_delete').attr('checked', true); else jQuery('.tcp_delete').attr('checked', false);" value="1"/> <?php _e( 'Delete', 'tcp-do' ); ?>
	</th>
	<!--<th scope="col">&nbsp;</th>-->
</tr>
</thead>

<tfoot>
	<th>&nbsp;</th>
<?php foreach( $attributes as $attribute ) : if ( ! $attribute ) continue; ?>
	<th scope="col" class="manage-column">
		<a href="<?php echo 'edit-tags.php?taxonomy=' . $attribute->name . '&post_type=' . TCP_DYNAMIC_OPTIONS_POST_TYPE; ?>" title="<?php _e( 'Edit attribute', 'tcp-do' ); ?>"><?php echo $attribute->labels->name; ?></a>
	</th>
<?php endforeach; ?>
	<th scope="col" class="manage-column">
		<?php _e( 'Price', 'tcp-do' ); ?>
	</th>
	<?php do_action( 'tcp_dynamic_options_lists_header', $post ); ?>
	<th scope="col" class="manage-column">
		<?php _e( 'Order', 'tcp-do' ); ?>
		<?php global $thecartpress;
		if ( $thecartpress && 'order' != $thecartpress->get_setting( 'dynamic_options_order_by', 'title' ) ) echo '*'; ?>
	</th>
	<th scope="col" class="manage-column">
		<?php _e( 'Delete', 'tcp-do' ); ?>
	</th>
	<!--<th scope="col">&nbsp;</th>-->
</tr>
</tfoot>

<tbody>
<?php $filter['tax_query'] = array( 'relation' => 'AND' );
foreach( $attributes as $attribute ) {
	if ( ! $attribute ) continue;
	if ( isset( $_REQUEST['tcp_filter_' . $attribute->name] ) ) {
		$term = $_REQUEST['tcp_filter_' . $attribute->name];
		if ( strlen( $term ) > 0 ) {
			$filter['tax_query'][] = array(
				'taxonomy'	=> $attribute->name,
				'field'		=> 'slug',
				'terms'		=> array( $term ),
			);
		}
	}
}

$children = tcp_get_dynamic_options( $post_id, $filter, false );
if ( is_array( $children ) && count( $children > 0 ) ) 
	foreach( $children as $id ) : $child = get_post( $id ); ?>
<tr>
	<td>
	<div class="tcp_dynamic_options_edit_option">
	<?php $image = tcp_get_the_thumbnail( $child->ID, 0, 0, 32 );
	if ( $image == '' ) $image = '<img src="' . plugins_url() . '/thecartpress/images/tcp_icon_gray.png" />';
	echo $image; ?>
	<a href="post.php?action=edit&post=<?php echo $child->ID; ?>"><?php _e( 'edit', 'tcp-do' );?></a>
	<?php do_action( 'tcp_dynamic_options_edit_option', $child->ID ); ?>
	</div>
	<!-- |
	<span class="hide-if-no-js"><a title="<?php _e( 'Set featured image', 'tcp-do' ); ?>" href="<?php admin_url('media-upload.php?post_id=' . $child->ID . '&type=image&TB_iframe=1&width=640&height=876' ); ?>" id="set-post-thumbnail" class="thickbox"><?php _e( 'Set featured image', 'tcp-do' ); ?></a></span>-->
	</td>
	<?php foreach( $attributes as $attribute ) if ( $attribute ) : 
	$child_terms = wp_get_object_terms( $child->ID, $attribute->name ); 
	$child_term = isset( $child_terms[0]->slug ) ? $child_terms[0]->slug : ''; ?>
	<td>
		<select name="tcp_attributes_<?php echo $attribute->name; ?>[]" class="tcp_attributes tcp_attributes_<?php echo $attribute->name; ?>">
			<option value="" <?php selected( count( $child_terms ) == 0 ); ?>><?php _e( 'No one', 'tcp-do' ); ?></option>
		<?php $args = array( 'hide_empty' => false );
		$terms = get_terms( $attribute->name, $args ); 
		foreach( $terms as $term) : ?>
			<option value="<?php echo $term->slug; ?>" <?php selected( $child_term, $term->slug ); ?>><?php echo $term->name; ?></option>
		<?php endforeach; ?>
		</select>
	</td>
	<?php endif; ?>
	<td>
		<input type="text" min="0" step="any" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_price[]" class="tcp_price" value="<?php echo tcp_number_format( tcp_get_the_price( $child->ID, false ) );?>" class="regular-text tcp_count" size="5" maxlength="9" />&nbsp;<?php tcp_the_currency();?>
		<input type="hidden" name="tcp_post_id[]" value="<?php echo $child->ID; ?>"/>
	</td>
	<?php do_action( 'tcp_dynamic_options_lists_row', $child->ID ); ?>
	<td>
		<input type="text" name="tcp_order[]" class="tcp_order" size="3" maxlength="9" value="<?php echo tcp_get_the_order( $child->ID );?>"/>
	</td>
	<td>
		<input type="checkbox" name="tcp_delete[]" class="tcp_delete" value="<?php echo $child->ID; ?>" />
	</td>
	<!--<td>
		<div><a href="#" onclick="jQuery('.delete_options').hide();jQuery('#delete_<?php echo $child->ID; ?>').show(200);return false;" class="delete"><?php _e( 'delete', 'tcp-do' ); ?></a></div>
		<div id="delete_<?php echo $child->ID; ?>" class="delete_options" style="display:none; border: 1px dotted orange; padding: 2px">
			<p><?php _e( 'Do you really want to delete this option?', 'tcp-do' ); ?></p>
			<?php
			$url = add_query_arg( 'option_id', $child->ID );
			$url = add_query_arg( 'post_id', $original_post_id, $url );
			$url = add_query_arg( 'tcp_delete', 'y', $url ); ?>
			<a href="<?php echo $url; ?>" class="delete"><?php _e( 'Yes' , 'tcp-do' ); ?></a> |
			<a href="#" onclick="jQuery('#delete_<?php echo $child->ID; ?>').hide(100);return false;"><?php _e( 'No, I don\'t' , 'tcp-do' ); ?></a>
		</div>
	</td>-->
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php global $thecartpress;
if ( $thecartpress && 'order' != $thecartpress->get_setting( 'dynamic_options_order_by', 'title' ) ) { ?>
<span class="description">
(*) <?php _e( 'Options will be ordered by title or price, this field will not be considered.', 'tcp-do' ); ?>
&nbsp;<?php printf( __( 'To change the order, visit <a href="%s">Theme compatibility</a> page.', 'tcp-do' ), get_admin_url() . 'admin.php?page=thecartpress/TheCartPress.class.php/appearance#dynamic_options_settings' ); ?>
</span>
<?php } ?>
<p>
	<input type="submit" name="tcp_save" value="<?php _e( 'save', 'tcp-do' ); ?>" class="button-primary"/>
	<!--<span class="description"><?php _e( 'All the options with the delete checkbox checked will be deleted.', 'tcp-do' ); ?></span>-->

	<input type="submit" name="tcp_delete_options" value="<?php _e( 'delete', 'tcp-do' ); ?>" class="button-secondary"/>
	<span class="description"><?php _e( 'All the options with the delete checkbox checked will be deleted.', 'tcp-do' ); ?></span>
</p>
</form>

</div><!-- .wrap -->
