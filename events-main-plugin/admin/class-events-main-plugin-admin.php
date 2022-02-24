<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://usoftware.co/
 * @since      1.0.0
 *
 * @package    Events_Main_Plugin
 * @subpackage Events_Main_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Events_Main_Plugin
 * @subpackage Events_Main_Plugin/admin
 * @author     usoftware <tech@usoftware.co>
 */
class Events_Main_Plugin_Admin {

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
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		include plugin_dir_path( __FILE__ ) . 'partials/events_category_tags_boxes.php';
		include plugin_dir_path( __FILE__ ) . 'partials/events_all_metaboxes.php';
		include plugin_dir_path( __FILE__ ) . 'partials/registration-form-api-endpoint.php';
		include plugin_dir_path( __FILE__ ) . 'partials/events_multilingualpress_helpers.php';

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'init', array( $this, 'events_categories' ) );
		add_action( 'init', array( $this, 'events_tags' ) );
		add_action( 'init', array( $this, 'custom_post_types_create' ) );


		/**custom page template for CPT dffmain-events */
		add_filter( 'single_template', array( $this, 'redirect_dffmain_events_template' ), 99, 1 );
		/**custom page template for Event (user registers on ) Thank you page */
		add_filter( 'page_template', array( $this, 'redirect_dffmain_events_thank_you' ), 99, 1 );
		/**create page on new site creation */
		add_action( 'wp_initialize_site', array( $this, 'create_events_thank_you_page' ), 99, 1 );




		add_action( 'init', array( $this, 'event_register_custom_post_status' ) );		
		add_action( 'admin_footer-post.php', array( $this, 'events_cancelled_status' ) );
		add_action( 'transition_post_status', array( $this, 'events_post_status' ), 10, 3 );

		add_action( 'add_meta_boxes', array( $this, 'event_editor_meta_boxes' ) );

		add_action('admin_menu',  array( $this, 'event_register_settings_page' ) );


		add_action( 'save_post', array( $this, 'save_event_editor_meta_boxes' ) );
		add_action( 'admin_menu', array( $this, 'events_remove_boxes' ), 20 );

		add_action( 'network_admin_menu', array( $this, 'add_events_settings' ) );

		add_action( 'wp_ajax_category_add_submit', array( $this, 'category_add_submit' ) );
		add_action( 'wp_ajax_nopriv_category_add_submit', array( $this, 'category_add_submit' ) );

		add_action( 'wp_ajax_cancel_event_ajax', array( $this, 'cancel_event_ajax' ) );
		add_action( 'wp_ajax_trash_event_ajax', array( $this, 'trash_event_ajax' ) );
		add_action( 'wp_ajax_tags_add_submit', array( $this, 'tags_add_submit' ) );
		add_action( 'wp_ajax_nopriv_tags_add_submit', array( $this, 'tags_add_submit' ) );
		add_action( 'wp_ajax_category_add_arabic_submit', array( $this, 'category_add_arabic_submit' ) );
		add_action( 'wp_ajax_nopriv_category_add_arabic_submit', array( $this, 'category_add_arabic_submit' ) );
		add_action( 'wp_ajax_tags_add_arabic_submit', array( $this, 'tags_add_arabic_submit' ) );
		add_action( 'wp_ajax_nopriv_tags_add_arabic_submit', array( $this, 'tags_add_arabic_submit' ) );
		add_action( 'wp_ajax_add_child_sites_action', array( $this, 'add_child_sites_action' ) );
		add_action( 'wp_ajax_nopriv_add_child_sites_action', array( $this, 'add_child_sites_action' ) );
		add_action( 'wp_ajax_delete_sites_action', array( $this, 'delete_sites_action' ) );
		add_action( 'wp_ajax_nopriv_delete_sites_action', array( $this, 'delete_sites_action' ) );

		add_filter( 'enter_title_here', array( $this, 'dff_event_title_place_holder' ), 20, 2 );
		add_filter( 'manage_dffmain-events_posts_columns', array( $this, 'set_dff_events_list_columns' ) );
		add_action( 'manage_dffmain-events_posts_custom_column', array( $this, 'custom_dff_events_column_value' ), 10, 2 );
		add_filter( 'bulk_actions-edit-dffmain-events', array( $this, 'remove_edit_from_bulk_actions_events' ) );
		add_filter( 'tiny_mce_before_init', array( $this, 'dff_setEditorToRTL' ), 10, 2 );

		add_action( 'wp_ajax_dff_save_next_click_ajax', array( $this, 'dff_save_next_click_ajax' ) );
		add_action( 'wp_ajax_nopriv_dff_save_next_click_ajax', array( $this, 'dff_save_next_click_ajax' ) );
		add_action( 'wp_ajax_event_send_special_single_email', array( $this, 'event_send_special_single_email' ) );
		add_action( 'wp_ajax_nopriv_event_send_special_single_email', array( $this, 'event_send_special_single_email' ) );

		add_filter( 'post_row_actions', array( $this, 'ssp_remove_member_bulk_actions' ) );

		/**TODO change check-in */
		add_action( 'wp_ajax_dff_checkin_ajax', array( $this, 'dff_checkin_ajax' ) );
		add_action( 'wp_ajax_nopriv_dff_checkin_ajax', array( $this, 'dff_checkin_ajax' ) );

		// form builder
		add_action( 'rest_api_init', 'register_routes' );
		add_filter( 'manage_registration-forms_posts_columns', array( $this, 'set_registration_forms_list_columns' ) );
		add_action( 'manage_registration-forms_posts_custom_column', array( $this, 'custom_registration_forms_column_value' ), 10, 2 );
		add_filter( 'manage_edit-registration-forms_sortable_columns', array( $this, 'set_custom_registration_forms_sortable_columns' ) );
		add_filter( 'bulk_actions-edit-registration-forms', '__return_empty_array', 100 );
		add_action( 'wp_ajax_select_registration_form_for_event', 'select_registration_form_for_event_callback' );
		add_action( 'wp_ajax_nopriv_select_registration_form_for_event', 'select_registration_form_for_event_callback' );
		add_action( 'wp_ajax_save_registration_form_for_event', 'save_registration_form_for_event_callback' );
		add_action( 'wp_ajax_nopriv_save_registration_form_for_event', 'save_registration_form_for_event_callback' );

		// Attendee Management
		add_filter( 'manage_attendees_posts_columns', array( $this, 'set_attendees_list_columns' ) );
		add_action( 'manage_attendees_posts_custom_column', array( $this, 'custom_attendees_column_value' ), 20, 2 );
		add_filter( 'manage_edit-attendees_sortable_columns', array( $this, 'set_attendees_sortable_columns' ) );
		add_action( 'manage_posts_extra_tablenav', array( $this, 'admin_attendee_list_top_export_button' ), 20, 1 );
		add_action( 'init', array( $this, 'export_attendee_list' ) );
		add_action( 'wp_ajax_get_attendee_details', 'get_attendee_details_callback' );
		add_action( 'wp_ajax_nopriv_get_attendee_details', 'get_attendee_details_callback' );
		add_action( 'pre_get_posts', array( $this, 'set_attendees_orderby' ) );
		add_filter( 'post_row_actions', array( $this, 'remove_attendees_quick_edit' ), 10, 1 );
		add_filter( 'bulk_actions-edit-attendees', array( $this, 'remove_edit_from_bulk_actions_attendee' ) );
		add_filter( 'views_edit-attendees', array( $this, 'remove_mine_filter_from_attendee' ) );

		add_action( 'wp_trash_post', array( $this, 'my_wp_trash_post' ) );

		// remove Visibility Option
		add_action( 'admin_head', array( $this, 'event_wpseNoVisibility' ) ); /**TODO remove better */

	}

	/**
	 * Register Events category
	 */
	public function events_categories() {

		$labels = array(
			'name'              => _x( 'Categories', 'events-main-plugin' ),
			'singular_name'     => _x( 'Category', 'events-main-plugin' ),
			'search_items'      => __( 'Search Category', 'events-main-plugin' ),
			'all_items'         => __( 'All Categories', 'events-main-plugin' ),
			'parent_item'       => __( 'Parent Category', 'events-main-plugin' ),
			'parent_item_colon' => __( 'Parent Topic:', 'events-main-plugin' ),
			'edit_item'         => __( 'Edit Category', 'events-main-plugin' ),
			'update_item'       => __( 'Update Category', 'events-main-plugin' ),
			'add_new_item'      => __( 'Add New Category', 'events-main-plugin' ),
			'new_item_name'     => __( 'New Category', 'events-main-plugin' ),
			'menu_name'         => __( 'Categories', 'events-main-plugin' ),
		);

		register_taxonomy(
			'events_categories', array( 'dffmain-events' ), array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'topic' ),
				'capabilities'      => array(
					'manage_terms' => 'manage_dffmain_cats',
					'edit_terms'   => 'edit_dffmain_cats',
					'delete_terms' => 'delete_dffmain_cats',
					'assign_terms' => 'edit_dffmain_cats',
				),
			)
		);

	}

	/**
	 * Register Event tag
	 */
	public function events_tags() {

		$labels = array(
			'name'              => _x( 'Tags', 'events-main-plugin' ),
			'singular_name'     => _x( 'Tag', 'events-main-plugin' ),
			'search_items'      => __( 'Search Tag', 'events-main-plugin' ),
			'all_items'         => __( 'All Tags', 'events-main-plugin' ),
			'parent_item'       => __( 'Parent Tag', 'events-main-plugin' ),
			'parent_item_colon' => __( 'Parent Topic:', 'events-main-plugin' ),
			'edit_item'         => __( 'Edit Tag', 'events-main-plugin' ),
			'update_item'       => __( 'Update Tag', 'events-main-plugin' ),
			'add_new_item'      => __( 'Add New Tag', 'events-main-plugin' ),
			'new_item_name'     => __( 'New Tag', 'events-main-plugin' ),
			'menu_name'         => __( 'Tags', 'events-main-plugin' ),
		);

		register_taxonomy(
			'events_tags', array( 'dffmain-events' ), array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'topic' ),
				'capabilities'      => array(
					'manage_terms' => 'manage_dffmain_tags',
					'edit_terms'   => 'edit_dffmain_tags',
					'delete_terms' => 'delete_dffmain_tags',
					'assign_terms' => 'edit_dffmain_tags',
				),
			)
		);

	}


	/**
	 * Create Custom Post Types
	 */
	public function custom_post_types_create() {
		$labels = array(
			'name'               => _x( 'Events', 'Post Type General Name', 'events-main-plugin' ),
			'singular_name'      => _x( 'Event', 'Post Type Singular Name', 'events-main-plugin' ),
			'menu_name'          => __( 'Events', 'events-main-plugin' ),
			'parent_item_colon'  => __( 'Parent Event', 'events-main-plugin' ),
			'all_items'          => __( 'All Events', 'events-main-plugin' ),
			'view_item'          => __( 'View Event', 'events-main-plugin' ),
			'add_new_item'       => __( 'Add Event Detail', 'events-main-plugin' ),
			'add_new'            => __( 'Add New Event', 'events-main-plugin' ),
			'edit_item'          => __( 'Edit Event Detail', 'events-main-plugin' ),
			'update_item'        => __( 'Update Event', 'events-main-plugin' ),
			'search_items'       => __( 'Search Event', 'events-main-plugin' ),
			'not_found'          => __( 'Not Found', 'events-main-plugin' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'events-main-plugin' ),
			'featured_image'     => __( 'Featured Image', 'events-main-plugin' ),
		);

		$args = array(
			'label'               => __( 'Event', 'events-main-plugin' ),
			'description'         => __( 'Event', 'events-main-plugin' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'author', 'thumbnail', 'revisions' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'exclude_from_search' => false,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'capabilities'        => [
				'publish_posts'       => 'publish_dffmain_events',
				'edit_posts'          => 'edit_dffmain_events',
				'delete_posts'        => 'delete_dffmain_events',
				'edit_others_posts'   => 'edit_others_dffmain_events',
				'delete_others_posts' => 'delete_others_dffmain_events',
				'read_private_posts'  => 'read_private_dffmain_events',
				'edit_post'           => 'edit_dffmain_event',
				'delete_post'         => 'delete_dffmain_event',
				'read_post'           => 'read_dffmain_event',
			],
			'menu_icon'    => 'dashicons-calendar',
			'rewrite'      => array( 'slug' => 'events','with_front' => false ),
			'show_in_rest' => true,
		);
		register_post_type( 'dffmain-events', $args );

		/**
		 * Registered Custom Post Type for Registration Forms
		 */
		$registration_labels = array(
			'name'               => _x( 'Registration Forms', 'Post Type General Name', 'events-main-plugin' ),
			'singular_name'      => _x( 'Registration Form', 'Post Type Singular Name', 'events-main-plugin' ),
			'menu_name'          => __( 'Registration Forms', 'events-main-plugin' ),
			'parent_item_colon'  => __( 'Parent Event', 'events-main-plugin' ),
			'all_items'          => __( 'All Registration Forms', 'events-main-plugin' ),
			'view_item'          => __( 'View Event', 'events-main-plugin' ),
			'add_new_item'       => __( 'Create Registration Form Template', 'events-main-plugin' ),
			'add_new'            => __( 'Add Registration Template', 'events-main-plugin' ),
			'edit_item'          => __( 'Edit Registration Form Template', 'events-main-plugin' ),
			'update_item'        => __( 'Update Registration', 'events-main-plugin' ),
			'search_items'       => __( 'Search Registration', 'events-main-plugin' ),
			'not_found'          => __( 'Not Found', 'events-main-plugin' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'events-main-plugin' ),
		);

		$registration_args = array(
			'label'               => __( 'Registration Form', 'events-main-plugin' ),
			'description'         => __( 'Registration Form', 'events-main-plugin' ),
			'labels'              => $registration_labels,
			'supports'            => array( 'title', 'author', 'thumbnail', 'revisions' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-media-text',
			'show_in_rest'        => true,
		);
		register_post_type( 'registration-forms', $registration_args );

		/**
		 * Registered Custom Post Type for Attendee
		 */
		$registration_labels = array(
			'name'               => _x( 'Attendee List', 'Post Type General Name', 'events-main-plugin' ),
			'singular_name'      => _x( 'Attendee', 'Post Type Singular Name', 'events-main-plugin' ),
			'menu_name'          => __( 'Attendees', 'events-main-plugin' ),
			'parent_item_colon'  => __( 'Parent Event', 'events-main-plugin' ),
			'all_items'          => __( 'All Attendee', 'events-main-plugin' ),
			'search_items'       => __( 'Search Attendees by Name', 'events-main-plugin' ),
			'not_found'          => __( 'Not Found', 'events-main-plugin' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'events-main-plugin' ),

		);

		$registration_args = array(
			'label'               => __( 'Attendee', 'events-main-plugin' ),
			'description'         => __( 'Attendee', 'events-main-plugin' ),
			'labels'              => $registration_labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts'        => 'create_attendees',
				'read'                => 'read_attendees',
				'publish_posts'       => 'publish_attendees',
				'edit_posts'          => 'edit_attendees',
				'delete_posts'        => 'delete_attendees',
				'edit_others_posts'   => 'edit_others_attendees',
				'delete_others_posts' => 'delete_others_attendees',
				'read_private_posts'  => 'read_private_attendees',
				'edit_post'           => 'edit_attendee',
				'delete_post'         => 'delete_attendee',
				'read_post'           => 'read_attendee',
			),
			'map_meta_cap'        => true,
			'menu_icon'           => 'dashicons-businessman',
			'show_in_rest'        => true,
		);
		register_post_type( 'attendees', $registration_args );

	}

	/**
	 * Redirects to plugin's template for single event
	 *
	 * @param $template
	 * @return void
	 */
	public function redirect_dffmain_events_template( $template ) {

		if ( is_singular( 'dffmain-events' ) ){
			$template = EVENTS_MAIN_PLUGIN_PATH . 'templates/template-single-events.php';
		}
		return $template;
    }

	/**
	 * Create Thank you page used after user registers himself for Event
	 *
	 * @param [object] $new_site
	 * @return void
	 */
	public function redirect_dffmain_events_thank_you( $template ) {

		if ( is_page('event-registration-thank-you') ) {
			$template = EVENTS_MAIN_PLUGIN_PATH . 'templates/template-event_thank_you.php';
		}
		return $template;
	}

	public function create_events_thank_you_page( $new_site ) {

		if ( is_plugin_active_for_network( 'events-main-plugin/events-main-plugin.php' ) ) {

			switch_to_blog( $new_site->blog_id );
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

	/**
	 * Add new status "Cancel Event" for the event post type
	 */
	public function event_register_custom_post_status() {
		register_post_status(
			'cancelled', array(
				'label'                     => _x( 'cancelled', 'Cancelled', 'events-main-plugin' ),
				'public'                    => true,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>' ),
			)
		);
	}

	/**
	 * Add dropdown to the list of statuses.
	 */
	public function events_cancelled_status() {
		global $post;
		$complete = '';
		if ( 'dffmain-events' === $post->post_type ) {
			if ( 'cancelled' === $post->post_status ) {
				$complete = ' selected="selected"';
			}
			?>

			  <script>
			  jQuery(document).ready(function($){
				   $("select#post_status").append("<option value='cancelled' <?php echo esc_attr( $complete ); ?> >Cancelled</option>");
				   $(".misc-pub-section label").append("<span id='post-status-display'>Cancelled</span>");
			  });
			  </script>
			<?php
			if ( 'cancelled' === $post->post_status ) {
				?>
			  <script>
				   $("#post-status-display").html("<span id='post-status-display'>Cancelled</span>");
			  </script>
				<?php
			}
		}
	}

	/**
	 * Send e-mail on cancelling Event
	 */
	public function events_post_status( $new_status, $old_status, $post ) {

		if ( 'dffmain-events' === $post->post_type && 'cancelled' === $new_status && 'publish' === $old_status ) {

			// Send Email to Attendees.
			$this->dff_events_cancel_mail( $post->ID );
		}
	}

	/**
	 * Send cancel email to attendee.
	 *
	 * @param $eid 
	 */
	public function dff_events_cancel_mail( $eid ) {

		$post_id = $eid;

		$settings_array_get          = get_option( 'events_general_settings' );
		$events_general_settings_get = json_decode( $settings_array_get );
		$events_general_settings_get = (array) $events_general_settings_get;

		$sendgrid_apikey      = $events_general_settings_get['send_grid_key'];
		$send_grid_from_email = $events_general_settings_get['send_grid_from_email'];
		$send_grid_from_name  = $events_general_settings_get['send_grid_from_name'];

		$url = 'https://api.sendgrid.com/';

		$dffmain_event_content          = get_option( 'events_content_event_cancel' );
		$subject_event_cancel = get_option( 'subject_event_cancel' );

		$dffmain_post_title = get_post_meta( $post_id, 'dffmain_post_title', true );

		$event_date = get_post_meta( $post_id, 'event_date_select', true );
		$event_end_date = get_post_meta( $post_id, 'event_end_date_select', true );

		$event_time_start_select = get_post_meta( $post_id, 'event_time_start_select', true );
		$event_time_end_select   = get_post_meta( $post_id, 'event_time_end_select', true );

		$event_date = new DateTime( "$event_date" );
		$event_date = $event_date->format( 'F d, Y' );

		if( isset( $event_end_date ) && !empty( $event_end_date ) ) {

			$event_end_date = new DateTime( "$event_end_date" );
			$event_end_date = $event_end_date->format( 'F d, Y' );

			$event_date = $event_date ." - " . $event_end_date;
		}

		$event_time_start_select = new DateTime( "$event_time_start_select" );
		$event_time_start_select = $event_time_start_select->format( 'h:i A' );

		$event_time_end_select = new DateTime( "$event_time_end_select" );
		$event_time_end_select = $event_time_end_select->format( 'h:i A' );

		$dffmain_event_location = get_post_meta( $post_id, 'dffmain_event_location', true );

		$dffmain_attendee_data = array();


		$args_attendees = array(
			'post_type'  => 'attendees',
			'meta_query' => array(
				array(
					'key'   => 'event_id',
					'value' => "$post_id",
				),
			),
			'fields'     => 'ids',
		);

		$query_attendees = new WP_Query( $args_attendees );

		if ( isset( $query_attendees->posts ) && ! empty( $query_attendees->posts ) ) {
			foreach ( $query_attendees->posts as $query_attendees_data ) {
				$attendee_data = get_post_meta( $query_attendees_data, 'attendee_data', true );

				/** TODO choose language  
				 *  if ( 'en' === $attendee_data['languageType'] ) {
				*/
				$event_date_en = str_replace( ' - ', ' to ', $event_date );
				$dffmain_attendee_data['e_attendee_fname'][] = $attendee_data['FirstName'];
				$dffmain_attendee_data['e_attendee_lname'][] = $attendee_data['LastName'];
				$dffmain_attendee_data['Email'][]            = $attendee_data['Email'];
				$dffmain_attendee_data['event_name'][]       = $dffmain_post_title;
				$dffmain_attendee_data['date'][]             = $event_date_en;
				$dffmain_attendee_data['time_frame'][]       = $event_time_start_select . ' to ' . $event_time_end_select;
				$dffmain_attendee_data['location'][]         = $dffmain_event_location;

				if( isset( $event_end_date ) && !empty( $event_end_date ) ) {
					$dffmain_attendee_data['date_output'][] = '{{date}}';
				} else {
					$dffmain_attendee_data['date_output'][] = '{{date}} from {{time}} (GMT+4)';
				}

			}
		}

		/**
		 * mail sent for english attendee
		 */
		$template_id = 'd-e0a56b842d0541b0b34be68709f8798c'; /** TODO !!! Hardcodded ID */
		if ( isset( $dffmain_attendee_data['Email'] ) && ! empty( $dffmain_attendee_data['Email'] ) ) {

			foreach( $dffmain_attendee_data['Email'] as $k => $v ) {

				$subject_event_cancel = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'][$k], $subject_event_cancel );
				$subject_event_cancel = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'][$k], $subject_event_cancel );
				$subject_event_cancel = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'][$k], $subject_event_cancel );
				$subject_event_cancel = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'][$k], $subject_event_cancel );
				$subject_event_cancel = str_replace( '{{date}}', $dffmain_attendee_data['date'][$k], $subject_event_cancel );
				$subject_event_cancel = str_replace( '{{location}}', $dffmain_attendee_data['location'][$k], $subject_event_cancel );
				$subject_event_cancel = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'][$k], $subject_event_cancel );

				$dffmain_event_content = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'][$k], $dffmain_event_content );
				$dffmain_event_content = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'][$k], $dffmain_event_content );
				$dffmain_event_content = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'][$k], $dffmain_event_content );
				$dffmain_event_content = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'][$k], $dffmain_event_content );
				$dffmain_event_content = str_replace( '{{date}}', $dffmain_attendee_data['date'][$k], $dffmain_event_content );
				$dffmain_event_content = str_replace( '{{location}}', $dffmain_attendee_data['location'][$k], $dffmain_event_content );
				$dffmain_event_content = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'][$k], $dffmain_event_content );
				
				$params_ar = (object) array(
					'from' => array( 'email' => 'no-reply@dubaifuture.ae' ),
					'personalizations' => array( 
						array(
							'to' => array( array( 'email' => $v ) ),
							'dynamic_template_data' => array(
								'EMAIL_SUBJECT' => $subject_event_cancel,
								'EMAIL_CONTENT' => $dffmain_event_content,
								'DISPLAY_NAME' => $dffmain_attendee_data['e_attendee_fname'][$k],
								'HELLO' => 'Dear',
							),
						)
					),
					'template_id' => $template_id,
				);

				$request      = $url . 'v3/mail/send';
				$response_ar = wp_remote_post(
					$request, array(
						'method'  => 'POST',
						'headers' => array( 'Authorization' => 'Bearer ' . $sendgrid_apikey, 'Content-Type' => 'application/json' ),
						'body'    => wp_json_encode( $params_ar ),
					)
				);

				$subject_event_cancel = str_replace( $dffmain_attendee_data['e_attendee_fname'][$k], '{{e_attendee_fname}}', $subject_event_cancel );
				$subject_event_cancel = str_replace( $dffmain_attendee_data['e_attendee_lname'][$k], '{{e_attendee_lname}}', $subject_event_cancel );

				$dffmain_event_content = str_replace( $dffmain_attendee_data['e_attendee_fname'][$k], '{{e_attendee_fname}}', $dffmain_event_content );
				$dffmain_event_content = str_replace( $dffmain_attendee_data['e_attendee_lname'][$k], '{{e_attendee_lname}}', $dffmain_event_content );

			}

		}

		die();
	}

	/**
	 *  DFF custom events meta box
	 */
	public function event_editor_meta_boxes() {

		global $current_user;

		$post_id         = get_the_id();
		$event_cancelled = get_post_status( $post_id );

		add_meta_box( 'registration_form', __( 'Registration Form', 'events-main-plugin' ), array( $this, 'registration_form_callback' ), 'registration-forms', 'normal', 'high' );
		add_meta_box( 'tab_editor_id', __( 'Main', 'events-main-plugin' ), 'tab_editor_function', 'dffmain-events', 'normal', 'high' );
		add_meta_box( 'event_cost_id', __( 'Event Cost', 'events-main-plugin' ), 'event_cost_function', 'dffmain-events', 'side', 'low' );

		add_meta_box( 'event_reminder_id', __( 'Event Reminder', 'events-main-plugin' ), 'event_reminder_function', 'dffmain-events', 'side', 'low' );
		add_meta_box( 'event_date_id', __( 'Event Start Date', 'events-main-plugin' ), 'event_date_function', 'dffmain-events', 'side', 'low' );
		add_meta_box( 'event_end_date_id', __( 'Event End Date', 'events-main-plugin' ), 'event_end_date_function', 'dffmain-events', 'side', 'low' );
		add_meta_box( 'event_time_id', __( 'Event Time', 'events-main-plugin' ), 'event_time_function', 'dffmain-events', 'side', 'low' );
		add_meta_box( 'event_google_map_id', __( 'Google Maps URL', 'events-main-plugin' ), 'event_google_map_function', 'dffmain-events', 'side', 'low' );
		
		add_meta_box( 'event_attendee_limit', __( 'Maximum Attendee of Event', 'events-main-plugin' ), 'event_attendee_limit_function', 'dffmain-events', 'side', 'low' );
		add_meta_box( 'event_attendee_limit_message', __( 'Registration Closed Message', 'events-main-plugin' ), 'event_attendee_limit_message_function', 'dffmain-events', 'side', 'low' );

		add_meta_box( 'event_detail_image_id', __( 'Event Detail Image', 'events-main-plugin' ), 'event_detail_image_function', 'dffmain-events', 'side', 'low' );
		add_meta_box( 'event_security_code_id', __( 'Invitation Code', 'events-main-plugin' ), 'event_security_code_function', 'dffmain-events', 'side', 'low' );
		add_meta_box( 'event_special_instruction_id', __( 'Special Instruction', 'events-main-plugin' ), 'event_special_instruction_function', 'dffmain-events', 'side', 'low' );
		add_meta_box( 'google_embed_maps_code_id', __( 'Google Maps Embed Code', 'events-main-plugin' ), 'google_embed_maps_code_function', 'dffmain-events', 'side', 'low' );

		$user_roles = $current_user->roles;
		$user_role  = array_shift( $user_roles );

		if ( 'event_manager' === $user_role ) {
			remove_meta_box( 'authordiv', 'dffmain-events', 'normal' );
		}

		if ( 'cancelled' !== $event_cancelled ) {
			add_meta_box( 'event_cancel_id', __( 'Cancel Event', 'events-main-plugin' ), 'cancel_event_function', 'dffmain-events', 'side', 'low' );
		}
	}

	/**
	 *  DFF custom events settings page
	 */
	public function event_register_settings_page() {

		add_submenu_page( 
			'edit.php?post_type=dffmain-events', 
			'event-settings', 
			'Events settings', 
			'manage_options', 
			'diffmain-events-settings-page', 
			array( $this, 'single_site_events_settings_page' )
		);
	}

	/**
	 *  DFF custom events settings page function
	 */
	public function single_site_events_settings_page() {

		include plugin_dir_path( __FILE__ ) . 'partials/single_site_events_settings_page.php';
	}

	/**
	 * Function for Registration Form Fields metabox
	 */
	public function registration_form_callback() {
		?>
		<script>jQuery('body').addClass('registration-form-body');</script>
		<div id="registration-form-wrap" class="registration-form-wrap"></div>
	<?php
	}

	/**
	 * Event Trash click callback
	 *
	 * @param $post_id
	 */
	public function my_wp_trash_post( $post_id ) {
		$post_type   = get_post_type( $post_id );
		$post_status = get_post_status( $post_id );
		if ( $post_type === 'dffmain-events' && in_array( $post_status, array( 'publish', 'draft', 'cancelled', 'pending' ), true ) ) {
			$args_attendees = array(
				'post_type'      => 'attendees',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'   => 'event_id',
						'value' => "$post_id",
					),
				),
				'fields'         => 'ids',
			);

			$query_attendees = new WP_Query( $args_attendees );
			$found_posts     = $query_attendees->found_posts ? $query_attendees->found_posts : 0;
			if ( 0 < $found_posts ) {
				foreach ( $query_attendees->posts as $query_attendees_data ) {
					$attendee_post = array(
						'ID'          => $query_attendees_data,
						'post_type'   => 'attendees',
						'post_status' => 'trash',
					);
					wp_update_post( $attendee_post );
				}
			}
		}
	}

	/**
	 * Cancel the event.
	 */
	public function cancel_event_ajax() {

		$eid = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		// Change from draft to published
		$event_post = array(
			'ID'          => $eid,
			'post_type'   => 'dffmain-events',
			'post_status' => 'cancelled',
		);
		// Update the post into the database
		wp_update_post( $event_post );
	}

	/**
	 * Trash the event.
	 */
	public function trash_event_ajax() {

		$eid = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		// Change from draft to published
		$event_post = array(
			'ID'          => $eid,
			'post_type'   => 'dffmain-events',
			'post_status' => 'trash',
		);
		// Update the post into the database
		wp_update_post( $event_post );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/events-main-plugin-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-dataTables', plugin_dir_url( __FILE__ ) . 'css/dataTables.jqueryui.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'form-builder.css', plugin_dir_url( __FILE__ ) . 'css/form-builder.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/events-main-plugin-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . 'datatables', plugin_dir_url( __FILE__ ) . 'js/datatables.min.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name, 'ajax_object', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
		global $post;
		wp_enqueue_script( 'form-builder-js', plugin_dir_url( __FILE__ ) . 'form-builder/build/bundle.js', array( 'jquery', 'wp-element' ), $this->version, true );
		if ( isset( $post ) ) {
			wp_localize_script(
				'form-builder-js', 'formBuilderObj', array(
					'postID' => $post->ID,
				)
			);
		}
		wp_enqueue_script( 'form-setup-js', plugin_dir_url( __FILE__ ) . 'js/form-setup.js', array( 'jquery' ), $this->version, true );
		wp_localize_script(
			'form-setup-js', 'formObj', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 *  Remove Metabox of DFF category and tags
	 */
	public function events_remove_boxes() {

		remove_meta_box( 'events_categoriesdiv', 'dffmain-events', 'side' );
		remove_meta_box( 'events_tagsdiv', 'dffmain-events', 'side' );
		remove_meta_box( 'authordiv', 'registration-forms', 'side' );
		remove_meta_box( 'slugdiv', 'registration-forms', 'side' );
		remove_meta_box( 'events_arabic_tagsdiv', 'dffmain-events', 'side' );

	}

	/**
	 *  Add Events settings admin page
	 */
	public function add_events_settings() {

		add_menu_page( 
			__( 'Multisite Events Settings Page', 'events-main-plugin' ), 
			__( 'Events Settings', 'events-main-plugin' ), 
			'manage_network_options', 
			'network-dffmain-events-settings-page', 
			array( $this, 'events_settings_page' ), 
			'dashicons-calendar', 
			50 
		);
	}

	/**
	 * Save metabox values
	 *
	 * @throws Exception
	 */
	public function save_event_editor_meta_boxes( $post_id ) {

		if ( !ms_is_switched() ) {

			$dffmain_post_title = filter_input( INPUT_POST, 'dffmain_post_title', FILTER_SANITIZE_STRING );
			$dffmain_post_title = isset( $dffmain_post_title ) ? esc_html( $dffmain_post_title ) : '';
			$dffmain_events_overview = isset( $_POST['events_overview'] ) ? wp_kses_post( $_POST['events_overview'] ) : '';
			$dffmain_events_agenda = isset( $_POST['dffmain_events_agenda'] ) ? wp_kses_post( $_POST['dffmain_events_agenda'] ) : '';
			$dffmain_event_location = isset( $_POST['dffmain_event_location'] ) ? wp_kses_post( $_POST['dffmain_event_location'] ) : '';

			// terms
			$emp_category = filter_input( INPUT_POST, 'emp_category', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$emp_category = isset( $emp_category ) ? $emp_category : '';
			if ( isset( $emp_category ) && ! empty( $emp_category ) ) {
				$emp_category = implode( ',', $emp_category );
			}

			$emp_tags = filter_input( INPUT_POST, 'emp_tags', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$emp_tags = isset( $emp_tags ) ? $emp_tags : '';
			if ( isset( $emp_tags ) && ! empty( $emp_tags ) ) {
				$emp_tags = implode( ',', $emp_tags );
			}


			update_post_meta( $post_id, 'dffmain_post_title', $dffmain_post_title );
			update_post_meta( $post_id, 'events_overview', $dffmain_events_overview );
			update_post_meta( $post_id, 'dffmain_events_agenda', $dffmain_events_agenda );
			update_post_meta( $post_id, 'dffmain_event_location', $dffmain_event_location );

			// terms
			wp_set_post_terms( $post_id, $emp_category, 'events_categories', false );
			wp_set_post_terms( $post_id, $emp_tags, 'events_tags', false );

			update_post_meta( $post_id, 'emp_category', $emp_category );
			update_post_meta( $post_id, 'emp_tags', $emp_tags );
		}

		/**
		 * setings to update globaly (over all translations)
		 */

		// Cost settings TODO -- not saving -- maybe filter in theme
		$event_cost_name = filter_input( INPUT_POST, 'event_cost_name', FILTER_SANITIZE_STRING );
		$event_cost_name = isset( $event_cost_name ) ? esc_html( $event_cost_name ) : '';

		// Event Reminder settings
		$event_reminder_select_box = filter_input( INPUT_POST, 'event_reminder_select_box', FILTER_SANITIZE_STRING );
		$event_reminder_select_box = isset( $event_reminder_select_box ) ? esc_html( $event_reminder_select_box ) : '';

		// Date settings
		$event_date_select = filter_input( INPUT_POST, 'event_date_select', FILTER_SANITIZE_STRING );
		$event_date_select = isset( $event_date_select ) ? esc_html( $event_date_select ) : '';

		// End date settings
		$event_end_date_select = filter_input( INPUT_POST, 'event_end_date_select', FILTER_SANITIZE_STRING );
		$event_end_date_select = isset( $event_end_date_select ) ? esc_html( $event_end_date_select ) : '';

		// Time settings
		$event_time_start_select = filter_input( INPUT_POST, 'event_time_start_select', FILTER_SANITIZE_STRING );
		$event_time_start_select = isset( $event_time_start_select ) ? esc_html( $event_time_start_select ) : '';
		$event_time_end_select = filter_input( INPUT_POST, 'event_time_end_select', FILTER_SANITIZE_STRING );
		$event_time_end_select = isset( $event_time_end_select ) ? esc_html( $event_time_end_select ) : '';


		// Google map settings
		$event_google_map_input = filter_input( INPUT_POST, 'event_google_map_input', FILTER_SANITIZE_STRING );
		$event_google_map_input = isset( $event_google_map_input ) ? esc_html( $event_google_map_input ) : '';

		// Detail image
		$meta_key = filter_input( INPUT_POST, 'event_detail_img', FILTER_SANITIZE_STRING );
		$meta_key = isset( $meta_key ) ? $meta_key : '';

		// Security Code Setting
		$security_code_checkbox = filter_input( INPUT_POST, 'security_code_checkbox', FILTER_SANITIZE_STRING );
		$security_code_checkbox = isset( $security_code_checkbox ) ? $security_code_checkbox : '';
		$event_security_code = filter_input( INPUT_POST, 'event_security_code', FILTER_SANITIZE_STRING );
		$event_security_code = isset( $event_security_code ) ? $event_security_code : '';


		// Reminder date
		$date = date( $event_date_select );
		$event_reminder_date = date( 'Y-m-d', strtotime( $date . ' - ' . $event_reminder_select_box . ' days' ) );


		// Special instruction
		$event_special_instruction = filter_input( INPUT_POST, 'event_special_instruction', FILTER_SANITIZE_STRING );
		$event_special_instruction = isset( $event_special_instruction ) ? $event_special_instruction : '';

		// Google maps embeded code TODO -- not saving -- maybe filter in theme
		$allow_tags = array(
			'iframe' => array(
				'src'             => array(),
				'width'           => array(),
				'height'          => array(),
				'frameborder'     => array(),
				'style'           => array(),
				'allowfullscreen' => array(),
				'aria-hidden'     => array(),
				'tabindex'        => array(),
			),
		);
		$google_embed_maps_code = isset( $_POST['google_embed_maps_code'] ) ? wp_kses( $_POST['google_embed_maps_code'], $allow_tags ) : "";
		
		

		// Attendee meta data
		$event_attendee_limit_count = filter_input( INPUT_POST, 'event_attendee_limit_count', FILTER_SANITIZE_STRING );
		$event_attendee_limit_count = isset( $event_attendee_limit_count ) ? $event_attendee_limit_count : '';
		$event_registration_close_message = filter_input( INPUT_POST, 'event_registration_close_message', FILTER_SANITIZE_STRING );
		$event_registration_close_message = isset( $event_registration_close_message ) ? $event_registration_close_message : '';


		$current_site_id = get_current_blog_id();
		$translations = multilingualpress_get_ids( $post_id, $current_site_id );

		foreach ( $translations as $site_id => $post_id ) {

			switch_to_blog( $site_id );

				// Cost settings
				update_post_meta( $post_id, 'event_cost_name', $event_cost_name );

				// Reminder settings
				update_post_meta( $post_id, 'event_reminder_select_box', $event_reminder_select_box );

				// Date settings
				update_post_meta( $post_id, 'event_date_select', $event_date_select );

				// End date settings
				update_post_meta( $post_id, 'event_end_date_select', $event_end_date_select );

				// Time settings
				update_post_meta( $post_id, 'event_time_start_select', $event_time_start_select );
				update_post_meta( $post_id, 'event_time_end_select', $event_time_end_select );

				// Google map settings
				update_post_meta( $post_id, 'event_google_map_input', $event_google_map_input );

				// Detail image
				update_post_meta( $post_id, 'event_detail_img', sanitize_text_field( $meta_key ) );

				// Security Code Setting
				update_post_meta( $post_id, 'security_code_checkbox', $security_code_checkbox );
				update_post_meta( $post_id, 'event_security_code', $event_security_code );

				// Reminder date
				update_post_meta( $post_id, 'event_reminder_date', $event_reminder_date );

				// Special instruction
				update_post_meta( $post_id, 'event_special_instruction', $event_special_instruction );

				// Google maps embeded code
				update_post_meta( $post_id, 'google_embed_maps_code', $google_embed_maps_code );


				// Attendee meta data
				update_post_meta( $post_id, 'event_attendee_limit_count', $event_attendee_limit_count );
				update_post_meta( $post_id, 'event_registration_close_message', $event_registration_close_message );

			restore_current_blog();
		}
	}

	/**
	 * Ajax function for add english category
	 */
	public function category_add_submit() {
		$newevents_categories = filter_input( INPUT_POST, 'newevents_categories', FILTER_SANITIZE_STRING );
		$newevents_categories = isset( $newevents_categories ) ? $newevents_categories : '';

		$newevents_categories_parent = filter_input( INPUT_POST, 'newevents_categories_parent', FILTER_SANITIZE_NUMBER_INT );
		$newevents_categories_parent = isset( $newevents_categories_parent ) ? $newevents_categories_parent : '';

		if ( '-1' === $newevents_categories_parent ) {
			$newevents_categories_parent = '0';
		}

		$inserted_term = wp_insert_term(
			$newevents_categories,
			'events_categories',
			array(
				'parent' => $newevents_categories_parent,
			)
		);

		if ( isset( $inserted_term ) && ! empty( $inserted_term ) ) {
			$term_name = get_term_by( 'id', $inserted_term['term_id'], 'events_categories' );
			?>
			<li>
				<label class="post_type_lable" for="<?php echo esc_attr( $inserted_term['term_id'] ); ?>">
				<input
					name="emp_category[]" type="checkbox"
					id="<?php echo esc_attr( $inserted_term['term_id'] ); ?>"
					value="<?php echo esc_attr( $inserted_term['term_id'] ); ?>"
					checked
				>
				<?php echo esc_html( $term_name->name ); ?>
				</label>
			</li>
			<?php
		}

		wp_die();
	}

	/**
	 * Ajax function for add english tag
	 */
	public function tags_add_submit() {
		$newevents_tags = filter_input( INPUT_POST, 'newevents_tags', FILTER_SANITIZE_STRING );
		$newevents_tags = isset( $newevents_tags ) ? $newevents_tags : '';

		$inserted_term = wp_insert_term(
			$newevents_tags,
			'events_tags'
		);

		if ( isset( $inserted_term ) && ! empty( $inserted_term ) ) {
			$term_name = get_term_by( 'id', $inserted_term['term_id'], 'events_tags' );
			?>
			<li>
				<label class="post_type_lable" for="<?php echo esc_attr( $inserted_term['term_id'] ); ?>">
				<input
					name="emp_tags[]" type="checkbox"
					id="<?php echo esc_attr( $inserted_term['term_id'] ); ?>"
					value="<?php echo esc_attr( $inserted_term['term_id'] ); ?>"
					checked
				>
				<?php echo esc_html( $term_name->name ); ?>
				</label>
			</li>
			<?php
		}

		wp_die();
	}

	/**
	 * Ajax function for add arabic category
	 */
	public function category_add_arabic_submit() {

		$newevents_arabic_categories        = filter_input( INPUT_POST, 'newevents_arabic_categories', FILTER_SANITIZE_STRING );
		$newevents_arabic_categories        = isset( $newevents_arabic_categories ) ? $newevents_arabic_categories : '';
		$newevents_arabic_categories_parent = filter_input( INPUT_POST, 'newevents_arabic_categories_parent', FILTER_SANITIZE_NUMBER_INT );
		$newevents_arabic_categories_parent = isset( $newevents_arabic_categories_parent ) ? $newevents_arabic_categories_parent : '';

		if ( '-1' === $newevents_arabic_categories_parent ) {
			$newevents_arabic_categories_parent = '0';
		}

		$inserted_term = wp_insert_term(
			$newevents_arabic_categories,
			'events_arabic_categories',
			array(
				'parent' => $newevents_arabic_categories_parent,
			)
		);

		if ( isset( $inserted_term ) && ! empty( $inserted_term ) ) {
			$term_name = get_term_by( 'id', $inserted_term['term_id'], 'events_arabic_categories' );
			?>
			<li><label class="post_type_lable" for="<?php echo esc_attr( $inserted_term['term_id'] ); ?>"><input
						name="emp_arabic_category[]" type="checkbox"
						id="<?php echo esc_attr( $inserted_term['term_id'] ); ?>"
						value="<?php echo esc_attr( $inserted_term['term_id'] ); ?>"
						checked><?php echo esc_html( $term_name->name ); ?></label></li>
											<?php
		}

		?>
		<?php

		wp_die();
	}

	/**
	 * Ajax function for add arabic tag
	 */
	public function tags_add_arabic_submit() {

		$newevents_arabic_tags        = filter_input( INPUT_POST, 'newevents_arabic_tags', FILTER_SANITIZE_STRING );
		$newevents_arabic_tags        = isset( $newevents_arabic_tags ) ? $newevents_arabic_tags : '';
		$newevents_arabic_tags_parent = filter_input( INPUT_POST, 'newevents_arabic_tags_parent', FILTER_SANITIZE_NUMBER_INT );
		$newevents_arabic_tags_parent = isset( $newevents_arabic_tags_parent ) ? $newevents_arabic_tags_parent : '';

		if ( '-1' === $newevents_arabic_tags_parent ) {
			$newevents_arabic_tags_parent = '0';
		}

		$inserted_term = wp_insert_term(
			$newevents_arabic_tags,
			'events_arabic_tags',
			array(
				'parent' => $newevents_arabic_tags_parent,
			)
		);

		if ( isset( $inserted_term ) && ! empty( $inserted_term ) ) {
			$term_name = get_term_by( 'id', $inserted_term['term_id'], 'events_arabic_tags' );
			?>
			<li>
				<label class="post_type_lable" for="<?php echo esc_attr( $inserted_term['term_id'] ); ?>">
				<input 
					name="emp_arabic_tags[]" 
					type="checkbox"
					id="<?php echo esc_attr( $inserted_term['term_id'] ); ?>"
					value="<?php echo esc_attr( $inserted_term['term_id'] ); ?>"
					checked
				>
					<?php echo esc_html( $term_name->name ); ?>
				</label>
			</li>
			<?php
		}

		wp_die();
	}

	/**
	 * Events settings page.
	 */
	public function events_settings_page() {
		include plugin_dir_path( __FILE__ ) . 'partials/events_settings_page.php';
	}

	/**
	 * Ajax function for add child site name
	 */
	public function add_child_sites_action() {

		$data_from_site_button  = filter_input( INPUT_POST, 'add_sites_field', FILTER_SANITIZE_STRING );

		$add_child_sites_action = isset( $data_from_site_button ) ? $data_from_site_button : '';
		$add_child_sites_action = preg_replace( '(^https?://)', '', rtrim( $add_child_sites_action, '/\\' ) );

		$npm_added_sites = get_option( 'npm_added_child_sites' );

		$token = bin2hex( random_bytes( 8 ) );

		$new_site_array = array();

		if ( isset( $npm_added_sites ) && ! empty( $npm_added_sites ) ) {
			$new_site_array['siteurl'] = $add_child_sites_action;
			$new_site_array['token']   = $token;
		} else {
			$new_site_array[0]['siteurl'] = $add_child_sites_action;
			$new_site_array[0]['token']   = $token;
		}

		if ( isset( $npm_added_sites ) && ! empty( $npm_added_sites ) ) {
			array_push( $npm_added_sites, $new_site_array );
			update_option( 'npm_added_child_sites', $npm_added_sites );
		} else {
			update_option( 'npm_added_child_sites', $new_site_array );
		}
		?>
		<tr>
			<td><?php echo esc_html( $add_child_sites_action ); ?></td>
			<td><?php echo esc_html( $token ); ?></td>
			<td class="action"><span class="dashicons dashicons-no-alt delete_site_button"></span></td>
		</tr>
		<?php

		die();
	}

	/**
	 * Ajax function for delete site
	 */
	public function delete_sites_action() {
		$delete_site_button = filter_input( INPUT_POST, 'delete_site_button', FILTER_SANITIZE_STRING );
		$delete_site_button = isset( $delete_site_button ) ? $delete_site_button : '';

		$npm_added_sites = get_option( 'npm_added_child_sites' );

		foreach ( $npm_added_sites as $k => $v ) {
			if ( $v['siteurl'] === $delete_site_button ) {
				unset( $npm_added_sites[ $k ] );
			}
		}

		update_option( 'npm_added_child_sites', $npm_added_sites );
		die();
	}

	/**
	 * Update Placeholder of DFF event post title
	 *
	 * @param $title
	 * @param $post
	 * @return string
	 */
	public function dff_event_title_place_holder( $title, $post ) {

		if ( 'dffmain-events' === $post->post_type ) {
			$my_title = ' Event Name (slug name)';

			return $my_title;
		}

		return $title;
	}

	/**
	 * Set Custom Column in Dff post listing page
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function set_dff_events_list_columns( $columns ) {

		$columns['author'] = __( 'Created By', 'events-main-plugin' );
		$columns['title']  = __( 'Event Name', 'events-main-plugin' );

		$columns['total_attendees'] = __( 'Attendees', 'events-main-plugin' );
		$columns['attendee']        = __( '	Add Attendee', 'events-main-plugin' );
		$columns['cancelled']       = __( 'Status', 'events-main-plugin' );

		unset(
			$columns['taxonomy-events_categories'],
			$columns['taxonomy-events_arabic_categories'],
			$columns['taxonomy-events_tags'],
			$columns['taxonomy-events_arabic_tags']
		);

		return $columns;

	}

	/**
	 * Set Value of Custom Column in Dff post listing page
	 *
	 * @param $column
	 * @param $post_id
	 */
	public function custom_dff_events_column_value( $column, $post_id ) {
		global $wpdb;
		$template_id = filter_input( INPUT_GET, 'template_id', FILTER_SANITIZE_NUMBER_INT );
		$template_id = isset( $template_id ) && ! empty( $template_id ) ? $template_id : '';
		if ( ! empty( $template_id ) ) {
			$attendee_list = $wpdb->get_results( $wpdb->prepare( "SELECT post_id from $wpdb->postmeta WHERE meta_key = '%s' AND meta_value = '%d'", 'event_id', $post_id ) );
			if ( ! empty( $attendee_list ) ) {
				$found_posts = count( $attendee_list );
			} else {
				$found_posts = 0;
			}
		} else {
			$args        = array(
				'post_type'  => 'attendees',
				'meta_query' => array(
					array(
						'key'   => 'event_id',
						'value' => "$post_id",
					),
				),
			);
			$query       = new WP_Query( $args );
			$found_posts = $query->found_posts ? $query->found_posts : 0;
			$event_attendee_limit_count = get_post_meta( $post_id, 'event_attendee_limit_count', true );
			$remaining_attendee_count = (int)$event_attendee_limit_count - (int)$found_posts;
		}

		// Check if the event is cancelled.
		$event_cancelled = get_post_status( $post_id );
		//$event_cancelled = 'cancelled' === $event_cancelled ? 'Yes' : '-';

		$current_date = date( 'Y-m-d' );

		$event_date_select = get_post_meta( $post_id, 'event_date_select', true );
		$event_end_date_select = get_post_meta( $post_id, 'event_end_date_select', true );

		if( isset( $event_end_date_select ) && !empty( $event_end_date_select ) ) {
			$event_date = $event_end_date_select;
		} else {
			$event_date = $event_date_select;
		}

		if( 'cancelled' === $event_cancelled ) {
			$event_cancelled = 'Cancelled';
		} else if( strtotime( $event_date ) >= strtotime( $current_date ) ) {
			$event_cancelled = 'Upcoming';
		} else {
			$event_cancelled = 'Past';
		}

		switch ( $column ) {

			case 'total_attendees':
				if( isset( $event_attendee_limit_count ) && !empty( $event_attendee_limit_count ) ) {
					echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=attendees&event_id=' . $post_id ) ) . '" target="_blank">' . esc_html( $found_posts ) ."/". esc_html( $event_attendee_limit_count ) . '</a>';
				} else {
					echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=attendees&event_id=' . $post_id ) ) . '" target="_blank">' . esc_html( $found_posts ) . '</a>';
				}
				break;
			case 'attendee':
				echo '<a class="" href="' . esc_url( get_the_permalink( $post_id ) ) . '?lang=en" target="_blank">Add Attendee</a>';
				break;
			case 'cancelled':
				echo esc_html( $event_cancelled );
				break;

		}

	}


	/**
	 * removed restore from bulk action from Events
	 *
	 * @param $actions
	 * @return mixed
	 */
	public function remove_edit_from_bulk_actions_events( $actions ) {
		unset( $actions['untrash'] );
		return $actions;
	}

	/**
	 * Set Custom column in registraion-form post type listing page
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function set_registration_forms_list_columns( $columns ) {
		$columns['title']                   = __( 'Template Name', 'events-main-plugin' );
		$columns['author']                  = __( 'Created By', 'events-main-plugin' );
		$columns['total_associated_events'] = __( 'Total Associated Events', 'events-main-plugin' );

		return $columns;
	}

	/**
	 * Set Value of custom column in registraion-form post type listing page
	 *
	 * @param $column
	 *
	 * @param $post_id
	 */
	public function custom_registration_forms_column_value( $column, $post_id ) {
		$args        = array(
			'post_type'  => 'any',
			'meta_query' => array(
				array(
					'key'   => '_wp_template_id',
					'value' => "$post_id",
				),
			),
		);
		$query       = new WP_Query( $args );
		$found_posts = $query->found_posts ? $query->found_posts : 0;

		switch ( $column ) {

			case 'total_associated_events':
				echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=dffmain-events&template_id=' . $post_id ) ) . '" target="_blank">' . esc_html( $found_posts ) . '</a>';
				break;
		}
	}

	/**
	 * Sort custom column of registraion-form post type listing page
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function set_custom_registration_forms_sortable_columns( $columns ) {
		unset( $columns['action'] );
		$columns['total_associated_events'] = 'total_associated_events';

		return $columns;
	}



	/**
	 * Set Custom column in attendees post type listing page
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function set_attendees_list_columns( $columns ) {
		unset( $columns['date'] );
		unset( $columns['title'] );
		$columns['name']         = __( 'Name', 'events-main-plugin' );
		$columns['company_name'] = __( 'Company Name', 'events-main-plugin' );
		$columns['email']        = __( 'Email', 'events-main-plugin' );
		$columns['event_name']   = __( 'Event Name', 'events-main-plugin' );
		$columns['check_in']     = __( 'Check In', 'events-main-plugin' );
		$columns['date_time']    = __( 'Date & Time', 'events-main-plugin' );
		$columns['action']       = __( 'Action', 'events-main-plugin' );

		return $columns;
	}


	/**
	 * Set Value of custom column of attendees post type listing page
	 *
	 * @param $columns
	 *
	 * @param $post_id
	 */
	public function custom_attendees_column_value( $column, $post_id ) {
		$company_name     = get_post_meta( $post_id, 'company_name', true );
		$event_name       = get_post_meta( $post_id, 'event_name', true );
		$event_id         = get_post_meta( $post_id, 'event_id', true );
		$cancelled_status = '';
		$event_url        = 'javascript:void(0);';
		if ( $event_id ) {
			$event_url   = get_edit_post_link( $event_id );
			$post_status = get_post_status( $event_id );

			if ( 'cancelled' === $post_status ) {
				$cancelled_status = ' - Cancelled Event';
			}
		}
		$email   = get_post_meta( $post_id, 'email', true );
		$checkin = get_post_meta( $post_id, 'checkin', true );

		if ( 'true' === $checkin ) {
			$checked      = 'checked=checked';
			$checked_html = 'Checked-in';
			$color        = 'green';
		} else {
			$checked      = '';
			$checked_html = '';
			$color        = '#555';
		}
		switch ( $column ) {

			case 'name':
				echo '<strong><a href="javascript:void(0)" class="view-detail" AttendeeId="' . esc_attr( $post_id ) . '">' . esc_html( get_the_title() ) . '</a></strong>';
				break;

			case 'company_name':
				echo ( ! empty( $company_name ) && isset( $company_name ) ) ? esc_html( $company_name ) : '-';
				break;

			case 'email':
				echo ( ! empty( $email ) && isset( $email ) ) ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '-';
				break;

			case 'event_name':
				echo ( ! empty( $event_name ) && isset( $event_name ) ) ? '<a href="' . esc_url( $event_url ) . '" target="_blank" >' . esc_html( $event_name . $cancelled_status ) . '</a>' : '-';
				break;

			case 'check_in':
				echo '<label for="check_in_' . esc_attr( $post_id ) . '"><input aria-labelledby="check_in_' . esc_attr( $post_id ) . '" type="checkbox" ' . esc_attr( $checked ) . ' id="check_in_' . esc_attr( $post_id ) . '" value="" ><span class=screen-reader-text>checkin</span></label><span class="checkin-label" style="color:' . esc_attr( $color ) . '">' . esc_html( $checked_html ) . '</span>';
				break;

			case 'date_time':
				echo get_the_date( 'd, F-Y H:i A', $post_id );
				break;

			case 'action':
				echo '<a href="javascript:void(0)" class="view-detail" AttendeeId="' . esc_attr( $post_id ) . '">View</a>';
				break;
		}
	}

	/**
	 * Sort custom column of attendees post type listing page
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */

	public function set_attendees_sortable_columns( $columns ) {
		$columns['name']         = 'name';
		$columns['company_name'] = 'company_name';
		$columns['date_time']    = 'date_time';
		$columns['event_name']   = 'event_name';

		return $columns;
	}

	/**
	 * Sort custom column by post meta
	 *
	 * @param $query
	 */
	public function set_attendees_orderby( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$event_id = filter_input( INPUT_GET, 'event_id', FILTER_SANITIZE_NUMBER_INT );
		$event_id = isset( $event_id ) && ! empty( $event_id ) ? $event_id : '';
		$orderby  = $query->get( 'orderby' );
		switch ( $orderby ) {
			case 'event_name':
				$query->set( 'meta_key', 'event_name' );
				$query->set( 'orderby', 'meta_value meta_value_num' );
				break;

			case 'company_name':
				$query->set( 'meta_key', 'company_name' );
				$query->set( 'orderby', 'meta_value meta_value_num' );
				break;
		}
		if ( ! empty( $event_id ) ) {
			$meta_query = array( 'relation' => 'OR' );
			array_push(
				$meta_query, array(
					'key'     => 'event_id',
					'value'   => $event_id,
					'compare' => 'LIKE',
				)
			);
			$query->set( 'meta_query', $meta_query );
		}
		$template_id = filter_input( INPUT_GET, 'template_id', FILTER_SANITIZE_NUMBER_INT );
		$template_id = isset( $template_id ) && ! empty( $template_id ) ? $template_id : '';
		if ( ! empty( $template_id ) ) {
			$meta_query = array( 'relation' => 'OR' );
			array_push(
				$meta_query, array(
					'key'     => '_wp_template_id',
					'value'   => $template_id,
					'compare' => 'LIKE',
				)
			);
			$query->set( 'post_type', 'dffmain-events' );
			$query->set( 'meta_query', $meta_query );
		}
	}


	/**
	 * Remove attendees post listing action
	 *
	 * @param $actions
	 * @return mixed
	 */
	public function remove_attendees_quick_edit( $actions ) {
		if ( get_post_type() === 'attendees' ) {
			unset( $actions['edit'] );
			unset( $actions['view'] );
			unset( $actions['trash'] );
			unset( $actions['inline'] );
			unset( $actions['inline hide-if-no-js'] );
		}
		if ( get_post_type() === 'dffmain-events' ) {
			unset( $actions['untrash'] );
		}

		if ( get_post_type() === 'registration-forms' ) {
			unset( $actions['view'] );
		}

		return $actions;
	}

	/**
	 * removed Edit from bulk action for Attendee list
	 *
	 * @param $actions
	 * @return mixed
	 */
	public function remove_edit_from_bulk_actions_attendee( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}


	/**
	 * removed 'mine' filter from Attendee list
	 *
	 * @param $views
	 * @return mixed
	 */
	public function remove_mine_filter_from_attendee( $views ) {
		unset( $views['mine'] );
		return $views;
	}

	/**
	 * Add Export List Button in Attendee List Table
	 *
	 * @param $which
	 */
	public function admin_attendee_list_top_export_button( $which ) {
		global $typenow, $wpdb;
		if ( 'attendees' === $typenow && 'top' === $which ) {
			$post_type   = 'dffmain-events';
			$post_status = 'publish';
			$event_list  = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_status FROM {$wpdb->prefix}posts WHERE {$wpdb->prefix}posts.post_type = '%s' AND ( {$wpdb->prefix}posts.post_status = '%s' OR {$wpdb->prefix}posts.post_status = '%s' )  ORDER BY {$wpdb->prefix}posts.post_date DESC", $post_type, $post_status, 'cancelled' ) );
			$event_id    = filter_input( INPUT_GET, 'event_id', FILTER_SANITIZE_NUMBER_INT );
			$event_id    = isset( $event_id ) && ! empty( $event_id ) ? $event_id : '';
			if ( $event_list ) {
				?>
				<div class="alignleft actions">
					<label for="filter-by-event" class="screen-reader-text">Filter by Event</label>
					<select name="event_id" id="filter-by-date">
						<option value="">All Events</option>
						<?php
						foreach ( $event_list as $event ) {
							$post_id     = $event->ID;
							$post_title  = $event->post_title;
							$post_status = $event->post_status;
							if ( 'cancelled' === $post_status ) {
								$status = ' - Cancelled Event';
							} else {
								$status = '';
							}
							?>
							<option value="<?php echo esc_attr( $post_id ); ?>" <?php echo ( intval( $post_id ) === intval( $event_id ) ) ? 'selected="selected"' : ''; ?>"><?php echo esc_html( $post_title . $status ); ?></option>
						<?php } ?>
					</select>
					<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
				</div>
			<?php
			}
			if ( ! empty( $event_id ) ) {
				$arg            = array(
					'post_type'      => 'attendees',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'   => 'event_id',
							'value' => "$event_id",
						),
					),
				);
				$query          = new WP_Query( $arg );
				$found_attendee = $query->found_posts ? $query->found_posts : 0;
				if ( 0 < $found_attendee ) {
				?>
					<input type="submit" name="export_list" id="export_list" class="button button-primary"
						   value="Export List"/>
				<?php } ?>
				<div class="attendee-list-title"><p>Attendee List :
						<strong><?php echo get_the_title( $event_id ); ?></strong></p>
					<a class="button button-primary add-attendee"
					   href="<?php echo esc_url( get_the_permalink( $event_id ) ) . '?lang=en&checkin=true'; ?>" target="_blank">Add
						Walk-in</a>
				</div>
			<?php
			}
		}
	}

	/**
	 *  Export Attendee List
	 */
	public function export_attendee_list() {
		$export_list = filter_input( INPUT_GET, 'export_list', FILTER_SANITIZE_STRING );
		if ( isset( $export_list ) ) {
			$event_id = filter_input( INPUT_GET, 'event_id', FILTER_SANITIZE_NUMBER_INT );
			$event_id = isset( $event_id ) && ! empty( $event_id ) ? $event_id : '';
			header( 'Content-type: text/csv' );
			header( 'Content-Disposition: attachment; filename="Attendee_List.csv"' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			$file = fopen( 'php://output', 'w' );

			if ( ! empty( $event_id ) ) {
				$arg   = array(
					'post_type'      => 'attendees',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_query'     => array(
						'key'   => 'event_id',
						'value' => $event_id,
					),
				);
				$query = new WP_Query( $arg );

				if ( $query->have_posts() ) {
					$field_preference = get_post_meta( $event_id, '_wp_field_preference', true );
					$header           = array( 'Name', 'Email', 'Event Name', 'Check In', 'Langauge Type', 'Date & Time' );
					$header_for_loop  = $header;
					if ( isset( $field_preference ) && ! empty( $field_preference ) ) {
						foreach ( $field_preference as $key => $val ) {
							$replacement_key = substr( $key, 2 );
							$updated_key     = preg_replace( '/(?<!\ )[A-Z]/', ' $0', $replacement_key );
							array_push( $header, $updated_key );
							array_push( $header_for_loop, $replacement_key );
						}
					}
					fputcsv( $file, $header );
					while ( $query->have_posts() ) {
						$row = array();
						$query->the_post();
						$post_id       = get_the_ID();
						$name          = get_the_title();
						$email         = get_post_meta( $post_id, 'email', true );
						$event_name    = get_post_meta( $post_id, 'event_name', true );
						$checkin       = get_post_meta( $post_id, 'checkin', true );
						$check_in      = ( 'true' === $checkin ) ? 'Yes' : 'No';
						$language_type = get_post_meta( $post_id, 'language_type', true );
						$language_type = ( 'ar' === $language_type ) ? 'Arabic' : 'English';
						$date          = get_the_date( 'Y/m/d H:i A', $post_id );
						$time          = ( ! empty( $date ) && isset( $date ) ) ? $date : '-';
						array_push( $row, $name, $email, $event_name, $check_in, $language_type, $time );
						$attendee_data = get_post_meta( $post_id, 'attendee_data', true );

						foreach ( $header_for_loop as $key => $value ) {
							if ( 'Name' !== $value && 'Email' !== $value && 'Langauge Type' !== $value && 'Date & Time' !== $value && 'Event Name' !== $value && 'Check In' !== $value ) {
								$row_data = ( ! empty( $attendee_data[ $value ] ) && isset( $attendee_data[ $value ] ) ) ? $attendee_data[ $value ] : '-';
								if ( is_array( $row_data ) ) {
									$row_data = implode( ', ', $row_data );
								} elseif ( preg_match( '/<[^<]+>/', $row_data, $m ) !== 0 ) {
									preg_match_all( '/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $row_data, $result );
									$row_data = implode( ', ', $result['href'] );
								}
								array_push( $row, $row_data );
							}
						}
						fputcsv( $file, $row );
					}

					exit();
				}
			}
		}
	}

	/**
	 * Set Typing direction for arabic fields
	 *
	 * @param $settings
	 * @param $editor_id
	 * @return mixed
	 */
	function dff_setEditorToRTL( $settings, $editor_id ) {

		$to_rtl_arr = [];

		$translations = dffmain_mlp_get_translations();
		foreach ( $translations as $translation ) {
			$language        = $translation->language();
			$language_name   = $language->isoName();
			$is_rtl          = $language->isRtl();

			if ( $is_rtl ) {
				$to_rtl_arr[] = 'event_send_special_email_' . $language_name;
				$to_rtl_arr[] = 'events_content_thank_you_after_registration_' . $language_name;
				$to_rtl_arr[] = 'events_content_event_reminder_' . $language_name;
				$to_rtl_arr[] = 'events_content_event_cancel_' . $language_name;
			}
		}

		if ( in_array( $editor_id, $to_rtl_arr ) ) {
			$settings['directionality'] = 'rtl';
		}

		return $settings;

	}

	/**
	 * Ajax call for Save and Next button click in DFF post
	 */
	public function dff_save_next_click_ajax() {

		$postID = filter_input( INPUT_POST, 'postID', FILTER_SANITIZE_NUMBER_INT );
		$postID = isset( $postID ) ? $postID : '';

		$dffmain_post_title = filter_input( INPUT_POST, 'dffmain_post_title', FILTER_SANITIZE_STRING );
		$dffmain_post_title = isset( $dffmain_post_title ) ? $dffmain_post_title : '';

		$events_overview = isset( $_POST['events_overview'] ) ? $_POST['events_overview'] : '';

		$dffmain_events_agenda = isset( $_POST['dffmain_events_agenda'] ) ? $_POST['dffmain_events_agenda'] : '';

		$dffmain_event_location = isset( $_POST['dffmain_event_location'] ) ? $_POST['dffmain_event_location'] : '';

		$emp_category = filter_input( INPUT_POST, 'emp_category', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( isset( $emp_category ) && ! empty( $emp_category ) ) {
			$emp_category = implode( ',', $emp_category );
		}

		$emp_tags = filter_input( INPUT_POST, 'emp_tags', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( isset( $emp_tags ) && ! empty( $emp_tags ) ) {
			$emp_tags = implode( ',', $emp_tags );
		}

		update_post_meta( $postID, 'dffmain_post_title', $dffmain_post_title );
		update_post_meta( $postID, 'events_overview', $events_overview );
		update_post_meta( $postID, 'dffmain_events_agenda', $dffmain_events_agenda );
		update_post_meta( $postID, 'dffmain_event_location', $dffmain_event_location );

		update_post_meta( $postID, 'emp_category', $emp_category );
		update_post_meta( $postID, 'emp_tags', $emp_tags );

		wp_set_post_terms( $postID, $emp_category, 'events_categories', false );
		wp_set_post_terms( $postID, $emp_tags, 'events_tags', false );

		die();

	}

	/**
	 * Ajax call for email sent
	 */
	public function event_send_special_single_email() {
		

		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$post_id = isset( $post_id ) ? $post_id : '';
		// delete_post_meta( $post_id, 'event_email_history' );
		$site_id = filter_input( INPUT_POST, 'site_id', FILTER_SANITIZE_NUMBER_INT );
		$site_id = isset( $site_id ) ? $site_id : '';

		// $site_language = filter_input( INPUT_POST, 'site_language', FILTER_SANITIZE_NUMBER_INT );
		// $site_language = isset( $site_language ) ? $site_language : '';

		$dffmain_event_special_mail_data = isset( $_POST['dffmain_event_special_mail_data'] ) ? $_POST['dffmain_event_special_mail_data'] : [];
		$dffmain_event_content = [];
		$dffmain_event_subject = [];
		foreach ($dffmain_event_special_mail_data as $value) {
			$dffmain_event_content[$value['language']] = $value['content'];
			$dffmain_event_subject[$value['language']] = $value['subject'];
		}


		// echo ("<pre>");
		// // var_dump($dffmain_event_special_mail_data);
		// print_r($dffmain_event_subject);
		// echo ("</pre>");
		// wp_die();


		// $dffmain_event_content = isset( $_POST['dffmain_event_content'] ) ? $_POST['dffmain_event_content'] : [];

		// $dffmain_event_subject = isset( $_POST['dffmain_event_subject'] ) ? $_POST['dffmain_event_subject'] : [];

		$settings_array_get          = get_option( 'events_general_settings' );
		$events_general_settings_get = json_decode( $settings_array_get );
		$events_general_settings_get = (array) $events_general_settings_get;
		$sendgrid_apikey      = $events_general_settings_get['send_grid_key'];
		$send_grid_from_email = $events_general_settings_get['send_grid_from_email'];
		$send_grid_from_name  = $events_general_settings_get['send_grid_from_name'];
		$url = 'https://api.sendgrid.com/';

		$tmp_arr = [];
		$translations_data = dffmain_mlp_get_translations();
		foreach ( $translations_data as $translation ) {

			$language       = $translation->language();
			$language_name  = $language->isoName();
			$key_site_id = $translation->remoteSiteId();

			$tmp_arr[$key_site_id]['language_name'] = $language_name;
		}

		$translation_ids = multilingualpress_get_ids( $post_id, $site_id );
		foreach ( $translation_ids as $site_id => $post_id ) {
			if ( get_main_site_id() == $site_id ) {
				$curr_title    = get_post_meta( $post_id, 'dffmain_post_title', true );
				$curr_location = get_post_meta( $post_id, 'dffmain_event_location', true );
				
				$tmp_arr[$site_id]['curr_title']    = $curr_title;
				$tmp_arr[$site_id]['curr_location'] = $curr_location;
			}else {
				$curr_title = multisite_post_meta( $site_id, $post_id, 'dffmain_post_title' );
				$curr_location = multisite_post_meta( $site_id, $post_id, 'dffmain_event_location' );

				$tmp_arr[$site_id]['curr_title'] = $curr_title;
				$tmp_arr[$site_id]['curr_location'] = $curr_location;
			}
		}
		$dffmain_post_title     = [];
		$dffmain_event_location = [];
		foreach ($tmp_arr as $value) {
			$dffmain_post_title[$value['language_name']]     = $value['curr_title'];
			$dffmain_event_location[$value['language_name']] = $value['curr_location'];
		}

		$event_date              = get_post_meta( $post_id, 'event_date_select', true );
		$event_end_date          = get_post_meta( $post_id, 'event_end_date_select', true );

		$event_time_start_select = get_post_meta( $post_id, 'event_time_start_select', true );
		$event_time_end_select   = get_post_meta( $post_id, 'event_time_end_select', true );

		$event_date = new DateTime( "$event_date" );
		$event_date = $event_date->format( 'F d, Y' );

		if( isset( $event_end_date ) && !empty( $event_end_date ) ) {

			$event_end_date = new DateTime( "$event_end_date" );
			$event_end_date = $event_end_date->format( 'F d, Y' );

			$event_date = $event_date ." - ". $event_end_date;
		}

		$event_time_start_select = new DateTime( "$event_time_start_select" );
		$event_time_start_select = $event_time_start_select->format( 'h:i A' );

		$event_time_end_select = new DateTime( "$event_time_end_select" );
		$event_time_end_select = $event_time_end_select->format( 'h:i A' );

		$dffmain_attendee_data = [];
		$args_attendees = array(
			'post_type'      => 'attendees',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => 'event_id',
					'value' => "$post_id",
				),
			),
			'fields' => 'ids',
		);

		$query_attendees = new WP_Query( $args_attendees );

		if ( isset( $query_attendees->posts ) && ! empty( $query_attendees->posts ) ) {
			foreach ( $query_attendees->posts as $query_attendees_data ) {

				$attendee_data = get_post_meta( $query_attendees_data, 'attendee_data', true );

				$translations = dffmain_mlp_get_translations();
				foreach ( $translations as $translation ) {
					$language      = $translation->language();
					$language_name = $language->isoName();

					$attendee_language = convert_locale_to_full_name( $attendee_data['languageType'] );
					
					if ( $attendee_language == $language_name ) {
						$dffmain_attendee_data['e_attendee_fname'] = $attendee_data['FirstName'];
						$dffmain_attendee_data['e_attendee_lname'] = $attendee_data['LastName'];
						$dffmain_attendee_data['Email']            = $attendee_data['Email'];
					}

					$dffmain_attendee_data['event_name'] = $dffmain_post_title[$attendee_language];
					$dffmain_attendee_data['location']   = $dffmain_event_location[$attendee_language];
				}

				$event_date = str_replace( ' - ', ' to ', $event_date );

				$dffmain_attendee_data['date']       = $event_date;
				$dffmain_attendee_data['time_frame'] = $event_time_start_select . ' to ' . $event_time_end_select;

				if( isset( $event_end_date ) && !empty( $event_end_date ) ) {
					$dffmain_attendee_data['date_output'] = '{{date}}';
				} else {
					$dffmain_attendee_data['date_output'] = '{{date}} from {{time}} (GMT+4)';
				}

				/** mail sent for attendee */
				$template_id = 'd-e0a56b842d0541b0b34be68709f8798c'; /** TODO !!! Hardcodded ID */



				$dffmain_event_subject[$attendee_language] = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'], $dffmain_event_subject[$attendee_language] );
				$dffmain_event_subject[$attendee_language] = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'], $dffmain_event_subject[$attendee_language] );
				$dffmain_event_subject[$attendee_language] = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'], $dffmain_event_subject[$attendee_language] );
				$dffmain_event_subject[$attendee_language] = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'], $dffmain_event_subject[$attendee_language] );
				$dffmain_event_subject[$attendee_language] = str_replace( '{{date}}', $dffmain_attendee_data['date'], $dffmain_event_subject[$attendee_language] );
				$dffmain_event_subject[$attendee_language] = str_replace( '{{location}}', $dffmain_attendee_data['location'], $dffmain_event_subject[$attendee_language] );
				$dffmain_event_subject[$attendee_language] = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'], $dffmain_event_subject[$attendee_language] );
				$dffmain_event_subject[$attendee_language] = str_replace("&#039;", "'", $dffmain_event_subject[$attendee_language]);

				$dffmain_event_content[$attendee_language] = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'], $dffmain_event_content[$attendee_language] );
				$dffmain_event_content[$attendee_language] = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'], $dffmain_event_content[$attendee_language] );
				$dffmain_event_content[$attendee_language] = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'], $dffmain_event_content[$attendee_language] );
				$dffmain_event_content[$attendee_language] = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'], $dffmain_event_content[$attendee_language] );
				$dffmain_event_content[$attendee_language] = str_replace( '{{date}}', $dffmain_attendee_data['date'], $dffmain_event_content[$attendee_language] );
				$dffmain_event_content[$attendee_language] = str_replace( '{{location}}', $dffmain_attendee_data['location'], $dffmain_event_content[$attendee_language] );
				$dffmain_event_content[$attendee_language] = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'], $dffmain_event_content[$attendee_language] );
				$dffmain_event_content[$attendee_language] = str_replace("&#039;", "'", $dffmain_event_content[$attendee_language]);

				$json_string_eng = (object) array(
					'from' => array( 'email' => 'no-reply@dubaifuture.ae' ),
					'personalizations' => array( 
						array(
							'to' => array( array( 'email' => $dffmain_attendee_data['Email'] ) ),
							'dynamic_template_data' => array(
								'EMAIL_SUBJECT' => $dffmain_event_subject[$attendee_language],
								'EMAIL_CONTENT' => $dffmain_event_content[$attendee_language],
								'DISPLAY_NAME' => $dffmain_attendee_data['e_attendee_fname'],
								'HELLO' => 'Hello',
							),
						)
					),
					'template_id' => $template_id,
				);				
			
				$request      = $url . 'v3/mail/send';
				$response = wp_remote_post(
					$request, array(
						'method'  => 'POST',
						'headers' => array( 'Authorization' => 'Bearer ' . $sendgrid_apikey, 'Content-Type' => 'application/json' ),
						'body'    => wp_json_encode( $json_string_eng ),
					)
				);
					
				$dffmain_event_subject[$attendee_language] = str_replace( $dffmain_attendee_data['e_attendee_fname'], '{{e_attendee_fname}}', $dffmain_event_subject[$attendee_language] );
				$dffmain_event_subject[$attendee_language] = str_replace( $dffmain_attendee_data['e_attendee_lname'], '{{e_attendee_lname}}', $dffmain_event_subject[$attendee_language] );

				$dffmain_event_content[$attendee_language] = str_replace( $dffmain_attendee_data['e_attendee_fname'], '{{e_attendee_fname}}', $dffmain_event_content[$attendee_language] );
				$dffmain_event_content[$attendee_language] = str_replace( $dffmain_attendee_data['e_attendee_lname'], '{{e_attendee_lname}}', $dffmain_event_content[$attendee_language] );

				$response_data = ['qwerty' => 'sdfg'];
				if ( 200 === $response['response']['code'] ) {
					$response_data['response'] = 'Sent';
				} else {
					$response_data['response'] = 'Fail';
				}
	
				$response_data['dffmain_email_subject'] = $dffmain_event_subject[$attendee_language];
				$response_data['dffmain_email_content'] = $dffmain_event_content[$attendee_language];
				$response_data['email_date'] = date( 'd-M-Y | h:i:s' );
						// echo ("<pre>");
						var_dump( file_exists( plugin_dir_path( __FILE__ ) . 'includes/class-events-main-plugin.php' ));
						echo plugin_dir_path(__FILE__) . 'includes/qqq-events.php';
						// print_r($response_data);
						// echo ("</pre>");
						// wp_die();
				add_post_meta( $post_id, 'event_email_history', $response_data );
			}/**foreach ( $query_attendees->posts as $query_attendees_data ) { */
		}/**if ( isset( $query_attendees->posts ) && ! empty( $query_attendees->posts ) ) { */
wp_die();
		/**
		 * mail sent for attendee
		 */
		// $template_id = 'd-e0a56b842d0541b0b34be68709f8798c'; /** TODO !!! Hardcodded ID */

		// if ( isset( $dffmain_event_subject ) 
		// 	&& ! empty( $dffmain_event_subject ) 
		// 	&& isset( $dffmain_event_content ) 
		// 	&& ! empty( $dffmain_event_content ) ) {

		// 	foreach( $dffmain_attendee_data['Email'] as $k => $v ) {

		// 		$dffmain_event_subject = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'][$k], $dffmain_event_subject );
		// 		$dffmain_event_subject = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'][$k], $dffmain_event_subject );
		// 		$dffmain_event_subject = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'][$k], $dffmain_event_subject );
		// 		$dffmain_event_subject = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'][$k], $dffmain_event_subject );
		// 		$dffmain_event_subject = str_replace( '{{date}}', $dffmain_attendee_data['date'][$k], $dffmain_event_subject );
		// 		$dffmain_event_subject = str_replace( '{{location}}', $dffmain_attendee_data['location'][$k], $dffmain_event_subject );
		// 		$dffmain_event_subject = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'][$k], $dffmain_event_subject );
		// 		$dffmain_event_subject = str_replace("&#039;", "'", $dffmain_event_subject);

		// 		$dffmain_event_content = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'][$k], $dffmain_event_content );
		// 		$dffmain_event_content = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'][$k], $dffmain_event_content );
		// 		$dffmain_event_content = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'][$k], $dffmain_event_content );
		// 		$dffmain_event_content = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'][$k], $dffmain_event_content );
		// 		$dffmain_event_content = str_replace( '{{date}}', $dffmain_attendee_data['date'][$k], $dffmain_event_content );
		// 		$dffmain_event_content = str_replace( '{{location}}', $dffmain_attendee_data['location'][$k], $dffmain_event_content );
		// 		$dffmain_event_content = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'][$k], $dffmain_event_content );

		// 		$dffmain_event_content = str_replace("&#039;", "'", $dffmain_event_content);

		// 		$json_string_eng = (object) array(
		// 			'from' => array( 'email' => 'no-reply@dubaifuture.ae' ),
		// 			'personalizations' => array( 
		// 				array(
		// 					'to' => array( array( 'email' => $v ) ),
		// 					'dynamic_template_data' => array(
		// 						'EMAIL_SUBJECT' => $dffmain_event_subject,
		// 						'EMAIL_CONTENT' => $dffmain_event_content,
		// 						'DISPLAY_NAME' => $dffmain_attendee_data['e_attendee_fname'][$k],
		// 						'HELLO' => 'Hello',
		// 					),
		// 				)
		// 			),
		// 			'template_id' => $template_id,
		// 		);

				
			
		// 		$request      = $url . 'v3/mail/send';
		// 		$response = wp_remote_post(
		// 			$request, array(
		// 				'method'  => 'POST',
		// 				'headers' => array( 'Authorization' => 'Bearer ' . $sendgrid_apikey, 'Content-Type' => 'application/json' ),
		// 				'body'    => wp_json_encode( $json_string_eng ),
		// 			)
		// 		);

					
		// 		$dffmain_event_subject = str_replace( $dffmain_attendee_data['e_attendee_fname'][$k], '{{e_attendee_fname}}', $dffmain_event_subject );
		// 		$dffmain_event_subject = str_replace( $dffmain_attendee_data['e_attendee_lname'][$k], '{{e_attendee_lname}}', $dffmain_event_subject );

		// 		$dffmain_event_content = str_replace( $dffmain_attendee_data['e_attendee_fname'][$k], '{{e_attendee_fname}}', $dffmain_event_content );
		// 		$dffmain_event_content = str_replace( $dffmain_attendee_data['e_attendee_lname'][$k], '{{e_attendee_lname}}', $dffmain_event_content );

		// 	}

		// 	if ( 200 === $response['response']['code'] ) {
		// 		$response_data['english_response'] = 'Sent';
		// 	} else {
		// 		$response_data['english_response'] = 'Fail';
		// 	}

		// 	$response_data['dffmain_email_subject'] = $dffmain_event_subject;
		// 	$response_data['dffmain_email_content'] = $dffmain_event_content;
		// }

		// $response_data['email_date'] = date( 'd-M-Y | h:i:s' );
		// add_post_meta( $post_id, 'event_email_history', $response_data );

		?>
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
			$event_email_history = get_post_meta( $post_id, 'event_email_history', false );
			$event_email_history = array_reverse( $event_email_history );

			if ( isset( $event_email_history ) && ! empty( $event_email_history ) ) {
				$count = 1;
				foreach ( $event_email_history as $event_email_history_data ) {
					?>
					<tr>
						<td><?php echo esc_html( $count ); ?></td>
						<td><?php echo esc_html( $event_email_history_data['email_date'] ); ?></td>
						<td><?php echo ! empty( $event_email_history_data['dffmain_email_subject'] ) ? esc_html( $event_email_history_data['dffmain_email_subject'] ) : esc_html( $event_email_history_data['arabic_subject'] ); ?></td>
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
												<h3>English Email</h3>
											</div>
											<div class="accordian-body" style="display:block;">
												<h4>
													Subject: <?php echo esc_html( $event_email_history_data['dffmain_email_subject'] ); ?></h4>
												<h3>Content</h3>
												<hr>
												<?php echo wp_kses_post( $event_email_history_data['dffmain_email_content'] ); ?>
											</div>
										</div>
										<?php
									}

									if ( ! empty( $event_email_history_data['arabic_subject'] ) ) {
									?>
									<div class="accordian-main">
										<div class="accordian-title">
											<h3>Arabic Email</h3>
										</div>
										<div class="accordian-body"
											 style="unicode-bidi: bidi-override !important;direction: rtl !important;text-align:right;">
											<hr>
											<div>
												<h4>
													Subject: <?php echo esc_html( $event_email_history_data['arabic_subject'] ); ?></h4>
												<h3>Content</h3>
												<hr>
												<div style="word-wrap: break-word;">
													<?php echo wp_kses_post( $event_email_history_data['arabic_content'] ); ?>
												</div>
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
				}
			}
			?>
			</tbody>
		</table>
		<?php

		die();
	}



	/**
	 * Cron function for send email reminder email.
	 */
	public static function cron_event_reminder() {

		$current_date                = date( 'Y-m-d' );
		$settings_array_get          = get_option( 'events_general_settings' );
		$events_general_settings_get = json_decode( $settings_array_get );
		$events_general_settings_get = (array) $events_general_settings_get;
		$send_grid_from_email        = $events_general_settings_get['send_grid_from_email'];
		$send_grid_from_name         = $events_general_settings_get['send_grid_from_name'];

		$events_content_event_reminder = get_option( 'events_content_event_reminder' );
		$events_arabic_event_reminder  = get_option( 'events_arabic_event_reminder' );
		$subject_event_reminder        = get_option( 'subject_event_reminder' );
		$arabic_event_reminder         = get_option( 'arabic_event_reminder' );
		$url                           = 'https://api.sendgrid.com/';

		/**
		 * Fetch event details of today's date.
		 */
		$args_dff_events = array(
			'post_type'      => 'dffmain-events',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => 'event_reminder_date',
					'value' => "$current_date",
				),
			),
			'fields'         => 'ids',
		);

		$dff_events_record = new WP_Query( $args_dff_events );

		if ( isset( $dff_events_record->posts ) && ! empty( $dff_events_record->posts ) ) {
			foreach ( $dff_events_record->posts as $dff_events_record_ids ) {

				$dffmain_post_title      = get_post_meta( $dff_events_record_ids, 'dffmain_post_title', true );
				$arabic_post_title       = get_post_meta( $dff_events_record_ids, 'arabic_post_title', true );
				$event_date              = get_post_meta( $dff_events_record_ids, 'event_date_select', true );
				$event_end_date          = get_post_meta( $dff_events_record_ids, 'event_end_date_select', true );
				$event_time_start_select = get_post_meta( $dff_events_record_ids, 'event_time_start_select', true );
				$event_time_end_select   = get_post_meta( $dff_events_record_ids, 'event_time_end_select', true );
				$dffmain_event_location  = get_post_meta( $dff_events_record_ids, 'dffmain_event_location', true );
				$arabic_event_location   = get_post_meta( $dff_events_record_ids, 'arabic_event_location', true );

				$events_overview         = get_post_meta( $dff_events_record_ids, 'events_overview', true );
				$events_arabic_overview  = get_post_meta( $dff_events_record_ids, 'events_arabic_overview', true );
				$dffmain_events_agenda   = get_post_meta( $dff_events_record_ids, 'dffmain_events_agenda', true );
				$events_arabic_agenda    = get_post_meta( $dff_events_record_ids, 'events_arabic_agenda', true );

				$sendgrid_apikey = $events_general_settings_get['send_grid_key'];

				$event_date = new DateTime( "$event_date" );
				$event_date = $event_date->format( 'F d, Y' );

				if( isset( $event_end_date ) && !empty( $event_end_date ) ) {
					$event_end_date = new DateTime( "$event_end_date" );
					$event_end_date = $event_end_date->format( 'F d, Y' );

					$event_date = $event_date . " - " . $event_end_date;
				}

				$event_time_start_select = new DateTime( "$event_time_start_select" );
				$event_time_start_select = $event_time_start_select->format( 'h:i A' );

				$event_time_end_select = new DateTime( "$event_time_end_select" );
				$event_time_end_select = $event_time_end_select->format( 'h:i A' );

				/**
				 * Fetch attendee data of this event.
				 */
				$args_attendees        = array(
					'post_type'      => 'attendees',
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'   => 'event_id',
							'value' => "$dff_events_record_ids",
						),
					),
					'fields'         => 'ids',
				);
				$dffmain_attendee_data = array();
				$arabic_attendee_data  = array();
				$query_attendees       = new WP_Query( $args_attendees );

				if ( isset( $query_attendees->posts ) && ! empty( $query_attendees->posts ) ) {
					foreach ( $query_attendees->posts as $query_attendees_data ) {
						$attendee_data = get_post_meta( $query_attendees_data, 'attendee_data', true );

						/**TODO languge */
						if ( 'en' === $attendee_data['languageType'] ) {

							$event_date_en = str_replace( ' - ', ' to ', $event_date );

							$dffmain_attendee_data['e_attendee_fname'][] = $attendee_data['FirstName'];
							$dffmain_attendee_data['e_attendee_lname'][] = $attendee_data['LastName'];
							$dffmain_attendee_data['Email'][]            = $attendee_data['Email'];
							$dffmain_attendee_data['event_name'][]       = $dffmain_post_title;
							$dffmain_attendee_data['date'][]             = $event_date_en;
							$dffmain_attendee_data['time_frame'][]       = $event_time_start_select . ' To ' . $event_time_end_select;
							$dffmain_attendee_data['location'][]         = $dffmain_event_location;
							$dffmain_attendee_data['e_event_detail'][]   = $events_overview . '<br><br>' . $dffmain_events_agenda;

							if( isset( $event_end_date ) && !empty( $event_end_date ) ) {
								$dffmain_attendee_data['date_output'][] = '{{date}}';
							} else {
								$dffmain_attendee_data['date_output'][] = '{{date}} from {{time}} (GMT+4)';
							}

						} elseif ( 'ar' === $attendee_data['languageType'] ) {

							$event_date_ar = str_replace( ' - ', ' إلى ', $event_date );

							$arabic_attendee_data['a_attendee_fname'][] = $attendee_data['FirstName'];
							$arabic_attendee_data['a_attendee_lname'][] = $attendee_data['LastName'];
							$arabic_attendee_data['Email'][]            = $attendee_data['Email'];
							$arabic_attendee_data['event_name'][]       = $arabic_post_title;
							$arabic_attendee_data['date'][]             = $event_date_ar;
							$arabic_attendee_data['time_frame'][]       = $event_time_start_select . ' إلى ' . $event_time_end_select;
							$arabic_attendee_data['location'][]         = $arabic_event_location;
							$arabic_attendee_data['a_event_detail'][]   = $events_arabic_overview . '<br><br>' . $events_arabic_agenda;
							$event_details_ar                               = $events_arabic_overview . '<br><br>' . $events_arabic_agenda;

							if( isset( $event_end_date ) && !empty( $event_end_date ) ) {
								$arabic_attendee_data['date_output'][] = '{{date}}';
							} else {
								$arabic_attendee_data['date_output'][] = '{{date}} من {{time}} (توقيت دبي)';
							}

						}
					}
				}

				/**
				 * mail sent for english attendee
				 */
				$template_id = 'd-e0a56b842d0541b0b34be68709f8798c';
				if ( isset( $dffmain_attendee_data['Email'] ) && ! empty( $dffmain_attendee_data['Email'] ) ) {

					
					foreach( $dffmain_attendee_data['Email'] as $k => $v ) {

						$dffmain_attendee_data['date_output'][$k] = str_replace( 'إلى', ' to ', $dffmain_attendee_data['date_output'][$k] );

						$subject_event_reminder = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'][$k], $subject_event_reminder );
						$subject_event_reminder = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'][$k], $subject_event_reminder );
						$subject_event_reminder = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'][$k], $subject_event_reminder );
						$subject_event_reminder = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'][$k], $subject_event_reminder );
						$subject_event_reminder = str_replace( '{{date}}', $dffmain_attendee_data['date'][$k], $subject_event_reminder );
						$subject_event_reminder = str_replace( '{{location}}', $dffmain_attendee_data['location'][$k], $subject_event_reminder );
						$subject_event_reminder = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'][$k], $subject_event_reminder );

						$events_content_event_reminder = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'][$k], $events_content_event_reminder );
						$events_content_event_reminder = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'][$k], $events_content_event_reminder );
						$events_content_event_reminder = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'][$k], $events_content_event_reminder );
						$events_content_event_reminder = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'][$k], $events_content_event_reminder );
						$events_content_event_reminder = str_replace( '{{date}}', $dffmain_attendee_data['date'][$k], $events_content_event_reminder );
						$events_content_event_reminder = str_replace( '{{location}}', $dffmain_attendee_data['location'][$k], $events_content_event_reminder );
						$events_content_event_reminder = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'][$k], $events_content_event_reminder );

						$events_content_event_reminder = str_replace( '{{e_event_details}}', $dffmain_attendee_data['e_event_detail'][$k], $events_content_event_reminder );

						$params_ar = (object) array(
							'from' => array( 'email' => 'no-reply@dubaifuture.ae' ),
							'personalizations' => array( 
								array(
									'to' => array( array( 'email' => $v ) ),
									'dynamic_template_data' => array(
										'EMAIL_SUBJECT' => $subject_event_reminder,
										'EMAIL_CONTENT' => wpautop( $events_content_event_reminder ),
										'DISPLAY_NAME' => $dffmain_attendee_data['e_attendee_fname'][$k],
										'HELLO' => 'Dear',
									),
								)
							),
							'template_id' => $template_id,
						);

						$request      = $url . 'v3/mail/send';
						$response_ar = wp_remote_post(
							$request, array(
								'method'  => 'POST',
								'headers' => array( 'Authorization' => 'Bearer ' . $sendgrid_apikey, 'Content-Type' => 'application/json' ),
								'body'    => wp_json_encode( $params_ar ),
							)
						);

						$subject_event_reminder = str_replace( $dffmain_attendee_data['e_attendee_fname'][$k], '{{e_attendee_fname}}', $subject_event_reminder );
						$subject_event_reminder = str_replace( $dffmain_attendee_data['e_attendee_lname'][$k], '{{e_attendee_lname}}', $subject_event_reminder );

						$events_content_event_reminder = str_replace( $dffmain_attendee_data['e_attendee_fname'][$k], '{{e_attendee_fname}}', $events_content_event_reminder );
						$events_content_event_reminder = str_replace( $dffmain_attendee_data['e_attendee_lname'][$k], '{{e_attendee_lname}}', $events_content_event_reminder );

					}

				}

				if ( isset( $arabic_attendee_data['Email'] ) && ! empty( $arabic_attendee_data['Email'] ) ) {

					foreach( $arabic_attendee_data['Email'] as $k => $v ) {

						$arabic_event_reminder = str_replace( '{{date/time}}', $arabic_attendee_data['date_output'][$k], $arabic_event_reminder );
						$arabic_event_reminder = str_replace( '{{a_attendee_fname}}', $arabic_attendee_data['a_attendee_fname'][$k], $arabic_event_reminder );
						$arabic_event_reminder = str_replace( '{{a_attendee_lname}}', $arabic_attendee_data['a_attendee_lname'][$k], $arabic_event_reminder );
						$arabic_event_reminder = str_replace( '{{a_eventname}}', $arabic_attendee_data['event_name'][$k], $arabic_event_reminder );
						$arabic_event_reminder = str_replace( '{{date}}', $arabic_attendee_data['date'][$k], $arabic_event_reminder );
						$arabic_event_reminder = str_replace( '{{location}}', $arabic_attendee_data['location'][$k], $arabic_event_reminder );
						$arabic_event_reminder = str_replace( '{{time}}', $arabic_attendee_data['time_frame'][$k], $arabic_event_reminder );

						$events_arabic_event_reminder = str_replace( '{{date/time}}', $arabic_attendee_data['date_output'][$k], $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( '{{a_attendee_fname}}', $arabic_attendee_data['a_attendee_fname'][$k], $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( '{{a_attendee_lname}}', $arabic_attendee_data['a_attendee_lname'][$k], $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( '{{a_eventname}}', $arabic_attendee_data['event_name'][$k], $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( '{{date}}', $arabic_attendee_data['date'][$k], $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( '{{location}}', $arabic_attendee_data['location'][$k], $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( '{{time}}', $arabic_attendee_data['time_frame'][$k], $events_arabic_event_reminder );

						$events_arabic_event_reminder = str_replace( '{{a_event_details}}', $event_details_ar, $events_arabic_event_reminder );

						$events_arabic_event_reminder = str_replace( 'January', 'كانون الثاني', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'February', 'شهر فبراير', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'March'   , 'مارس', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'April'   , 'أبريل', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'May'     , 'مايو', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'June'    , 'يونيو', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'July'    , 'يوليو', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'August'  , 'أغسطس', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'September', 'سبتمبر', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace('October' , 'اكتوبر', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'November', 'شهر نوفمبر', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'December', 'ديسمبر', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'AM', 'صباحًا', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( 'PM', 'مساءً', $events_arabic_event_reminder );

						$params_ar = (object) array(
							'from' => array( 'email' => 'no-reply@dubaifuture.ae' ),
							'personalizations' => array( 
								array(
									'to' => array( array( 'email' => $v ) ),
									'dynamic_template_data' => array(
										'ARABIC' =>  true,
										'EMAIL_SUBJECT' => $arabic_event_reminder,
										'EMAIL_CONTENT' => wpautop( $events_arabic_event_reminder ),
										'DISPLAY_NAME' => $arabic_attendee_data['a_attendee_fname'][$k],
										'HELLO' => 'السيد',
										'THANKS' => 'نراك قريبا!',
									),
								)
							),
							'template_id' => $template_id,
						);
		
						$request      = $url . 'v3/mail/send';
						$response_ar = wp_remote_post(
							$request, array(
								'method'  => 'POST',
								'headers' => array( 'Authorization' => 'Bearer ' . $sendgrid_apikey, 'Content-Type' => 'application/json' ),
								'body'    => wp_json_encode( $params_ar ),
							)
						);

						$arabic_event_reminder = str_replace( $arabic_attendee_data['a_attendee_fname'][$k], '{{a_attendee_fname}}', $arabic_event_reminder );
						$arabic_event_reminder = str_replace( $arabic_attendee_data['a_attendee_lname'][$k], '{{a_attendee_lname}}', $arabic_event_reminder );

						$events_arabic_event_reminder = str_replace( $arabic_attendee_data['a_attendee_fname'][$k], '{{a_attendee_fname}}', $events_arabic_event_reminder );
						$events_arabic_event_reminder = str_replace( $arabic_attendee_data['a_attendee_lname'][$k], '{{a_attendee_lname}}', $events_arabic_event_reminder );
					}
				}
			}
		}
	}

	/**
	 * Remove quick edit option from the site.
	 *
	 * @param $actions
	 *
	 * @return mixed
	 */
	public function ssp_remove_member_bulk_actions( $actions ) {

		unset( $actions['inline hide-if-no-js'] );
		return $actions;

	}



	/**
	 * remove Visibility from WP admin
	 */
	public function event_wpseNoVisibility() {
		echo '<style>div#visibility.misc-pub-section.misc-pub-visibility{display:none}</style>';
	}

	/**
	 * Checkin ajax call for Attendee listing page click.
	 */
	public function dff_checkin_ajax() {

		$checked     = filter_input( INPUT_POST, 'checked', FILTER_SANITIZE_STRING );
		$attendee_id = filter_input( INPUT_POST, 'attendee_id', FILTER_SANITIZE_STRING );
		$attendee_id = str_replace( 'check_in_', '', $attendee_id );

		if ( 'true' === $checked ) {
			update_post_meta( $attendee_id, 'checkin', $checked );
		} else {
			update_post_meta( $attendee_id, 'checkin', $checked );
		}

		die();
	}

}

