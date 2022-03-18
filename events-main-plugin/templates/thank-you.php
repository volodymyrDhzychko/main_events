<?php
$api_registration = isset( $_POST['api_registration'] ) ? $_POST['api_registration'] : '';

// Unsetting the POST vars to prevent it to be added as meta.
unset( $_POST['token'] );
unset( $_POST['domain'] );
unset( $_POST['api_registration'] );

if ( 'yes' !== $api_registration ) {
	require_once '../../../../wp-load.php';
}

if ( $_POST ) {
	$attendee_arr  = array();
	$event_name    = '';
	$email         = '';
	$company_name  = '';
	$checkin       = '';
	$language_type = '';
	$future_id     = 'false';
	$event_id      = 'false';
	foreach ( $_POST as $key => $value ) {
		if ( 'g-recaptcha-response' !== $key && 'scode' !== $key ) {
			if ( 'event_title' === $key ) {
				$attendee_arr['eventName'] = $value;
				$event_name                = $value;
			} elseif ( 'event_id' === $key ) {
				$event_id = $value;
			} elseif ( 'language_type' === $key ) {
				$attendee_arr['languageType'] = $value;
				$language_type                = $value;
			} elseif ( 'checkin' === $key ) {
				$attendee_arr['checkin'] = $value;
				$checkin                 = $value;
			} elseif ( 'Email' === substr( $key, 2 ) ) {
				$replacement_key                  = substr( $key, 2 );
				$attendee_arr[ $replacement_key ] = $value;
				$email                            = $value;
			} elseif ( 'CompanyName' === substr( $key, 2 ) ) {
				$replacement_key                  = substr( $key, 2 );
				$attendee_arr[ $replacement_key ] = $value;
				$company_name                     = $value;
			} elseif ( 'futureID' === $key ) {
				$future_id = $value;
			} elseif ( 'subscribe_now' === $key ) {
				$subscribe_now = $value;
			} else {
				$replacement_key                  = substr( $key, 2 );
				$attendee_arr[ $replacement_key ] = $value;
			}
		}
	}

	if ( $_FILES ) {
		$multiple_file   = false;
		$file_field_name = '';
		foreach ( $_FILES as $key => $value ) {
			if ( is_array( $value['error'] ) ) {
				$file_link = array();
				for ( $i = 0; $i < sizeof( $value['error'] ); $i++ ) {
					$error = isset( $value['error'][ $i ] ) ? $value['error'][ $i ] : 0;
					if ( 0 === $error ) {
						$file_name     = $value['name'][ $i ];
						$file_type     = $value['type'][ $i ];
						$file_tmp_name = $value['tmp_name'][ $i ];
						$file_size     = $value['size'][ $i ];

						$file_name_with_time = time() . '_' . $file_name;
						$uploads_dir         = trailingslashit( wp_upload_dir( __FILE__ )['basedir'] ) . 'attendee-files';
						wp_mkdir_p( $uploads_dir );

						move_uploaded_file( $file_tmp_name, $uploads_dir . '/' . $file_name_with_time );
						$base_url    = trailingslashit( wp_upload_dir( __FILE__ )['baseurl'] ) . 'attendee-files/' . $file_name_with_time;
						$file_link[] = '<a href="' . $base_url . '" target="_blank">' . $file_name_with_time . '</a>';
					}
					$file_link_string = implode( ', ', $file_link );
				}
				$multiple_file = true;
			} else {
				$error = isset( $value['error'] ) ? $value['error'] : 0;
				if ( 0 === $error ) {
					$file_name     = $value['name'];
					$file_type     = $value['type'];
					$file_tmp_name = $value['tmp_name'];
					$file_size     = $value['size'];

					$file_name_with_time = time() . '_' . $file_name;
					$uploads_dir         = trailingslashit( wp_upload_dir( __FILE__ )['basedir'] ) . 'attendee-files';
					wp_mkdir_p( $uploads_dir );
					$test             = move_uploaded_file( $file_tmp_name, $uploads_dir . '/' . $file_name_with_time );
					$base_url         = trailingslashit( wp_upload_dir( __FILE__ )['baseurl'] ) . 'attendee-files/' . $file_name_with_time;
					$file_link_string = '<a href="' . $base_url . '" target="_blank">' . $file_name_with_time . '</a>';

				}
				$multiple_file = false;
			}
			$file_field_name = $replacement_key = substr( $key, 2 );
		}

		if ( $multiple_file ) {
			$attendee_arr[ $file_field_name ] = $file_link_string;
		} else {
			$attendee_arr[ $file_field_name ] = $file_link_string;
		}
	}

	if ( ! empty( $attendee_arr ) ) {
		$attendee_fullname = $attendee_arr['FirstName'] . ' ' . $attendee_arr['LastName'];
		$event_name_internal = get_the_title( $event_id );

		$meta_values = [
			'attendee_data' => $attendee_arr,
			'event_name'    => $event_name_internal,
			'email'         => $email,
			'company_name'  => $company_name,
			'checkin'       => $checkin,
			'language_type' => $language_type,
			'future_id'     => $future_id,
			'event_id'      => $event_id
		];

		$my_post           = [
			'post_title'  => wp_strip_all_tags( $attendee_fullname ),
			'post_status' => 'publish',
			'post_type'   => 'attendees',
			'meta_input'=> $meta_values
		];
		$post_id = wp_insert_post( $my_post );

		if ( $post_id ) {

			// Registration platform.
			if ( 'yes' === $api_registration ) { 
				add_post_meta( $post_id, 'registration_platform', 'mobile_app' );
				$attendee_arr['registration_platform'] = 'Mobile App';
			} else {
				add_post_meta( $post_id, 'registration_platform', 'web' );
				$attendee_arr['registration_platform'] = 'Web';
			}		
		}
	}

	$settings_array_get          = main_site_get_option( 'events_general_settings' );
	$events_general_settings_get = json_decode( $settings_array_get );
	$events_general_settings_get = (array) $events_general_settings_get;

	$sendgrid_apikey      = $events_general_settings_get['send_grid_key'];
	$send_grid_from_email = $events_general_settings_get['send_grid_from_email'];
	$send_grid_from_name  = $events_general_settings_get['send_grid_from_name'];

	$url = 'https://api.sendgrid.com/';

	$post_metas = get_post_meta( $event_id );

	$event_date              = $post_metas['event_date_select'][0];
	$event_end_date          = $post_metas['event_end_date_select'][0];
	$event_time_start_select = $post_metas['event_time_start_select'][0];
	$event_time_end_select   = $post_metas['event_time_end_select'][0];


	$event_date       = new DateTime( "$event_date" );
	$event_date_email = $event_date->format( 'F d, Y' );

	/**
	 * For event end date is available.
	 */
	if( isset( $event_end_date ) && !empty( $event_end_date ) ) {

		$event_end_date       = new DateTime( "$event_end_date" );
		$event_end_date_email = $event_end_date->format( 'F d, Y' );
		$event_date_email = $event_date_email . " - " . $event_end_date_email;

		$event_end_date_ical = $event_end_date->format( 'Ymd' );

	}

	$event_date = $event_date->format( 'Ymd' );
	
	$event_time_start_select       = new DateTime( "$event_time_start_select" );
	$event_time_start_select_email = $event_time_start_select->format( 'h:i A' );
	$event_time_start_select       = $event_time_start_select->format( 'His' );

	$event_time_end_select       = new DateTime( "$event_time_end_select" );
	$event_time_end_select_email = $event_time_end_select->format( 'h:i A' );
	$event_time_end_select       = $event_time_end_select->format( 'His' );

	$event_google_map_input = $post_metas['event_google_map_input'][0];

	$event_time_frame       = $event_time_start_select . ' - ' . $event_time_end_select;
	$event_time_frame_email = $event_time_start_select_email . ' - ' . $event_time_end_select_email;

	$events_overview = $post_metas['events_overview'][0];
	$events_agenda   = $post_metas['dffmain_events_agenda'][0];
    $event_details   = wpautop( $events_overview ) . '<br><br>' . wpautop( $events_agenda  );

    $dffmain_post_title        = $post_metas['dffmain_post_title'][0];
    $dffmain_event_location    = $post_metas['dffmain_event_location'][0];
	$event_special_instruction = $post_metas['event_special_instruction'][0];

	$subject_thank_you_arr = main_site_get_option( 'subject_thank_you' );
	$content_thank_you_arr = main_site_get_option( 'events_content_thank_you_after_registration' );

            /**
             * Future connect ID code. TODO -- how we will do it?
             */
            // $future_connect_id_key = $events_general_settings_get['future_connect_id'];
            
            // $future_url            = 'https://dff-future-id-identity-management-develop.apps.moti.us/api/v1/invite/createURL';
            //  //$future_connect_id_key = 'WF+z1VIiAJeswA6YETb6dgFxswhrgHHPmXT6JMn8ZoBg0JFl6Dkt6g==';
            //  //$future_url            = 'https://identity.id.dubaifuture.gov.ae/api/v1/invite/createURL';

            // $args = array(
            // 	'method'      => 'POST',
            // 	'headers'     => array( 'Content-Type' => 'application/json; charset=utf-8', 'origin' => 'https://events.dff.production.dbai.co/' ),
            // 	'body'        => wp_json_encode(
            // 		array(
            // 			'email'     => $email,
            // 			'firstName' => $attendee_arr['FirstName'],
            // 			'lastName'  => $attendee_arr['LastName'],
            // 			'apiKey'    => $future_connect_id_key,
            // 		)
            // 	),
            // 	'data_format' => 'body',
            // );

            // $response = wp_remote_post( $future_url, $args );


            // if ( isset( $response['response']['code'] ) && ! empty( $response['response']['code'] ) ) {

            // 	$response_code = $response['response']['code'];
            // 	$response_body = json_decode( $response['body'] );

            // 	if ( 200 === $response_code ) {
            // 		$future_connect_link = 'Future ID invitation link: <a href="' . $response_body->url . '">' . $response_body->url . '</a>';
            // 		$future_id_link      = $response_body->url;
            // 	}
            // }


	if ( 'ar' === $language_type ) {

		$subject_thank_you = $subject_thank_you_arr['Arabic'];
        $events_arabic_thank_you_after_registration = $content_thank_you_arr['Arabic'];

        $email_content = "<html lang='ar'><head><style>#arabic_email *{unicode-bidi: bidi-override !important;direction: rtl !important;text-align:right;}</style><title></title></head><body><div id='arabic_email'>$events_arabic_thank_you_after_registration</div></body></html>";

        /** TODO -- do we need ar translation? */
        $subject = 'Thank you for register to {{a_eventname}}';
        
		$json_string_ar = array(
			'to'  => array( $email ),
			'sub' => array(
				'{{special_instruction}}' => array( $event_special_instruction ),
				'{{a_event_details}}'     => array( $event_details ),
				'{{a_attendee_fname}}'    => array( $attendee_arr['FirstName'] ),
				'{{a_attendee_lname}}'    => array( $attendee_arr['LastName'] ),
				'{{a_eventname}}'         => array( $dffmain_post_title ),
				'{{date}}'                => array( $event_date_email ),
				'{{time}}'                => array( $event_time_frame_email ),
				'{{location}}'            => array( $dffmain_event_location ),
				// '{{future_id_link}}'      => array( $future_connect_link ),TODO -- how we will do it?
			),
		);

		$event_name = $dffmain_post_title;

	} else {

        $subject_thank_you = $subject_thank_you_arr['English'];
		$email_content     = $content_thank_you_arr['English'];
		$subject           = 'Thank you for register to {{e_eventname}}';

		$json_string_ar = array(
			'to'  => array( $email ),
			'sub' => array(
				'{{special_instruction}}' => array( $event_special_instruction ),
				'{{e_event_details}}'     => array( $event_details ),
				'{{e_attendee_fname}}'    => array( $attendee_arr['FirstName'] ),
				'{{e_attendee_lname}}'    => array( $attendee_arr['LastName'] ),
				'{{e_eventname}}'         => array( $dffmain_post_title ),
				'{{date}}'                => array( $event_date_email ),
				'{{time}}'                => array( $event_time_frame_email ),
				'{{location}}'            => array( $dffmain_event_location ),
				// '{{future_id_link}}'      => array( $future_connect_link ), TODO -- how we will do it?
			),
		);

		$event_name = $dffmain_post_title;
	}

	if ( isset( $subject_thank_you ) && ! empty( $subject_thank_you ) && isset( $email_content ) && ! empty( $email_content ) ) {

		$date     = $event_date . 'T';
		$end_date = $event_end_date_ical . 'T';

		$time_start = $event_time_start_select;
		$time_end   = $event_time_end_select;

		$event_start = $date . $time_start;
		$event_end   = $date . $time_end;

		if( isset( $event_end_date ) && !empty( $event_end_date ) ) {

			$event_end_start = $end_date . $time_start;
			$event_end_end   = $end_date . $time_end;
			$new_start_date =  date('Ymd',(strtotime ( '-1 day' , strtotime ( $event_date) ) ));

			$iCal = 'BEGIN:VCALENDAR
			VERSION:2.0
			PRODID:-//hacksw/handcal//NONSGML v1.0//EN
			BEGIN:VEVENT
			UID:' . md5( uniqid( wp_rand(), true ) ) . '@yourhost.test
			DTSTAMP:' . $new_start_date .'T'. '240000'. '
			DTSTART:' . $new_start_date .'T'. '240000'. '
			DTEND:' . $end_date .'235900'. '
			SUMMARY:' . $event_name . '
			END:VEVENT
			LOCATION:' . $event_google_map_input . '
			END:VCALENDAR';

			
		} else {
			$iCal = 'BEGIN:VCALENDAR
			VERSION:2.0
			PRODID:-//hacksw/handcal//NONSGML v1.0//EN
			BEGIN:VEVENT
			UID:' . md5( uniqid( wp_rand(), true ) ) . '@yourhost.test
			DTSTAMP:' . date( 'Ymd' ) . 'T' . date( 'His' ) . '
			DTSTART:' . $event_start . '
			DTEND:' . $event_end . '
			SUMMARY:' . $event_name . '
			END:VEVENT
			LOCATION:' . $event_google_map_input . '
			END:VCALENDAR';

		}

		/**TODO - do we need it - !!! stops subsite attendee registration !!! */
		// // set correct content-type-header
		// header( 'Content-type: text/calendar; charset=utf-8' );
		// header( 'Content-Disposition: inline; filename=event.ics' );

		// $params_ar = array(
		// 	'to'               => $email,
		// 	'toname'           => $attendee_arr['FirstName'],
		// 	'from'             => $send_grid_from_email,
		// 	'fromname'         => $send_grid_from_name,
		// 	'subject'          => $subject_thank_you,
		// 	'html'             => $email_content,
		// 	'x-smtpapi'        => wp_json_encode( $json_string_ar ),
		// 	'files[event.ics]' => $iCal,
		// );

		$request = $url . 'api/mail.send.json';

		if ( 'ar' === $language_type ) {

            $email_content = $content_thank_you_arr['Arabic'];

            $event_subject_thank_you = $subject_thank_you_arr['Arabic'];

			$event_post_title = $post_metas['dffmain_post_title'][0];
			$event_location   = $post_metas['dffmain_event_location'][0];
			$event_details    = wpautop( $events_overview ) . '<br><br>' . wpautop( $events_agenda  );

		
			if( isset( $event_end_date ) && !empty( $event_end_date ) ) {
				$email_content     = str_replace( '{{date/time}}', '{{date}}', $email_content );
			} else {
				$email_content     = str_replace( '{{date/time}}', '{{date}} من {{time}} (توقيت دبي)', $email_content );
			}
			
			$event_date_email = str_replace( ' - ', ' إلى ', $event_date_email );
			$event_time_frame_email = str_replace( ' - ', ' إلى ', $event_time_frame_email );

			$email_content = str_replace( '{{a_event_details}}', $event_details, $email_content );
			$email_content = str_replace( '{{a_attendee_fname}}', $attendee_arr['FirstName'], $email_content );
			$email_content = str_replace( '{{a_attendee_lname}}', $attendee_arr['LastName'], $email_content );
			$email_content = str_replace( '{{a_eventname}}', $event_post_title, $email_content );

			$event_subject_thank_you = str_replace( '{{a_eventname}}', $event_post_title, $event_subject_thank_you );

			// $subject_thank_you = get_option( 'arabic_mail_subject_thank_you' ); TODO - need?

			$in_arabic = true;
			$dear_text = 'السيد';
			$thank_you_text = 'نراك قريبا!';

		} else {

            $email_content = $content_thank_you_arr['English'];

            $event_subject_thank_you = $subject_thank_you_arr['English'];

			$event_post_title = $post_metas['dffmain_post_title'][0];
			$event_post_title = str_replace("&#039;", "'", $event_post_title);

			$event_location = $post_metas['dffmain_event_location'][0];
			$event_details  = $events_overview . '<br><br>' . $events_agenda;


			if( isset( $event_end_date ) && !empty( $event_end_date ) ) {
				$email_content     = str_replace( '{{date/time}}', '{{date}}', $email_content );
			} else {
				$email_content     = str_replace( '{{date/time}}', '{{date}} from {{time}} (GMT+4)', $email_content );
			}
			
			$event_date_email = str_replace( ' - ', ' to ', $event_date_email );
			$event_time_frame_email = str_replace( ' - ', ' to ', $event_time_frame_email );

			$email_content     = str_replace( '{{e_event_details}}', $event_details, $email_content );
			$email_content     = str_replace( '{{e_attendee_fname}}', $attendee_arr['FirstName'], $email_content );
			$email_content     = str_replace( '{{e_attendee_lname}}', $attendee_arr['LastName'], $email_content );
			$email_content     = str_replace( '{{e_eventname}}', $event_post_title, $email_content );

			$event_subject_thank_you = str_replace( '{{e_eventname}}', $event_post_title, $event_subject_thank_you );

			// $subject_thank_you = get_option( 'english_mail_subject_thank_you' ); TODO - need?

			$in_arabic = '';
			$dear_text = 'Dear';
			$thank_you_text = 'See you soon!';

		}

		
		$email_content = str_replace( '{{special_instruction}}', $event_special_instruction, $email_content );
		$email_content = str_replace( '{{location}}', $event_location, $email_content );
		// $email_content = str_replace( '{{future_id_link}}', $future_connect_link, $email_content ); TODO -- how we will do it?
		$email_content = str_replace( '{{date}}', $event_date_email, $email_content );
		$email_content = str_replace( '{{time}}', $event_time_frame_email, $email_content );

		if ( 'ar' === $language_type ) {

			$english_time_names = [
				'January',
				'February',
				'March',
				'April',
				'May',
				'June',
				'July',
				'August',
				'September',
				'October',
				'November',
				'December',
				'AM',
				'PM'
			];
			$arabic_time_names = [
				'كانون الثاني', 
				'شهر فبراير', 
				'مارس', 
				'أبريل', 
				'مايو', 
				'يونيو', 
				'يوليو', 
				'أغسطس', 
				'سبتمبر', 
				'اكتوبر', 
				'شهر نوفمبر', 
				'ديسمبر', 
				'صباحًا', 
				'مساءً', 
			];

			$email_content = str_replace( $english_time_names, $arabic_time_names, $email_content );
		}
		
		$event_subject_thank_you = str_replace( "'", "'", $event_subject_thank_you );

		if( isset( $future_id_link ) && !empty( $future_id_link ) ) { /** TODO -- how we do it? */

			$json_string = (object) array(
				'from'             => array( 'email' => 'no-reply@dubaifuture.ae' ),
				'personalizations' => array(
					array(
						'to'                    => array( array( 'email' => $email ) ),
						'dynamic_template_data' => array(
							'ARABIC'        => $in_arabic,
							'EMAIL_SUBJECT' => $event_subject_thank_you,
							'EMAIL_CONTENT' => wpautop( $email_content ),
							'DISPLAY_NAME'  => $attendee_arr['FirstName'],
							'HELLO'         => $dear_text,
							'EMAIL_HEADER'  => $event_subject_thank_you,
							'THANKS'        => $thank_you_text,
							'BUTTON'        => array(
								'TEXT'      => 'REGISTER FOR FUTURE ID',
								'LINK_TEXT' => 'Or paste this link into your browser:',
								'LINK'      => $future_id_link,
							),
							'FOOTER'        => '',
						),
					),
				),
				'template_id'      => 'd-e0a56b842d0541b0b34be68709f8798c',
				'attachments'      => array(
					array(
						'content'  => base64_encode( $iCal ),
						'filename' => 'event.ics',
					),
				),
			);

		} else {

			$json_string = (object) array(
				'from'             => array( 'email' => 'no-reply@dubaifuture.ae' ),
				'personalizations' => array(
					array(
						'to'                    => array( array( 'email' => $email ) ),
						'dynamic_template_data' => array(
							'ARABIC'        => $in_arabic,
							'EMAIL_SUBJECT' => $event_subject_thank_you,
							'EMAIL_CONTENT' => wpautop( $email_content ),
							'DISPLAY_NAME'  => $attendee_arr['FirstName'],
							'HELLO'         => $dear_text,
							'EMAIL_HEADER'  => $event_subject_thank_you,
							'THANKS'        => $thank_you_text,
							'FOOTER'        => '',
						),
					),
				),
				'template_id'      => 'd-e0a56b842d0541b0b34be68709f8798c',
				'attachments'      => array(
					array(
						'content'  => base64_encode( $iCal ),
						'filename' => 'event.ics',
					),
				),
			);

		}

		$request      = $url . 'v3/mail/send';
		$response_email = wp_remote_post( 
			$request, array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => 'Bearer ' . $sendgrid_apikey,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode( $json_string ),
			)
		);

	}

	if ( 'yes' === $api_registration ) {
		// Return string to the Rest Call back.
		$html = 'success';
	}

	if ( isset( $subscribe_now ) && 'Yes' === $subscribe_now ) {
		// API to mailchimp.
		$settings_array_get          = main_site_get_option( 'events_general_settings' );
		$events_general_settings_get = json_decode( $settings_array_get );
		$events_general_settings_get = (array) $events_general_settings_get;

		$list_id = $events_general_settings_get['mailchimp_list_id'];
		$api_key = $events_general_settings_get['mailchimp_auth_token'];

		$args = array(
			'method'  => 'PUT',
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
			),
			'body'    => wp_json_encode(
				array(
					'email_address' => $attendee_arr['Email'],
					'status'        => 'subscribed',
					'merge_fields'  => array(
						'FNAME' => $attendee_arr['FirstName'],
						'LNAME' => $attendee_arr['LastName'],
					),
					'interests'     => array(
						'0a7fcaab5f' => true,
						'7038234ce0' => true,
					),
				)
			),
		);

		$response = wp_remote_post( 'https://' . substr( $api_key, strpos( $api_key, '-' ) + 1 ) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5( strtolower( $email ) ), $args );

	}

	if ( 'yes' !== $api_registration ) {
		$subscribe_data = array(
			'event_id'    => $event_id,
			'attendee_id' => $post_id,
		);
		setcookie( 'subscriber_detail', wp_json_encode( $subscribe_data, JSON_UNESCAPED_SLASHES ), time() + ( 86400 * 30 ), '/' );
		setcookie( 'dff_event_id', $event_id, time() + ( 86400 * 30 ), '/' );
		header( 'location:' . get_bloginfo( 'url' ) . '/event-registration-thank-you' ); 
	}

}

