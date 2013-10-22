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

function tcp_sortDropDownListByTextAsc() {
	jQuery( '.tcp_dynamic_options_select').each( function() {
		var id = jQuery( this ).attr( 'id' );
		//var selectedValue = jQuery( this ).val();
		jQuery( '#' + id ).html( jQuery( '#' + id + ' option' ).sort( function ( a, b ) {
			return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
		} ) );
		//jQuery( this ).val( selectedValue );
		jQuery( this ).val( jQuery( '#' + id + ' option:first' ).val() );
	} );
}

function tcp_sortDropDownListByTextDsc() {
	jQuery( '.tcp_dynamic_options_select').each( function() {
		var id = jQuery( this ).attr( 'id' );
		//var selectedValue = jQuery( this ).val();
		jQuery( '#' + id ).html( jQuery( '#' + id + ' option' ).sort( function ( a, b ) {
			return a.text == b.text ? 0 : a.text < b.text ? 1 : -1
		} ) );
		//jQuery( this ).val( selectedValue );
		jQuery( this ).val( jQuery( '#' + id + ' option:first' ).val() );
	} );
}
