<?php
/**
 * Plugin Name: GravityWP - List Datepicker
 * Plugin URI: https://gravitywp.com/add-on/list-datepicker
 * Description: Adds a datepicker input to the Gravity Forms List field.
 * Version: 2.0.8
 * Requires PHP: 7.0
 * Author: GravityWP
 * Author URI: https://gravitywp.com
 * License: GPL-3.0+
 * Text Domain: gravitywplistdatepicker
 * Domain Path: /languages
 * Credits: Adrian Gordon for the initial Gravity Forms List Field Date Picker plugin.
 *
 * ------------------------------------------------------------------------
 * Copyright 2021 GravityWP.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses.
 *
 * @package    GravityWP_List_Datepicker
 * @subpackage Classes/GWP_List_Datepicker_Bootstrap
 */

defined( 'ABSPATH' ) || die();

// Defines the current version of the GravityWP List Datepicker Add-On.
define( 'GWP_LIST_DATEPICKER_FILE', __FILE__ );

// Defines the current version of the GravityWP List Datepicker Add-On.
define( 'GWP_LIST_DATEPICKER_VERSION', '2.0.8' );

// Defines the minimum version of Gravity Forms required to run GravityWP List Datepicker Add-On.
define( 'GWP_LIST_DATEPICKER_MIN_GF_VERSION', '2.5' );

// Initialize the autoloader.
require_once 'includes/autoload.php';

// After Gravity Forms is loaded, load the Add-On.
add_action( 'gform_loaded', array( 'GWP_List_Datepicker_Bootstrap', 'load_addon' ), 5 );

/**
 * Loads the GravityWP List Datepicker Add-On.
 *
 * Includes the main class and registers it with GFAddOn.
 *
 * @since 1.0
 */
class GWP_List_Datepicker_Bootstrap {

	/**
	 * Loads the required files.
	 *
	 * @since  1.0
	 * 
	 * @return void
	 */
	public static function load_addon() {

		// Autoloader for vendor libraries.
		require_once __DIR__ . '/lib/autoload.php';

		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		// Registers the class name with GFAddOn.
		GFAddOn::register( GravityWP\GravityWP_List_Datepicker\List_Datepicker::class );

	}

}

/**
 * Returns an instance of the List_Datepicker class
 *
 * @since  1.0
 *
 * @return GravityWP\GravityWP_List_Datepicker\List_Datepicker|bool An instance of the List_Datepicker class
 */
function gwp_list_datepicker() {

	return class_exists( 'GravityWP\GravityWP_List_Datepicker\List_Datepicker' ) ? GravityWP\GravityWP_List_Datepicker\List_Datepicker::get_instance() : false;

}
