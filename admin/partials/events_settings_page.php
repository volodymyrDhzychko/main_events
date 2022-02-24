<?php
/**
 * Get multilingualpress translations
 */
$translations = dffmain_mlp_get_translations();

/**
 * Save settings fields options.
 */
$submit_settings = filter_input( INPUT_POST, 'submit_settings', FILTER_SANITIZE_STRING );
if ( isset( $submit_settings ) ) {
	$send_grid_key = filter_input( INPUT_POST, 'send_grid_key', FILTER_SANITIZE_STRING );
	$send_grid_key = isset( $send_grid_key ) ? $send_grid_key : '';

	$send_grid_from_name = filter_input( INPUT_POST, 'send_grid_from_name', FILTER_SANITIZE_STRING );
	$send_grid_from_name = isset( $send_grid_from_name ) ? $send_grid_from_name : '';

	$send_grid_from_email = filter_input( INPUT_POST, 'send_grid_from_email', FILTER_SANITIZE_STRING );
	$send_grid_from_email = isset( $send_grid_from_email ) ? $send_grid_from_email : '';

	$site_key = filter_input( INPUT_POST, 'site_key', FILTER_SANITIZE_STRING );
	$site_key = isset( $site_key ) ? $site_key : '';

	$secret_key = filter_input( INPUT_POST, 'secret_key', FILTER_SANITIZE_STRING );
	$secret_key = isset( $secret_key ) ? $secret_key : '';

	$future_connect_id = filter_input( INPUT_POST, 'future_connect_id', FILTER_SANITIZE_STRING );
	$future_connect_id = isset( $future_connect_id ) ? $future_connect_id : '';

	$mailchimp_list_id = filter_input( INPUT_POST, 'mailchimp_list_id', FILTER_SANITIZE_STRING );
	$mailchimp_list_id = isset( $mailchimp_list_id ) ? $mailchimp_list_id : '';

	$mailchimp_auth_token = filter_input( INPUT_POST, 'mailchimp_auth_token', FILTER_SANITIZE_STRING );
	$mailchimp_auth_token = isset( $mailchimp_auth_token ) ? $mailchimp_auth_token : '';

	$settings_array                         = array();
	$settings_array['send_grid_key']        = $send_grid_key;
	$settings_array['send_grid_from_name']  = $send_grid_from_name;
	$settings_array['send_grid_from_email'] = $send_grid_from_email;
	$settings_array['site_key']             = $site_key;
	$settings_array['secret_key']           = $secret_key;
	$settings_array['future_connect_id']    = $future_connect_id;
	$settings_array['mailchimp_list_id']    = $mailchimp_list_id;
	$settings_array['mailchimp_auth_token'] = $mailchimp_auth_token;
	$events_general_settings                = wp_json_encode( $settings_array );
	update_option( 'events_general_settings', $events_general_settings, false );
}

/**
 * Save email template options.
 */
$submit = filter_input( INPUT_POST, 'submit', FILTER_SANITIZE_STRING );

foreach ( $translations as $translation ) {
	$language        = $translation->language();
	$language_name   = $language->isoName();
	$is_rtl          = $language->isRtl();

	if ( isset( $submit ) ) {

		$events_content_thank_you_after_registration = 'events_content_thank_you_after_registration_' . $language_name;
		$$events_content_thank_you_after_registration = isset( $_POST['events_content_thank_you_after_registration_' . $language_name] ) ? wp_kses_post( $_POST['events_content_thank_you_after_registration_' . $language_name] ) : '';
		$$events_content_thank_you_after_registration = $$events_content_thank_you_after_registration ? wpautop( $$events_content_thank_you_after_registration ) : '';
		$events_content_thank_you_after_registration_arr = get_option('events_content_thank_you_after_registration') ? get_option('events_content_thank_you_after_registration') : [];
		$events_content_thank_you_after_registration_arr[$language_name] = $$events_content_thank_you_after_registration;
		update_option( 'events_content_thank_you_after_registration', $events_content_thank_you_after_registration_arr, false );


		$events_content_event_reminder = 'events_content_event_reminder' . $language_name;
		$$events_content_event_reminder = isset( $_POST['events_content_event_reminder_' . $language_name] ) ? wp_kses_post( $_POST['events_content_event_reminder_' . $language_name] ) : '';
		$$events_content_event_reminder = $$events_content_event_reminder ? wp_kses_post( wpautop( $$events_content_event_reminder ) ) : '';
		$events_content_event_reminder_arr = get_option('events_content_event_reminder') ? get_option('events_content_event_reminder') : [];
		$events_content_event_reminder_arr[$language_name] = $$events_content_event_reminder;
		update_option( 'events_content_event_reminder', $events_content_event_reminder_arr, false );


		$events_content_event_cancel = 'events_content_event_cancel_' . $language_name;
		$$events_content_event_cancel = isset( $_POST['events_content_event_cancel_' . $language_name] ) ? wp_kses_post( $_POST['events_content_event_cancel_' . $language_name] ) : '';
		$$events_content_event_cancel = isset( $$events_content_event_cancel ) ? wp_kses_post( wpautop( $$events_content_event_cancel ) ) : '';
		$events_content_event_cancel_arr = get_option('events_content_event_cancel') ? get_option('events_content_event_cancel') : [];
		$events_content_event_cancel_arr[$language_name] = $$events_content_event_cancel;
		update_option( 'events_content_event_cancel', $events_content_event_cancel_arr, false );


		$subject_thank_you = 'subject_thank_you_' .$language_name;
		$$subject_thank_you = isset( $_POST['subject_thank_you_' .$language_name] ) ? $_POST['subject_thank_you_' .$language_name] : '';
		$subject_thank_you_arr = get_option('subject_thank_you') ? get_option('subject_thank_you') : [];
		$subject_thank_you_arr[$language_name] = $$subject_thank_you;
		update_option( 'subject_thank_you', $subject_thank_you_arr, false );


		$subject_event_reminder = 'subject_event_reminder_' .$language_name;
		$$subject_event_reminder = isset( $_POST['subject_event_reminder_' .$language_name] ) ? wp_kses_post( $_POST['subject_event_reminder_'. $language_name] ) : '';
		$subject_event_reminder_arr = get_option('subject_event_reminder') ? get_option('subject_event_reminder') : [];
		$subject_event_reminder_arr[$language_name] = $$subject_event_reminder;
		update_option( 'subject_event_reminder', $subject_event_reminder_arr, false );


		$subject_event_cancel = 'subject_event_cancel_' . $language_name;
		$$subject_event_cancel = isset( $_POST['subject_event_cancel_' . $language_name] ) ? wp_kses_post( $_POST['subject_event_cancel_' . $language_name] ) : '';
		$subject_event_cancel_arr = get_option('subject_event_cancel') ? get_option('subject_event_cancel') : [];
		$subject_event_cancel_arr[$language_name] = $$subject_event_cancel;
		update_option( 'subject_event_cancel', $subject_event_cancel_arr, false );
	}

	$settings_array_get          = get_option( 'events_general_settings' );
	$events_general_settings_get = json_decode( $settings_array_get );
	$events_general_settings_get = (array) $events_general_settings_get;

	$events_content_thank_you_after_registration = get_option( 'events_content_thank_you_after_registration' );
	$events_content_event_reminder               = get_option( 'events_content_event_reminder' );
	$events_content_event_cancel                 = get_option( 'events_content_event_cancel' );

	$subject_thank_you = get_option( 'subject_thank_you' );
	$subject_event_reminder = get_option( 'subject_event_reminder' );
	$subject_event_cancel   = get_option( 'subject_event_cancel' );
}
?>
<div class="wrap news_master_settings_section">
	<h1>
		Events Settings
	</h1>
	<div class="event_general_section">
		<?php
		$update_settings = filter_input( INPUT_GET, 'update', FILTER_SANITIZE_STRING );
		$update_settings = isset( $update_settings ) ? $update_settings : '';

		$update_email_temp = filter_input( INPUT_GET, 'email_temp', FILTER_SANITIZE_STRING );
		$update_email_temp = isset( $update_email_temp ) ? $update_email_temp : '';

		if ( ! empty( $update_email_temp ) && 'yes' === $update_email_temp ) {
			?>
			<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
				<p>
					<strong>Email templates saved.</strong>
				</p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text">
						Dismiss this notice.
					</span>
				</button>
			</div>
			<div class="tab">
				<span class="tablinks" onclick="openSettings(event, 'general')">Credentials</span>
				<span class="tablinks" onclick="openSettings(event, 'child_sites')">Child Sites</span>
				<span class="tablinks active" onclick="openSettings(event, 'email_templates')">Email Templates</span>
			</div>
			<?php
		} elseif ( ! empty( $update_settings ) && 'yes' === $update_settings ) {
			?>
			<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
				<p>
					<strong>Settings saved.</strong>
				</p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text">Dismiss this notice.</span>
				</button>
			</div>
			<div class="tab">
				<span class="tablinks active" onclick="openSettings(event, 'general')">Credentials</span>
				<span class="tablinks" onclick="openSettings(event, 'child_sites')">Child Sites</span>
				<span class="tablinks" onclick="openSettings(event, 'email_templates')">Email Templates</span>
			</div>
			<?php
		} else {
			?>
			<div class="tab">
				<span class="tablinks active" onclick="openSettings(event, 'general')">Credentials</span>
				<span class="tablinks" onclick="openSettings(event, 'child_sites')">Child Sites</span>
				<span class="tablinks" onclick="openSettings(event, 'email_templates')">Email Templates</span>
			</div>
			<?php
		}
		?>
		<div class="tabcontent" id="general" <?php if ( empty( $update_email_temp ) ) echo ' style="display: block;"'; ?>>
			<div id="config">
				<form action="admin.php?page=network-dffmain-events-settings-page" method="post">

					<div class="page_section send_grid_credentials">
						<h3>
							Sendgrid Credentials
						</h3>
						<label for="send_grid_key">
							<span>
								Enter Sendgrid Key
							</span>
							<input 
								type="text" 
								id="send_grid_key"
								name="send_grid_key"
								class="send_grid_key"
								placeholder="Enter Sendgrid Key"
								value="<?php echo isset( $events_general_settings_get['send_grid_key'] ) ? esc_html( $events_general_settings_get['send_grid_key'] ) : ''; ?>"
							>
						</label>
						<label for="send_grid_from_name">
							<span>
								Enter Sendgrid From Name
							</span>
							<input 
								type="text"
								id="send_grid_from_name"
								name="send_grid_from_name"
								class="send_grid_from_name"
								placeholder="Enter Sendgrid From Name"
								value="<?php echo isset( $events_general_settings_get['send_grid_from_name'] ) ? esc_html( $events_general_settings_get['send_grid_from_name'] ) : ''; ?>"
							>
						</label>
						<label for="send_grid_from_email">
							<span>
								Enter Sendgrid From Email
							</span>
							<input 
								type="email"
								id="send_grid_from_email"
								name="send_grid_from_email"
								class="send_grid_from_email"
								placeholder="Enter Send From Email"
								value="<?php echo isset( $events_general_settings_get['send_grid_from_email'] ) ? esc_html( $events_general_settings_get['send_grid_from_email'] ) : ''; ?>"
							>
						</label>
					</div>

					<div class="page_section google_recaptcha_credentials">
						<h3>
							Google Recaptcha Credentials
						</h3>
						<label for="site_key">
							<span>
								Enter Site Key
							</span>
							<input 
								type="text" 
								id="site_key"
								name="site_key" 
								class="site_key"
								placeholder="Enter Site Key"
								value="<?php echo isset( $events_general_settings_get['site_key'] ) ? esc_html( $events_general_settings_get['site_key'] ) : ''; ?>"
							>
						</label>
						<label for="secret_key">
							<span>
								Enter Secret Key
							</span>
							<input 
							 	type="text" 
								id="secret_key"
								name="secret_key" 
								class="secret_key"
								placeholder="Enter Secret Key"
								value="<?php echo isset( $events_general_settings_get['secret_key'] ) ? esc_html( $events_general_settings_get['secret_key'] ) : ''; ?>"
							>
						</label>
					</div>

					<div class="page_section future_connect_id_credentials">
						<h3>
							Future ID Credentials
						</h3>
						<label for="future_connect_id">
							<span>
								Enter Future ID
							</span>
							<input 
								type="text"
								id="future_connect_id"
								name="future_connect_id"
								class="future_connect_id"
								placeholder="Enter Future ID"
								value="<?php echo isset( $events_general_settings_get['future_connect_id'] ) ? esc_html( $events_general_settings_get['future_connect_id'] ) : ''; ?>"
							>
						</label>
					</div>

					<div class="page_section google_recaptcha_credentials">
						<h3>
							Mailchimp Credentials
						</h3>
						<label for="future_connect_id">
							<span>
								Enter Mailchimp AuthToken
							</span>
							<input 
								type="text"
								id="mailchimp_auth_token"
								name="mailchimp_auth_token"
								class="mailchimp_auth_token"
								placeholder="Enter Mailchimp AuthToken"
								value="<?php echo isset( $events_general_settings_get['mailchimp_auth_token'] ) ? esc_html( $events_general_settings_get['mailchimp_auth_token'] ) : ''; ?>"
							>
						</label>
						<label for="future_connect_id">
							<span>
								Enter Mailchimp List Id
							</span>
							<input 
								type="text"
								id="mailchimp_list_id"
								name="mailchimp_list_id"
								class="mailchimp_list_id"
								placeholder="Enter Mailchimp List Id"
								value="<?php echo isset( $events_general_settings_get['mailchimp_list_id'] ) ? esc_html( $events_general_settings_get['mailchimp_list_id'] ) : ''; ?>"
							>
						</label>
					</div>

					<input 
						type="submit" 
						name="submit_settings" 
						id="submit_settings" 
						class="button button-primary"
						value="Save Changes"
					>
				</form>
			</div>

		</div>
		<div class="tabcontent" id="child_sites">
			<h3>Child Site List</h3>
			<div class="add_sites_group">
				<input type="text" class="add_sites_field" placeholder="Example: abc.com( without http/https )">
				<input type="button" name="submit" class="add_sites_button button-primary" value="Generate Token">
			</div>
			<div class="add_site_table_group">
				<table class="add_site_table">
					<tbody>
					<tr>
						<th>Site URL</th>
						<th>OAuth Token</th>
						<th>Action</th>
					</tr>
					<?php
					$npm_added_sites = get_option( 'npm_added_child_sites' );

					if ( isset( $npm_added_sites ) && ! empty( $npm_added_sites ) ) {
						foreach ( $npm_added_sites as $npm_added_sites_data ) {
							?>
							<tr>
								<td class="siteurl">
									<?php echo esc_html( $npm_added_sites_data['siteurl'] ); ?>
								</td>
								<td class="token">
									<?php echo esc_html( $npm_added_sites_data['token'] ); ?>
								</td>
								<td class="action">
									<?php 
									if( 'mobile_apps' === $npm_added_sites_data['siteurl'] ) {
										?>
										<span>-</span>
										<?php
									} else {
										?>
										<span class="dashicons dashicons-no-alt delete_site_button"></span>
										<?php
									}
									?>
								</td>
							</tr>
							<?php
						}
					}
					?>
					</tbody>
				</table>
			</div>
		</div>

		
		<div class="tabcontent" id="email_templates" <?php if ( ! empty( $update_email_temp ) && 'yes' === $update_email_temp ) echo 'style="display: block;"'; ?>>
			<form 
				action="admin.php?page=network-dffmain-events-settings-page&email_temp=yes"
				method="post" 
				novalidate="novalidate"
			>
				<?php
				foreach ($translations as $translation) {
					$language        = $translation->language();
					$language_name   = $language->isoName();
					$is_rtl          = $language->isRtl();
					?>
					<h3>
						Templates for <?php echo $language_name; ?>
					</h3>
					<h4>
						<?php _e( 'Thank you Email After Registration', 'events-main-plugin' ); ?>
					</h4>
					<hr>
					<div class="main_email_template">
						<div class="col">
							<div class="subject_field_wrap">
								<input 
									type="text" 
									name="subject_thank_you_<?php echo $language_name; ?>" 
									size="30" 
									spellcheck="true" 
									autocomplete="off" 
									value="<?php echo isset( $subject_thank_you[$language_name] ) ? wp_kses_post( $subject_thank_you[$language_name] ) : ''; ?>"
									placeholder="<?php _e( 'Thank you Email After Registration Subject', 'events-main-plugin' ); ?>"
									<?php if ( $is_rtl ) echo 'dir="rtl"'; ?>
								>
							</div>
							<?php
							wp_editor(
								$events_content_thank_you_after_registration[$language_name],
								'events_content_thank_you_after_registration_' . $language_name,
								array(
									'media_buttons' => true,
									'textarea_rows' => 10,
									'editor_height' => 200,
								)
							);
							?>
							<p>
								<b><?php _e( 'You can use these predefined tags for ' . $language_name . '.', 'events-main-plugin' ); ?></b>
							</p>
							<p>
								<b><?php _e( 'Description: Attribute:', 'events-main-plugin' ); ?></b>
							</p>
							<ol>
								<li><?php _e( 'Attendee First Name : ', 'events-main-plugin' ); ?><b>{{e_attendee_fname}}</b></li>
								<li><?php _e( 'Attendee Last Name : ', 'events-main-plugin' ); ?> <b>{{e_attendee_lname}}</b></li>
								<li><?php _e( 'Event Name :', 'events-main-plugin' ); ?> <b>{{e_eventname}}</b></li>
								<li><?php _e( 'Event Date/Time :', 'events-main-plugin' ); ?> <b>{{date/time}}</b></li>
								<li><?php _e( 'Event Location :', 'events-main-plugin' ); ?> <b>{{location}}</b></li>
								<li><?php _e( 'Event Details :', 'events-main-plugin' ); ?> <b>{{e_event_details}}</b></li>
								<li><?php /* TODO is it in use???? */ _e( 'Special Instruction :', 'events-main-plugin' ); ?> <b>{{special_instruction}}</b></li>
								<li><?php /* TODO is it in use???? */ _e( 'Future ID Link :', 'events-main-plugin' ); ?> <b>{{future_id_link}}</b></li>
							</ol>
						</div>
					</div>
						
					<!-- <div class="save_email_template">
						<input type="submit" name="submit" class="button button-primary" value="Save Changes">
					</div> -->
				

					<h4>
						<?php _e( 'Event Reminder', 'events-main-plugin' ); ?>
					</h4>
					<hr>
					<div class="main_email_template">
						<div class="col">
							<div class="subject_field_wrap">
								<input 
									type="text" 
									name="subject_event_reminder_<?php echo $language_name; ?>" 
									size="30"  
									spellcheck="true"
									autocomplete="off" class=""
									value="<?php echo isset( $subject_event_reminder[$language_name] ) ? wp_kses_post( $subject_event_reminder[$language_name] ) : ''; ?>"
									placeholder="<?php _e( 'Event Reminder Subject', 'events-main-plugin' ); ?>"
									<?php if ( $is_rtl ) echo 'dir="rtl"'; ?>
								>
							</div>
							<?php
							wp_editor(
								$events_content_event_reminder[$language_name],
								'events_content_event_reminder_' . $language_name,
								array(
									'media_buttons' => true,
									'textarea_rows' => 10,
									'editor_height' => 200,
								)
							);
							?>
							<p>
								<b><?php _e( 'You can use these predefined tags for ' . $language_name . '.', 'events-main-plugin' ); ?></b>
							</p>
							<p>
								<b><?php _e( 'Description: Attribute:', 'events-main-plugin' ); ?></b>
							</p>
							<ol>
								<li><?php _e( 'Attendee First Name : ', 'events-main-plugin' ); ?><b>{{e_attendee_fname}}</b></li>
								<li><?php _e( 'Attendee Last Name : ', 'events-main-plugin' ); ?> <b>{{e_attendee_lname}}</b></li>
								<li><?php _e( 'Event Name :', 'events-main-plugin' ); ?> <b>{{e_eventname}}</b></li>
								<li><?php _e( 'Event Date/Time :', 'events-main-plugin' ); ?> <b>{{date/time}}</b></li>
								<li><?php _e( 'Event Location :', 'events-main-plugin' ); ?> <b>{{location}}</b></li>
								<li><?php _e( 'Event Details :', 'events-main-plugin' ); ?> <b>{{e_event_details}}</b></li>
							</ol>
						</div>
					</div>

					<!-- <div class="save_email_template">
						<input type="submit" name="submit" id="submit_template" class="button button-primary" value="Save Changes">
					</div> -->


					<h4>
						<?php _e( 'Event Cancel', 'events-main-plugin' ); ?>
					</h4>
					<hr>
					<div class="main_email_template">
						<div class="col">
							<div class="subject_field_wrap">
								<input 
									type="text" 
									name="subject_event_cancel_<?php echo $language_name; ?>" 
									size="30" 
									spellcheck="true"
									autocomplete="off" class=""
									value="<?php echo isset( $subject_event_cancel[$language_name] ) ? wp_kses_post( $subject_event_cancel[$language_name] ) : ''; ?>"
									placeholder="<?php _e( 'Event Cancel Subject', 'events-main-plugin' ); ?>"
									<?php if ( $is_rtl ) echo 'dir="rtl"'; ?>
								>
							</div>
							<?php
							wp_editor(
								$events_content_event_cancel[$language_name],
								'events_content_event_cancel_' . $language_name,
								array(
									'media_buttons' => true,
									'textarea_rows' => 10,
									'editor_height' => 200,
								)
							);
							?>
							<p>
								<b><?php _e( 'You can use these predefined tags for ' . $language_name . '.', 'events-main-plugin' ); ?></b>
							</p>
							<p>
								<b><?php _e( 'Description: Attribute:', 'events-main-plugin' ); ?></b>
							</p>
							<ol>
								<li><?php _e( 'Attendee First Name : ', 'events-main-plugin' ); ?> <b>{{e_attendee_fname}}</b></li>
								<li><?php _e( 'Attendee Last Name : ', 'events-main-plugin' ); ?> <b>{{e_attendee_lname}}</b></li>
								<li><?php _e( 'Event Name :', 'events-main-plugin' ); ?> <b>{{e_eventname}}</b></li>
								<li><?php _e( 'Event Date/Time :', 'events-main-plugin' ); ?> <b>{{date/time}}</b></li>
								<li><?php _e( 'Event Location :', 'events-main-plugin' ); ?> <b>{{location}}</b></li>
							</ol>
						</div>
					</div>

					<div class="save_email_template">
						<input type="submit" name="submit" class="button button-primary" value="Save Changes">
					</div>
					<?php
				}/**foreach ($translations as $translation) { */
				?>
			</form>
		</div>
	</div>

</div>
