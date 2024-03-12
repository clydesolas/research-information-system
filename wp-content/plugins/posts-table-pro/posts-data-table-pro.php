<?php
/**
 * The main plugin file for Posts Table Pro.
 *
 * This file is included during the WordPress bootstrap process if the plugin is active.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 *
 * @wordpress-plugin
 * Plugin Name:     Posts Table Pro
 * Plugin URI:      https://barn2.com/wordpress-plugins/posts-table-pro/
 * Description:     Display your site's posts, pages, and custom post types in a sortable, searchable and filterable data table.
 * Version:         2.4.1
 * Author:          Barn2 Plugins
 * Author URI:      https://barn2.com
 * Text Domain:     posts-table-pro
 * Domain Path:     /languages
 *
 * Copyright:       Barn2 Media Ltd
 * License:         GNU General Public License v3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Barn2\Plugin\Posts_Table_Pro;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PLUGIN_FILE    = __FILE__;
const PLUGIN_VERSION = '2.4.1';

// Include autoloader.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Helper function to access the shared plugin instance.
 *
 * @return Plugin The plugin instance.
 */
function ptp() {
	return Plugin_Factory::create( PLUGIN_FILE, PLUGIN_VERSION );
}

// Load the plugin.
ptp()->register();
