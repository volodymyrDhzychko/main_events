<?php
/**
 * Template Name: Thank you Page
 */

get_header();


// $language_type = filter_input( INPUT_GET, 'lang', FILTER_SANITIZE_STRING );
// $language_type = isset( $language_type ) && ! empty( $language_type ) ? $language_type : 'en';
$current_is_rtl = dffmain_mlp_check_if_is_rtl();

$event_id = filter_input( INPUT_COOKIE, 'dff_event_id', FILTER_SANITIZE_STRING );
$event_id = isset( $event_id ) ? $event_id : '';

$featured_image_id = get_post_thumbnail_id( $event_id );
$image_title       = get_the_title( $featured_image_id );

$image_alt = get_post_meta( $featured_image_id, '_wp_attachment_image_alt', true );

if ( !isset( $image_alt ) || empty( $image_alt ) ) {
    $image_alt = $image_title;
}

$image_size       = 'full';
$event_detail_img = get_post_meta( $event_id, 'event_detail_img', true );
$image_attributes = wp_get_attachment_image_src( $event_detail_img, $image_size );

$event_date_select = get_post_meta( $event_id, 'event_date_select', true );
$event_date_select = ( ! empty( $event_date_select ) ) ? date( 'F d, Y', strtotime( $event_date_select ) ) : '-';

$event_end_date_select = get_post_meta( $event_id, 'event_end_date_select', true );
$event_end_date_select = ( ! empty( $event_end_date_select ) ) ? date( 'F d, Y', strtotime( $event_end_date_select ) ) : '';

$event_time_start_select = get_post_meta( $event_id, 'event_time_start_select', true );
$event_time_end_select   = get_post_meta( $event_id, 'event_time_end_select', true );

$event_google_map_link = get_post_meta( $event_id, 'event_google_map_input', true );

$event_start_time = new DateTime( "$event_time_start_select" );
$event_end_time   = new DateTime( "$event_time_end_select" );
$event_time_frame = $event_start_time->format( 'h:i A' ) . ' - ' . $event_end_time->format( 'h:i A' );


if ( $current_is_rtl ) {

	$thank_you_message      = 'شكرا لتسجيلك!';
	$thank_you_subtitle     = 'أنت تحضر:';
	$heading                = get_post_meta( $event_id, 'dffmain_post_title', true );
	$events_arabic_overview = get_post_meta( $event_id, 'events_overview', true );

	if( '' === $heading || '' === $events_arabic_overview ) {

        $event_main_site_link = '';

        $translation_ids = get_translations_ids();
        foreach ( $translation_ids as $site_id => $post_id ) {

            if ( get_main_site_id() == $site_id ) {

                switch_to_blog( $site_id );
                    $event_main_site_link = get_permalink( $post_id );
                restore_current_blog();
            }
        }
		wp_safe_redirect( $event_main_site_link );
		exit;
	}

	$paragraph  = 'ستتلقى بريداً إلكترونياً يحتوي على مزيد من المعلومات عن الفعالية. في حال لم تستلم أي رسالة الكترونية منّا خلال الدقائق القليلة القادمة، يرجى التحقق من ملف البريد غير الهام. يُرجى التأكد من إدراج عنوان البريد الإلكتروني <a href="mailto:no-reply@dubaifuture.ae"><b>no-reply@dubaifuture.ae</b></a> في القوائم المعتمدة.)';
	$wrap_class = 'ar-wrap';
	$location   = get_post_meta( $event_id, 'dffmain_event_location', true );
	$detail_url = get_permalink( $event_id );

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
	if ( ! empty( $event_date_select ) ) {

        $day   = date( 'd', strtotime( $event_date_select ) );
        $month = date( 'F', strtotime( $event_date_select ) );
        $year  = date( 'Y', strtotime( $event_date_select ) );
        $month = $months[ $month ];

        $event_date_select = $month . ' ' . $day . ' ,' . $year;
	}

	if ( ! empty( $event_end_date_select ) ) {
		$day   = date( 'd', strtotime( $event_end_date_select ) );
		$month = date( 'F', strtotime( $event_end_date_select ) );
		$year  = date( 'Y', strtotime( $event_end_date_select ) );
		$month = $months[ $month ];

		$event_end_date_select = $month . ' ' . $day . ' ,' . $year;
	}

	$event_time_frame = str_replace( 'AM', 'صباحًا', $event_time_frame );
	$event_time_frame = str_replace( 'PM', 'مساءً', $event_time_frame );

	$right_date     = 'التاريخ';
	$right_timing   = 'توقيت';
	$right_address  = 'عنوان';
	$right_location = 'موقعك';
	$right_details  = 'تفاصيل';


} else {
    $paragraph = 'You will be receiving an email shortly with more information about the event. If you do not receive the email within the next few minutes, please check your junk folder. (Please ensure that the email address <a href="mailto:no-reply@dubaifuture.ae"><b>no-reply@dubaifuture.ae</b></a> is whitelisted.)';
	$thank_you_message  = 'Thank you for registering!';
	$thank_you_subtitle = 'You are attending:';
	$heading            = get_post_meta( $event_id, 'dffmain_post_title', true );
	$wrap_class         = 'en-wrap';
	$location           = get_post_meta( $event_id, 'dffmain_event_location', true );
	$detail_url         = get_permalink( $event_id );

	$right_date     = 'Date';
	$right_timing   = 'Timing';
	$right_address  = 'Address';
	$right_location = 'Location';
	$right_details  = 'Details';
}
?>

<div class="thank-you-main <?php echo esc_attr( $wrap_class ); ?>">
	<div class="container">
		<div class="thanks-inner">
			<div class="thank-you-left">
				<h2>
                    <?php echo esc_html( $thank_you_message ); ?>
                </h2>
				<?php
				if ( isset( $image_attributes[0] ) && ! empty( $image_attributes[0] ) ) {
					?>
                    <div class="thank-you-img">
                        <img src="<?php echo esc_url( $image_attributes[0] ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
                    </div>
					<?php
				}
				?>
				<div class="thank-you-left-content">
					<?php
					if ( isset( $heading ) && ! empty( $heading ) ) {
						?>
                        <p class="big_font sub-title">
                            <?php echo esc_html( $thank_you_subtitle ); ?>
                        </p>
                        <h1>
                            <?php echo esc_html( $heading ); ?>
                        </h1>
						<?php
					}
					?>	
					<p class="big_font">
                        <?php echo wp_kses_post( $paragraph ); ?>
                    </p>
				</div>
			</div>
			<div class="thank-you-right">
				<div class="thank-you-right-content">
					<div class="thanks-main">
						<div class="thanks-row">
							<div class="thanks-col">
								<span class="thank_you_detail"><?php echo esc_html( $right_date ); ?></span>
							</div>
							<div class="thanks-col">
                                <p>
                                    <?php echo esc_html( $event_date_select ); ?>
                                    <?php
                                    if ( isset( $event_end_date_select ) && ! empty( $event_end_date_select ) ) {
                                        echo ' - ' . esc_html( $event_end_date_select ); }
                                    ?>
                                </p>
							</div>
						</div>
                        <?php 
                        if( empty( $event_end_date_select ) ) {
                            ?>
                            <div class="thanks-row">
                                <div class="thanks-col">
                                    <span class="thank_you_detail"><?php echo esc_html( $right_timing ); ?></span>
                                </div>
                                <div class="thanks-col">
                                    <p>
                                        <?php echo esc_html( $event_time_frame ); ?>
                                    </p>
                                </div>
                            </div>
                            <?php
                        }

                        if ( isset( $location ) && ! empty( $location ) ) {
                            ?>
                            <div class="thanks-row">
                                <div class="thanks-col">
                                    <span class="thank_you_detail"><?php echo esc_html( $right_location ); ?></span>
                                </div>
                                <div class="thanks-col">
                                    <?php 
                                    if ( ! empty( $event_google_map_link ) ) { 
                                        ?>
                                            <p>
                                                <a 
                                                    aria-label="event_google_map_link" 
                                                    href="<?php echo esc_url( $event_google_map_link ); ?>"
                                                    target="_blank"
                                                >
                                                    <?php echo esc_html( $location ); ?>
                                                </a>
                                            </p>
                                        <?php 
                                    } else { 
                                        ?>
                                        <p>
                                            <?php echo esc_html( $location ); ?>
                                        </p>
                                        <?php 
                                    } 
                                    ?>
                                </div>
                            </div>
                            <?php
                        }

                        $google_embed_maps_code = get_post_meta( $event_id, 'google_embed_maps_code', true );
                        if ( isset( $google_embed_maps_code ) && ! empty( $google_embed_maps_code ) ) {
                            $allow_tags             = array(
                                'iframe' => array(
                                    'src'             => array(),
                                    'width'           => array(),
                                    'height'          => array(),
                                    'frameborder'     => array(),
                                    'style'           => array(),
                                    'allowfullscreen' => array(),
                                    'aria-hidden'     => array(),
                                    'tabindex'        => array(),
                                    'title'           => array(),
                                ),
                            );
                            $google_embed_maps_code = str_replace( '<iframe', "<iframe title='google embed maps'", $google_embed_maps_code );
                            ?>
                            <div class="thanks-row">
                                <div class="thanks-col">
                                    <span class="thank_you_detail"><?php echo esc_html( $right_address ); ?></span>
                                </div>
                                <div class="thanks-col">
                                    <?php echo wp_kses( $google_embed_maps_code, $allow_tags ); ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
						<div class="thanks-row">
							<div class="thanks-col">
								<span class="thank_you_detail"><?php echo esc_html( $right_details ); ?></span>
							</div>
							<div class="thanks-col">
								<p>
                                    <a href="<?php echo esc_url( $detail_url ); ?>">
                                        <?php echo esc_html( $detail_url ); ?>
                                    </a>
                                </p>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>


<?php get_footer(); ?>
