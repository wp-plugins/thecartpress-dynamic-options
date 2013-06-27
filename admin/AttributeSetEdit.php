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
$id = $_REQUEST['id'];
if ( isset( $_REQUEST['tcp_save'] ) ) {
	$title = trim( $_REQUEST['tcp_title'] );
	if ( strlen( $title ) == 0 ) : ?>
		<div id="message" class="error"><p>
		<?php _e( 'The Set has not been inserted. The title has been omitted.', 'tcp-do' ); ?>
		</p></div>
	<?php else :
		if ( tcp_update_attribute_set( $id, $title, $_REQUEST['tcp_desc'], $_REQUEST['tcp_taxonomies'] ) ) : ?>
			<div id="message" class="updated"><p>
			<?php _e( 'The Set has been updated sucesfuly.', 'tcp-do' ); ?>
			</p></div>
		<?php endif; ?>
	<?php endif; ?>
<?php } 
$attribute_set = tcp_get_attribute_set( $id ); ?>
<div class="wrap">
<h2><?php printf( __( 'Attribute Set: %s', 'tcp-do' ), $attribute_set['title'] ); ?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>AttributeSetsList.php"><?php _e( 'Return to Attribute Sets list', 'tcp-do' ); ?></a></li>
	<li>|</li>
	<li><a href="<?php echo TCP_DYNAMIC_OPTIONS_ADMIN_PATH; ?>AttributeList.php"><?php _e( 'Attributes', 'tcp-do' ); ?></a></li>
</ul>
<div class="clear"></div>

<form method="post">
<input type="hidden" name="tcp_id[]" value="<?php echo $id; ?>" />

<table class="form-table">
<thead>
<tr>
	<th scope="row" class="manage-column"><label for="tcp_title"><?php _e( 'Title', 'tcp-do' ); ?></label>:</th>
	<td><input type="text" name="tcp_title" id="tcp_title" size="20" maxlength="40" value="<?php echo $attribute_set['title']; ?>"/></td>
</tr><tr>
	<th scope="row" class="manage-column"><label for="tcp_desc"><?php _e( 'Description', 'tcp-do' ); ?></label>:</th>
	<td><textarea name="tcp_desc" id="tcp_desc" maxlength="40" cols="32" rows="2"><?php echo $attribute_set['desc']; ?></textarea></td>
</tr><tr>
	<th scope="row" class="manage-column"><label><?php _e( 'Attributes', 'tcp-do' ); ?></label>:</th>
	<td>

<?php //var_dump( $attribute_set['taxonomies'] ); echo '<br><br>'; ?>


	<?php $attributes = tcp_get_attributes( 'objects' );
	if ( is_array( $attributes ) && count( $attributes ) > 0 ) {
		foreach( $attributes as $attribute_id => $attribute ) { ?>
<?php //echo $attribute_id, '<br>'; var_dump($attribute); echo '<br><br>'; ?>
			<label><input id="tcp_taxonomies_<?php echo $id;?>" type="checkbox" name="tcp_taxonomies[]" value="<?php echo $attribute_id; ?>" <?php tcp_checked_multiple( $attribute_set['taxonomies'], $attribute->name ); ?>/> <?php echo $attribute->labels->name; ?></label>
			&nbsp;
			<a href="admin.php?page=thecartpress/admin/TaxonomyEdit.php&taxonomy=<?php echo $attribute->name; ?>" title="<?php _e( 'edit attribute', 'tcp-do' ); ?>"><?php _e( 'edit attribute', 'tcp-do' ); ?></a> |
			<a href="edit-tags.php?taxonomy=<?php echo $attribute->name; ?>&post_type=<?php echo TCP_DYNAMIC_OPTIONS_POST_TYPE; ?>" title="<?php _e( 'add terms', 'tcp-do' ); ?>"><?php _e( 'add terms', 'tcp-do' ); ?></a>
			<br />
		<?php }
	} else { ?>
		<p class="description"><?php printf( __( 'You need to create <a href="%s">attributes</a> for Dynamic Options, as Colours, Sizes, etc.', 'tcp-do' ), TCP_DYNAMIC_OPTIONS_ADMIN_PATH . 'AttributeList.php' ); ?></p>
	<?php } ?>

	</td>
</tr>
</thead>
</tbody>
</table>
<p><input type="submit" name="tcp_save" value="<?php _e( 'Save', 'tcp-do' ); ?>" class="button-primary" /></p>
</form>

</div><!-- .wrap-->
