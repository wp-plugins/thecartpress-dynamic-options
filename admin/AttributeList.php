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

/*tcp_delete_custom_taxonomy( 'compass-rose-50-gallon-corner-aquarium-with-aquarium-kit' );
tcp_delete_custom_taxonomy( 'compass-rose-36-gallon-corner-fish-tank-with-aquarium-kit' );
tcp_delete_custom_taxonomy( 'compass-rose-36-gallon-corner-fish-tank-beech' );*/

if ( isset( $_REQUEST['tcp_insert'] ) ) :
	$name = trim( $_REQUEST['tcp_name'] );
	if ( strlen( $name ) == 0 ) : ?>
		<div id="message" class="error"><p>
			<?php _e( 'The "name" field must be completed', 'tcp' );?>
		</p></div><?php	
	else :
		$id = strtolower( str_replace( ' ' , '-', $name ) );
		$id = str_replace( '_' , '-', $id );
		$taxo = array(
			'post_type'			=> TCP_DYNAMIC_OPTIONS_POST_TYPE,
			'name'				=> $name,
			'name_id'			=> $id,
			'activate'			=> true,
			'label'				=> $name,
			'singular_label'	=> $name,
			'singular_name'		=> $name,
			'search_items'		=> sprintf( __( 'Search %s', 'tcp' ), $name ),
			'all_items'			=> sprintf( __( 'All %s', 'tcp' ), $name ),
			'parent_item'		=> '',
			'parent_item_colon'	=> '',
			'edit_item'			=> sprintf( __( 'Edit %s', 'tcp' ), $name ),
			'update_item'		=> sprintf( __( 'Update %s', 'tcp' ), $name ),
			'add_new_item'		=> sprintf( __( 'Add new %s', 'tcp' ), $name ),
			'new_item_name'		=> sprintf( __( 'New %s name', 'tcp' ), $name ),
			'desc'				=> $_REQUEST['tcp_desc'],
			'hierarchical'		=> false,
			'rewrite'			=> false,
		);
		tcp_update_custom_taxonomy( $id, $taxo ); ?>
		<div id="message" class="updated"><p>
			<?php _e( 'Taxonomy saved', 'tcp' );?>
		</p></div><?php
	endif;
elseif ( isset( $_REQUEST['tcp_delete_taxonomy'] ) && isset( $_REQUEST['taxonomy_id'] ) ) :
	tcp_delete_custom_taxonomy( $_REQUEST['taxonomy_id'] ); ?>
	<div id="message" class="updated"><p>
		<?php _e( 'The taxonomy has been deleted', 'tcp' );?>
	</p></div><?php	
endif;
?>
<div class="wrap">
<h2><?php _e( 'Attributes', 'tcp' ); ?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>AttributeSets.php"><?php _e( 'Return to Attributes Sets', 'tcp' ); ?></a></li>
</ul>
<div class="clear"></div>

<h3><?php _e( 'Create new attribute', 'tcp' ); ?></h3>
<form method="post">
<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
<tbody>
<tr>
	<td><input type="text" name="tcp_name" size="20" maxlength="255" /></td>
	<td><textarea name="tcp_desc" cols="40" rows="4"></textarea></td>
	<td><input type="submit" name="tcp_insert" value="<?php _e( 'insert', 'tcp' ); ?>" class="button-primary" /></td>
</tr>
</tbody>
</table>
</form>

<br/>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th></tr>
</tfoot>
<tbody>
<?php $taxonomies = tcp_get_custom_taxonomies( TCP_DYNAMIC_OPTIONS_POST_TYPE );
if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) :
	foreach( $taxonomies as $taxonomy_id => $taxonomy ) : ?>
<tr>
	<td><?php echo $taxonomy['name']; ?></td>
	<td><?php echo $taxonomy['desc']; ?>&nbsp;</td>
	<td>
		<a href="admin.php?page=thecartpress/admin/TaxonomyEdit.php&taxonomy=<?php echo $taxonomy_id; ?>" title="<?php _e( 'edit attribute', 'tcp' ); ?>"><?php _e( 'edit', 'tcp' ); ?></a> |
		<a href="edit-tags.php?taxonomy=<?php echo $taxonomy_id; ?>&post_type=<?php echo TCP_DYNAMIC_OPTIONS_POST_TYPE; ?>" title="<?php _e( 'add terms', 'tcp' ); ?>"><?php _e( 'add', 'tcp' ); ?></a> |
		<a href="#" onclick="jQuery('.delete_taxonomy').hide();jQuery('#delete_<?php echo $taxonomy_id; ?>').show();" class="delete"><?php _e( 'delete', 'tcp' ); ?></a></div>
		<div id="delete_<?php echo $taxonomy_id; ?>" class="delete_taxonomy" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post" name="frm_delete_<?php echo $taxonomy_id; ?>">
				<input type="hidden" name="taxonomy_id" value="<?php echo $taxonomy_id; ?>" />
				<input type="hidden" name="tcp_delete_taxonomy" value="y" />
				<p><?php _e( 'Do you really want to delete this taxonomy?', 'tcp' ); ?></p>
				<a href="javascript:document.frm_delete_<?php echo $taxonomy_id; ?>.submit();" class="delete"><?php _e( 'Yes' , 'tcp' ); ?></a> |
				<a href="#" onclick="jQuery('#delete_<?php echo $taxonomy_id; ?>').hide();"><?php _e( 'No, I don\'t' , 'tcp' ); ?></a>
			</form>
		</div>
	</td>
</tr>
	<?php endforeach; ?>
<?php else : ?>
<tr>
	<td colspan="3"><?php _e( 'The list is empty', 'tcp' ); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
