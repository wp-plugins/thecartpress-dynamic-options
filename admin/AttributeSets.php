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

function tcp_att_set_get_id( $title ) {
	$title = strtolower( $title );
	$title = str_replace( ' ', '-', $title );
	$title = str_replace( '_', '-', $title );
	return $title;
}

$attribute_sets	= get_option( 'tcp_attribute_sets', array() );
$attributes		= tcp_get_attributes( 'objects' );
if ( isset( $_REQUEST['tcp_insert'] ) ) {
	$title = trim( $_REQUEST['tcp_title'] );
	if ( strlen( $title ) == 0 ) : ?>
		<div id="message" class="error"><p>
		<?php _e( 'The Set has not been inserted. The title has been omitted.', 'tcp-do' ); ?>
		</p></div>
	<?php else :
		$id = tcp_att_set_get_id( $title );
		if ( isset( $attribute_sets[$id] ) ) : ?>
			<div id="message" class="error"><p>
			<?php _e( 'The Set has not been inserted, it already exists.', 'tcp-do' ); ?>
			</p></div>
		<?php else :
			$new_set = array(
				'title'			=> $title,
				'desc'			=> $_REQUEST['tcp_desc'],
				'taxonomies'	=> isset( $_REQUEST['tcp_taxonomies'] ) ? $_REQUEST['tcp_taxonomies'] : array(),
			);
			$attribute_sets[$id] = $new_set;
			update_option( 'tcp_attribute_sets', $attribute_sets); ?>
			<div id="message" class="updated"><p>
			<?php _e( 'The Set has been inserted sucesfuly.', 'tcp-do' ); ?>
			</p></div>
		<?php endif;
	endif;
} elseif ( isset( $_REQUEST['tcp_save'] ) ) {
	unset( $attribute_sets );
	$attribute_sets = array();
	foreach( $_REQUEST['tcp_id'] as $i => $id ) {
		$title = $_REQUEST['tcp_title'][$i];
		$attribute_sets[tcp_att_set_get_id( $title )] = array(
			'title'			=> $title,
			'desc'			=> $_REQUEST['tcp_desc'][$i],
			'taxonomies'	=> isset( $_REQUEST['tcp_taxonomies'][$id] ) ? $_REQUEST['tcp_taxonomies'][$id] : array(),
		);
	}
	update_option( 'tcp_attribute_sets', $attribute_sets ); ?>
	<div id="message" class="updated"><p>
	<?php _e( 'The Sets have been updated sucesfuly.', 'tcp-do' ); ?>
	</p></div>
<?php } elseif ( isset( $_REQUEST['tcp_delete'] ) ) {
	$id = $_REQUEST['id'];
	unset( $attribute_sets[$id] );
	update_option( 'tcp_attribute_sets', $attribute_sets); ?>
	<div id="message" class="updated"><p>
	<?php _e( 'The Set has been deleted sucesfuly.', 'tcp-do' ); ?>
	</p></div>
<?php }
?>
<div class="wrap">
<h2><?php _e( 'Attribute Sets', 'tcp-do' ); ?></h2>

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
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp-do' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Attributes', 'tcp-do' ); ?></th>
</tr>
</thead>
<tbody>
<tr>
	<td><input type="text" name="tcp_title" size="20" maxlength="255" /></td>
	<td><textarea name="tcp_desc" cols="25" rows="1"></textarea></td>
	<td>
	<?php foreach( $attributes as $attribute ) : ?>
		<label><input id="tcp_taxonomies" type="checkbox" name="tcp_taxonomies[]" value="<?php echo $attribute->name; ?>" /> <?php echo $attribute->labels->name; ?></label>
		<br />
	<?php endforeach; ?>
	<a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>AttributeList.php"><?php _e( 'Attributes', 'tcp-do' ); ?></a>
	</td>
</tr>
</tbody>
</table>
<p><input type="submit" name="tcp_insert" value="<?php _e( 'Insert', 'tcp-do' ); ?>" class="button-primary" /></p>
</form>

<h3><?php _e( 'Current Attribute Set List', 'tcp-do' ); ?></h3>

<form method="post">
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
<?php if ( is_array( $attribute_sets ) && count( $attribute_sets ) > 0 ) :
	foreach( $attribute_sets as $id => $attribute_set ) : ?>
<tr>
	<td><input type="hidden" name="tcp_id[]" value="<?php echo $id; ?>" /><input type="text" name="tcp_title[]" size="20" maxlength="40" value="<?php echo $attribute_set['title']; ?>"/></td>
	<td><textarea name="tcp_desc[]" maxlength="40" cols="32" rows="2"><?php echo $attribute_set['desc']; ?></textarea></td>
	<td>
	<?php foreach( $attributes as $attribute ) : ?>
		<label><input id="tcp_taxonomies_<?php echo $id;?>" type="checkbox" name="tcp_taxonomies[<?php echo $id;?>][]" value="<?php echo $attribute->name; ?>" <?php tcp_checked_multiple( $attribute_set['taxonomies'], $attribute->name ); ?>/> <?php echo $attribute->labels->name; ?></label>
		&nbsp;<a href="edit-tags.php?taxonomy=<?php echo $attribute->name; ?>&post_type=<?php echo TCP_DYNAMIC_OPTIONS_POST_TYPE; ?>" title="<?php _e( 'edit attribute', 'tcp-do' ); ?>"><?php _e( 'edit', 'tcp-do' ); ?></a>
		<br />
	<?php endforeach; ?>
	</td>
	<td>
		<div><a href="#" onclick="jQuery('.delete_att_set').hide();jQuery('#delete_att_set_<?php echo $id; ?>').show();return false;" class="delete"><?php _e( 'delete', 'tcp-do' ); ?></a></div>
		<div id="delete_att_set_<?php echo $id; ?>" class="delete_att_set" style="display:none; border: 1px dotted orange; padding: 2px">
			<p><?php _e( 'Do you really want to delete this Attribute Set?', 'tcp-do' ); ?></p>
			<?php
			$url = add_query_arg( 'id', $id );
			$url = add_query_arg( 'tcp_delete', '', $url ); ?>
			<a href="<?php echo $url; ?>" class="delete"><?php _e( 'Yes' , 'tcp-do' ); ?></a> |
			<a href="#" onclick="jQuery('#delete_att_set_<?php echo $id; ?>').hide();return false;"><?php _e( 'No, I don\'t' , 'tcp-do' ); ?></a>
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
<?php if ( is_array( $attribute_sets ) && count( $attribute_sets ) > 0 ) : ?>
<p><input type="submit" name="tcp_save" value="<?php _e( 'Save', 'tcp-do' ); ?>" class="button-primary" /></p>
<?php endif; ?>
</form>

</div><!-- .wrap-->
