/* eslint-disable linebreak-style */
/* eslint-disable camelcase */
import '../css/style.scss';

function gwp_maybe_apply_min_max_on_datepicker(datepicker_input) {

	let minDate = jQuery(datepicker_input).data('min-date');
	let maxDate = jQuery(datepicker_input).data('max-date');
	
	if(minDate) {
		jQuery(datepicker_input).datepicker('option', 'minDate', minDate);
	}

	if(maxDate) {
		jQuery(datepicker_input).datepicker('option', 'maxDate', maxDate);
	}
}

function gwp_list_datepicker_add_list_item( new_row ) {
	// run for each existing list row
	jQuery( new_row ).find( 'input.datepicker' ).each( function() {
		const datepickerField = jQuery( this );
		datepickerField.removeClass( 'hasDatepicker' ).removeAttr( 'id' ).removeClass( 'initialized' );
		datepickerField.unbind( '.datepicker' );
		datepickerField.datepicker( 'destroy' );
		const datepickerIcon = datepickerField.next( '.ui-datepicker-trigger' );
		// preserve the datepicker icon url.
		datepickerField.parent().siblings( "[id^='gforms_calendar_icon_input']" ).val( datepickerIcon.attr('src') );
		datepickerIcon.remove();
	} );

	// Init the datepickers.
	gformInitDatepicker();

	// Set default value.
	jQuery( new_row ).find( 'input.datepicker' ).each( function() {
		if ( '' == jQuery( this ).val() ) {
			const field_default_date = jQuery( this ).data( 'default-date' );
			jQuery( this ).val( field_default_date );
		}
	});

	// Set Min date and Max date on the new datepicker field
	jQuery( new_row ).find( 'input.datepicker' ).each( function() {

		gwp_maybe_apply_min_max_on_datepicker(this);
	});
}

// runs the main function when the page loads
jQuery( document ).bind( 'gform_post_render', function( $ ) {
	// Init date pickers.
	gformInitDatepicker();

	// Apply min date and max date limit on existing date pickers
	jQuery.each(jQuery('input.datepicker'), function() {
		gwp_maybe_apply_min_max_on_datepicker(this);
	});

} );

gform.addAction( 'gform_list_post_item_add', function ( item, container ) {
    gwp_list_datepicker_add_list_item( item );
} );
