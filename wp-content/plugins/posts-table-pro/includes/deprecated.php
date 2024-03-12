<?php

use Barn2\Plugin\Posts_Table_Pro\Plugin,
	Barn2\Plugin\Posts_Table_Pro\Frontend_Scripts,
	Barn2\Plugin\Posts_Table_Pro\Table_Shortcode,
	Barn2\Plugin\Posts_Table_Pro\Table_Query,
	Barn2\Plugin\Posts_Table_Pro\Ajax_Handler,
	Barn2\Plugin\Posts_Table_Pro\Table_Cache,
	Barn2\Plugin\Posts_Table_Pro\Table_Factory,
	Barn2\Plugin\Posts_Table_Pro\Posts_Table,
	Barn2\Plugin\Posts_Table_Pro\Config_Builder,
	Barn2\Plugin\Posts_Table_Pro\Table_Hooks,
	Barn2\Plugin\Posts_Table_Pro\Table_Columns,
	Barn2\Plugin\Posts_Table_Pro\Data\Data_Factory,
	Barn2\Plugin\Posts_Table_Pro\Util\Options,
	Barn2\Plugin\Posts_Table_Pro\Util\Util,
	Barn2\Plugin\Posts_Table_Pro\Util\Columns_Util,
	Barn2\Plugin\Posts_Table_Pro\Admin\Admin_Controller,
	Barn2\Plugin\Posts_Table_Pro\Admin\Settings_Page,
	Barn2\Plugin\Posts_Table_Pro\Table_Args,
	Barn2\PTP_Lib\Table\Table_Data_Interface,
	Barn2\Plugin\Posts_Table_Pro\Data\Abstract_Table_Data;
use const Barn2\Plugin\Posts_Table_Pro\PLUGIN_FILE,
		  Barn2\Plugin\Posts_Table_Pro\PLUGIN_VERSION;

add_action( 'plugins_loaded', function() {
	/**
	 * @deprecated 2.2 Don't use PTP_INCLUDES_DIR.
	 */
	if ( ! defined( 'PTP_INCLUDES_DIR' ) ) {
		define( 'PTP_INCLUDES_DIR', plugin_dir_path( PLUGIN_FILE ) . 'includes/' );
	}

	/**
	 * @deprecated 2.2 Don't use PTP_PLUGIN_BASENAME.
	 */
	if ( ! defined( 'PTP_PLUGIN_BASENAME' ) ) {
		define( 'PTP_PLUGIN_BASENAME', plugin_basename( PLUGIN_FILE ) );
	}
} );

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Plugin
 */
class Posts_Table_Pro_Plugin {

	const NAME    = Plugin::NAME;
	const VERSION = PLUGIN_VERSION;
	const FILE    = PLUGIN_FILE;

	private static $_instance = null;

	/**
	 * Get the single instance.
	 *
	 * @return \Posts_Table_Pro_Plugin
	 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\ptp
	 */
	public static function instance() {
		_deprecated_function( __METHOD__, '2.2', 'Barn2\Plugin\Posts_Table_Pro\ptp()' );

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

}

/**
 * Helper function to return the main plugin instance.
 *
 * @return \Posts_Table_Pro_Plugin The main plugin instance
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\ptp
 */
function Posts_Table_Pro() {
	_deprecated_function( __METHOD__, '2.2', 'Barn2\Plugin\Posts_Table_Pro\ptp()' );
	return Posts_Table_Pro_Plugin::instance();
}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Util\Util
 */
class Posts_Table_Util {

	/**
	 * @deprecated 2.2 Replaced by Barn2\PTP_Lib\Util::get_script_suffix()
	 */
	public static function get_script_suffix() {
		_deprecated_function( __METHOD__, '2.2', Barn2\PTP_Lib\Util::class . "::get_script_suffix" );
		return Barn2\PTP_Lib\Util::get_script_suffix();
	}

	/**
	 * @deprecated 2.2 Replaced by get_terms()
	 */
	public static function get_terms( $args ) {
		_deprecated_function( __METHOD__, '2.2', 'get_terms' );
		return get_terms( $args );
	}

	public static function __callStatic( $name, $args ) {
		if ( method_exists( Util::class, $name ) ) {
			_deprecated_function( __METHOD__, '2.2', Util::class . "::$name" );
			return call_user_func_array( [ Util::class, $name ], $args );
		}
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Util\Options
 */
class Posts_Table_Settings {

	const SHORTCODE_DEFAULTS_OPTION = Options::SHORTCODE_OPTION_KEY;
	const MISC_SETTINGS_OPTION      = Options::MISC_OPTION_KEY;

	/**
	 * @deprecated 2.2 Replaced by Options::get_shortcode_options()
	 */
	public static function get_setting_shortcode_defaults() {
		_deprecated_function( __METHOD__, '2.2', Options::class . '::get_shortcode_options' );
		return Options::get_shortcode_options();
	}

	/**
	 * @deprecated 2.2 Replaced by Options::get_additional_options()
	 */
	public static function get_setting_misc() {
		_deprecated_function( __METHOD__, '2.2', Options::class . '::get_additional_options' );
		return Options::get_additional_options();
	}

	/**
	 * @deprecated 2.2 Only used internally.
	 */
	public static function settings_to_shortcode_args( $settings ) {
		_deprecated_function( __METHOD__, '2.2' );
		return $settings;
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Frontend_Scripts
 */
class Posts_Table_Frontend_Scripts extends Frontend_Scripts {

	/**
	 * @deprecated 2.2 Replaced by Frontend_Scripts->register()
	 */
	public static function load_scripts() {
		_deprecated_function( __METHOD__, '2.2', Frontend_Scripts::class . '->register' );

		$scripts = new Frontend_Scripts();
		$scripts->register();
	}

	/**
	 * @deprecated 2.2 Replaced by Frontend_Scripts::load_table_scripts()
	 */
	public static function register_table_scripts( Posts_Table_Args $args ) {
		_deprecated_function( __METHOD__, '2.2', Frontend_Scripts::class . '::load_table_scripts' );

		Frontend_Scripts::load_table_scripts( $args );
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Ajax_Handler
 */
class Posts_Table_Ajax_Handler extends Ajax_Handler {

	/**
	 * @deprecated 2.2 Replaced by Ajax_Handler->register()
	 */
	public static function register_ajax_events() {
		_deprecated_function( __METHOD__, '2.2', Ajax_Handler::class . '->register' );

		$scripts = new Ajax_Handler();
		$scripts->register();
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Table_Shortcode
 */
class Posts_Table_Shortcode extends Table_Shortcode {

	/**
	 * @deprecated 2.2 Replaced by Table_Shortcode->register()
	 */
	public static function register_shortcode() {
		_deprecated_function( __METHOD__, '2.2', Table_Shortcode::class . '->register' );

		$sc = new Table_Shortcode();
		$sc->register();
	}

	public static function __callStatic( $name, $args ) {
		if ( method_exists( Table_Shortcode::class, $name ) ) {
			_deprecated_function( __METHOD__, '2.2', Table_Shortcode::class . "::$name" );
			return call_user_func_array( [ Table_Shortcode::class, $name ], $args );
		}
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Table_Factory
 */
class Posts_Table_Factory extends Table_Factory {

	public static function __callStatic( $name, $args ) {
		if ( method_exists( Table_Factory::class, $name ) ) {
			_deprecated_function( __METHOD__, '2.2', Table_Factory::class . "::$name" );
			return call_user_func_array( [ Table_Factory::class, $name ], $args );
		}
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Posts_Table
 */
class Posts_Data_Table extends Posts_Table {

	public function __construct( $id, $args = [] ) {
		_deprecated_function( __METHOD__, '2.2', Posts_Table::class );
		parent::__construct( $id, $args );
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Table_Cache
 */
class Posts_Table_Cache extends Table_Cache {

	public static function __callStatic( $name, $args ) {

		if ( method_exists( Table_Cache::class, $name ) ) {
			_deprecated_function( __METHOD__, '2.2', Table_Cache::class . "::$name" );
			return call_user_func_array( [ Table_Cache::class, $name ], $args );
		}
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Table_Query
 */
class Posts_Table_Query extends Table_Query {

	public function __construct( Table_Args $args ) {
		_deprecated_function( __METHOD__, '2.2', Table_Query::class );
		parent::__construct( $args );
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Table_Columns
 */
class Posts_Table_Columns extends Table_Columns {

	public function __construct( Table_Args $args ) {
		_deprecated_function( __METHOD__, '2.2', Table_Columns::class );
		parent::__construct( $args );
	}

	public static function __callStatic( $name, $args ) {
		// Static functions are replaced by Columns_Util.
		if ( method_exists( Columns_Util::class, $name ) ) {
			_deprecated_function( __METHOD__, '2.2', Columns_Util::class . "::$name" );
			return call_user_func_array( [ Columns_Util::class, $name ], $args );
		}
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Config_Builder
 */
class Posts_Table_Config_Builder extends Config_Builder {

	public function __construct( $id, Table_Args $args, Table_Columns $columns ) {
		_deprecated_function( __METHOD__, '2.2', Config_Builder::class );
		parent::__construct( $id, $args, $columns );
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Table_Hooks
 */
class Posts_Table_Hook_Manager extends Table_Hooks {

	public function __construct( Table_Args $args ) {
		_deprecated_function( __METHOD__, '2.2', Table_Hooks::class );
		parent::__construct( $args );
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Data_Factory
 */
class Posts_Table_Data_Factory extends Data_Factory {

	public function __construct( Table_Args $args ) {
		_deprecated_function( __METHOD__, '2.2', Data_Factory::class );
		parent::__construct( $args );
	}

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Title
 */
class Posts_Table_Data_Title extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Title {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Author
 */
class Posts_Table_Data_Author extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Author {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Categories
 */
class Posts_Table_Data_Categories extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Categories {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Content
 */
class Posts_Table_Data_Content extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Content {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Id
 */
class Posts_Table_Data_Id extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Id {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Image
 */
class Posts_Table_Data_Image extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Image {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Tags
 */
class Posts_Table_Data_Tags extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Tags {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Status
 */
class Posts_Table_Data_Status extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Status {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Excerpt
 */
class Posts_Table_Data_Excerpt extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Excerpt {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Date
 */
class Posts_Table_Data_Date extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Date {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Custom_Field
 */
class Posts_Table_Data_Custom_Field extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Custom_Field {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Post_Custom_Taxonomy
 */
class Posts_Table_Data_Custom_Taxonomy extends Barn2\Plugin\Posts_Table_Pro\Data\Post_Custom_Taxonomy {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Admin\Admin_Controller
 */
class Posts_Table_Admin_General extends Admin_Controller {

}

/**
 * @deprecated 2.2 Replaced by Barn2\Plugin\Posts_Table_Pro\Admin\Settings_Page
 */
class Posts_Table_Admin_Settings_Page extends Settings_Page {

}

/**
 * @deprecated 2.3 Replaced by Barn2\Plugin\Posts_Table_Pro\Table_Args
 */
class Posts_Table_Args extends Table_Args {

	public function __construct( array $args = [] ) {
		_deprecated_function( __METHOD__, '2.3', Table_Args::class );
		parent::__construct( $args );
	}

}

/**
 * @deprecated 2.3 Replaced by Barn2\PTP_Lib\Table\Table_Data_Interface
 */
interface Posts_Table_Data extends Table_Data_Interface {

}

/**
 * @deprecated 2.3 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Abstract_Table_Data
 */
abstract class Abstract_Posts_Table_Data extends Abstract_Table_Data {

	public function __construct( $post, $links = '' ) {
		_deprecated_function( __METHOD__, '2.3', Abstract_Table_Data::class );
		parent::__construct( $post, $links );
	}

}
