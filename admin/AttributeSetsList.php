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

if ( isset( $_REQUEST['tcp_insert'] ) ) {
	$title = trim( $_REQUEST['tcp_title'] );
	if ( strlen( $title ) == 0 ) : ?>
		<div id="message" class="error"><p>
		<?php _e( 'The Set has not been inserted. The title has been omitted.', 'tcp-do' ); ?>
		</p></div>
	<?php else :
		$id = tcp_att_set_get_id( $title );
		if ( ! tcp_insert_attribute_set( $title, $_REQUEST['tcp_desc'] ) ) : ?>
			<div id="message" class="error"><p>
			<?php _e( 'The Set has not been inserted, it already exists.', 'tcp-do' ); ?>
			</p></div>
		<?php else : ?>
			<div id="message" class="updated"><p>
			<?php _e( 'The Set has been inserted sucesfuly.', 'tcp-do' ); ?>
			</p></div>
		<?php endif;
	endif;
} elseif ( isset( $_REQUEST['tcp_delete'] ) ) {
	$id = $_REQUEST['id'];
	tcp_delete_attribute_set( $id ); ?>
	<div id="message" class="updated"><p>
	<?php _e( 'The Set has been deleted sucesfuly.', 'tcp-do' ); ?>
	</p></div>
<?php }
?>
<div class="wrap">
<h2><?php _e( 'Attribute Sets', 'tcp-do' ); ?></h2>
<p><?php _e( 'Allows to create Attribute sets. These sets will contain attributes as "colour", "size". Each attribute will contain values, as "red", "blue", "Large", etc.', 'tcp-do'); ?></p>
<p><?php _e( 'To create Color variations for a product (for example, for "Cars") you should create a set called "Car variations", then create an Attribute called "Car Colours", and finally add colours to the attribute.', 'tcp-do'); ?></p>
<p><?php _e( 'With all these options defined for a product, you should visit "Dynamic Options", for each product, and create variations for the different values and attributes defined.', 'tcp-do'); ?></p>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>AttributeList.php"><?php _e( 'Attributes', 'tcp-do' ); ?></a></li>
</ul>
<div class="clear"></div>

<h3><?php _e( 'New Attribute Set', 'tcp-do' ); ?></h3>
<form method="post"><!-- New item -->
<table class="widefat fixed">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Title', 'tcp-do' ); ?></th>
	<th scope="col" colspan="2" class="manage-column"><?php _e( 'Description', 'tcp-do' ); ?></th>
</tr>
</thead>
<tbody>
<tr>
	<td><input type="text" name="tcp_title" size="20" maxlength="255" /></td>
	<td><textarea name="tcp_desc" cols="20" rows="1"></textarea></td>
	<td><input type="submit" name="tcp_insert" value="<?php _e( 'Insert', 'tcp-do' ); ?>" class="button-primary" /></td>
</tr>
</tbody>
</table>
</form>

<h3><?php _e( 'Current Attribute Set List', 'tcp-do' ); ?></h3>

<table class="widefat fixed">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Title', 'tcp-do' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp-do' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Attributes', 'tcp-do' ); ?></th>
	<th scope="col" class="manage-column">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Title', 'tcp-do' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp-do' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Attributes', 'tcp-do' ); ?></th>
	<th scope="col" class="manage-column">&nbsp;</th>
</tr>
</tfoot>
<tbody>
<?php $attribute_sets	= get_option( 'tcp_attribute_sets', array() );
if ( is_array( $attribute_sets ) && count( $attribute_sets ) > 0 ) :
	$attributes = tcp_get_attributes( 'objects' );
	foreach( $attribute_sets as $id => $attribute_set ) : ?>
<tr>
	<td><?php echo $attribute_set['title']; ?></td>
	<td><?php echo $attribute_set['desc']; ?></td>
	<td>
	<?php $attributes_to_display = array();
	if ( is_array( $attribute_set['taxonomies'] ) && count( $attribute_set['taxonomies'] ) > 0 ) {
		foreach( $attribute_set['taxonomies'] as $tax_id ) {
			$attribute = $attributes[$tax_id];
			$attributes_to_display[] = $attribute->labels->name;
		}
		echo implode( $attributes_to_display, ',&nbsp;' );
	 } else {
	 	_e( 'No Attributes', 'tcp-do' );
	 } ?>
	</td>
	<td>
		<div><a href="<?php echo add_query_arg( 'id', $id, TCP_DYNAMIC_OPTIONS_ADMIN_PATH . 'AttributeSetEdit.php' ); ?>"><?php _e( 'edit', 'tcp-do' ); ?></a> | <a href="#" onclick="jQuery('.delete_att_set').hide();jQuery('#delete_att_set_<?php echo $id; ?>').show(200);return false;" class="delete"><?php _e( 'delete', 'tcp-do' ); ?></a></div>
		<div id="delete_att_set_<?php echo $id; ?>" class="delete_att_set" style="display:none; border: 1px dotted orange; padding: 2px">
			<p><?php _e( 'Do you really want to delete this Attribute Set?', 'tcp-do' ); ?></p>
			<?php
			$url = add_query_arg( 'id', $id );
			$url = add_query_arg( 'tcp_delete', '', $url ); ?>
			<a href="<?php echo $url; ?>" class="delete"><?php _e( 'Yes' , 'tcp-do' ); ?></a> |
			<a href="#" onclick="jQuery('#delete_att_set_<?php echo $id; ?>').hide(100);return false;"><?php _e( 'No, I don\'t' , 'tcp-do' ); ?></a>
		</div>
	</td>
</tr>
	<?php endforeach;
else : ?>
<tr>
	<td colspan="4"><?php _e( 'The Attribute Sets list is empty', 'tcp-do' ); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>

</div><!-- .wrap-->
