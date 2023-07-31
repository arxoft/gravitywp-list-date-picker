/* eslint-disable linebreak-style */
/* eslint-disable camelcase */
/* global RefreshSelectedFieldPreview, GetInputType, jQuery, rgars, rgar, GWPListDatepicker, gwp_listdatepicker_admin_js_settings, field, inputType */
// ADD datepicler options to list fields with multiple columns in form editor - hooks into existing GetFieldChoices function.
( function( w ) {
	// Preserve the original GF GetFieldChoices function.
	const GetFieldChoicesGF = w.GetFieldChoices;

	// Reassign GetFieldChoices to our function.
	w.GetFieldChoices = function() {
		// Capture output of the original GetFieldChoices function.
		let str;
		str = GetFieldChoicesGF.apply( this, [ field ] );

		// return early if no field choices present.
		if ( ( ! field.choices ) || Object.keys( field.choices ).length === 0 ) {
			return str;
		}

		// Add datepicker fields to each list field column.
		const inputType = GetInputType( field );
		if ( inputType === 'list' ) {
			for ( let i = 0; i < field.choices.length; i++ ) {
				const isDatePicker = ( typeof field.choices[ i ].isDatePicker !== 'undefined' && field.choices[ i ].isDatePicker ) ? 'checked' : '';
				const columnLabel = ( typeof field.choices[ i ].value !== 'undefined' ) ? String( field.choices[ i ].value ) : field.choices[ i ].text;
				const isDatePickerDefaultDate = typeof field.choices[ i ].isDatePickerDefaultDate !== 'undefined' ? field.choices[ i ].isDatePickerDefaultDate : '';
				const isDatePickerMinDate = typeof field.choices[ i ].isDatePickerMinDate !== 'undefined' ? field.choices[ i ].isDatePickerMinDate : '';
				const isDatePickerMaxDate = typeof field.choices[ i ].isDatePickerMaxDate !== 'undefined' ? field.choices[ i ].isDatePickerMaxDate : '';

				str += GWPListDatepicker.getSettingTemplate( i, inputType, columnLabel, isDatePicker, isDatePickerDefaultDate, isDatePickerMinDate, isDatePickerMaxDate);
			}
		}
		// work around to ensure icon is displayed - GF is in the habit of hiding the icon just after selecting the field.
		jQuery( '.ginput_container_list img#gfield_input_datepicker_icon' ).css( 'display', 'inline' );
		return str;
	};
}( window || {} ) );

( function( $ ) {
	window.GWPListDatepicker = function() {
		// Cannot use `self = this`; because `this` would be referred to the `window`.
		const self = GWPListDatepicker;
		self.init = function() {
			// Initialize the datepicker setting when field is opened in the form editor.
			$( document ).bind( 'gform_load_field_settings', function( event, field, form ) {
				self.initListDatepickerSettings( field );
			} );

			/* This doesn't seem to work, but it is not a dealbreaker. Disable it for now.
			// trigger for when column titles are updated.
			$( document ).on( 'change', '#gfield_settings_columns_container #field_columns li.field-choice-row', function( event, field, form ) {
				self.initListDatepickerSettings( event, field, form );
			} );
			*/

			// trigger when 'Enable multiple columns' is ticked
			$( document ).on( 'change', '#field_settings input[id=field_columns_enabled]', function( event, field ) {
				self.initListDatepickerSettings( field );
			} );

			// Update field preview in editor after edit.
			gform.addAction( 'gform_after_refresh_field_preview', function( field_id ) {
				return self.actionRefreshFieldPreview( field_id );
			} );

			// create field preview on load:
			gform.addAction( 'gform_after_get_field_markup', function( form, field, index ) {
				return self.actionRefreshFieldPreview( index );
			} );
		};

		/**
		 * Initialize the fields settings tab in the form editor.
		 *
		 * @param {*} field
		 */
		self.initListDatepickerSettings = function( field ) {
			const inputType = rgar( field, 'type' );
			if ( 'list' === inputType ) {
				if ( field.enableColumns ) {
					// handles displaying the date format option for multi column lists
					for ( let index = 0; index < field.choices.length; index++ ) {
						if ( field.choices[ index ].isDatePicker === true ) {
							$( '.gwp_list_datepicker_options_' + index ).show();
							self.loadDatepickerSettings( inputType, index );
							self.actionRefreshFieldPreview (field.id );
						} else {
							$( '.gwp_list_datepicker_options_' + index ).hide();
						}
					}
				} else {
					// Handle single column.
					$( 'li.gwp_list_datepicker_settings' ).show();
					if ( field.isDatePicker === true ) {
						$( '.gwp_list_datepicker_options_single' ).show();
						self.loadDatepickerSettings( inputType, 'single' );
						self.actionRefreshFieldPreview (field.id );
					} else {
						$( '.gwp_list_datepicker_options_single' ).hide();
					}
				}
			}
		};

		self.loadDatepickerSettings = function( inputType, index ) {
			if ( index === 'single' ) {
				$( '#' + inputType + '_choice_datepicker_' + index ).prop( 'checked', Boolean( rgar( field, 'isDatePicker' ) ) );
				$( '#' + inputType + '_choice_datepickerformat_' + index ).val( rgar( field, 'isDatePickerFormat' ) );
				$( '#' + inputType + '_choice_defaultdate_' + index ).val( rgar( field, 'isDatePickerDefaultDate' ) );
				$( '#' + inputType + '_choice_mindate_' + index ).val( rgar( field, 'isDatePickerMinDate' ) );
				$( '#' + inputType + '_choice_maxdate_' + index ).val( rgar( field, 'isDatePickerMaxDate' ) );

				let icon_value = rgar( field, 'isDatePickerIcon' );
				icon_value = icon_value === '' ? 'itsg_list_field_datepicker_icon_none' : icon_value;
				$( 'input:radio[name="list_field_datepicker_icon_' + index + '"]' ).filter( '[value=' + icon_value + ']' ).prop( 'checked', true );
			} else {
				$( '#' + inputType + '_choice_datepicker_' + index ).prop( 'checked', Boolean( rgars( field, 'choices/' + index + '/isDatePicker' ) ) );
				$( '#' + inputType + '_choice_datepickerformat_' + index ).val( rgars( field, 'choices/' + index + '/isDatePickerFormat' ) );
				$( '#' + inputType + '_choice_defaultdate_' + index ).val( rgars( field, 'choices/' + index + '/isDatePickerDefaultDate' ) );
				let icon_value = rgars( field, 'choices/' + index + '/isDatePickerIcon' );
				icon_value = icon_value === '' ? 'itsg_list_field_datepicker_icon_none' : icon_value;
				$( 'input:radio[name="list_field_datepicker_icon_' + index + '"]' ).filter( '[value=' + icon_value + ']' ).prop( 'checked', true );
			}
		};

		//this initializes the settings for multiple columns?.
		self.updateDatepickerSettings = function( inputType, index ) {
			if ( 'list' === inputType ) {
				const isDatePicker = $( '#' + inputType + '_choice_datepicker_' + index ).is( ':checked' );
				const isDatePickerFormat = $( '#' + inputType + '_choice_datepickerformat_' + index ).val();
				const isDatePickerIcon = $( 'input:radio[name=' + inputType + '_field_datepicker_icon_' + index + ']:checked' ).val();
				const isDatePickerDefaultDate = $( '#' + inputType + '_choice_defaultdate_' + index ).val();
				const isDatePickerMinDate = $( '#' + inputType + '_choice_mindate_' + index ).val();
				const isDatePickerMaxDate = $( '#' + inputType + '_choice_maxdate_' + index ).val();

				if ( index === 'single' ) {
					field.isDatePicker = isDatePicker;
					field.isDatePickerFormat = isDatePickerFormat;
					field.isDatePickerIcon = isDatePickerIcon;
					field.isDatePickerDefaultDate = isDatePickerDefaultDate;
					field.isDatePickerMinDate = isDatePickerMinDate;
					field.isDatePickerMaxDate = isDatePickerMaxDate;
				} else {
					field.choices[ index ].isDatePicker = isDatePicker;
					field.choices[ index ].isDatePickerFormat = isDatePickerFormat;
					field.choices[ index ].isDatePickerIcon = isDatePickerIcon;
					field.choices[ index ].isDatePickerDefaultDate = isDatePickerDefaultDate;
				}
			}

			self.initListDatepickerSettings(field);
			// Update the form editor preview.
			RefreshSelectedFieldPreview();
		};

		self.actionRefreshFieldPreview = function( field_id ) {
			field = GetSelectedField();
			if ( rgar( field, 'type' ) === 'list' ) {
				if ( field.enableColumns ) {
					for ( let index = 0; index < field.choices.length; index++ ) {
						const listDatepickerEnable = 'undefined' !== typeof field.choices[ index ].isDatePicker ? field.choices[ index ].isDatePicker : false;

						if ( true == listDatepickerEnable ) {
							const listDatePickerDefaultDate = ( 'undefined' !== typeof field.choices[ index ].isDatePickerDefaultDate ) ? field.choices[ index ].isDatePickerDefaultDate : '';
							const listDatePickerFormat = jQuery( '#list_choice_datepickerformat_' + index + ' option:selected' ).text();
							const previewValue = listDatePickerDefaultDate !== '' ? listDatePickerDefaultDate : listDatePickerFormat;

							const new_input = '<input type="text" disabled="disabled" value="' + previewValue + '">';
							const column = index + 1;
							jQuery( 'li#field_' + field.id + ' table.gfield_list_container tbody tr td:nth-child(' + column + ')' ).html( new_input );
							jQuery( '#field_' + field_id + ' .gfield_list_14_cell' + column + ' input' ).val( previewValue );
						}
					}
				} else if ( rgar( field, 'isDatepicker' ) ) {
					const listDatePickerDefaultDate = ( 'undefined' !== typeof field.isDatePickerDefaultDate ) ? field.isDatePickerDefaultDate : '';
					const listDatePickerFormat = jQuery( '#list_choice_datepickerformat_single option:selected' ).text();
					const previewValue = listDatePickerDefaultDate !== '' ? listDatePickerDefaultDate : listDatePickerFormat;
					const new_input = '<input type="text" disabled="disabled" value="' + previewValue + '">';
					jQuery( 'li#field_' + field.id + ' table.gfield_list_container tbody tr td.gfield_list_cell' ).html( new_input );
					jQuery( '#field_' + field_id + ' .ginput_container input' ).val( previewValue );
				}
			}
		};

		self.getSettingTemplate = function( i, inputType, columnLabel, isDatePicker, isDatePickerDefaultDate, isDatePickerMinDate, isDatePickerMaxDate ) {
			let str = '';
			if ( i === 0 ) {
				str += '<p><strong>' + gwp_listdatepicker_admin_js_settings.text_datepicker_title + '</strong><br>' + gwp_listdatepicker_admin_js_settings.text_datepicker_instructions + '</p>';
			}
			str += '<div>';
			str += "<input type='checkbox' name='choice_datepicker' id='" + inputType + '_choice_datepicker_' + i + "' " + isDatePicker + " onclick=\"GWPListDatepicker.updateDatepickerSettings( '" + inputType + "', " + i + ');" /> ';
			str += "<label class='inline' for='" + inputType + '_choice_datepicker_' + i + "'>" + columnLabel + ' - ' + gwp_listdatepicker_admin_js_settings.text_make_datepicker + '</label>';
			str += "<div style='display:none; background: rgb(244, 244, 244) none repeat scroll 0px 0px; padding: 10px; border-bottom: 1px solid grey; margin: 10px 0;' class='gwp_list_datepicker_options_" + i + "'>";
			str += "<label for='" + inputType + '_choice_datepickerformat_' + i + "'>";
			str += gwp_listdatepicker_admin_js_settings.text_date_format + '</label>';
			str += "<select class='choice_datepickerformat' id='" + inputType + '_choice_datepickerformat_' + i + "' onchange=\"GWPListDatepicker.updateDatepickerSettings( '" + inputType + "', " + i + ");\" style='margin-bottom: 10px;' >";
			str += "<option value='mdy'>mm/dd/yyyy</option>";
			str += "<option value='dmy'>dd/mm/yyyy</option>";
			str += "<option value='dmy_dash'>dd-mm-yyyy</option>";
			str += "<option value='dmy_dot'>dd.mm.yyyy</option>";
			str += "<option value='ymd_slash'>yyyy/mm/dd</option>";
			str += "<option value='ymd_dash'>yyyy-mm-dd</option>";
			str += "<option value='ymd_dot'>yyyy.mm.dd</option>";
			str += '</select>';
			str += "<div class='datepickericon'>";
			str += "<input style='margin: 8px;' id='" + inputType + '_choice_datepickericonnone_' + i + "' type='radio' onclick=\"GWPListDatepicker.updateDatepickerSettings( '" + inputType + "', " + i + ");\" value='itsg_list_field_datepicker_icon_none' name='" + inputType + '_field_datepicker_icon_' + i + "'>";
			str += "<label class='inline' for='" + inputType + '_choice_datepickericonnone_' + i + "'> " + gwp_listdatepicker_admin_js_settings.text_no_icon + ' </label>';
			str += "<input style='margin: 8px;' id='" + inputType + '_choice_datepickericoncalendar_' + i + "' type='radio' onclick=\"GWPListDatepicker.updateDatepickerSettings( '" + inputType + "', " + i + ");\" value='itsg_list_field_datepicker_icon_calendar' name='" + inputType + '_field_datepicker_icon_' + i + "'>";
			str += "<label class='inline' for='" + inputType + '_choice_datepickericoncalendar_' + i + "'> " + gwp_listdatepicker_admin_js_settings.text_calendar_icon + ' </label>';
			str += '</div>';
			str += '<br>';
			str += "<label for='" + inputType + '_choice_defaultdate_' + i + "'>";
			str += '' + gwp_listdatepicker_admin_js_settings.text_default_date + '</label>';
			str += "<input type='text' value=\"" + isDatePickerDefaultDate.replace( /"/g, '&quot;' ) + "\" class='choice_datepickerdefaultdate' id='" + inputType + '_choice_defaultdate_' + i + "' onblur=\"GWPListDatepicker.updateDatepickerSettings( '" + inputType + "', " + i + ' );">';

			// Min Date
			str += '<br>';
			str += "<label for='" + inputType + '_choice_mindate_' + i + "'>";
			str += '' + gwp_listdatepicker_admin_js_settings.text_min_date + '</label>';
			str += "<input type='text' value=\"" + isDatePickerMinDate.replace( /"/g, '&quot;' ) + "\" class='choice_datepickermindate' id='" + inputType + '_choice_mindate_' + i + "' onblur=\"GWPListDatepicker.updateDatepickerSettings( '" + inputType + "', " + i + ' );">';

			// Max Date
			str += '<br>';
			str += "<label for='" + inputType + '_choice_maxdate_' + i + "'>";
			str += '' + gwp_listdatepicker_admin_js_settings.text_max_date + '</label>';
			str += "<input type='text' value=\"" + isDatePickerMaxDate.replace( /"/g, '&quot;' ) + "\" class='choice_datepickermaxdate' id='" + inputType + '_choice_maxdate_' + i + "' onblur=\"GWPListDatepicker.updateDatepickerSettings( '" + inputType + "', " + i + ' );">';

			str += '</div>';
			str += '</div>';
			return str;
		};

		self.init();
	};

	$( document ).ready( GWPListDatepicker );
}( jQuery ) );

