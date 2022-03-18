<?php

/**
 * Fired during plugin activation
 *
 * @link       https://usoftware.co/
 * @since      1.0.0
 *
 * @package    Events_Main_Plugin
 * @subpackage Events_Main_Plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Events_Main_Plugin
 * @subpackage Events_Main_Plugin/includes
 * @author     usoftware <tech@usoftware.com>
 */
class Events_Main_Plugin_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate( $network_wide ) {

		/**Create thank-you page for Event registration */
		if ( is_multisite() && $network_wide ) { 
			$sites = get_sites( ['fields'=>'ids'] );
			if ( isset( $sites ) && ! empty( $sites ) ) {
				foreach ( $sites as $blog_id ) {
					switch_to_blog($blog_id);
						$page = get_page_by_path( 'event-registration-thank-you' );
						if ( !isset($page) ) {
							$thank_you_page_data = [
								'post_title' => 'Event Registration Thank You',
								'post_name' => 'event-registration-thank-you',
								'post_type' => 'page',
								'post_status'   => 'publish'
							];
							wp_insert_post($thank_you_page_data);
						}
					restore_current_blog();
				} 
			}
		}


		/**TODO activate only if MultilingualPress & sendgrid-email-delivery-simplified */

		// include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		// if ( is_plugin_active( 'multilingualpress/multilingualpress.php' ) ) {
				
		// 	// Deactivate the plugin
		// 	deactivate_events_main_plugin();
			
		// 	// Throw an error in the wordpress admin console
		// 	$error_message = 'This plugin requires <a href="https://multilingualpress.org/">MultilingualPress</a> plugin to be active!';
		// 	die($error_message);
		// }


		// if ( !in_array( 'multilingualpress/multilingualpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) 
		// // || !in_array( 'sendgrid-email-delivery-simplified/wpsendgrid.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) 
		// ) {
				
		// 		// Deactivate the plugin
		// 		deactivate_events_main_plugin();
				
		// 		// Throw an error in the wordpress admin console
		// 		// $error_message = 'This plugin requires <a href="https://multilingualpress.org/">MultilingualPress</a> plugin to be active!';
		// 		$error_message = var_dump( apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		// 		die($error_message);
				
		// }

		/** refresh permalinks */
		flush_rewrite_rules();

	}

}
