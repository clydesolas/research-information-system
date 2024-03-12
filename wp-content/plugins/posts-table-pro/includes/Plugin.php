<?php
namespace Barn2\Plugin\Posts_Table_Pro;

use Barn2\PTP_Lib\Registerable,
	Barn2\PTP_Lib\Translatable,
	Barn2\PTP_Lib\Service_Provider,
	Barn2\PTP_Lib\Plugin\Premium_Plugin,
	Barn2\PTP_Lib\Plugin\Licensed_Plugin,
	Barn2\PTP_Lib\Service_Container;

/**
 * The main plugin class. Responsible for setting up to core plugin services.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Premium_Plugin implements Registerable, Translatable, Service_Provider, Licensed_Plugin {

	use Service_Container;

	const NAME    = 'Posts Table Pro';
	const ITEM_ID = 8381;

	/**
	 * Constructs and initializes the plugin data.
	 *
	 * @param string $file The path to the main plugin file.
	 * @param string $version The current plugin version.
	 */
	public function __construct( $file = null, $version = '1.0' ) {
		parent::__construct( [
			'name'               => self::NAME,
			'item_id'            => self::ITEM_ID,
			'version'            => $version,
			'file'               => $file,
			'settings_path'      => 'options-general.php?page=posts_table',
			'documentation_path' => 'kb-categories/posts-table-pro-kb/',
			'legacy_db_prefix'   => 'ptp'
		] );
	}

	/**
	 * Registers the plugin with WordPress.
	 */
	public function register() {
		parent::register();

		add_action( 'plugins_loaded', [ $this, 'register_services' ] );
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'posts-table-pro', false, $this->get_slug() . '/languages' );
	}

	public function get_services() {
		$services = [
			'admin'         => new Admin\Admin_Controller( $this ),
			'settings_page' => new Admin\Settings_Page( $this )
		];

		// Initialize plugin if valid
		if ( $this->has_valid_license() ) {
			$services['frontend_scripts']  = new Frontend_Scripts( $this );
			$services['ajax_handler']      = new Ajax_Handler();
			$services['table_shortcode']   = new Table_Shortcode();
			$services['theme_integration'] = new Integration\Theme_Integration();
		}

		return $services;
	}

}
