<?php
/**
 * Save settings fields options.
 */
$locale_submit_settings = filter_input( INPUT_POST, 'locale_submit_settings', FILTER_SANITIZE_STRING );

if ( isset( $locale_submit_settings ) ) {

    $local_site_key   = dffmain_is_var_empty( filter_input( INPUT_POST, 'local_site_key', FILTER_SANITIZE_STRING ) );
    $local_secret_key = dffmain_is_var_empty( filter_input( INPUT_POST, 'local_secret_key', FILTER_SANITIZE_STRING ) );
    $local_overview   = dffmain_is_var_empty( filter_input( INPUT_POST, 'local_overview', FILTER_SANITIZE_STRING ) );
	$local_agenda     = dffmain_is_var_empty( filter_input( INPUT_POST, 'local_agenda', FILTER_SANITIZE_STRING ) );

    $local_registration_form = filter_input( INPUT_POST, 'local_registration_form', FILTER_SANITIZE_STRING );
	$local_registration_form = isset( $local_registration_form ) ? $local_registration_form : 'Registration Form';

    $local_registration_is_closed = filter_input( INPUT_POST, 'local_registration_is_closed', FILTER_SANITIZE_STRING );
	$local_registration_is_closed = isset( $local_registration_is_closed ) ? $local_registration_is_closed : 'Registration is closed';

	$local_date = filter_input( INPUT_POST, 'local_date', FILTER_SANITIZE_STRING );
	$local_date = isset( $local_date ) ? $local_date : 'Date';

	$local_time = filter_input( INPUT_POST, 'local_time', FILTER_SANITIZE_STRING );
	$local_time = isset( $local_time ) ? $local_time : 'Time';

	$local_cost = filter_input( INPUT_POST, 'local_cost', FILTER_SANITIZE_STRING );
	$local_cost = isset( $local_cost ) ? $local_cost : 'Cost';

	$local_location = filter_input( INPUT_POST, 'local_location', FILTER_SANITIZE_STRING );
	$local_location = isset( $local_location ) ? $local_location : 'Location';

	$local_category = filter_input( INPUT_POST, 'local_category', FILTER_SANITIZE_STRING );
	$local_category = isset( $local_category ) ? $local_category : 'Category';

	$local_register = filter_input( INPUT_POST, 'local_register', FILTER_SANITIZE_STRING );
	$local_register = isset( $local_register ) ? $local_register : 'Register';

	$local_remaining_seats = filter_input( INPUT_POST, 'local_remaining_seats', FILTER_SANITIZE_STRING );
	$local_remaining_seats = isset( $local_remaining_seats ) ? $local_remaining_seats : 'Remaining Seats';

	$local_events = filter_input( INPUT_POST, 'local_events', FILTER_SANITIZE_STRING );
	$local_events = isset( $local_events ) ? $local_events : 'Events';

	$local_event_details = filter_input( INPUT_POST, 'local_event_details', FILTER_SANITIZE_STRING );
	$local_event_details = isset( $local_event_details ) ? $local_event_details : 'Event Details';


	$local_settings_array = [];

    $local_settings_array['local_site_key'] = $local_site_key;
    $local_settings_array['local_secret_key'] = $local_secret_key;

    $local_settings_array['local_overview']               = $local_overview;
	$local_settings_array['local_agenda']                 = $local_agenda;
	$local_settings_array['local_registration_form']      = $local_registration_form;
	$local_settings_array['local_registration_is_closed'] = $local_registration_is_closed;
	$local_settings_array['local_date']                   = $local_date;
	$local_settings_array['local_time']                   = $local_time;
	$local_settings_array['local_cost']                   = $local_cost;
	$local_settings_array['local_location']               = $local_location;
	$local_settings_array['local_category']               = $local_category;
	$local_settings_array['local_register']               = $local_register;
	$local_settings_array['local_remaining_seats']        = $local_remaining_seats;
	$local_settings_array['local_events']                 = $local_events;
	$local_settings_array['local_event_details']          = $local_event_details;

    $local_events_general_settings = wp_json_encode( $local_settings_array );
	update_option( 'locale_events_general_settings', $local_events_general_settings, false );
}

$locale_settings_array_get          = get_option( 'locale_events_general_settings' );
$locale_events_general_settings_get = json_decode( $locale_settings_array_get );
$locale_events_general_settings_get = (array) $locale_events_general_settings_get;

?>
<div class="wrap news_master_settings_section">
	<h1>
		Events Settings
	</h1>
	<div class="event_general_section">
        <div id="config">
            <form action="edit.php?post_type=dffmain-events&page=diffmain-events-settings-page" method="post">

                <div class="page_section google_recaptcha_credentials">
                    <h3>
                        Google Recaptcha Credentials
                    </h3>

                    <?php
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_site_key', 'Enter Site Key', true );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_secret_key', 'Enter Secret Key', true );
                    ?>
                </div>

                <div class="page_section single_event_translations google_recaptcha_credentials">
                    <h3>
                        Translate:
                    </h3>
                    <?php
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_overview', 'Overview' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_agenda', 'Agenda' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_registration_form', 'Registration Form' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_registration_is_closed', 'Registration is closed' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_date', 'Date' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_time', 'Time' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_cost', 'Cost' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_location', 'Location' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_category', 'Category' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_register', 'Register' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_remaining_seats', 'Remaining Seats' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_events', 'Events' );
                    diffmain_the_settins_imput( $locale_events_general_settings_get, 'local_event_details', 'Event Details' );
                    ?>
                   
                </div>

                <input 
                    type="submit" 
                    name="locale_submit_settings" 
                    id="locale_submit_settings" 
                    class="button button-primary"
                    value="Save Changes"
                >
            </form>
        </div>

	</div>

</div>
