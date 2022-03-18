<?php

/**
 * Class for Custom User Roles
 *
 * @link       https://usoftware.co/
 * @since      1.0.0
 *
 * @package    Events_Custom_UserRoles
 */

class Events_Custom_UserRoles {

	/**
	 * Class Constructor
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'dffmain_custom_roles' ) );
		add_action( 'admin_init', array( $this, 'dffmain_custom_caps' ) );

	}

	/**
	 * Custom Roles.
	 */
	public function dffmain_custom_roles() {
		// add the new user role
		add_role(
			'event_manager',
			'Event Manager'
		);

	}

	/**
	 * Custom Caps of the roles.
	 */
	public function dffmain_custom_caps() {
		$roles = array( 'administrator', 'event_manager' );

		foreach ( $roles as $role ) {

			$role = get_role( $role );

			if ( $role ) {
				// Events caps
				$role->add_cap( 'upload_files' );
				$role->add_cap( 'publish_dffmain_events' );
				$role->add_cap( 'edit_dffmain_events' );
				$role->add_cap( 'edit_others_dffmain_events' );
				$role->add_cap( 'delete_dffmain_events' );
				$role->add_cap( 'delete_others_dffmain_events' );
				$role->add_cap( 'read_private_dffmain_events' );
				$role->add_cap( 'edit_dffmain_event' );
				$role->add_cap( 'delete_dffmain_event' );
				$role->add_cap( 'read_dffmain_event' );

				// events_categories caps
				$role->add_cap( 'manage_dffmain_cats' );
				$role->add_cap( 'edit_dffmain_cats' );
				$role->add_cap( 'delete_dffmain_cats' );
				$role->add_cap( 'edit_dffmain_cats' );

				// events_tags caps
				$role->add_cap( 'edit_dffmain_tags' );
				$role->add_cap( 'manage_dffmain_tags' );
				$role->add_cap( 'delete_dffmain_tags' );
				$role->add_cap( 'edit_dffmain_tags' );

				// Attendees caps
				$role->add_cap( 'publish_attendees' );
				$role->add_cap( 'edit_attendees' );
				$role->add_cap( 'delete_attendees' );
				$role->add_cap( 'edit_others_attendees' );
				$role->add_cap( 'delete_others_attendees' );
				$role->add_cap( 'read_private_attendees' );
				$role->add_cap( 'edit_attendee' );
				$role->add_cap( 'delete_attendee' );
				$role->add_cap( 'read_attendees' );
				$role->add_cap( 'read_attendee' );
				$role->add_cap( 'create_attendees' );

			}
		}
	}
}

new Events_Custom_UserRoles();
