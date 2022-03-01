<?php

/**
 * Function for Events editor screen with tabs.
 */
function tab_editor_function() {
	global $post;
	$events_overview         = get_post_meta( $post->ID, 'events_overview', true );
	$dffmain_events_agenda   = get_post_meta( $post->ID, 'dffmain_events_agenda', true );
	$dffmain_event_location  = get_post_meta( $post->ID, 'dffmain_event_location', true );
	$arabic_event_location   = get_post_meta( $post->ID, 'arabic_event_location', true );
	$dffmain_post_title      = get_post_meta( $post->ID, 'dffmain_post_title', true );
	$template_id             = get_post_meta( $post->ID, '_wp_template_id', true );
	$action                  = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
	$action                  = isset( $action ) ? $action : '';
	$event_cancelled         = get_post_status( $post->ID );
	$post_id     = $post->ID;

	$args = array(
		'post_type'     => 'attendees',
		'post_per_page' => 1,
		'meta_query'    => array(
			array(
				'key'   => 'event_id',
				'value' => "$post_id",
			),
		),
	);
	$query       = new WP_Query( $args );
	$found_posts = $query->found_posts ? $query->found_posts : 0;

	?>

	<?php if ( ! empty( $template_id ) && 'edit' === $action ) { ?>
		<div class="attendee-button-wrap">
			<div class="button-wrap">

				<?php if ( 'cancelled' === $event_cancelled ) { ?>
					<a class="button button-primary cancelled" href="javascript:void(0)">
						Event is Cancelled
					</a>
				<?php } else { ?>
					<a 
						class="button button-primary"
					   	href="<?php echo esc_url( admin_url( 'edit.php?post_type=attendees&event_id=' . $post->ID ) ); ?>"
					   	target="_blank"
					>
						Attendees List
					</a>
					<a 
						class="button button-primary"
					   	href="<?php echo esc_url( get_the_permalink( $post->ID ) . '?lang=en' ); ?>" 
						target="_blank"
					>
						Add Attendee
					</a>
					<a 
						class="button button-primary"
					   	href="<?php echo esc_url( get_the_permalink( $post->ID ) . '?lang=en&checkin=true' ); ?>"
					   	target="_blank"
					>
						Add Walk-In
					</a>
				<?php } ?>
			</div>
		</div>

	<?php } ?>

	<div class="event_editor_section">
		<div class="tab">
			<span class="tablinks main active" data-title="main" onclick="openEditor(event, 'main')">
				Event
			</span>
			<span class="tablinks registration_form" onclick="openEditor(event, 'registration_form')">
				Registration Form
			</span>
			<span class="tablinks general_settings" onclick="openEditor(event, 'general_settings')">
				General Settings
			</span>
		</div>

		<div class="tabcontent" id="main" style="display: block;">
			<div class="event_titlediv">
				<input 
					type="text" 
					name="dffmain_post_title" 
					size="30" 
					id="title" 
					spellcheck="true" 
					autocomplete="off"
					class="" 
					value="<?php echo esc_attr( $dffmain_post_title ); ?>"
					placeholder="Enter Title Here"
				>
			</div>
			<div class="heading_section">
				<h3>
					Overview: <span>event</span>
				</h3>
			</div>
			<label for="events_overview">
				<span class="screen-reader-text">events_overview</span>
				<?php
				
				wp_editor(
					$events_overview,
					'events_overview',
					array(
						'media_buttons' => true,
						'textarea_rows' => 150,
						'editor_height' => 300,
						'quicktags'     => true,
					)
				);
				?>
			</label>
			<div class="heading_section">
				<h3>
					Agenda: <span>event</span>
				</h3>
			</div>
			<label for="dffmain_events_agenda">
				<span class="screen-reader-text">dffmain_events_agenda</span>
				<?php
				wp_editor(
					$dffmain_events_agenda,
					'dffmain_events_agenda',
					array(
						'media_buttons' => true,
						'textarea_rows' => 150,
						'editor_height' => 300,
						'quicktags'     => true,
					)
				);
				?>
			</label>
			<?php
			/** Get custom boxes for categoris and tags.*/
			get_category_box(); 
			?>
			<div class="heading_section">
				<h3>
					Event Location
				</h3>
			</div>
			<label for="dffmain_event_location">
				<span class="screen-reader-text">dffmain_event_location</span>
				<textarea 
					rows="8" 
					cols="60" 
					id="dffmain_event_location" 
					class="dffmain_event_location"
					name="dffmain_event_location"
				>
					<?php echo wp_kses_post( $dffmain_event_location ); ?>
				</textarea>
			</label>
			<div class="save_next_section">
				<input type="button" name="save_next" class="save_next button-primary" value="Save & Next >">
				<input type="hidden" name="next_step_id" class="next_step_id" value="registration_form">
			</div>
		</div>

		<div class="tabcontent" id="registration_form">
			<div class="registration-form-section">
				<div class="registration-form-select">
					<select 
						id="u2583_input" 
						class="u2583_input select-form" 
						<?php if ( ! empty( $template_id ) && 0 < $found_posts ) echo 'disabled'; ?>
					>
						<option class="u2583_input_option" value="">
							Select Registration Template
						</option>
						<?php
						$args_register_forms = array(
							'post_type'      => 'registration-forms',
							'post_status'    => array( 'publish' ),
							'posts_per_page' => -1,
							'fields'         => 'ids',
						);
						$register_forms_data = new WP_Query( $args_register_forms );

						if ( isset( $register_forms_data->posts ) && ! empty( $register_forms_data->posts ) ) {
							foreach ( $register_forms_data->posts as $register_forms_data_value ) {
								?>
								<option 
									class="u2583_input_option"
									value="<?php echo esc_attr( $register_forms_data_value ); ?>" 
									<?php if ( intval( $register_forms_data_value ) === intval( $template_id ) ) echo 'selected="selected"'; ?>
								>
									<?php echo esc_html( get_the_title( $register_forms_data_value ) ); ?>
								</option>
								<?php
							}
						}
						?>
					</select>
					<input 
						type="hidden" 
						name="templateId" 
						value="<?php echo esc_attr( $template_id ); ?>"
						class="templateId" id="templateId"
					/>
				</div>
				<div class="registration-form-action">
					<input 
						type="button" 
						name="submit" 
						id="undo_change" 
						class="button button-primary reset"
						templateid="<?php echo esc_attr( $template_id ); ?>" 
						value="Undo Changes"
						disabled="disabled"
					>
				</div>
			</div>
			<div class="registration-form-wrap"></div>
			<div class="save_next_section">
				<input type="button" name="save_next" class="save_next button-primary" value="Save & Next >">
				<input type="hidden" name="next_step_id" class="next_step_id" value="general_settings">
			</div>
		</div>

		<div class="tabcontent" id="general_settings">
			<?php $current_language_name = get_current_language_name(); ?>

			<input type="hidden" class="curr_post_id" value="<?php echo esc_attr( $post->ID ); ?>">
			<input type="hidden" class="curr_site_id" value="<?php echo esc_attr( get_current_blog_id() ); ?>">
	
			<h3>
				Send Special Email
			</h3>
			<h4 class="heading-tag">
				For <?php echo $current_language_name; ?> Attendees
			</h4>
			<div class="event_titlediv">
				<input 
					type="text" 
					size="30" 
					spellcheck="true" 
					autocomplete="off"
					id="dffmain_special_mail_subject_<?php echo $current_language_name; ?>" 
					data-lang="<?php echo $current_language_name; ?>" 
					class="dffmain_mail_subject" 
					placeholder="{{e_eventname}} | Email Subject Here"
				>
			</div>
			<?php
			wp_editor(
				'',
				'event_send_special_email_' . $current_language_name,
				array(
					'media_buttons' => false,
					'textarea_rows' => 100,
					'editor_height' => 200,
					'quicktags'     => false,
				)
			);
			?>
			<p>
				<b>You can use these predefined tags for <?php echo $current_language_name; ?>.</b>
			</p>
			<p>
				<b>Description: Attribute:</b>
			</p>
			<div class="predefine_attributes">
				<table  style="width:100%">
					<tr role="row">
						<td >Attendee First Name : <b>{{e_attendee_fname}}</b></td>
						<td>Attendee Last Name : <b>{{e_attendee_lname}}</b></td>
						<td>Event Name : <b>{{e_eventname}}</b></td>
					</tr>
					<tr role="row">
						<td>Event Date/Time : <b>{{date/time}}</b></td>
						<td>Event Location : <b>{{location}}</b></td>
					</tr>
				</table>
			</div>
			<?php 
			$translations = get_translations_data();
			foreach ( $translations as $translation ) {
				$language_name = $translation['language_name'];
				?>
				<h4 class="heading-tag">
					For <?php echo $language_name; ?> Attendee
				</h4>
				<div class="event_titlediv">
					<input 
						type="text" 
						size="30" 
						spellcheck="true" 
						autocomplete="off"
						id="dffmain_special_mail_subject_<?php echo $language_name; ?>" 
						data-lang="<?php echo $language_name; ?>"
						class="dffmain_mail_subject"  
						<?php if ( $translation['is_rtl'] ) echo 'dir="rtl"'; ?>
						placeholder="{{e_eventname}} | Email Subject Here">
				</div>
				<?php
				wp_editor(
					'',
					'event_send_special_email_' . $language_name,
					array(
						'media_buttons' => false,
						'textarea_rows' => 100,
						'editor_height' => 200,
						'quicktags'     => false,
					)
				);
				?>
				<p>
					<b>You can use these predefined tags for <?php echo $language_name; ?>.</b>
				</p>
				<p>
					<b>Description: Attribute</b>
				</p>

				<div class="predefine_attributes">
					<table  style="width:100%">
						<tr role="row">
							<td >Attendee First Name : <b>{{a_attendee_fname}}</b></td>
							<td>Attendee Last Name : <b>{{a_attendee_lname}}</b></td>
							<td>Event Name : <b>{{a_eventname}}</b></td>
						</tr>
						<tr role="row">
							<td>Event Date/Time : <b>{{date/time}}</b></td>
							<td>Event Location : <b>{{location}}</b></td>
						</tr>
					</table>
				</div>
				<?php
			}
			?>
			<div class="event_send_email">
				<input 
					type="button" 
					name="submit" 
					id="submit" 
					data-id="<?php echo esc_attr( $post->ID ); ?>"
					class="button button-primary btn-send-email" 
					value="Send" 
				>
			</div>

			<div class="accordian_email_history">
				<div class="accordian-main accordian-open">
					<div class="heading-tag-email accordian-title">
						<h4 class="heading-tag">
							Email History
						</h4>
					</div>
					<div class="email_history accordian-body">
						<table id="email_history" class="display nowrap" style="width:100%">
							<thead>
								<tr>
									<th>#</th>
									<th>Date & Time</th>
									<th>Subject</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$event_email_history = get_post_meta( $post->ID, 'event_email_history', false );
								$event_email_history = array_reverse( $event_email_history );

								if ( isset( $event_email_history ) && ! empty( $event_email_history ) ) {
									$count = 1;
									foreach ( $event_email_history as $event_email_history_data ) {
										?>
										<tr>
											<td>
												<?php echo esc_html( $count ); ?>
												
											<td>
												<?php echo esc_html( $event_email_history_data['email_date'] ); ?>
											</td>
											<td>
												<?php echo esc_html( $event_email_history_data['dffmain_email_subject'] ); ?>
											</td>
											<td>
												<span class="view_history_action">View</span>
												<div class="email_history_popup">
													<div class="email_history_wrapper">
														<span class="close_popup dashicons dashicons-no-alt"></span>
														<?php
														if ( ! empty( $event_email_history_data['dffmain_email_subject'] ) ) {
															?>
															<div class="accordian-main accordian-open">
																<div class="accordian-title">
																	<h3>
																		Email
																	</h3>
																</div>
																<div class="accordian-body" style="display:block;">
																	<h4>
																		Subject: <?php echo esc_html( $event_email_history_data['dffmain_email_subject'] ); ?>
																	</h4>
																	<h3>
																		Content
																	</h3>
																	<hr>
																	<?php echo wp_kses_post( $event_email_history_data['dffmain_email_content'] ); ?>
																</div>
															</div>
															<?php
														}
														?>
													</div>
												</div>
											</td>
										</tr>
										<?php
										$count++;
									}/**foreach ( $event_email_history as $event_email_history_data ) { */
								}/**if ( isset( $event_email_history ) && ! empty( $event_email_history ) ) { */
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
		
	</div>

	<?php
}

/**
 * Function for event cost meta box.
 */
function event_cost_function() {
	global $post;
	$event_cost_name = get_post_meta( $post->ID, 'event_cost_name', true );
	?>
	<label for="free">
		<span class="screen-reader-text">event_cost_name_free</span>
		<input 
			type="radio" 
			name="event_cost_name" 
			id="free"
			value="Free" 
			<?php checked( $event_cost_name, 'Free' ); ?> 
		>
		Free
	</label>
	</label>
	<label for="paid">
		<span class="screen-reader-text">event_cost_name_paid</span>
		<input 
			type="radio" 
			name="event_cost_name" 
			id="paid"
			value="Paid" 
			<?php checked( $event_cost_name, 'Paid' ); ?>
		>
		Paid
	</label>
	<?php

}

/**
 * Function for event reminder meta box.
 */
function event_reminder_function() {
	global $post;
	$event_reminder_select_box = get_post_meta( $post->ID, 'event_reminder_select_box', true );
	?>
	<label for="event_reminder_select_box">
	<span class="screen-reader-text">event reminder select box</span>
		<select id="u1832_input" name="event_reminder_select_box" class="event_reminder_select_box" id="event_reminder_select_box">
			<option class="u1832_input_option" value="1">Select Reminder Day</option>
			<option class="u1832_input_option" value="1" <?php selected( $event_reminder_select_box, '1' ); ?>>1</option>
			<option class="u1832_input_option" value="2" <?php selected( $event_reminder_select_box, '2' ); ?>>2</option>
			<option class="u1832_input_option" value="3" <?php selected( $event_reminder_select_box, '3' ); ?>>3</option>
			<option class="u1832_input_option" value="4" <?php selected( $event_reminder_select_box, '4' ); ?>>4</option>
			<option class="u1832_input_option" value="5" <?php selected( $event_reminder_select_box, '5' ); ?>>5</option>
			<option class="u1832_input_option" value="6" <?php selected( $event_reminder_select_box, '6' ); ?>>6</option>
			<option class="u1832_input_option" value="7" <?php selected( $event_reminder_select_box, '7' ); ?>>7</option>
		</select>
	</label>
	<?php

}

/**
 * Function for event date meta box.
 */
function event_date_function() {

	global $post;
	$event_date_select = get_post_meta( $post->ID, 'event_date_select', true );
	?>
	<label for="event_date_select">
		<span class="screen-reader-text">event_date_select</span>
			<input type="date" id="event_date_select" class="event_date_select" name="event_date_select" value="<?php echo esc_attr( $event_date_select ); ?>" placeholder=""/>
	</label>
	<?php
}

/**
 * Function for event end date meta box.
 */
function event_end_date_function() {
	

	global $post;
	$event_end_date_select = get_post_meta( $post->ID, 'event_end_date_select', true );
	?>
	<label for="event_end_date_select">
		<span class="screen-reader-text">event_end_date_select</span>
			<input type="date" id="event_end_date_select" class="event_end_date_select" name="event_end_date_select" onchange="handler(event);" value="<?php echo esc_attr( $event_end_date_select ); ?>" placeholder=""/>
	</label>
	<?php

}

/**
 * Function for event time meta box.
 */
function event_time_function() {

	global $post;
	$event_time_start_select = get_post_meta( $post->ID, 'event_time_start_select', true );
	$event_time_end_select   = get_post_meta( $post->ID, 'event_time_end_select', true );
	?>
	<label for="event_time_start_select">
		<span class="screen-reader-text">event_time_start_select</span>
		<input type="time" id="event_time_start_select" class="event_time_start_select" name="event_time_start_select"
		   value="<?php echo esc_attr( $event_time_start_select ); ?>" placeholder=""/>
	</label>
	<label for="event_time_end_select">
		<span class="screen-reader-text">event_time_end_select</span>
		<input type="time" id="event_time_end_select" class="event_time_end_select" name="event_time_end_select"
		   value="<?php echo esc_attr( $event_time_end_select ); ?>" placeholder=""/>
	</label>
	<?php
}

/**
 * Function for event_special_instruction_function
 */
function event_special_instruction_function() {

	global $post;
	$event_special_instruction = get_post_meta( $post->ID, 'event_special_instruction', true );
	?>
	<label for="event_special_instruction">
		<span class="screen-reader-text">event_special_instruction</span>
		<textarea id="event_special_instruction" name="event_special_instruction" rows="5" cols="25"><?php echo esc_attr( $event_special_instruction ); ?></textarea>
	</label>
	<?php

}


/**
 * Function for google_embed_maps_code_function
 */
function google_embed_maps_code_function() {

	global $post;
	$google_embed_maps_code = get_post_meta( $post->ID, 'google_embed_maps_code', true );
	?>
	<label for="google_embed_maps_code">
		<span class="screen-reader-text">google_embed_maps_code</span>
		<textarea id="google_embed_maps_code" name="google_embed_maps_code" rows="5" cols="25">
			<?php echo esc_attr( $google_embed_maps_code ); ?>
		</textarea>
	</label>
	<?php

}

/**
 * Function for event_google_map_function
 */
function event_google_map_function() {

	global $post;
	$event_google_map_input = get_post_meta( $post->ID, 'event_google_map_input', true );
	?>
	<label for="event_google_map_input">
		<span class="screen-reader-text">event_google_map_input</span>
		<input type="text" id="event_google_map_input" class="event_google_map_input" name="event_google_map_input"
		   value="<?php echo esc_attr( $event_google_map_input ); ?>" placeholder=""/>
	</label>
	<?php
}

/**
 * Function for event_detail_image_function
 */
function event_detail_image_function() {
	global $post;
	$meta_key = 'event_detail_img';
	echo event_detail_image_uploader_field( $meta_key, get_post_meta($post->ID, $meta_key, true) );
}

function event_detail_image_uploader_field( $name, $value = '') {
	$image = '">Set Event Detail  image';
	$image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
	$display = 'none'; // display state ot the "Remove image" button

	if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {

		// $image_attributes[0] - image URL
		// $image_attributes[1] - image width
		// $image_attributes[2] - image height

		$image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
		$display = 'inline-block';

	}

	return '
	<div>
		<a href="javascript:void(0)" class="upload_event_detail_image_button' . $image . '</a>
		<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . esc_attr( $value ) . '" />
		<a href="#" class="remove_event_detail_image" style="display:inline-block;display:' . $display . '">Remove Event Detail  image</a>
		<span class="tool-tip" data-tip="The Event Detail Image is shown on the event’s details page. Please ensure it is 950 px by 230 px. This image can be larger in height and may contain text."><i>!</i></span>
	</div>';
}


/**
 * Function for event_security_code_function
 */
function event_security_code_function() {
	global $post;

	$security_code_checkbox = get_post_meta( $post->ID, 'security_code_checkbox', true );
	$security_code_value    = get_post_meta( $post->ID, 'event_security_code', true );

	?>
	<label for="security_code_checkbox">
		<input type="checkbox" id="security_code_checkbox" name="security_code_checkbox" class="security_code_checkbox"
			   value="true" <?php checked( $security_code_checkbox, 'true' ); ?>>
		Enable to enter invitation code.<span class="tool-tip"
											  data-tip="In the registration form of the front side, Attendee need to enter valid invitation code which is set here."><i>!</i></span>
	</label>
	<div id="security_code" class="security_code"
		 style="
			<?php
			if ( ! $security_code_checkbox ) {
				echo 'display: none;';}
	?>
	">
		<h5>Enter Invitation Code</h5>
		<label for="event_security_code">
			<input type="text" value="<?php echo esc_attr( $security_code_value ); ?>" id="event_security_code"
				   class="event_security_code" name="event_security_code" placeholder=""/>
		</label>
	</div>
	<?php
}

/**
 * Cancel Event button in event edit
 */
function cancel_event_function() {

	global $post;
	$event_cancelled = get_post_status( $post->ID );

	if ( 'cancelled' !== $event_cancelled ) {
		?>
		<div class="cancel_event_button_section">
			<input type="button" name="cancel_event_button" id="cancel_event_button"
				   class="cancel_event_button button-primary" value="Cancel Event">
		</div>

		<div id="cancel-event-modal" class="event-modal-main">
			<div class="event-modal-content">
				<span class="event-modal-close">×</span>
				<div class="modal-title">
					<h3>Confirmation</h3>
				</div>
				<div class="modal-body">
					<p>Are you sure you wish to cancel this event?</p>
					<i>This will cancel the event and notify all attendees about the cancellation via Email.</i>
					<div class="button-wrap">
						<a href="javascript:void(0)" class="event-modal-close button-primary">No</a>
						<a href="javascript:void(0)" id="cancel_event_now" class="button-primary btn-danger">Yes, Cancel
							the event</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

/**
 * Event attendee limit function.
 */
function event_attendee_limit_function() {
	global $post;

	$event_attendee_limit_count = get_post_meta( $post->ID, 'event_attendee_limit_count', true );
	?>
		<input type="number" name="event_attendee_limit_count" class="event_attendee_limit_count" value="<?php echo isset( $event_attendee_limit_count ) ? esc_html( $event_attendee_limit_count ) : ""; ?>">
		<br><br>
		<span><i>If you have limited the number of attendees, please enter a ‘Registration Closed’ message.</i></span>
	<?php
}

/**
 * Event attendee limit message function.
 */
function event_attendee_limit_message_function() {
	global $post;

	$event_registration_close_message = get_post_meta( $post->ID, 'event_registration_close_message', true );

	?>
	<h4>Limit Message</h4>
	<textarea id="event_registration_close_message" name="event_registration_close_message" rows="5" cols="25"><?php echo esc_attr( $event_registration_close_message ); ?></textarea>

	<?php

}