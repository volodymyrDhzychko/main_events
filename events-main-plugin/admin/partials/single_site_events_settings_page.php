<?php
/**
 * Save settings fields options.
 */
$locale_submit_settings = filter_input( INPUT_POST, 'locale_submit_settings', FILTER_SANITIZE_STRING );
if ( isset( $locale_submit_settings ) ) {

    $local_site_key = filter_input( INPUT_POST, 'local_site_key', FILTER_SANITIZE_STRING );
	$local_site_key = isset( $local_site_key ) ? $local_site_key : '';

    $local_secret_key = filter_input( INPUT_POST, 'local_secret_key', FILTER_SANITIZE_STRING );
	$local_secret_key = isset( $local_secret_key ) ? $local_secret_key : '';


    $local_overview = filter_input( INPUT_POST, 'local_overview', FILTER_SANITIZE_STRING );
	$local_overview = isset( $local_overview ) ? $local_overview : 'Overview';

	$local_agenda = filter_input( INPUT_POST, 'local_agenda', FILTER_SANITIZE_STRING );
	$local_agenda = isset( $local_agenda ) ? $local_agenda : 'Agenda';

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

    $local_settings_array['local_overview'] = $local_overview;
	$local_settings_array['local_agenda'] = $local_agenda;
	$local_settings_array['local_registration_form'] = $local_registration_form;
	$local_settings_array['local_registration_is_closed'] = $local_registration_is_closed;
	$local_settings_array['local_date'] = $local_date;
	$local_settings_array['local_time'] = $local_time;
	$local_settings_array['local_cost'] = $local_cost;
	$local_settings_array['local_location'] = $local_location;
	$local_settings_array['local_category'] = $local_category;
	$local_settings_array['local_register'] = $local_register;
	$local_settings_array['local_remaining_seats'] = $local_remaining_seats;
	$local_settings_array['local_events'] = $local_events;
	$local_settings_array['local_event_details'] = $local_event_details;

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
                    <label for="local_site_key">
                        <span>
                            Enter Site Key
                        </span>
                        <input 
                            type="text" 
                            id="local_site_key"
                            name="local_site_key" 
                            placeholder="Enter Site Key"
                            value="<?php echo isset( $locale_events_general_settings_get['local_site_key'] ) ? esc_html( $locale_events_general_settings_get['local_site_key'] ) : ''; ?>"
                        >
                    </label>
                    <label for="local_secret_key">
                        <span>
                            Enter Secret Key
                        </span>
                        <input 
                            type="text" 
                            id="local_secret_key"
                            name="local_secret_key" 
                            placeholder="Enter Secret Key"
                            value="<?php echo isset( $locale_events_general_settings_get['local_secret_key'] ) ? esc_html( $locale_events_general_settings_get['local_secret_key'] ) : ''; ?>"
                        >
                    </label>
                </div>

                <div class="page_section single_event_translations google_recaptcha_credentials">
                    <h3>
                        Translate:
                    </h3>

                    <label for="local_overview">
                        <span>
                            Overview
                        </span>
                        <input 
                            type="text" 
                            id="local_overview"
                            name="local_overview"
                            value="<?php echo isset( $locale_events_general_settings_get['local_overview'] ) ? esc_html( $locale_events_general_settings_get['local_overview'] ) : 'Overview'; ?>"
                        >
                    </label>

                    <label for="local_agenda">
                        <span>
                            Agenda
                        </span>
                        <input 
                            type="text" 
                            id="local_agenda"
                            name="local_agenda"
                            value="<?php echo isset( $locale_events_general_settings_get['local_agenda'] ) ? esc_html( $locale_events_general_settings_get['local_agenda'] ) : 'Agenda'; ?>"
                        >
                    </label>

                    <label for="local_registration_form">
                        <span>
                            Registration Form
                        </span>
                        <input 
                            type="text" 
                            id="local_registration_form"
                            name="local_registration_form"
                            value="<?php echo isset( $locale_events_general_settings_get['local_registration_form'] ) ? esc_html( $locale_events_general_settings_get['local_registration_form'] ) : 'Registration Form'; ?>"
                        >
                    </label>

                    <label for="local_registration_is_closed">
                        <span>
                            Registration is close
                        </span>
                        <input 
                            type="text" 
                            id="local_registration_is_closed"
                            name="local_registration_is_closed"
                            value="<?php echo isset( $locale_events_general_settings_get['local_registration_is_closed'] ) ? esc_html( $locale_events_general_settings_get['local_registration_is_closed'] ) : 'Registration is closed'; ?>"
                        >
                    </label>

                    <label for="local_date">
                        <span>
                            Date
                        </span>
                        <input 
                            type="text" 
                            id="local_date"
                            name="local_date"
                            value="<?php echo isset( $locale_events_general_settings_get['local_date'] ) ? esc_html( $locale_events_general_settings_get['local_date'] ) : 'Date'; ?>"
                        >
                    </label>


                    <label for="local_time">
                        <span>
                            Time
                        </span>
                        <input 
                            type="text" 
                            id="local_time"
                            name="local_time"
                            value="<?php echo isset( $locale_events_general_settings_get['local_time'] ) ? esc_html( $locale_events_general_settings_get['local_time'] ) : 'Time'; ?>"
                        >
                    </label>

                    <label for="local_cost">
                        <span>
                            Cost
                        </span>
                        <input 
                            type="text" 
                            id="local_cost"
                            name="local_cost"
                            value="<?php echo isset( $locale_events_general_settings_get['local_cost'] ) ? esc_html( $locale_events_general_settings_get['local_cost'] ) : 'Cost'; ?>"
                        >
                    </label>


                    <label for="local_location">
                        <span>
                            Location
                        </span>
                        <input 
                            type="text" 
                            id="local_location"
                            name="local_location"
                            value="<?php echo isset( $locale_events_general_settings_get['local_location'] ) ? esc_html( $locale_events_general_settings_get['local_location'] ) : 'Location'; ?>"
                        >
                    </label>

                    <label for="local_category">
                        <span>
                            Category
                        </span>
                        <input 
                            type="text" 
                            id="local_category"
                            name="local_category"
                            value="<?php echo isset( $locale_events_general_settings_get['local_category'] ) ? esc_html( $locale_events_general_settings_get['local_category'] ) : 'Category'; ?>"
                        >
                    </label>

                    <label for="local_register">
                        <span>
                            Register
                        </span>
                        <input 
                            type="text" 
                            id="local_register"
                            name="local_register"
                            value="<?php echo isset( $locale_events_general_settings_get['local_register'] ) ? esc_html( $locale_events_general_settings_get['local_register'] ) : 'Register'; ?>"
                        >
                    </label>

                    <label for="local_remaining_seats">
                        <span>
                            Remaining Seats 
                        </span>
                        <input 
                            type="text" 
                            id="local_remaining_seats"
                            name="local_remaining_seats"
                            value="<?php echo isset( $locale_events_general_settings_get['local_remaining_seats'] ) ? esc_html( $locale_events_general_settings_get['local_remaining_seats'] ) : 'Remaining Seats'; ?>"
                        >
                    </label>

                    <label for="local_">
                        <span>
                            Events
                        </span>
                        <input 
                            type="text" 
                            id="local_events"
                            name="local_events"
                            value="<?php echo isset( $locale_events_general_settings_get['local_events'] ) ? esc_html( $locale_events_general_settings_get['local_events'] ) : 'Events'; ?>"
                        >
                    </label>

                    <label for="local_event_details">
                        <span>
                            Event Details
                        </span>
                        <input 
                            type="text" 
                            id="local_event_details"
                            name="local_event_details"
                            value="<?php echo isset( $locale_events_general_settings_get['local_event_details'] ) ? esc_html( $locale_events_general_settings_get['local_event_details'] ) : 'Event Details'; ?>"
                        >
                    </label>
                    
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
