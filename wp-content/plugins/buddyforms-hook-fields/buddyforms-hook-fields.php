<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/*
 * Plugin Name: BuddyForms Hook Fields
 * Plugin URI: https://themekraft.com/products/buddyforms-hook-fields/
 * Description: BuddyForms Hook Fields
 * Version: 1.3.15
 * Author: ThemeKraft
 * Author URI: https://themekraft.com/buddyforms/
 * Licence: GPLv3
 * Network: false
 * Text Domain: buddyforms
 * Svn: buddyforms-hook-fields
 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */
//
// Check the plugin dependencies
//
add_action(
    'init',
    function () {
    require dirname( __FILE__ ) . '/includes/list-all-post-fields.php';
    require dirname( __FILE__ ) . '/includes/form-options.php';
    require dirname( __FILE__ ) . '/includes/templates-handler.php';
    require dirname( __FILE__ ) . '/includes/gutenberg/shortcodes-to-blocks.php';
    // Only Check for requirements in the admin
    if ( !is_admin() ) {
        return;
    }
    // Require TGM
    require dirname( __FILE__ ) . '/includes/resources/tgm/class-tgm-plugin-activation.php';
    // Hook required plugins function to the tgmpa_register action
    add_action( 'tgmpa_register', function () {
        // Create the required plugins array
        
        if ( !defined( 'BUDDYFORMS_PRO_VERSION' ) ) {
            $plugins['buddyforms'] = array(
                'name'     => 'BuddyForms',
                'slug'     => 'buddyforms',
                'required' => true,
            );
            $config = array(
                'id'           => 'buddyforms-tgmpa',
                'parent_slug'  => 'plugins.php',
                'capability'   => 'manage_options',
                'has_notices'  => true,
                'dismissable'  => false,
                'is_automatic' => true,
            );
            // Call the tgmpa function to register the required plugins
            tgmpa( $plugins, $config );
        }
    
    } );
},
    1,
    1
);
// Create a helper function for easy SDK access.
function bhf_fs()
{
    global  $bhf_fs ;
    
    if ( !isset( $bhf_fs ) ) {
        // Include Freemius SDK.
        
        if ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php' ) ) {
            // Try to load SDK from parent plugin folder.
            require_once dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php';
        } elseif ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php' ) ) {
            // Try to load SDK from premium parent plugin folder.
            require_once dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php';
        }
        
        try {
            $bhf_fs = fs_dynamic_init( array(
                'id'                             => '412',
                'slug'                           => 'buddyforms-hook-fields',
                'type'                           => 'plugin',
                'public_key'                     => 'pk_834e229dbe701030d3c9d497b9ad0',
                'is_premium'                     => false,
                'has_paid_plans'                 => false,
                'parent'                         => array(
                'id'         => '391',
                'slug'       => 'buddyforms',
                'public_key' => 'pk_dea3d8c1c831caf06cfea10c7114c',
                'name'       => 'BuddyForms',
            ),
                'menu'                           => array(
                'slug'       => 'edit.php?post_type=buddyforms-hook-fields',
                'first-path' => 'plugins.php',
                'support'    => false,
            ),
                'bundle_license_auto_activation' => true,
                'is_live'                        => true,
            ) );
        } catch ( Freemius_Exception $e ) {
            return false;
        }
    }
    
    return $bhf_fs;
}

function bhf_fs_is_parent_active_and_loaded()
{
    // Check if the parent's init SDK method exists.
    return function_exists( 'buddyforms_core_fs' );
}

function bhf_fs_is_parent_active()
{
    $active_plugins_basenames = get_option( 'active_plugins' );
    foreach ( $active_plugins_basenames as $plugin_basename ) {
        if ( 0 === strpos( $plugin_basename, 'buddyforms/' ) || 0 === strpos( $plugin_basename, 'buddyforms-premium/' ) ) {
            return true;
        }
    }
    return false;
}

function bhf_fs_init()
{
    
    if ( bhf_fs_is_parent_active_and_loaded() ) {
        // Init Freemius.
        bhf_fs();
        // Parent is active, add your init code here.
    }

}


if ( bhf_fs_is_parent_active_and_loaded() ) {
    // If parent already included, init add-on.
    bhf_fs_init();
} elseif ( bhf_fs_is_parent_active() ) {
    // Init add-on only after the parent is loaded.
    add_action( 'buddyforms_core_fs_loaded', 'bhf_fs_init' );
} else {
    // Even though the parent is not activated, execute add-on for activation / uninstall hooks.
    bhf_fs_init();
}
