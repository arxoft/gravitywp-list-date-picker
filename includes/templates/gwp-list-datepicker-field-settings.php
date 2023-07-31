<li class="list_datepicker_settings gwp_list_datepicker_settings field_setting">
	<label class="section_label"><?php esc_html_e( 'Datepicker', 'gravitywp-list-datepicker' ); ?></label>
	<input type="checkbox" id="list_choice_datepicker_single" onclick="SetFieldProperty( 'isDatePicker', this.checked);GWPListDatepicker.updateDatepickerSettings( 'list','single' );">
	<label class="inline" for="list_choice_datepicker_single">
<?php esc_html_e( 'Make Datepicker', 'gravitywplistdatepicker' ); ?>
<?php gform_tooltip( 'gwp_list_datepicker' ); ?>
	</label>
	<div style='display:none; background: rgb(244, 244, 244) none repeat scroll 0px 0px; padding: 10px; border-bottom: 1px solid grey; margin: 10px 0;' class='gwp_list_datepicker_options_single'>
		<label for='list_choice_datepickerformat_single'><?php esc_html_e( 'Date Format', 'gravitywplistdatepicker' ); ?></label>
		<select class='choice_datepickerformat' id='list_choice_datepickerformat_single' onchange="GWPListDatepicker.updateDatepickerSettings( 'list', 'single' );" style='margin-bottom: 10px;' >
			<option value='mdy'>mm/dd/yyyy</option>
			<option value='dmy'>dd/mm/yyyy</option>
			<option value='dmy_dash'>dd-mm-yyyy</option>
			<option value='dmy_dot'>dd.mm.yyyy</option>
			<option value='ymd_slash'>yyyy/mm/dd</option>
			<option value='ymd_dash'>yyyy-mm-dd</option>
			<option value='ymd_dot'>yyyy.mm.dd</option>
		</select>
		<div class='datepickericon'>
			<input style='margin: 8px;' id='list_choice_datepickericonnone_single' type='radio' onclick="GWPListDatepicker.updateDatepickerSettings( 'list', 'single' );" value='itsg_list_field_datepicker_icon_none' name='list_field_datepicker_icon_single'>
			<label class='inline' for='list_choice_datepickericonnone_single'><?php esc_html_e( 'No Icon', 'gravitywplistdatepicker' ); ?> </label>
			<input style='margin: 8px;' id='list_choice_datepickericoncalendar_single' type='radio' onclick="GWPListDatepicker.updateDatepickerSettings( 'list', 'single' );" value='itsg_list_field_datepicker_icon_calendar' name='list_field_datepicker_icon_single'>
			<label class='inline' for='list_choice_datepickericoncalendar_single'><?php esc_html_e( 'Calendar Icon', 'gravitywplistdatepicker' ); ?> </label>
		</div>
		<br>
		<label for='list_choice_defaultdate_single'><?php esc_html_e( 'Default Date', 'gravitywplistdatepicker' ); ?></label>
		<input type='text' value="" class='datepicker_defaultdate' id='list_choice_defaultdate_single' onblur="GWPListDatepicker.updateDatepickerSettings( 'list', 'single' );">
	</div>
</li>
