<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://usoftware.co/
 * @since             1.0.0
 * @package           DFF Events
 *
 * @wordpress-plugin
 * Plugin Name:       DFF Events
 * Plugin URI:        https://usoftware.co/
 * Description:       Plugin for creation and publishing Events
 * Version:           1.0.0
 * Author:            usoftware
 * Author URI:        https://usoftware.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       events-main-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EVENTS_MAIN_PLUGIN_VERSION', '1.0.0' );

define( 'EVENTS_MAIN_PLUGIN_PATH', plugin_dir_path( __FILE__ ) ); 

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-events-main-plugin-activator.php
 */
function activate_events_main_plugin( $network_wide ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-events-main-plugin-activator.php';
    Events_Main_Plugin_Activator::activate( $network_wide );

    // if ( !wp_next_scheduled( 'cron_event_reminder' ) ) {
    //     wp_schedule_event( time(), 'daily', 'cron_event_reminder' );
    // }

}

// add_action( 'cron_event_reminder', 'cron_event_reminder_function' );
// function cron_event_reminder_function() {
//     require_once plugin_dir_path( __FILE__ ) . "admin/class-events-main-plugin-admin.php";
//     Events_Main_Plugin_Admin::cron_event_reminder();
// }


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-events-main-plugin-deactivator.php
 */
function deactivate_events_main_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-events-main-plugin-deactivator.php';
    Events_Main_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_events_main_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_events_main_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-events-main-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_events_main_plugin() {

    $plugin = new Events_Main_Plugin();
    $plugin->run();

}

run_events_main_plugin();
