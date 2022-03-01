<?php
/**
 * The template for displaying single Events
 */
global $post;
get_header();
the_post();

$current_is_rtl = dffmain_mlp_check_if_is_rtl();

$server_https     = filter_input( INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_STRING );
$server_http_host = filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING );
$server_req_url   = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING );

$event_link = ( isset( $server_https ) && $server_https === 'on' ? 'https' : 'http' ) . "://$server_http_host$server_req_url";

$event_id = filter_input( INPUT_GET, 'eid', FILTER_SANITIZE_NUMBER_INT );
$event_id = isset( $event_id ) ? $event_id : $post->ID;
/**TODO if we need eid? */
$event_id = $post->ID;

$language = filter_input( INPUT_GET, 'lang', FILTER_SANITIZE_STRING );
$language = isset( $language ) ? $language : 'en';
/** TODO check for language */
$language = 'en';

$checkin = filter_input( INPUT_GET, 'checkin', FILTER_SANITIZE_STRING );
$checkin = isset( $checkin ) ? $checkin : 'false';


$settings_array_get          = main_site_get_option( 'events_general_settings' );
$events_general_settings_get = json_decode( $settings_array_get );
$events_general_settings_get = (array) $events_general_settings_get;

$site_key   = isset( $events_general_settings_get['site_key'] ) && ! empty( $events_general_settings_get['site_key'] ) ? $events_general_settings_get['site_key'] : 'hardcode_recapcha_site_key';
$secret_key = isset( $events_general_settings_get['secret_key'] ) && ! empty( $events_general_settings_get['secret_key'] ) ? $events_general_settings_get['secret_key'] : 'hardcode_recapcha_secret_key';


$event_attendee_limit_count = get_post_meta( $event_id, 'event_attendee_limit_count', true );
if ( isset( $event_attendee_limit_count ) && ! empty( $event_attendee_limit_count ) ) {
	$args                     = array(
		'post_type'  => 'attendees',
		'meta_query' => array(
			array(
				'key'   => 'event_id',
				'value' => $event_id,
			),
		),
	);
	$query                    = new WP_Query( $args );
	$found_posts              = $query->found_posts ? $query->found_posts : 0;
	$remaining_attendee_count = (int) $event_attendee_limit_count - (int) $found_posts;
}

if ( isset( $language ) && ! empty( $language ) ) {
	$event_title           = '';
	$events_overview       = '';
	$events_agenda         = '';
	$event_location        = '';
	$emp_category          = '';
	$event_date            = '';
	$event_start_time      = '';
	$event_end_time        = '';
	$emp_tags              = '';
	$event_details_heading = '';
	$meta                  = get_post_meta( $event_id );


	$event_date               = isset( $meta['event_date_select'][0] ) ? $meta['event_date_select'][0] : '-';
	$event_all_date           = isset( $meta['event_date_select'][0] ) ? $meta['event_date_select'][0] : '-';
	$event_end_date           = isset( $meta['event_end_date_select'][0] ) ? $meta['event_end_date_select'][0] : '';
	$event_start_time         = isset( $meta['event_time_start_select'][0] ) ? $meta['event_time_start_select'][0] : '-';
	$event_end_time           = isset( $meta['event_time_end_select'][0] ) ? $meta['event_time_end_select'][0] : '-';
	$event_cost               = isset( $meta['event_cost_name'][0] ) ? $meta['event_cost_name'][0] : '-';
	$event_google_map_link    = isset( $meta['event_google_map_input'][0] ) ? $meta['event_google_map_input'][0] : '';
	$event_detail_img         = isset( $meta['event_detail_img'][0] ) ? $meta['event_detail_img'][0] : '-';
	$field_preference         = isset( $meta['_wp_field_preference'][0] ) ? $meta['_wp_field_preference'][0] : '';
	$registration_template_id = isset( $meta['_wp_template_id'][0] ) ? $meta['_wp_template_id'][0] : '';
	$event_security_code      = isset( $meta['event_security_code'][0] ) ? $meta['event_security_code'][0] : '';
	$security_code_checkbox   = isset( $meta['security_code_checkbox'][0] ) ? $meta['security_code_checkbox'][0] : '';
	$registration_form_data   = get_post_meta( $registration_template_id, '_registration_form_data', true );
	$field_preference         = get_post_meta( $event_id, '_wp_field_preference', true );

	if( isset( $event_end_date ) && !empty( $event_end_date ) ) {
		$event_all_date = $event_end_date;
	}

	$wrap_class      = ' en-wrap';
	$container_class = ' en-field';

	$event_title             = isset( $meta['dffmain_post_title'][0] ) ? $meta['dffmain_post_title'][0] : '-';
	$events_overview         = isset( $meta['events_overview'][0] ) ? $meta['events_overview'][0] : '-';
	$events_agenda           = isset( $meta['dffmain_events_agenda'][0] ) ? $meta['dffmain_events_agenda'][0] : '-';
	$event_location          = isset( $meta['dffmain_event_location'][0] ) ? $meta['dffmain_event_location'][0] : '-';
	$emp_category            = isset( $meta['emp_category'][0] ) ? $meta['emp_category'][0] : '';
	$emp_tags                = isset( $meta['emp_tags'][0] ) ? $meta['emp_tags'][0] : '-';

	$locale_settings_array_get          = get_option( 'locale_events_general_settings' );
	$locale_events_general_settings_get = json_decode( $locale_settings_array_get );
	$locale_events_general_settings_get = (array) $locale_events_general_settings_get;

	$event_description       = $locale_events_general_settings_get['local_overview'];
	$agenda                  = $locale_events_general_settings_get['local_agenda'];
	$registration_form       = $locale_events_general_settings_get['local_registration_form'];
	$registration_form_close = $locale_events_general_settings_get['local_registration_is_closed'];
	$date                    = $locale_events_general_settings_get['local_date'];
	$time                    = $locale_events_general_settings_get['local_time'];
	$cost                    = $locale_events_general_settings_get['local_cost'];
	$location                = $locale_events_general_settings_get['local_location'];
	$category                = $locale_events_general_settings_get['local_category'];
	$register                = $locale_events_general_settings_get['local_register'];
	$remaining_attendee      = $locale_events_general_settings_get['local_remaining_seats'];
	$events                  = $locale_events_general_settings_get['local_events'];
	$event_details_heading   = $locale_events_general_settings_get['local_event_details'];

	$event_date        = ( ! empty( $event_date ) ) ? date( 'F d, Y', strtotime( $event_date ) ) : '-';
	$event_end_date    = ( ! empty( $event_end_date ) ) ? date( 'F d, Y', strtotime( $event_end_date ) ) : '';

	if ( $current_is_rtl ) {
		$wrap_class      = ' ar-wrap';
		$container_class = ' ar-field';

		$months = [
			'January'   => 'كانون الثاني',
			'February'  => 'شهر فبراير',
			'March'     => 'مارس',
			'April'     => 'أبريل',
			'May'       => 'مايو',
			'June'      => 'يونيو',
			'July'      => 'يوليو',
			'August'    => 'أغسطس',
			'September' => 'سبتمبر',
			'October'   => 'اكتوبر',
			'November'  => 'شهر نوفمبر',
			'December'  => 'ديسمبر',
		];
		if ( ! empty( $event_date ) ) {
			$day   = date( 'd', strtotime( $event_date ) );
			$month = date( 'F', strtotime( $event_date ) );
			$year  = date( 'Y', strtotime( $event_date ) );
			$month = $months[ $month ];

			$event_date = $month . ' ' . $day . ' ,' . $year;
		}
		if ( ! empty( $event_end_date ) ) {
			$day   = date( 'd', strtotime( $event_end_date ) );
			$month = date( 'F', strtotime( $event_end_date ) );
			$year  = date( 'Y', strtotime( $event_end_date ) );
			$month = $months[ $month ];

			$event_end_date = $month . ' ' . $day . ', ' . $year;
		}
	}

	$emp_categoryArr = isset( $emp_category ) && ! empty( $emp_category ) ? explode( ',', $emp_category ) : [];
	$category_name   = '';

	if ( ! empty( $emp_categoryArr ) ) {
		foreach ( $emp_categoryArr as $val ) {
			$category_name .= get_term( $val )->name . ', ';
		}
		$category_name = rtrim( $category_name, ', ' );
	}

	$auth_details_array = array();

	$auth_details_array[0] = isset( $_COOKIE['auth_firstName'] ) ? $_COOKIE['auth_firstName'] : "";
	$auth_details_array[1] = isset( $_COOKIE['auth_lastName'] ) ? $_COOKIE['auth_lastName'] : "";
	$auth_details_array[2] = isset( $_COOKIE['auth_email'] ) ? $_COOKIE['auth_email'] : "";

	$field_html = '';
	if ( ! empty( $registration_form_data ) && isset( $registration_form_data ) ) {
		foreach ( $registration_form_data as $key => $item ) {

			if ( !$current_is_rtl ) {
				$field_arr  = $item['en'];
				$class_name = ' en-field';
				$name       = $field_arr['id'];
			} else {
				$field_arr  = $item['ar'];
				$class_name = ' ar-field';
				$name       = substr_replace( $field_arr['id'], 'en', 0, 2 );
			}

			if ( $field_arr['required'] ) {
				$required_field = '<sup class="medatory"> *</sup>';
				$required_class = ' required';
			} else {
				$required_field = '';
				$required_class = '';
			}
			
			if ( 3 > $key ) {
								
				if( isset( $auth_details_array ) && !empty( $auth_details_array ) ) {
					$field_html .= '<div class="field-wrap' . $required_class . '">
					<div class="field-container' . $class_name . '">
						<span class="field-label">' . $field_arr['label'] . $required_field . '</span>
						<label class="screen-reader-text" for="' . $field_arr['id'] . '">' . $field_arr['id'] . '</label>
							<input type="' . $field_arr['type'] . '" name="' . $field_arr['id'] . '" id="' . $field_arr['id'] . '" value="'.$auth_details_array[$key].'">
							</div>
								</div>';

				} else {
					$field_html .= '<div class="field-wrap' . $required_class . '">
                                    <div class="field-container' . $class_name . '">
                                        <span class="field-label">' . $field_arr['label'] . $required_field . '</span>
                                        <label class="screen-reader-text" for="' . $field_arr['id'] . '">' . $field_arr['id'] . '</label>
                                            <input type="' . $field_arr['type'] . '" name="' . $field_arr['id'] . '" id="' . $field_arr['id'] . '">
                                    </div>
								</div>';
				}

			} else {
				if ( 'true' === $field_preference[ $name ] ) {
					if ( 'Text Input' === $field_arr['control'] ) {
						$field_html .= '<div class="field-wrap' . $required_class . '">
                                            <div class="field-container' . $class_name . '">
                                                <span class="field-label">' . $field_arr['label'] . $required_field . '</span>
                                                <label class="screen-reader-text" for="' . $field_arr['id'] . '">' . $field_arr['id'] . '</label>
                                                <input type="' . $field_arr['type'] . '" name="' . $field_arr['id'] . '" id="' . $field_arr['id'] . '">
                                            </div>
                                        </div>';
					} elseif ( 'Text Area' === $field_arr['control'] ) {
						$field_html .= '<div class="field-wrap' . $required_class . '">
                                                        <div class="field-container' . $class_name . '">
                                                            <span class="field-label">' . $field_arr['label'] . $required_field . '</span>
                                                            <label class="screen-reader-text" for="' . $field_arr['id'] . '">' . $field_arr['id'] . '</label>
                                                            <textarea type="textarea" class="form-control" name="' . $field_arr['id'] . '" id="' . $field_arr['id'] . '"></textarea>
                                                        </div>
                                                    </div>';
					} elseif ( 'Dropdown Select' === $field_arr['control'] ) {
						$field_options = $field_arr['values'];
						sort( $field_options );
						$choose = ( 'en' === $language ) ? 'Choose' : 'أختر';
						if ( $field_arr['multiple'] ) {
							$multiple = 'multiple';
						} else {
							$multiple = '';
						}

						if ( ! empty( $field_options ) ) {
							$option_html = '';
							for ( $i = 0; $i < sizeof( $field_options ); $i++ ) {
								$option_html .= '<option value="' . $field_options[ $i ]['value'] . '">' . $field_options[ $i ]['value'] . '</option>';
							}
							$field_html .= '<div class="field-wrap' . $required_class . '">
                                                <div class="field-container' . $class_name . '">
                                                    <span class="field-label">' . $field_arr['label'] . $required_field . '</span>
                                                    <label class="screen-reader-text" for="' . $field_arr['id'] . '">' . $field_arr['id'] . '</label>
                                                    <select name="' . $field_arr['id'] . '" id="' . $field_arr['id'] . '" ' . $multiple . '>
                                                        <option value="">' . $choose . '</option>
                                                        ' . $option_html . '
                                                    </select>
                                                </div>
                                            </div>';
						}
					} elseif ( 'Radio' === $field_arr['control'] ) {
						$field_options = $field_arr['values'];

						if ( ! empty( $field_options ) ) {
							$option_html = '';
							for ( $i = 0; $i < sizeof( $field_options ); $i++ ) {
								$option_html .= '<div class="formbuilder-radio">
                                                        <input name="' . $field_arr['id'] . '" id="' . $field_arr['id'] . $i . '" type="radio" value="' . $field_options[ $i ]['value'] . '">
                                                        <label for="' . $field_arr['id'] . $i . '">' . $field_options[ $i ]['value'] . '</label>
                                                    </div>';
							}
							$field_html .= '<div class="field-wrap' . $required_class . '">
                                                <div class="field-container' . $class_name . '">
                                                    <span class="field-label">' . $field_arr['label'] . $required_field . '</span>
                                                    <div class="radio-group">
                                                        ' . $option_html . '
                                                    </div>
                                                </div>
                                            </div>';
						}
					} elseif ( 'Checkbox' === $field_arr['control'] ) {
						$field_options = $field_arr['values'];

						if ( ! empty( $field_options ) ) {
							$option_html = '';
							for ( $i = 0; $i < sizeof( $field_options ); $i++ ) {
								$option_html .= '<div class="formbuilder-checkbox">
                                                        <input name="' . $field_arr['id'] . '[]" id="' . $field_arr['id'] . $i . '" type="checkbox" value="' . $field_options[ $i ]['value'] . '">
                                                        <label for="' . $field_arr['id'] . $i . '">' . $field_options[ $i ]['value'] . '</label>
                                                    </div>';
							}
							$field_html .= '<div class="field-wrap' . $required_class . '">
                                                <div class="field-inner">
                                                    <div class="field-container' . $class_name . '">
                                                        <span class="field-label">' . $field_arr['label'] . $required_field . '</span>
                                                        <div class="checkbox-group">
                                                            ' . $option_html . '
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
						}
					} elseif ( 'File Upload' === $field_arr['control'] ) {
						$uploadOptions = $field_arr['uploadOptions'];
						$allowedType   = array();
						foreach ( $uploadOptions as $key => $val ) {
							if ( $val ) {
								$allowedType[] = $key;
							}
						}
						$button1     = str_replace( ' ', '', $field_arr['button1'] );
						$allowedType = implode( ', ', $allowedType );
						$field_html .= '<div class="field-wrap' . $required_class . '">
                                           <div class="field-container' . $class_name . '">
                                               <span class="field-label">' . $field_arr['label'] . $required_field . '</span>
                                               <div class="field-group">
                                                   <div class="file-upload-wrap">
                                                        <label for="en_filename" class="screen-reader-text">en_filename</label>
                                                        <input type="text" id="en_filename" class="filename" readOnly="true"  />
                                                   
                                                        <label for="' . $field_arr['id'] . '" class="screen-reader-text">' . $field_arr['id'] . '</label>
                                                        <input type="file" multiple name="' . $field_arr['id'] . '[]" id="' . $field_arr['id'] . '" class="form-control file-upload"   allowed-file-type="' . $allowedType . '" onchange="Handlechange(this.id);" />
                                                   </div>
                                                   <div class="button-wrap">
                                                       <label for="' . $button1 . '" class="screen-reader-text">' . $field_arr['id'] . '</label>
                                                       <input type="button" name="' . $button1 . '" value="' . $field_arr['button1'] . '" onclick="HandleBrowseClick(this.id);" id="' . $button1 . '" class="button button-primary" />
                                                   </div>
                                               </div>
                                           </div>
                                       </div>';
					}
				}
			}
		}
	}

	if ( ! empty( $event_security_code ) && '' !== $event_security_code && 'true' === $security_code_checkbox ) {
		if ( $current_is_rtl ) {
			$field_html .= '<div class="field-wrap required">
                                <div class="field-container ar-field">
                                    <span class="field-label">رمز الأمان / رمز الحدث<sup class="medatory"> *</sup></span>
                                    <label for="security_code" class="screen-reader-text">security_code</label>
                                    <input type="text" name="arSecurityCode" id="security_code" value="" class="form-control">
                                </div>
                            </div>';
		} else {
			$field_html .= '<div class="field-wrap required">
                                <div class="field-container en-field">
                                    <span class="field-label">Invitation code<sup class="medatory"> *</sup></span>
                                    <label for="security_code" class="screen-reader-text">security_code</label>
                                    <input type="text" name="enSecurityCode" id="security_code" value="" class="form-control">
                                </div>
                            </div>';
		}
	}

	if ( $current_is_rtl ) {
		$field_html .= '<div class="field-wrap subscribe">
                            <div class="field-container ar-field">                
                                <label for="subscribe_now" class="screen-reader-text"></label>
                                <input type="checkbox" id="subscribe_now" name="subscribe_now" class="subscribe_now" value="Yes"><span>أرغب في الاشتراك بنشرة أخبار مؤسسة دبي للمستقبل</span>
                            </div>
                        </div>';
	} else {
		$field_html .= '<div class="field-wrap subscribe">
            <div class="field-container en-field">
                <label for="subscribe_now" class="screen-reader-text">subscribe_now</label>
                <input type="checkbox" id="subscribe_now" name="subscribe_now" class="subscribe_now" value="Yes"><span>I would like to subscribe to the Dubai Future Foundation newsletter.</span>
            </div>
        </div>
        ';
	}



	// $field_html  .= '<div class="field-wrap google-captcha required">
    //                     <div class="field-container">
    //                         <div class="captcha-field">
    //                             <div class="g-recaptcha" data-sitekey="' . $site_key . '"></div>
    //                         </div>
    //                     </div>
    //                 </div>
    //                 ';
	$allowed_tags = array(
		'div'      => array(
			'class'        => array(),
			'style'        => array(),
			'data-sitekey' => array(),
		),
		'span'     => array(
			'class' => array(),
			'style' => array(),
		),
		'label'    => array(
			'for'   => array(),
			'style' => array(),
			'class' => array(),
		),
		'input'    => array(
			'id'                => array(),
			'type'              => array(),
			'value'             => array(),
			'name'              => array(),
			'class'             => array(),
			'readonly'          => array(),
			'multiple'          => array(),
			'allowed-file-type' => array(),
			'onchange'          => array(),
			'style'             => array(),
			'onclick'           => array(),
		),
		'textarea' => array(
			'id'    => array(),
			'type'  => array(),
			'value' => array(),
			'name'  => array(),
			'class' => array(),
		),
		'select'   => array(
			'id'       => array(),
			'name'     => array(),
			'multiple' => array(),
		),
		'option'   => array( 'value' => array() ),
		'sup'      => array( 'class' => array() ),
	);
	?>


	<ul class="breadcrumbs">
		<li class="breadcrumb">
			<a aria-label="home" role="complementary" target="_blank" href="<?php echo home_url(); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="17.333" height="16" viewBox="0 0 17.333 16">
					<path fill="currentColor" id="Icon_ionic-md-home" data-name="Icon ionic-md-home" d="M10.042,20.5V15.167h4V20.5h4.067v-8h2.6l-8.667-8-8.667,8h2.6v8Z" transform="translate(-3.375 -4.5)"></path>
				</svg>
			</a>
		</li>
		<li class="breadcrumb">
			<?php $page_for_events_archive_obj = get_page_by_path( 'events' ); ?>
			<a aria-label="Events" target="_blank" href="<?php echo get_permalink( $page_for_events_archive_obj ); ?>">
				<?php echo esc_html( get_the_title( $page_for_events_archive_obj ) ); ?>
			</a>
		</li>
		<li class="breadcrumb"><?php echo esc_html( get_the_title() ); ?></li>
	</ul>


	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="admin-front-register-page <?php echo esc_attr( $wrap_class ); ?>">
				<div class="container">
					<div class="row">
						<?php 
						if ( $current_is_rtl ) { 
							?>
							<div class="col col-right">
								<div class="event-placeholder-image event-placeholder-ar-align">
									<img src="<?php echo get_the_post_thumbnail_url( $event_id, 'thumbnail' ); ?>" alt="event-placeholder-image">
								</div>
								<div class="sidebar">
									<div class="sidebar-title">
										<h3>
											<?php echo esc_html( $event_details_heading ); ?>
										</h3>
									</div>
									<div class="data-item-main date-panel">
										<div class="data-item-content">
											<h4>
												<?php echo esc_html( $date ); ?>
											</h4>
											<p>
												<?php echo esc_html( $event_date ); ?><?php if( isset( $event_end_date ) && !empty( $event_end_date ) ) { echo " - ". esc_html( $event_end_date ); } ?>
											</p>
										</div>
									</div>
									<?php 
									if( empty( $event_end_date ) ) { ?>
										<div class="data-item-main time-panel">
											<div class="data-item-content">
												<h4>
													<?php echo esc_html( $time ); ?>
												</h4>
												<?php
												$event_start_time = new DateTime( "$event_start_time" );
												$event_end_time   = new DateTime( "$event_end_time" );
												if ( ! empty( $event_start_time ) && ! empty( $event_end_time ) ) {
													$event_time_frame = $event_start_time->format( 'h:i A' ) . ' - ' . $event_end_time->format( 'h:i A' );
													$event_time_frame = str_replace( 'AM', 'صباحًا', $event_time_frame );
													$event_time_frame = str_replace( 'PM', 'مساءً', $event_time_frame );
													?>
													<p>
														<?php echo esc_html( $event_time_frame ); ?>
													</p>
													<?php 
												} else { 
													?>
													<p>-</p>
													<?php 
												} 
												?>
											</div>
										</div>
										<?php 
									} 

									if ( isset( $event_cost ) && ! empty( $event_cost ) ) {
										?>
											<div class="data-item-main cost-panel">
												<div class="data-item-content">
													<h4><?php echo esc_html( $cost ); ?></h4>
													<p><?php echo esc_html( $event_cost ); ?></p>
												</div>
											</div>	
										<?php
									}

									if ( isset( $event_location ) && ! empty( $event_location ) ) {
										?>
										<div class="data-item-main location-panel">
											<div class="data-item-content">
												<h4>
													<?php echo esc_html( $location ); ?>
												</h4>													
												<?php 
												if ( ! empty( $event_google_map_link ) ) { 
													?>
													<p>
														<a aria-label="event_google_map_link" href="<?php echo esc_url( $event_google_map_link ); ?>"target="_blank">
															<?php echo esc_html( $event_location ); ?>
														</a>
													</p>
													<?php 
												} else { 
													?>
													<p>
														<?php echo esc_html( $event_location ); ?>
													</p>
													<?php 
												} 
												?>
											</div>
										</div>
										<?php
									}
								
									if ( isset( $category_name ) && ! empty( $category_name ) ) {
										?>
											<div class="data-item-main categroy-panel">
												<div class="data-item-content">
													<h4>
														<?php echo esc_html( $category ); ?>
													</h4>
													<?php 
													$category_name_array = explode( ",", $category_name );
													$single_cat_link = [];
													foreach( $category_name_array as $category_name_array_value ) {
														$single_cat_link[] = '<a href="https://www.dubaifuture.ae/events/?filter='.strtolower( str_replace(" ","-",$category_name_array_value) ).'"> '.$category_name_array_value.'</a>';
													}
													?>
													<p>
														<?php echo implode( ", ", $single_cat_link ); ?> 
													</p>
												</div>
											</div>
										<?php
									}

									if ( isset( $event_attendee_limit_count ) && ! empty( $event_attendee_limit_count ) ) {
										?>
										<div class="data-item-main attendee-count-panel">
											<div class="data-item-content">
												<h4>
													<?php echo esc_html( $remaining_attendee ); ?>
												</h4>
												<p>
													<?php
													if ( 0 === $remaining_attendee_count ) {
														echo '0';
													} else {
														echo esc_html( $remaining_attendee_count ); 
													}
													?>
												</p>
											</div>
										</div>
										<?php
									}
									?>
								</div>
							</div>
							<div class="col col-left">
								<div class="event-data-wrap">
									<div class="event-title-wrp">
										<div class="hero-scrollToIcon">
											<a aria-label="event_registration_form" href="#event_registration_form" class="hero-scrollTo">
												<svg xmlns="http://www.w3.org/2000/svg" role="complementary" width="10" height="26" viewBox="0 0 10 26">
													<path id="Union_1" data-name="Union 1" d="M-6020,20h4V0h2V20h4l-5,6Z" transform="translate(6020)" fill="white">
													</path>
												</svg>
											</a>
										</div>
										<h1>
											<?php echo esc_html( $event_title ); ?>
										</h1>
									</div>
									<?php
									$event_detail_img = get_post_meta( $event_id, 'event_detail_img', true );
									$image_attributes = wp_get_attachment_image_src( $event_detail_img, 'full' );
									$image_title      = get_the_title( $event_detail_img );
									$image_alt        = get_post_meta( $event_detail_img, '_wp_attachment_image_alt', true );
									$image_alt_display = '';
									if ( isset( $image_alt ) && ! empty( $image_alt ) ) {
										$image_alt_display = $image_alt;
									} else {
										$image_alt_display = $image_title; 
									}

									if ( isset( $image_attributes ) && ! empty( $image_attributes ) ) {
										?>
										<img 
											class="event_detail_image" 
											src="<?php echo esc_url( $image_attributes[0] ); ?>"
											alt="<?php echo $image_alt_display; ?>"
										>
									<?php
									}
									?>
									<div class="row event-data-row">
										<div class="col event-share-col">
											<div class="article-shareInner">
												<h4>SHARE</h4>
												<ul class="social-icons">
													<li>
														<a aria-label="facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $event_link ); ?>">
															<img src="<?php  echo plugin_dir_url( dirname( __FILE__ ) ) . 'public/images/facebook.png';; ?>" alt="facebook">
														</a>
													</li>
													<li>
														<a aria-label="twitter" target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo esc_attr( $event_title ); ?> url=<?php echo esc_attr( $event_link ); ?>">
															<img src="<?php  echo plugin_dir_url( dirname( __FILE__ ) ) . 'public/images/twitter.png';; ?>" alt="twitter">
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="col event-data-col">
											<?php
											if ( isset( $events_overview ) && ! empty( $events_overview ) ) {
												?>
													<div class="page-intro-content">
														<h4><?php echo esc_html( $event_description ); ?></h4>
														<p><?php echo wp_kses_post( wpautop( $events_overview ) ); ?></p>
													</div>
												<?php
											}
											?>
											<?php
											if ( isset( $events_agenda ) && ! empty( $events_agenda ) ) {
												?>
													<div class="event-agenda">
														<h4><?php echo esc_html( $agenda ); ?></h4>
														<?php echo wp_kses_post( wpautop( $events_agenda ) ); ?>
													</div>
												<?php
											}
											?>
										</div>
									</div>
								</div>
								<?php
								$event_cancelled = get_post_status( $post->ID );
								if( 'cancelled' === $event_cancelled ) { 
									?>
									<div class="admin-front-register-form">
										<h4 id="event_registration_form"><?php echo esc_html( 'تم إلغاء الحدث.' ); ?></h4>
									</div>
									<?php
								} else {
									if( 'true' === $checkin || ( (int) $remaining_attendee_count > 0 || empty( $event_attendee_limit_count ) ) && ( strtotime( $event_all_date ) >= strtotime( date( 'd F Y' ) ) ) ) {
										if ( ! empty( $registration_form_data ) ) {
											?>
												<div class="admin-front-register-form">
													<div class="event_registration_form_title_wrap">
														<h4 id="event_registration_form"><?php echo esc_html( $registration_form ); ?></h4>
														<?php 
															//if( 'true' !== $checkin ) {
																?>
																<!-- <div class="button-wrap arabic-connect-id">
																	<button id="login-button" onclick="return false"><?php //echo esc_html( 'سجل مع الهوية المستقبلية' ); ?></button>
																</div> -->
																<?php
															//}
														?>
													</div>												
													<form 
														class="attendee-form" 
														id="attendee-form"
														action="<?php echo plugins_url( 'thank-you.php', __FILE__ ); ?>"
														method="post" 
														enctype="multipart/form-data"
													>
														<div class="registration-template">
															<div class="form-field-wrapper">
																<?php echo wp_kses( $field_html, $allowed_tags ); ?>
															</div>
															<div class="hidden-fields">
																<input type="hidden" class="event_title" name="event_title"
																	value="<?php echo get_the_title( $event_id ); ?>">
																<input type="hidden" class="event_id" name="event_id"
																	value="<?php echo esc_attr( $event_id ); ?>">
																<input type="hidden" class="language_type" name="language_type"
																	value="<?php echo esc_attr( $language ); ?>">
																<input type="hidden" class="scode" name="scode"
																	value="<?php echo esc_attr( $event_security_code ); ?>">
																<input type="hidden" class="checkin" name="checkin"
																	value="<?php echo esc_attr( $checkin ); ?>">
															</div>
															<div class="button-wrap">
																<button class="button button-primary register"><?php echo esc_html( $register ); ?></button>
															</div>
														</div>
													</form>
												</div>
											<?php
										}
									} else {
										$event_registration_close_message_ar = get_post_meta( $post->ID, 'event_registration_close_message_ar', true );
										?>
										<div class="admin-front-register-form">
											<h4 id="event_registration_form"><?php echo esc_html( $registration_form_close ); ?></h4>
											<p><?php echo esc_html( $event_registration_close_message_ar ); ?></p>
										</div>
										<?php
									}
								}
								?>
							</div>
							<?php 
						} else { 
							?>
							<div class="col col-left">
								<div class="event-data-wrap">
									<div class="event-title-wrp">
										<h1><?php echo esc_html( get_the_title() ); ?></h1>
										<div class="hero-scrollToIcon">
											<a aria-label="event_registration_form" href="#event_registration_form" class="hero-scrollTo">
												<svg xmlns="http://www.w3.org/2000/svg" role="complementary" width="10" height="26" viewBox="0 0 10 26">
													<path id="Union_1" data-name="Union 1" d="M-6020,20h4V0h2V20h4l-5,6Z" transform="translate(6020)" fill="white"></path>
												</svg>
											</a>
										</div>
									</div>
									<?php
									$event_detail_img = get_post_meta( $event_id, 'event_detail_img', true );
									$image_attributes = wp_get_attachment_image_src( $event_detail_img, 'full' );
									$image_title      = get_the_title( $event_detail_img );
									$image_alt        = get_post_meta( $event_detail_img, '_wp_attachment_image_alt', true );

									$image_alt_display = '';
									if ( isset( $image_alt ) && ! empty( $image_alt ) ) {
										$image_alt_display = $image_alt;
									} else {
										$image_alt_display = $image_title; 
									}
									if ( isset( $image_attributes ) && ! empty( $image_attributes ) ) {
										?>
										<img 
											class="event_detail_image" 
											src="<?php echo esc_url( $image_attributes[0] ); ?>"
											alt="<?php echo $image_alt_display; ?>"
										>
										<?php
									}
									?>
									<div class="row event-data-row">
										<div class="col event-data-col">
											<?php
											if ( isset( $events_overview ) && ! empty( $events_overview ) ) {
												?>
												<div class="page-intro-content">
													<h4>
														<?php echo esc_html( $event_description ); ?>
													</h4>
													<p>
														<?php echo wp_kses_post( wpautop( $events_overview ) ); ?>
													</p>
												</div>
												<?php
											}

											if ( isset( $events_agenda ) && ! empty( $events_agenda ) ) {
												?>
													<div class="event-agenda">
														<h4><?php echo esc_html( $agenda ); ?></h4>
														<?php echo wp_kses_post( wpautop( $events_agenda ) ); ?>
													</div>
												<?php
											}
											?>
										</div>
										<div class="col event-share-col">
											<div class="article-shareInner">
												<h4>
													SHARE
												</h4>
												<ul class="social-icons">
													<li>
														<a aria-label="facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $event_link ); ?>">
															<img src="<?php  echo plugin_dir_url( dirname( __FILE__ ) ) . 'public/images/facebook.png';; ?>" alt="facebook">
														</a>
													</li>
													<li>
														<a aria-label="twitter" target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo esc_attr( $event_title ); ?> url=<?php echo esc_attr( $event_link ); ?>">
															<img src="<?php  echo plugin_dir_url( dirname( __FILE__ ) ) . 'public/images/twitter.png';; ?>" alt="twitter">
														</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<?php
								$event_cancelled = get_post_status( $post->ID );
								if( 'cancelled' === $event_cancelled ) { 
									?>
									<div class="admin-front-register-form">
										<h4 id="event_registration_form"><?php echo esc_html( 'The event is cancelled.' ); ?></h4>
									</div>
									<?php
								} else {
									
									if( 'true' === $checkin || ( (int) $remaining_attendee_count > 0 || empty( $event_attendee_limit_count ) ) && ( strtotime( $event_all_date ) >= strtotime( date( 'd F Y' ) ) ) ) {

										if ( ! empty( $registration_form_data ) ) {
											?>
												<div class="admin-front-register-form">
													<div class="event_registration_form_title_wrap">
														<h4 id="event_registration_form">
															<?php echo esc_html( $registration_form ); ?>
														</h4>
														<?php 
															/** TODO check in */
															//if( 'true' !== $checkin ) {
																?>
																<!-- <div class="button-wrap">
																	<button id="login-button" onclick="return false"><?php //echo esc_html( 'Register with FUTURE ID' ); ?></button>
																</div> -->
																<?php
															//}
														?>
													</div>
													<form 
														class="attendee-form" 
														id="attendee-form"
														action="<?php echo plugins_url( 'thank-you.php', __FILE__ ); ?>"
														method="post" 
														enctype="multipart/form-data"
													>
														<div class="registration-template">
															<div class="form-field-wrapper">
																<?php echo wp_kses( $field_html, $allowed_tags ); ?>
															</div>
															<div class="hidden-fields">
																<input 
																	type="hidden" 
																	class="event_title" 
																	name="event_title"
																	value="<?php echo get_the_title( $event_id ); ?>"
																>
																<input 
																	type="hidden" 
																	class="event_id" 
																	name="event_id"
																	value="<?php echo esc_attr( $event_id ); ?>"
																>
																<input 
																	type="hidden" 
																	class="language_type" 
																	name="language_type"
																	value="<?php echo esc_attr( $language ); ?>"
																>
																<input 
																	type="hidden" 
																	class="scode" 
																	name="scode"
																	value="<?php echo esc_attr( $event_security_code ); ?>"
																>
																<input 
																	type="hidden" 
																	class="checkin" 
																	name="checkin"
																	value="<?php echo esc_attr( $checkin ); ?>"
																>
															</div>
															<br>
															<div class="button-wrap">
																<button class="button button-primary register"><?php echo esc_html( $register ); ?></button>
															</div>
														</div>
													</form>
												</div>
										<?php
										}
									} else {
										$event_registration_close_message = get_post_meta( $post->ID, 'event_registration_close_message', true );
										?>
										<div class="admin-front-register-form">
											<h4 id="event_registration_form">
												<?php echo esc_html( $registration_form_close ); ?>
											</h4>
											<p>
												<?php echo esc_html( $event_registration_close_message ); ?>
											</p>
										</div>
										<?php
									} 

								}
								?>
							</div>
							<div class="col col-right">
								<div class="event-placeholder-image">
									<img src="<?php echo get_the_post_thumbnail_url( $event_id, 'thumbnail' ); ?>" alt="event-placeholder-image">
								</div>
								<div class="sidebar">
									<div class="sidebar-title">
										<h3>
											<?php echo esc_html( $event_details_heading ); ?>
										</h3>
									</div>
									<div class="data-item-main date-panel">
										<div class="data-item-content">
											<h4>
												<?php echo esc_html( $date ); ?>
											</h4>
											<p>
												<?php echo esc_html( $event_date ); ?><?php if( isset( $event_end_date ) && !empty( $event_end_date ) ) { echo " - " . esc_html( $event_end_date ); } ?>
											</p>
										</div>
									</div>
									<?php 
									if( empty( $event_end_date ) ) {
										?>
										<div class="data-item-main time-panel">
											<div class="data-item-content">
												<h4>
													<?php echo esc_html( $time ); ?>
												</h4>
												<?php
												$event_start_time = new DateTime( "$event_start_time" );
												$event_end_time   = new DateTime( "$event_end_time" );
												if ( ! empty( $event_start_time ) && ! empty( $event_end_time ) ) {
												?>
													<p>
														<?php echo esc_html( $event_start_time->format( 'h:i A' ) ); ?> - <?php echo esc_html( $event_end_time->format( 'h:i A' ) ); ?>
													</p>
													<?php
												} else {
													?>
													<p>-</p>
													<?php
												}
												?>
											</div>
										</div>	
										<?php
									}

									if ( isset( $event_cost ) && ! empty( $event_cost ) ) {
										?>
										<div class="data-item-main cost-panel">
											<div class="data-item-content">
												<h4>
													<?php echo esc_html( $cost ); ?>
												</h4>
												<p>
													<?php echo esc_html( $event_cost ); ?>
												</p>
											</div>
										</div>	
										<?php
									}

									if ( isset( $event_location ) && ! empty( $event_location ) ) {
										?>
										<div class="data-item-main location-panel">
											<div class="data-item-content">
												<h4>
													<?php echo esc_html( $location ); ?>
												</h4>
												<?php 
												if ( ! empty( $event_google_map_link ) ) { 
												?>
													<p>
														<a aria-label="event_google_map_link" href="<?php echo esc_url( $event_google_map_link ); ?>"target="_blank">
															<?php echo esc_html( $event_location ); ?>
														</a>
													</p>
												<?php 
												} else { 
												?>
													<p>
														<?php echo esc_html( $event_location ); ?>
													</p>
												<?php 
												} 
												?>
											</div>
										</div>
										<?php
									}

									if ( isset( $category_name ) && ! empty( $category_name ) ) {
										?>
										<div class="data-item-main categroy-panel">
											<div class="data-item-content">
												<h4>
													<?php echo esc_html( $category ); ?>
												</h4>
												<?php 
												$category_name_array = explode( ",", $category_name );
												$single_cat_link = [];
												foreach( $category_name_array as $category_name_array_value ) {
													$single_cat_link[] = '<a href="https://www.dubaifuture.ae/events/?filter='.strtolower( str_replace(" ","-",$category_name_array_value) ).'"> '.$category_name_array_value.'</a>';
												}
												?>
												<p>
													<?php echo implode( ", ", $single_cat_link ); ?>
												</p>
											</div>
										</div>
										<?php
									}

									if ( isset( $event_attendee_limit_count ) && ! empty( $event_attendee_limit_count ) ) {
										?>
										<div class="data-item-main attendee-count-panel">
											<div class="data-item-content">
												<h4>
													<?php echo esc_html( $remaining_attendee ); ?>
												</h4>
												<p>
													<?php
													if ( (int) $remaining_attendee_count <= 0 ) {
														echo '0';
													} else {
														echo esc_html( $remaining_attendee_count );
													}
													?>
												</p>
											</div>
										</div>
										<?php
									}
									?>
								</div>
							</div>
							<?php 
						} 
						?>
					</div>
				</div>
			</div>
			
<?php
}
get_footer();