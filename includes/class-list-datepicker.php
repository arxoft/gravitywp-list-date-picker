<?php
/**
 * List Datepicker Addon class.
 *
 * @package GravityWP_List_Datepicker
 */

namespace GravityWP\GravityWP_List_Datepicker;

defined( 'ABSPATH' ) || die();

use GF_Field;
use GFAPI;
use GFCommon;
use GFForms;
use GFAddOn;
use GFFormsModel;
use GravityWP\GravityWP_List_Datepicker\Field\Settings;
use GravityWP\GravityWP_List_Datepicker\Utils;
use RGFormsModel;

// Include the Gravity Forms Add-On Framework.
GFForms::include_addon_framework();

/**
 * GravityWP List Datepicker.
 *
 * @since      1.0
 * @package    GravityWP_List_Datepicker
 * @subpackage Classes/List_Datepicker
 * @author     GravityWP
 * @copyright  Copyright (c) 2021, GravityWP
 */
class List_Datepicker extends GFAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0
	 * @var    List_Datepicker $_instance If available, contains an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the GravityWP List Datepicker Add-On.
	 *
	 * @since  1.0
	 * @var    string $_version Contains the version.
	 */
	protected $_version = GWP_LIST_DATEPICKER_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = GWP_LIST_DATEPICKER_MIN_GF_VERSION;

	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gravitywplistdatepicker';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gravitywp-list-datepicker/listdatepicker.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = GWP_LIST_DATEPICKER_FILE;

	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since  1.0
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'https://gravitywp.com';

	/**
	 * Defines the title of this add-on.
	 *
	 * @since  1.0
	 * @var    string $_title The title of the add-on.
	 */
	protected $_title = 'GravityWP List Datepicker Add-On';

	/**
	 * Defines the short title of the add-on.
	 *
	 * @since  1.0
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'List Datepicker';

	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = false;

	/**
	 * Defines the capabilities needed for the GravityWP List Datepicker Add-On
	 *
	 * @since  1.0
	 * @access protected
	 * @var    array<string,string> $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = array( 'gravityforms_gravitywplistdatepicker', 'gravityforms_gravitywplistdatepicker_uninstall' );

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_gravitywplistdatepicker';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_gravitywplistdatepicker';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_gravitywplistdatepicker_uninstall';

	/**
	 * Store the initialized gwp license handler
	 *
	 * @since  1.0
	 * @access private
	 * @var    Object $_gwp_license_handler License Handler instance.
	 */
	private $_gwp_license_handler = null;

	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	public $gwp_site_slug = 'list-datepicker';

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since  1.0
	 *
	 * @return List_Datepicker $_instance An instance of the List_Datepicker class
	 */
	public static function get_instance() {

		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * Register initialization hooks.
	 *
	 * @since  1.0
	 * @return void
	 */
	public function init() {

		parent::init();

		if ( ! $this->is_gravityforms_supported() ) {
			return;
		}

		// Frontend filters.
		add_filter( 'gform_column_input_content', array( &$this, 'change_column_content' ), 10, 6 );
		add_filter( 'gform_validation', array( &$this, 'validate_datepicker_fields' ) );
	}

	/**
	 * Register admin initialization hooks.
	 *
	 * @since  1.0
	 * 
	 * @return void
	 */
	public function init_admin() {

		// Init license handler.
		if ( $this->_gwp_license_handler === null ) {
			$this->_gwp_license_handler = new GravityWP\LicenseHandler\LicenseHandler( __CLASS__, '3c0e1473-c7e6-433d-98d1-146ecae9e621', plugin_dir_path( __DIR__ ) . 'listdatepicker.php' );
		}

		parent::init_admin();

		if ( ! $this->is_gravityforms_supported() ) {
			return;
		}

		// Admin filters.
		add_filter( 'gform_tooltips', array( $this, 'field_datepicker_tooltip' ) );
		add_action( 'gform_field_standard_settings', array( $this, 'field_settings' ), 10, 2 );
	}

	/**
	 * Register scripts.
	 *
	 * @since  1.0
	 *
	 * @return array<mixed>
	 */
	public function scripts() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array(
			array(
				'handle'    => 'gform_datepicker_init',
				'deps'      => array(
					'jquery',
					'jquery-ui-datepicker',
					'gform_gravityforms',
					'gform_datepicker_legacy',
					'gform_i18n',
				),
				'src'       => GFCommon::get_base_url()  . "/js/datepicker{$min}.js",
				'enqueue'   => array(
					array( $this, 'frontend_script_callback' ),
				),
				'in_footer' => true,
			),
			array(
				'handle'    => 'gwp_listdatepicker_admin_js',
				'deps'      => array( 'jquery' ),
				'src'       => $this->get_base_url() . "/js/form_editor{$min}.js",
				'version'   => $this->_version,
				'enqueue'   => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
				'in_footer' => true,
				'callback'  => array( $this, 'localize_scripts_admin' ),
			),
			array(
				'handle'    => $this->get_slug() . '_scripts',
				'deps'      => array( 'jquery' ),
				'src'       => $this->get_base_url() . "/js/scripts{$min}.js",
				'version'   => $this->_version,
				'enqueue'   => array(
					array( $this, 'frontend_script_callback' ),
				),
				'in_footer' => true,
			),

		);

		return array_merge( parent::scripts(), $scripts );

	}

	/**
	 * Localize admin scripts.
	 *
	 * @param array<mixed> $form The form object.
	 * @param bool  $is_ajax True if this is an AJAX call.
	 * @return void
	 */
	public function localize_scripts_admin( $form, $is_ajax ) {
		$settings_array = array(
			'text_datepicker_title'        => esc_html__( 'Datepicker fields', 'gravitywplistdatepicker' ),
			'text_datepicker_instructions' => esc_html__( "Place a tick next to the column name to make it a date picker field. Select the date format from the 'Date Format' options.", 'gravitywplistdatepicker' ),
			'text_make_datepicker'         => esc_html__( 'Make Datepicker', 'gravitywplistdatepicker' ),
			'text_date_format'             => esc_html__( 'Date Format', 'gravitywplistdatepicker' ),
			'text_no_icon'                 => esc_html__( 'No Icon', 'gravitywplistdatepicker' ),
			'text_calendar_icon'           => esc_html__( 'Calendar Icon', 'gravitywplistdatepicker' ),
			'text_default_date'            => esc_html__( 'Default Date', 'gravitywplistdatepicker' ),
			'icon_url'                     => esc_url( GFCommon::get_base_url() ),
		);

		wp_localize_script( 'gwp_listdatepicker_admin_js', 'gwp_listdatepicker_admin_js_settings', $settings_array );
	}

	/**
	 * Register styles.
	 *
	 * @since  1.0
	 *
	 * @return array<mixed>
	 */
	public function styles() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$styles = array(
			array(
				'handle'  => $this->get_slug(),
				'src'     => $this->get_base_url() . "/css/style{$min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'frontend_styles_callback' ),
				),
			),
			array(
				'handle'  => 'gforms_datepicker_css',
				'src'     => GFCommon::get_base_url() . "/css/datepicker{$min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'frontend_styles_callback' ),
				),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	/**
	 * A temp fix to force the $_path value.
	 *
	 * Somehow our $_path value was loaded as "includes/listdatepicker.php" without respecting our property value when accessing $this->_path.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_path() {
		return 'gravitywp-list-datepicker/listdatepicker.php';
	}

	/**
	 * Returns the physical path of the plugins root folder.
	 *
	 * @since 1.0
	 *
	 * @param string $full_path The full path.
	 *
	 * @return string
	 */
	public function get_base_path( $full_path = '' ) {

		if ( empty( $full_path ) ) {
			// Change this from __FILE__ to __DIR__ because the main class is in the includes folder.
			$full_path = __DIR__;
		}

		$folder = basename( dirname( $full_path ) );

		return WP_PLUGIN_DIR . '/' . $folder;
	}

	/**
	 * Returns the url of the root folder of the current Add-On.
	 *
	 * @since 1.0
	 *
	 * @param string $full_path Optional. The full path to the plugin file.
	 *
	 * @return string
	 */
	public function get_base_url( $full_path = '' ) {

		if ( empty( $full_path ) ) {
			// Change this from $_full_path to __DIR__ because the main class is in the includes folder.
			$full_path = __DIR__;
		}

		return plugins_url( '', $full_path );

	}

	// # PLUGIN SETTINGS -----------------------------------------------------------------------------------------------
	/**
	 * Define plugin settings fields.
	 *
	 * @since  1.0.2
	 *
	 * @return array<mixed>
	 */
	public function plugin_settings_fields() {
		// Retrieve license fields.
		$license_fields = $this->_gwp_license_handler->plugin_settings_license_fields();
		$fields         = array( $license_fields );
		return $fields;
	}

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @author	Unknown
	 * @since	v0.0.1
	 * @version	v1.0.0	Wednesday, November 2nd, 2022.
	 * @access	public
	 * @return	string
	 */
	public function get_menu_icon(): string {
		return '<svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29.26 24.14"><defs><style>.cls-1{fill:#fff;stroke:#000;stroke-miterlimit:10;stroke-width:.5px;}</style></defs><path d="M25.26,1.05h1.91V.54c0-.17,.14-.31,.32-.31s.32,.14,.32,.31v.51h.53c.47,0,.85,.36,.85,.81V5.93c0,.45-.38,.81-.85,.81h-4.25c-.47,0-.85-.36-.85-.81V1.86c0-.45,.38-.81,.85-.81h.53V.54c0-.17,.14-.31,.32-.31s.32,.14,.32,.31v.51Zm-1.38,4.88c0,.11,.1,.2,.21,.2h4.25c.12,0,.21-.09,.21-.2V2.67h-4.67v3.25Z"/><path d="M25.26,9.83h1.91v-.51c0-.17,.14-.31,.32-.31s.32,.14,.32,.31v.51h.53c.47,0,.85,.36,.85,.81v4.07c0,.45-.38,.81-.85,.81h-4.25c-.47,0-.85-.37-.85-.81v-4.07c0-.45,.38-.81,.85-.81h.53v-.51c0-.17,.14-.31,.32-.31s.32,.14,.32,.31v.51Zm-1.38,4.88c0,.11,.1,.2,.21,.2h4.25c.12,0,.21-.09,.21-.2v-3.25h-4.67v3.25Z"/><path d="M25.26,18.45h1.91v-.51c0-.17,.14-.3,.32-.3s.32,.14,.32,.3v.51h.53c.47,0,.85,.36,.85,.81v4.07c0,.45-.38,.81-.85,.81h-4.25c-.47,0-.85-.37-.85-.81v-4.07c0-.45,.38-.81,.85-.81h.53v-.51c0-.17,.14-.3,.32-.3s.32,.14,.32,.3v.51Zm-1.38,4.88c0,.11,.1,.2,.21,.2h4.25c.12,0,.21-.09,.21-.2v-3.25h-4.67v3.25Z"/><rect class="cls-1" x=".26" y="2" width="20" height="4.5"/><rect class="cls-1" x=".25" y="9.85" width="20" height="4.5"/><rect class="cls-1" x=".25" y="18.85" width="20" height="4.5"/></svg>';
	}

	// # FIELD SETTINGS -----------------------------------------------------------------------------------------------
	/**
	 * Add standard field settings.
	 *
	 * @since 1.0
	 *
	 * @param int $position The position.
	 * @param int $form_id  The form id.
	 * @return void
	 */
	public function field_settings( $position, $form_id ) {
		if ( $position === 1287 ) {
			include_once trailingslashit( gwp_list_datepicker()->get_base_path() ) . 'includes/templates/gwp-list-datepicker-field-settings.php';
		}
	}

	// # FILTER FIELD VALUES -----------------------------------------------------------------------------------------------
	/**
	 * Validate List Datepicker
	 *
	 * @todo hook into gform_field_validation instead of gform_validation
	 *
	 * @param array<mixed>$validation_result Array with validation results.
	 *
	 * @return array<mixed>
	 */
	public function validate_datepicker_fields( $validation_result ) {
		$form = $validation_result['form'];
		if ( is_array( $form ) && self::is_list_datepicker_settings_enabled( $form ) ) {
			$current_page = rgpost( 'gform_source_page_number_' . $form['id'] ) ? rgpost( 'gform_source_page_number_' . $form['id'] ) : 1;
			foreach ( $form['fields'] as &$field ) {
				$field_page = $field->pageNumber;
				$is_hidden  = RGFormsModel::is_field_hidden( $form, $field, array() );
				if ( $field_page != $current_page || $is_hidden ) {
					continue;
				}
				$has_columns = is_array( $field->choices );
				if ( $has_columns ) {
					$number_of_columns = count( $field->choices );
					$column_number     = 0;
					$value             = rgpost( "input_{$field->id}" );
					if ( is_array( $value ) ) {
						foreach ( $value as $key => $column_value ) {
							if ( true == rgar( $field['choices'][ $column_number ], 'isDatePicker' ) ) {
								if ( apply_filters( 'itsg_list_field_datepicker_disable_validation', false, $form['id'], $field->id, $column_number + 1 ) ) {
									break;
								}
								$default_format = apply_filters( 'itsg_list_field_datepicker_default_format', 'mdy', $form['id'], $field->id, $column_number + 1 );
								$date_format    = '' !== rgar( $field['choices'][ $column_number ], 'isDatePickerFormat' ) ? $field['choices'][ $column_number ]['isDatePickerFormat'] : $default_format;
								$value          = $column_value;
								if ( ! empty( $value ) ) {
									$date = GFCommon::parse_date( $value, $date_format );
									if ( empty( $date ) || ! $this->checkdate( $date['month'], $date['day'], $date['year'] ) ) {
										$validation_result['is_valid'] = false; // Set the form validation to false.
										$field->failed_validation      = true;
										$format_name                   = '';
										switch ( $date_format ) {
											case 'mdy':
												$format_name = 'mm/dd/yyyy';
												break;
											case 'dmy':
												$format_name = 'dd/mm/yyyy';
												break;
											case 'dmy_dash':
												$format_name = 'dd-mm-yyyy';
												break;
											case 'dmy_dot':
												$format_name = 'dd.mm.yyyy';
												break;
											case 'ymd_slash':
												$format_name = 'yyyy/mm/dd';
												break;
											case 'ymd_dash':
												$format_name = 'yyyy-mm-dd';
												break;
											case 'ymd_dot':
												$format_name = 'yyyy.mm.dd';
												break;
										}
										$message                   = sprintf( esc_html__( "The column '%1\$s' requires a valid date in %2\$s format.", 'gravitywplistdatepicker' ), $field['choices'][ $column_number ]['text'], $format_name );
										$field->validation_message = $message;
									}
								}
							}
							if ( $column_number >= ( $number_of_columns - 1 ) ) {
								$column_number = 0; // reset column number.
							} else {
								$column_number++; // increment column number.
							}
						}
					}
				} elseif ( true == $field->isDatePicker ) {
					$default_format = apply_filters( 'itsg_list_field_datepicker_default_format', 'mdy', $form['id'], $field['id'], 1 );
					$date_format    = '' !== $field->isDatePickerFormat ? $field['isDatePickerFormat'] : $default_format;
					$value          = rgpost( "input_{$field->id}" );
					if ( is_array( $value ) ) {
						foreach ( $value as $key => $column_value ) {
							$value = $column_value;
							if ( ! empty( $value ) ) {
								$date = GFCommon::parse_date( $value, $date_format );
								if ( empty( $date ) || ! $this->checkdate( $date['month'], $date['day'], $date['year'] ) ) {
									$validation_result['is_valid'] = false; // set the form validation to false.
									$field->failed_validation      = true;
									$format_name                   = '';
									switch ( $date_format ) {
										case 'mdy':
											$format_name = 'mm/dd/yyyy';
											break;
										case 'dmy':
											$format_name = 'dd/mm/yyyy';
											break;
										case 'dmy_dash':
											$format_name = 'dd-mm-yyyy';
											break;
										case 'dmy_dot':
											$format_name = 'dd.mm.yyyy';
											break;
										case 'ymd_slash':
											$format_name = 'yyyy/mm/dd';
											break;
										case 'ymd_dash':
											$format_name = 'yyyy-mm-dd';
											break;
										case 'ymd_dot':
											$format_name = 'yyyy.mm.dd';
											break;
									}
									/* translators: argument is a date format, like mm/dd/yyyy. */
									$message                   = sprintf( esc_html__( 'Requires a valid date in %s format.', 'gravitywplistdatepicker' ), $format_name );
									$field->validation_message = $message;
								}
							}
						}
					}
				}
			}
		}
		// Assign modified $form object back to the validation result.
		$validation_result['form'] = $form;
		return $validation_result;
	}

	/**
	 * Changes column field if 'date field' option is ticked. Adds 'datepicker' CSS class.
	 *
	 * @param string   $input The current HTML content of the List field column.
	 * @param array<mixed>    $input_info The input info array.
	 * @param GF_Field $field Field Object.
	 * @param string   $text Current column name.
	 * @param string   $value Currently entered/selected value for the column’s input.
	 * @param int      $form_id ID of current form.
	 * @return string
	 */
	public function change_column_content( $input, $input_info, $field, $text, $value, $form_id ) {
		if ( GFCommon::is_form_editor() ) {
			$has_columns = is_array( $field->choices );
			if ( $has_columns ) {
				foreach ( $field->choices as $choice ) {
					if ( $text === rgar( $choice, 'text' ) && true === rgar( $choice, 'isDatePicker' ) ) {
						$default_date = rgar( $choice, 'isDatePickerDefaultDate', '' );
						if ( $default_date === '' ) {
							$default_date = $this->get_date_format( rgar( $choice, 'isDatePickerFormat' ) );
						}
						$input = str_replace( "value='' ", "value='{$default_date}' ", $input );
						if ('itsg_list_field_datepicker_icon_none' != rgar( $choice, 'isDatePickerIcon' ) ){
							$input  = str_replace( '<input ', "<input style='width:80%' ", $input );
							$input .= '<img style="display:inline" id="gfield_input_datepicker_icon" src="' . GFCommon::get_base_url() . "/images/datepicker/datepicker.svg" .'">';
						}
						return $input;
					} elseif ( $text == rgar( $choice, 'text' ) ) {
						return $input;
					}
				}
			} else {
				if ( rgar( $field , 'isDatePicker' ) === true ) {
					$default_date = esc_html( rgar( $field, 'isDatePickerDefaultDate', '' ) );
					if ( $default_date === '' ) {
						$default_date = $this->get_date_format( rgar( $field, 'isDatePickerFormat' ) );
					}
					$input = str_replace( "value='' ", "value='{$default_date}' ", $input );
					if ( rgar( $field, 'isDatePickerIcon' ) !== 'itsg_list_field_datepicker_icon_none' ) {
						$input  = str_replace( '<input ', "<input style='width:80%' ", $input );
						$input .= '<img style="display:inline" id="gfield_input_datepicker_icon" src="' . GFCommon::get_base_url() . "/images/datepicker/datepicker.svg" . '">';
					}
					return $input;
				}
				return $input;
			}
		} else {
			$field_id      = $field->id;
			$column_number = 1;
			$has_columns   = is_array( $field->choices );

			if ( $has_columns && ! rgar( $field, 'gwreadonly_enable' ) ) {
				$number_of_columns = count( $field->choices );
				foreach ( $field->choices as $choice ) {
					if ( $text == rgar( $choice, 'text' ) && true == rgar( $choice, 'isDatePicker' ) ) {
						$default_date = esc_html( GFCommon::replace_variables_prepopulate( rgar( $choice, 'isDatePickerDefaultDate', '' ) ) );
						if ( $default_date !== '' ) {
							// add default date if value is empty.
							$input = str_replace( "value='' ", "value='{$default_date}' data-default-date='{$default_date}' ", $input );
							// add default data attribute.
							$input = str_replace( "<input ", "<input data-default-date='{$default_date}' ", $input );
						} else {
							$default_date = $this->get_date_format( rgar( $choice, 'isDatePickerFormat' ) );
							$input  = str_replace( '<input ', "<input placeholder='$default_date' ", $input );
						}						
						$default_format   = apply_filters( 'itsg_list_field_datepicker_default_format', 'mdy', $form_id, $field_id, $column_number );
						$date_format      = '' !== rgar( $choice, 'isDatePickerFormat' ) ? $choice['isDatePickerFormat'] : esc_html( $default_format );
						$datepicker_class = 'itsg_list_field_datepicker_icon_none' == rgar( $choice, 'isDatePickerIcon' ) ? 'datepicker_no_icon' : 'datepicker_with_icon gdatepicker_with_icon';
						$datepicker_class .= ' datepicker gform-datepicker';
						$input        = str_replace( '<input ', "<input class='{$datepicker_class} list_datepicker {$date_format}' ", $input );
						$input        = "<span class='ginput_container_list_date'>" . $input . '</span>';
						$input       .= "<input id='gforms_calendar_icon_input_{$form_id}_{$field_id}_{$column_number}' class='gform_hidden' type='hidden' aria-hidden='true' aria-label='hidden-calendar-icon-url' data-aria-label-template='hidden-calendar-icon-url' value='" . GFCommon::get_base_url() . "/images/datepicker/datepicker.svg'  />";
						return $input;
					} elseif ( $text == rgar( $choice, 'text' ) ) {
						return $input;
					}
					if ( $column_number >= ( $number_of_columns ) ) {
						$column_number = 1; // reset column number.
					} else {
						$column_number++; // increment column number.
					}
				}
			} else {
				if ( true == $field->isDatePicker ) {
					$default_date = esc_html( GFCommon::replace_variables_prepopulate( rgar( $field, 'isDatePickerDefaultDate', '' ) ) );
					if ( $default_date !== '' ) {
						// add default date if value is empty.
						$input = str_replace( "value='' ", "value='{$default_date}' data-default-date='{$default_date}' ", $input );
						// add default data attribute.
						$input = str_replace( "<input ", "<input data-default-date='{$default_date}' ", $input );
					} else {
						$default_date = $this->get_date_format( rgar( $field, 'isDatePickerFormat' ) );
						$input  = str_replace( '<input ', "<input placeholder='$default_date' ", $input );
					}
					$default_format   = apply_filters( 'itsg_list_field_datepicker_default_format', 'mdy', $form_id, $field_id, 1 );
					$date_format      = '' !== $field->isDatePickerFormat ? $field['isDatePickerFormat'] : esc_html( $default_format );
					$datepicker_class = $field->isDatePickerIcon === 'itsg_list_field_datepicker_icon_none' ? 'datepicker_no_icon datepicker' : 'datepicker_with_icon gdatepicker_with_icon';
					$datepicker_class .= ' datepicker gform-datepicker';
					$input        = str_replace( '<input ', "<input class='{$datepicker_class} list_datepicker {$date_format} ' ", $input );
					$input        = "<span class='ginput_container_list_date'>" . $input . '</span>';
					$input       .= "<input id='gforms_calendar_icon_input_{$form_id}_{$field_id}_{$column_number}' class='gform_hidden' type='hidden' aria-hidden='true' aria-label='hidden-calendar-icon-url' data-aria-label-template='hidden-calendar-icon-url' value='" . GFCommon::get_base_url() . "/images/datepicker/datepicker.svg'/>";
					return $input;
				}
				return $input;
			}
		}
		return $input;
	}

	/**
	 * Tooltip for for datepicker option.
	 *
	 * @param array<string,string> $tooltips Array of tooltips.
	 * @return array<string,string>
	 */
	public function field_datepicker_tooltip( $tooltips ) {
		$tooltips['gwp_list_datepicker'] = '<h6>' . esc_html__( 'Datepicker', 'gravitywplistdatepicker' ) . '</h6>' . esc_html__( 'Makes list field column a datepicker.', 'gravitywplistdatepicker' );
		return $tooltips;
	}

	// # HELPER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * The frontend styles callback.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $form The form object.
	 *
	 * @return bool
	 */
	public function frontend_styles_callback( $form ) {

		return $form && $this->is_list_datepicker_settings_enabled( $form );

	}

	/**
	 * Validate if date elements have proper format.
	 *
	 * @param string $month Month MM.
	 * @param string $day Day DD.
	 * @param string $year Year YYYY.
	 * @return bool
	 */
	public function checkdate( $month, $day, $year ) {
		if ( empty( $month ) || ! is_numeric( $month ) || empty( $day ) || ! is_numeric( $day ) || empty( $year ) || ! is_numeric( $year ) || strlen( $year ) != 4 ) {
			return false;
		}

		return checkdate( (int) $month, (int) $day, (int) $year );
	}

	/**
	 * Check if the listdatepicker settings enabled.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $form The Form object.
	 *
	 * @return bool
	 */
	private function is_list_datepicker_settings_enabled( $form ) {
		foreach ( $form['fields'] as $field ) {
			if ( 'list' == $field->get_input_type() ) {
				$has_columns = is_array( $field->choices );
				if ( $has_columns ) {
					foreach ( $field->choices as $choice ) {
						if ( true == rgar( $choice, 'isDatePicker' ) ) {
							return true;
						}
					}
				} elseif ( true == $field->isDatePicker ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Check if the fronted scripts should be enqueued.
	 *
	 * @since  1.0
	 *
	 * @param array<mixed> $form The form currently being processed.
	 *
	 * @return bool If the script should be enqueued.
	 */
	public function frontend_script_callback( $form ) {

		return $form && $this->is_list_datepicker_settings_enabled( $form );

	}

	/**
	 * Get the custom field settings value.
	 *
	 * @since 1.0
	 *
	 * @param GF_Field $field The field.
	 * @param string   $group The settings group.
	 * @param string   $key   The settings key.
	 *
	 * @return float|int|string|bool
	 */
	private function get_field_setting( $field, $group, $key ) {

		$value = rgars( $field->gwp, "$group/$key" );

		switch ( $key ) {

			case 'enabled':
				return boolval( $value );

			default:
				// Always escape the output.
				return esc_html( $value );

		}

	}

	/**
	 * Helper method to get the date format by type. Based on GF Date Field helper.
	 *
	 * @since 2.0
	 * @param string $format_value The value to be converted.
	 * @param string $type  The returned value type. Can be 'label' or 'screen_reader_text'.
	 *
	 * @return string
	 */
	private function get_date_format( $format_value, $type = 'label' ) {
		$format_label = '';

		switch ( $format_value ) {
			case 'mdy':
				if ( $type === 'label' ) {
					$format_label = esc_attr__( 'mm/dd/yyyy', 'gravityforms' );
				} else {
					$format_label = esc_attr__( 'MM slash DD slash YYYY', 'gravityforms' );
				}
				break;
			case 'dmy':
				if ( $type === 'label' ) {
					$format_label = esc_attr__( 'dd/mm/yyyy', 'gravityforms' );
				} else {
					$format_label = esc_attr__( 'DD slash MM slash YYYY', 'gravityforms' );
				}
				break;
			case 'dmy_dash':
				if ( $type === 'label' ) {
					$format_label = esc_attr__( 'dd-mm-yyyy', 'gravityforms' );
				} else {
					$format_label = esc_attr__( 'DD dash MM dash YYYY', 'gravityforms' );
				}
				break;
			case 'dmy_dot':
				if ( $type === 'label' ) {
					$format_label = esc_attr__( 'dd.mm.yyyy', 'gravityforms' );
				} else {
					$format_label = esc_attr__( 'DD dot MM dot YYYY', 'gravityforms' );
				}
				break;
			case 'ymd_slash':
				if ( $type === 'label' ) {
					$format_label = esc_attr__( 'yyyy/mm/dd', 'gravityforms' );
				} else {
					$format_label = esc_attr__( 'YYYY slash MM slash DD', 'gravityforms' );
				}
				break;
			case 'ymd_dash':
				if ( $type === 'label' ) {
					$format_label = esc_attr__( 'yyyy-mm-dd', 'gravityforms' );
				} else {
					$format_label = esc_attr__( 'YYYY dash MM dash DD', 'gravityforms' );
				}
				break;
			case 'ymd_dot':
				if ( $type === 'label' ) {
					$format_label = esc_attr__( 'yyyy.mm.dd', 'gravityforms' );
				} else {
					$format_label = esc_attr__( 'YYYY dot MM dot DD', 'gravityforms' );
				}
				break;
		}

		return $format_label;
	}

}
