<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://usoftware.co/
 * @since      1.0.0
 *
 * @package    Events_Main_Plugin
 * @subpackage Events_Main_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Events_Main_Plugin
 * @subpackage Events_Main_Plugin/public
 * @author     usoftware <tech@usoftware.co>
 */
class Events_Main_Plugin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'wp_ajax_event_subscribe_button_press', array( $this, 'event_subscribe_button_press' ) );
		add_action( 'wp_ajax_nopriv_event_subscribe_button_press', array( $this, 'event_subscribe_button_press' ) );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/events-main-plugin-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**TODO mimify js file */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/events-main-plugin-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name, 'formObj', array(
				'ajaxurl'              => admin_url( 'admin-ajax.php' ),
				'diffmainTranslations' => get_translations_data(true)
			)
		);
        wp_enqueue_script( 'google-captcha-js', 'https://www.google.com/recaptcha/api.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Ajax Call to check Email Exist for the event
	 */
	public function check_email_exist_callback() {

		$event_id = filter_input( INPUT_POST, 'event_id', FILTER_SANITIZE_NUMBER_INT );
		$event_id = isset( $event_id ) ? $event_id : '';

		$email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_STRING );
		$email = isset( $email ) ? $email : '';
		if ( ! empty( $event_id ) ) {
			$arg = array(
				'post_type'      => 'attendees',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'relation' => 'AND',
						array(
							'key'     => 'event_id',
							'value'   => $event_id,
							'compare' => '=',
						),
						array(
							'key'     => 'email',
							'value'   => $email,
							'compare' => '=',
						),
					),
				),
			);

			$query = new WP_Query( $arg );
		}
		$dataArr             = array();
		$dataArr['status']   = true;
		$dataArr['count']    = $query->found_posts;
		$dataArr['query']    = $query;
		$dataArr['$email']   = $email;
		$dataArr['event_id'] = $event_id;

		echo wp_json_encode( $dataArr );
		wp_reset_postdata();
		exit();
	}

}