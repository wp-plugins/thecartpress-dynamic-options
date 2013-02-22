<?php
/**
 * This file is part of TheCartPress-TierPrice.
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

//$post_id -> Parent id
//$options -> array of dyamic options

foreach( $options as $option_id ) if ( tcp_has_tier_price( $option_id ) ) : ?>
<div class="tcp_do_tier_price tcp_do_tier_price_<?php echo $option_id; ?>" style="display: none;">
	<span class="tcp_do_tier_price_title"><?php echo tcp_get_the_title( $option_id ); ?></span>
	<?php tcp_the_tier_price( $option_id, true ); ?>
</div>
<script>
jQuery( function() {
	var select = jQuery( '.tcp_dynamic_options_<?php echo $post_id; ?>' );
	select.change( function() {
		var id = jQuery( '.tcp_dynamic_options_<?php echo $post_id; ?> option:selected' ).val();
		if ( ! jQuery.isNumeric( id ) ) id = tcp_is_valid_value_<?php echo $post_id; ?>( id + ',' );
		jQuery( '.tcp_do_tier_price' ).hide();
		jQuery( '.tcp_do_tier_price_' + id ).show(  );
	} );
	select.change();
} );
</script>
<?php endif; ?>
