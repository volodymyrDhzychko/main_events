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
		include plugin_dir_path( __FILE__ ) . 'partials/backwards_compatibility.php';

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'init', array( $this, 'events_categories' ) );
		add_action( 'init', array( $this, 'events_tags' ) );
		add_action( 'init', array( $this, 'custom_post_types_create' ) );


		/**custom page template for CPT dffmain-events */
		add_filter( 'single_template', array( $this, 'redirect_dffmain_events_template' ), 99, 1 );

		/**create page on new site creation */
		add_action( 'wp_initialize_site', array( $this, 'create_events_thank_you_page' ), 99, 1 );
		
		/**custom page template for Event  Thank you page */
		add_filter( 'theme_page_templates', array( $this, 'register_events_thank_you_template' ) );
		add_filter( 'template_include', array( $this, 'include_events_thank_you_template' ) );

		/**custom post status - cancelled */
		add_action( 'init', array( $this, 'event_register_custom_post_status' ) );		
		add_action( 'admin_footer-post.php', array( $this, 'events_cancelled_status' ) );
		add_action( 'transition_post_status', array( $this, 'events_post_status' ), 10, 3 );

		// settings pages
		add_action('admin_menu',  array( $this, 'event_register_settings_page' ) );
		add_action( 'network_admin_menu', array( $this, 'add_events_settings' ) );

		// mets boxes
		add_action( 'add_meta_boxes', array( $this, 'event_editor_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_event_editor_meta_boxes' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'events_remove_boxes' ), 20 );

		// add event categories at event edit screen
		add_action( 'wp_ajax_category_add_submit', array( $this, 'category_add_submit' ) );
		add_action( 'wp_ajax_nopriv_category_add_submit', array( $this, 'category_add_submit' ) );

		// add event tags at event edit screen
		add_action( 'wp_ajax_tags_add_submit', array( $this, 'tags_add_submit' ) );
		add_action( 'wp_ajax_nopriv_tags_add_submit', array( $this, 'tags_add_submit' ) );

		add_action( 'wp_ajax_cancel_event_ajax', array( $this, 'cancel_event_ajax' ) );
		add_action( 'wp_ajax_trash_event_ajax', array( $this, 'trash_event_ajax' ) );

		// manage event sharing 
		add_action( 'wp_ajax_add_child_sites_action', array( $this, 'add_child_sites_action' ) );
		add_action( 'wp_ajax_nopriv_add_child_sites_action', array( $this, 'add_child_sites_action' ) );
		add_action( 'wp_ajax_delete_sites_action', array( $this, 'delete_sites_action' ) );
		add_action( 'wp_ajax_nopriv_delete_sites_action', array( $this, 'delete_sites_action' ) );

		// add placeholder
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
		add_action( 'admin_head', array( $this, 'event_wpseNoVisibility' ) );

		add_action('multilingualpress.metabox_after_relate_posts', array( $this, 'copy_on_multilingual_add_translation' ), 10, 2);
		add_action( 'wp_insert_post', array( $this, 'update_common_meta_fields' ) );

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
	 * register events thank you page template
	 *
	 * @param [array] $templates
	 * 
	 * @return [array] $templates
	 */
	function register_events_thank_you_template( $templates ) {

		$templates['template-event-registration-thank-you.php'] = 'Thank you Events';
	
		return $templates;
	}

	/**
	 * include events thank you page template
	 *
	 * @param [array] $template
	 * 
	 * @return [array] $template
	 */
	function include_events_thank_you_template( $template ) {

		$page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
	
		if ( 'template-event-registration-thank-you.php' == basename( $page_template ) ) {
	
			return wp_normalize_path( EVENTS_MAIN_PLUGIN_PATH . '/templates/template-event-registration-thank-you.php' );
		}
	
		return $template;
	}

	/**
	 * copy meta fields on post creation by multilingvalPress
	 */
	public function update_common_meta_fields( $post_id ) {

		if ( 'dffmain-events' == get_post_type( $post_id ) ) {

			if ( ! wp_is_post_revision( $post_id ) ){
				remove_action( 'wp_insert_post', array( $this, 'update_common_meta_fields' ) );
		
				$post_data  = get_post( $post_id );
				$post_metas = get_post_meta( $post_id );
			
				$html  = '';
				$html .= $post_metas['events_overview'][0];
				$html .= '<h3>Event Agenda</h3>';
				$html .= $post_metas['dffmain_events_agenda'][0];
			
				
				$add_to_post = [
					'ID'           => $post_id,
					'post_content' => $html,
				];
				wp_update_post( $add_to_post );

				$meta_values = [
					'event_location'         => $post_metas['dffmain_event_location'][0],
					'event_cost_name'        => $post_metas['event_cost_name'][0], 
					'event_date_select'      => $post_metas['event_date_select'][0],
					'event_google_map_input' => $post_metas['event_google_map_input'][0],
					'event_slug'             => $post_data->post_name, 
					'eid'                    => get_current_blog_id() . $post_id
				];
				if( isset( $post_metas['event_end_date_select'][0] ) && !empty( $post_metas['event_end_date_select'][0] ) ) {
					$meta_values['event_end_date_select'] = $post_metas['event_end_date_select'][0];
				} else {
					$meta_values['event_time_start_select'] = $post_metas['event_time_start_select'][0];
					$meta_values['event_time_end_select'] = $post_metas['event_time_end_select'][0];
				}
		
				wp_update_post([
					'ID'        => $post_id,
					'meta_input'=> $meta_values,
				]);	
			
				/**?TODO cron??? need node back-end dev */
				update_post_meta( $post_id, 'upcoming', 'yes' );
				
				add_action( 'wp_insert_post', array( $this, 'update_common_meta_fields' ) );
			}
		}
	}

	/**
	 * copy meta fields on post creation by multilingvalPress
	 */
	public function copy_on_multilingual_add_translation ( $context, $request ) {

		switch_to_blog($context->sourceSiteId());

			$all_source_post_meta = get_post_meta( $context->sourcePostId() );

			$event_cost_name                  = $all_source_post_meta['event_cost_name'][0];
			$event_reminder_select_box        = $all_source_post_meta['event_reminder_select_box'][0]; 
			$event_date_select                = $all_source_post_meta['event_date_select'][0]; 
			$event_end_date_select            = $all_source_post_meta['event_end_date_select'][0]; 
			$event_time_start_select          = $all_source_post_meta['event_time_start_select'][0]; 
			$event_time_end_select            = $all_source_post_meta['event_time_end_select'][0]; 
			$event_google_map_input           = $all_source_post_meta['event_google_map_input'][0]; 
			$event_detail_img                 = $all_source_post_meta['event_detail_img'][0]; 
			$security_code_checkbox           = $all_source_post_meta['security_code_checkbox'][0]; 
			$event_security_code              = $all_source_post_meta['event_security_code'][0]; 
			$event_reminder_date              = $all_source_post_meta['event_reminder_date'][0]; 
			$event_special_instruction        = $all_source_post_meta['event_special_instruction'][0]; 
			$google_embed_maps_code           = $all_source_post_meta['google_embed_maps_code'][0]; 
			$event_attendee_limit_count       = $all_source_post_meta['event_attendee_limit_count'][0]; 
			$event_registration_close_message = $all_source_post_meta['event_registration_close_message'][0];

		restore_current_blog();

		$meta_values = [
			'event_cost_name' => $event_cost_name,
			'event_reminder_select_box' => $event_reminder_select_box, 
			'event_date_select' => $event_date_select,
			'event_end_date_select' => $event_end_date_select, 
			'event_time_start_select' => $event_time_start_select,
			'event_time_end_select' => $event_time_end_select, 
			'event_google_map_input' => $event_google_map_input,
			'event_detail_img' => $event_detail_img, 
			'security_code_checkbox' => $security_code_checkbox,
			'event_security_code' => $event_security_code, 
			'event_reminder_date' => $event_reminder_date,
			'event_special_instruction' => $event_special_instruction, 
			'google_embed_maps_code' => $google_embed_maps_code,
			'event_attendee_limit_count' => $event_attendee_limit_count, 
			'event_registration_close_message' => $event_registration_close_message
		];

		wp_update_post([
            'ID'        => $context->remotePostId(),
            'meta_input'=> $meta_values,
		]);	
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
			$template = EVENTS_MAIN_PLUGIN_PATH . '/templates/template-event-registration-thank-you.php';
			// $template = home_url();
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

		$settings_array_get          = main_site_get_option( 'events_general_settings' );
		$events_general_settings_get = json_decode( $settings_array_get );
		$events_general_settings_get = (array) $events_general_settings_get;

		$sendgrid_apikey = $events_general_settings_get['send_grid_key'];
		$template_id     = $events_general_settings_get['send_grid_template_id'];
		// $send_grid_from_email = $events_general_settings_get['send_grid_from_email'];
		// $send_grid_from_name  = $events_general_settings_get['send_grid_from_name'];

		$url = 'https://api.sendgrid.com/';

		$subject_event_cancel_arr  = main_site_get_option( 'subject_event_cancel' );
		$dffmain_event_content_arr = main_site_get_option( 'events_content_event_cancel' );

		$post_metas = get_post_meta( $post_id );
		
		$dffmain_post_title      = $post_metas['dffmain_post_title'][0];
		$event_date              = $post_metas['event_date_select'][0];  
		$event_end_date          = $post_metas['event_end_date_select'][0];
		$event_time_start_select = $post_metas['event_time_start_select'][0];
		$event_time_end_select   = $post_metas['event_time_end_select'][0];
		$dffmain_event_location  = $post_metas['dffmain_event_location'][0];

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

		$dffmain_attendee_data = [];


		$args_attendees = array(
			'post_type'  => 'attendees',
			'meta_query' => array(
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

				$curr_attendee_lang = 'English';
				if ( 'ar' === $attendee_data['languageType'] ) {
					$curr_attendee_lang = 'Arabic';
				}
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
		wp_reset_postdata();

		/**
		 * mail sent for english attendee
		 */
		if ( isset( $dffmain_attendee_data['Email'] ) && ! empty( $dffmain_attendee_data['Email'] ) ) {

			foreach( $dffmain_attendee_data['Email'] as $k => $v ) {

				$subject_event_cancel = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'][$k], $subject_event_cancel_arr[$curr_attendee_lang] );
				$subject_event_cancel = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'][$k], $subject_event_cancel_arr[$curr_attendee_lang] );
				$subject_event_cancel = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'][$k], $subject_event_cancel_arr[$curr_attendee_lang] );
				$subject_event_cancel = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'][$k], $subject_event_cancel_arr[$curr_attendee_lang] );
				$subject_event_cancel = str_replace( '{{date}}', $dffmain_attendee_data['date'][$k], $subject_event_cancel_arr[$curr_attendee_lang] );
				$subject_event_cancel = str_replace( '{{location}}', $dffmain_attendee_data['location'][$k], $subject_event_cancel_arr[$curr_attendee_lang] );
				$subject_event_cancel = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'][$k], $subject_event_cancel_arr[$curr_attendee_lang] );

				$dffmain_event_content = str_replace( '{{date/time}}', $dffmain_attendee_data['date_output'][$k], $dffmain_event_content_arr[$curr_attendee_lang] );
				$dffmain_event_content = str_replace( '{{e_attendee_fname}}', $dffmain_attendee_data['e_attendee_fname'][$k], $dffmain_event_content_arr[$curr_attendee_lang] );
				$dffmain_event_content = str_replace( '{{e_attendee_lname}}', $dffmain_attendee_data['e_attendee_lname'][$k], $dffmain_event_content_arr[$curr_attendee_lang] );
				$dffmain_event_content = str_replace( '{{e_eventname}}', $dffmain_attendee_data['event_name'][$k], $dffmain_event_content_arr[$curr_attendee_lang] );
				$dffmain_event_content = str_replace( '{{date}}', $dffmain_attendee_data['date'][$k], $dffmain_event_content_arr[$curr_attendee_lang] );
				$dffmain_event_content = str_replace( '{{location}}', $dffmain_attendee_data['location'][$k], $dffmain_event_content_arr[$curr_attendee_lang] );
				$dffmain_event_content = str_replace( '{{time}}', $dffmain_attendee_data['time_frame'][$k], $dffmain_event_content_arr[$curr_attendee_lang] );
				
				$dear_text = ( 'English' == $curr_attendee_lang ) ? 'Dear' : 'السيد';

				$params_ar = (object) array(
					'from' => array( 'email' => 'no-reply@dubaifuture.ae' ),
					'personalizations' => array( 
						array(
							'to' => array( array( 'email' => $v ) ),
							'dynamic_template_data' => array(
								'EMAIL_SUBJECT' => $subject_event_cancel,
								'EMAIL_CONTENT' => $dffmain_event_content,
								'DISPLAY_NAME' => $dffmain_attendee_data['e_attendee_fname'][$k],
								'HELLO' => $dear_text,
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

		wp_die();
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
			wp_reset_postdata();
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
	 *  Remove Metabox of DFF category and tags
	 */
	public function events_remove_boxes() {

		remove_meta_box( 'events_categoriesdiv', 'dffmain-events', 'side' );
		remove_meta_box( 'tagsdiv-events_tags', 'dffmain-events', 'side' );
		remove_meta_box( 'authordiv', 'registration-forms', 'side' );
		remove_meta_box( 'slugdiv', 'registration-forms', 'side' );
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
	 * @param [int] $post_id
	 * @param [WP_Post] $post
	 * @param [bool] $update
	 * 
	 * @return void
	 */
	public function save_event_editor_meta_boxes( $post_id, $post, $update ) {

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

			// terms
			wp_set_post_terms( $post_id, $emp_category, 'events_categories', false );
			wp_set_post_terms( $post_id, $emp_tags, 'events_tags', false );

			/**
			 * setings to update globaly (over all translations)
			 */

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

			$meta_values = [
				'dffmain_post_title'               => $dffmain_post_title,
				'events_overview'                  => $dffmain_events_overview,
				'dffmain_events_agenda'            => $dffmain_events_agenda,
				'dffmain_event_location'           => $dffmain_event_location,
				'emp_category'                     => $emp_category,
				'emp_tags'                         => $emp_tags,
				'event_cost_name'                  => $event_cost_name,
				'event_reminder_select_box'        => $event_reminder_select_box,
				'event_date_select'                => $event_date_select,
				'event_end_date_select'            => $event_end_date_select,
				'event_time_start_select'          => $event_time_start_select,
				'event_time_end_select'            => $event_time_end_select,
				'event_google_map_input'           => $event_google_map_input,
				'event_detail_img'                 => sanitize_text_field( $meta_key ),
				'security_code_checkbox'           => $security_code_checkbox,
				'event_security_code'              => $event_security_code,
				'event_reminder_date'              => $event_reminder_date,
				'event_special_instruction'        => $event_special_instruction,
				'google_embed_maps_code'           => $google_embed_maps_code,
				'event_attendee_limit_count'       => $event_attendee_limit_count,
				'event_registration_close_message' => $event_registration_close_message,
			];
			$add_to_post = [
				'ID'        => $post_id,
				'meta_input'=> $meta_values
			];

			//If calling wp_update_post, unhook this function so it doesn't loop infinitely
			remove_action( 'save_post', array( $this, 'save_event_editor_meta_boxes' ), 10 );

			wp_update_post( $add_to_post );

			// re-hook this function
			add_action( 'save_post', array( $this, 'save_event_editor_meta_boxes' ), 10, 3 );
		} // if ( !ms_is_switched() )
	}

	/**
	 * Ajax function for add category
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

		wp_die();
	}

	/**
	 * Ajax function for delete site
	 */
	public function delete_sites_action() {
		$delete_site_button = filter_input( INPUT_POST, 'delete_site_button', FILTER_SANITIZE_STRING );
		$delete_site_button = isset( $delete_site_button ) ? $delete_site_button : '';

		$npm_added_sites = get_option( 'npm_added_child_sites' );

		if ( isset( $npm_added_sites ) && ! empty( $npm_added_sites ) ) {
			foreach ( $npm_added_sites as $k => $v ) {
				if ( $v['siteurl'] === $delete_site_button ) {
					unset( $npm_added_sites[ $k ] );
				}
			}
		}

		update_option( 'npm_added_child_sites', $npm_added_sites );
		wp_die();
	}

	/**
	 * Update Placeholder of DFF event post title
	 *
	 * @param $title
	 * @param $post
	 * 
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
			$columns['taxonomy-events_tags'],
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
		$template_id = dffmain_is_var_empty( filter_input( INPUT_GET, 'template_id', FILTER_SANITIZE_NUMBER_INT ) );
		$post_metas = get_post_meta( $post_id );

		if ( ! empty( $template_id ) ) {
			$attendee_list = $wpdb->get_results( $wpdb->prepare( "SELECT post_id from $wpdb->postmeta WHERE meta_key = '%s' AND meta_value = '%d'", 'event_id', $post_id ) );
			if ( ! empty( $attendee_list ) ) {
				$found_posts = count( $attendee_list );
			} 
			else {
				$found_posts = 0;
			}
		} 
		else {
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
			$event_attendee_limit_count = $post_metas['event_attendee_limit_count'][0];
			$remaining_attendee_count = (int)$event_attendee_limit_count - (int)$found_posts;
			wp_reset_postdata();
		}

		// Check if the event is cancelled.
		$event_cancelled = get_post_status( $post_id );
		//$event_cancelled = 'cancelled' === $event_cancelled ? 'Yes' : '-';

		$current_date = date( 'Y-m-d' );

		$event_date_select     = $post_metas['event_date_select'][0];
		$event_end_date_select = $post_metas['event_end_date_select'][0];

		if( isset( $event_end_date_select ) && !empty( $event_end_date_select ) ) {
			$event_date = $event_end_date_select;
		} else {
			$event_date = $event_date_select;
		}

		if( 'cancelled' === $event_cancelled ) {
			$event_cancelled = 'Cancelled';
		} else if( strtotime( $event_date ) >= strtotime( $current_date ) ) {
			$event_cancelled = 'Upcoming'; /** TODO upcoming -- do we need it? */
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
		wp_reset_postdata();
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

		$post_metas = get_post_meta( $post_id );

		$company_name     = $post_metas['company_name'][0];
		$event_name       = $post_metas['event_name'][0];
		$event_id         = $post_metas['event_id'][0];
		$email            = $post_metas['email'][0];
		$checkin          = $post_metas['checkin'][0];

		$cancelled_status = '';
		$event_url        = 'javascript:void(0);';
		if ( $event_id ) {
			$event_url   = get_edit_post_link( $event_id );
			$post_status = get_post_status( $event_id );

			if ( 'cancelled' === $post_status ) {
				$cancelled_status = ' - Cancelled Event';
			}
		}

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

		$event_id = dffmain_is_var_empty( filter_input( INPUT_GET, 'event_id', FILTER_SANITIZE_NUMBER_INT ) );

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
		$template_id = dffmain_is_var_empty( filter_input( INPUT_GET, 'template_id', FILTER_SANITIZE_NUMBER_INT ) );

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
			$event_id = dffmain_is_var_empty( filter_input( INPUT_GET, 'event_id', FILTER_SANITIZE_NUMBER_INT ) );

			if ( $event_list ) {
				?>
				<div class="alignleft actions">
					<label for="filter-by-event" class="screen-reader-text">Filter by Event</label>
					<select name="event_id" id="filter-by-date">
						<option value="">All Events</option>
						<?php
						if ( isset( $event_list ) && ! empty( $event_list ) ) {
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
								<option value="<?php echo esc_attr( $post_id ); ?>" <?php echo ( intval( $post_id ) === intval( $event_id ) ) ? 'selected="selected"' : ''; ?>">
									<?php echo esc_html( $post_title . $status ); ?>
								</option>
								<?php 
							}
						} 
						?>
					</select>
					<input 	type="submit" 
							name="filter_action" 
							id="post-query-submit" 
							class="button" 
							value="Filter">
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
					<input 	type="submit" 
							name="export_list" 
							id="export_list" 
							class="button button-primary"
						   	value="Export List"/>
					<?php 
				} 
				wp_reset_postdata(); ?>
				
				<div class="attendee-list-title">
					<p>
						Attendee List :<strong><?php echo get_the_title( $event_id ); ?></strong>
					</p>
					<a 	class="button button-primary add-attendee"
					   	href="<?php echo esc_url( get_the_permalink( $event_id ) ) . '?lang=en&checkin=true'; ?>" 
						target="_blank">
						Add Walk-in
					</a>
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
			$event_id = dffmain_is_var_empty( filter_input( INPUT_GET, 'event_id', FILTER_SANITIZE_NUMBER_INT ) );

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

						$post_metas = get_post_meta( $post_id );
						$email         = $post_metas['email'][0];
						$event_name    = $post_metas['event_name'][0];
						$checkin       = $post_metas['checkin'][0];
						$language_type = $post_metas['language_type'][0];
						$attendee_data = $post_metas['attendee_data'][0];

						$post_id       = get_the_ID();
						$name          = get_the_title();
						$check_in      = ( 'true' === $checkin ) ? 'Yes' : 'No';
						$language_type = ( 'ar' === $language_type ) ? 'Arabic' : 'English';
						$date          = get_the_date( 'Y/m/d H:i A', $post_id );
						$time          = ( ! empty( $date ) && isset( $date ) ) ? $date : '-';
						array_push( $row, $name, $email, $event_name, $check_in, $language_type, $time );


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
					wp_reset_postdata();
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
		if ( isset( $translations ) && ! empty( $translations ) ) {
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

		$postID = dffmain_is_var_empty( filter_input( INPUT_POST, 'postID', FILTER_SANITIZE_NUMBER_INT ) );

		$dffmain_post_title = dffmain_is_var_empty( filter_input( INPUT_POST, 'dffmain_post_title', FILTER_SANITIZE_STRING ) );

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

		wp_set_post_terms( $postID, $emp_category, 'events_categories', false );
		wp_set_post_terms( $postID, $emp_tags, 'events_tags', false );


		$meta_values = [
			'dffmain_post_title' => $dffmain_post_title,
			'events_overview' => $events_overview,
			'dffmain_events_agenda' => $dffmain_events_agenda,
			'dffmain_event_location' => $dffmain_event_location,
			'emp_category' => $emp_category,
			'emp_tags' => $emp_tags,
		];
		$add_to_post = [
			'ID'           => $postID,
			'meta_input'=> $meta_values
		];
		wp_update_post( $add_to_post );

		// backwards compatibility aroun system
		backwards_compatibility_events( $postID );

		wp_die();

	}

	/**
	 * Ajax call for email sent
	 */
	public function event_send_special_single_email() {
		
		$post_id = dffmain_is_var_empty( filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT ) );
		$site_id = dffmain_is_var_empty( filter_input( INPUT_POST, 'site_id', FILTER_SANITIZE_NUMBER_INT ) );

		$dffmain_event_special_mail_data = isset( $_POST['dffmain_event_special_mail_data'] ) ? $_POST['dffmain_event_special_mail_data'] : [];

		$dffmain_event_content = [];
		$dffmain_event_subject = [];
		$response_data = [];

		if ( isset( $dffmain_event_special_mail_data ) && ! empty( $dffmain_event_special_mail_data ) ) {

			foreach ($dffmain_event_special_mail_data as $value) {

				$dffmain_event_subject[$value['language']] = $value['subject'];
				$dffmain_event_content[$value['language']] = $value['content'];

				$response_data[$value['language']]['dffmain_email_subject'] = $value['subject'];
				$response_data[$value['language']]['dffmain_email_content'] = $value['content'];
				$response_data[$value['language']]['email_date'] = date( 'd-M-Y | h:i:s' );
			}
		}

		$settings_array_get          = main_site_get_option( 'events_general_settings' );
		$events_general_settings_get = json_decode( $settings_array_get );
		$events_general_settings_get = (array) $events_general_settings_get;

		$sendgrid_apikey = $events_general_settings_get['send_grid_key'];
		$template_id     = $events_general_settings_get['send_grid_template_id'];

		$url = 'https://api.sendgrid.com/';

		$tmp_arr = [];
		$translations_data = dffmain_mlp_get_translations();
		if ( isset( $translations_data ) && ! empty( $translations_data ) ) {
			foreach ( $translations_data as $translation ) {

				$language       = $translation->language();
				$language_name  = $language->isoName();
				$key_site_id = $translation->remoteSiteId();

				$tmp_arr[$key_site_id]['language_name'] = $language_name;
			}
		}
		$translation_ids = multilingualpress_get_ids( $post_id, $site_id );
		if ( isset( $translation_ids ) && ! empty( $translation_ids ) ) {
			foreach ( $translation_ids as $loop_site_id => $loop_post_id ) {
				if ( get_main_site_id() == $site_id ) {
					$curr_title    = get_post_meta( $loop_post_id, 'dffmain_post_title', true );
					$curr_location = get_post_meta( $loop_post_id, 'dffmain_event_location', true );
					
					$tmp_arr[$loop_site_id]['curr_title']    = $curr_title;
					$tmp_arr[$loop_site_id]['curr_location'] = $curr_location;
				}else {
					$curr_title = multisite_post_meta( $loop_site_id, $loop_post_id, 'dffmain_post_title' );
					$curr_location = multisite_post_meta( $loop_site_id, $loop_post_id, 'dffmain_event_location' );

					$tmp_arr[$loop_site_id]['curr_title'] = $curr_title;
					$tmp_arr[$loop_site_id]['curr_location'] = $curr_location;
				}
			}
		}else {
			$curr_title    = get_post_meta( $post_id, 'dffmain_post_title', true );
			$curr_location = get_post_meta( $post_id, 'dffmain_event_location', true );
			
			$tmp_arr[$site_id]['curr_title']    = $curr_title;
			$tmp_arr[$site_id]['curr_location'] = $curr_location;
		}


		$dffmain_post_title     = [];
		$dffmain_event_location = [];
		foreach ( $tmp_arr as $value ) {

			if ( isset( $value['curr_title'] ) ) {
				$dffmain_post_title[$value['language_name']] = dffmain_is_var_empty( $value['curr_title'] );
			}
			if ( isset( $value['curr_location'] ) ) {
				$dffmain_event_location[$value['language_name']] = dffmain_is_var_empty( $value['curr_location'] );
			}	
		}

		$post_metas = get_post_meta( $post_id );

		$event_date              = $post_metas['event_date_select'][0];
		$event_end_date          = $post_metas['event_end_date_select'][0];
		$event_time_start_select = $post_metas['event_time_start_select'][0];
		$event_time_end_select   = $post_metas['event_time_end_select'][0];

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
				// check if no translations -- ml plugin returns empty
				if ( isset( $translations ) && ! empty( $translations ) ) {

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
				}else{

					$dffmain_attendee_data['e_attendee_fname'] = $attendee_data['FirstName'];
					$dffmain_attendee_data['e_attendee_lname'] = $attendee_data['LastName'];
					$dffmain_attendee_data['Email']            = $attendee_data['Email'];

					$current_language = get_current_language_name();
					$dffmain_attendee_data['event_name'] = $dffmain_post_title[$current_language];
					$dffmain_attendee_data['location']   = $dffmain_event_location[$current_language];
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

				$json_string = (object) array(
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
						'body'    => wp_json_encode( $json_string ),
					)
				);
					
				$dffmain_event_subject[$attendee_language] = str_replace( $dffmain_attendee_data['e_attendee_fname'], '{{e_attendee_fname}}', $dffmain_event_subject[$attendee_language] );
				$dffmain_event_subject[$attendee_language] = str_replace( $dffmain_attendee_data['e_attendee_lname'], '{{e_attendee_lname}}', $dffmain_event_subject[$attendee_language] );

				$dffmain_event_content[$attendee_language] = str_replace( $dffmain_attendee_data['e_attendee_fname'], '{{e_attendee_fname}}', $dffmain_event_content[$attendee_language] );
				$dffmain_event_content[$attendee_language] = str_replace( $dffmain_attendee_data['e_attendee_lname'], '{{e_attendee_lname}}', $dffmain_event_content[$attendee_language] );

				if ( 200 === $response['response']['code'] ) {
					$response_data[$attendee_language]['response'] = 'Sent';
				} else {
					$response_data[$attendee_language]['response'] = 'Fail';
				}
			}
			// foreach ( $query_attendees->posts as $query_attendees_data )


			foreach ( $response_data as $email_language => $email_letter ) {

				// if no attendees for language - do not save in post meta
				if ( empty( $email_letter['response'] ) ) {
					continue;
				}
				add_post_meta( $post_id, 'event_email_history', $email_letter );
			}
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
// var_dump($event_email_history);
				if ( isset( $event_email_history ) && ! empty( $event_email_history ) ) {
					$count = 1;
					foreach ( $event_email_history as $event_email_history_data ) {
						?>
						<tr>
							<td><?php echo esc_html( $count ); ?></td>
							<td><?php echo esc_html( $event_email_history_data['email_date'] ); ?></td>
							<td><?php echo ! empty( $event_email_history_data['dffmain_email_subject'] ) ? esc_html( $event_email_history_data['dffmain_email_subject'] ) : 'arqqq'; ?></td>
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
		}
		// if ( isset( $query_attendees->posts ) && ! empty( $query_attendees->posts ) )

		wp_reset_postdata();
		wp_die();
	}



	/**
	 * Cron function for send email reminder email.
	 */
	public static function cron_event_reminder() {

		$current_date                = date( 'Y-m-d' );
		$settings_array_get          = main_site_get_option( 'events_general_settings' );
		$events_general_settings_get = json_decode( $settings_array_get );
		$events_general_settings_get = (array) $events_general_settings_get;
		
		$sendgrid_apikey = $events_general_settings_get['send_grid_key'];
		$template_id     = $events_general_settings_get['send_grid_template_id'];
		// $send_grid_from_email        = $events_general_settings_get['send_grid_from_email'];
		// $send_grid_from_name         = $events_general_settings_get['send_grid_from_name'];

		$events_content_event_reminder = main_site_get_option( 'events_content_event_reminder' )['English'];
		$events_arabic_event_reminder  = main_site_get_option( 'events_content_event_reminder' )['Arabic'];
		$subject_event_reminder        = main_site_get_option( 'subject_event_reminder' )['English'];
		$arabic_event_reminder         = main_site_get_option( 'subject_event_reminder' )['Arabic'];

		$url = 'https://api.sendgrid.com/';

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

				$post_metas = get_post_meta( $dff_events_record_ids );
				
				$dffmain_post_title      = $post_metas['dffmain_post_title'][0];
				$event_date              = $post_metas['event_date_select'][0];
				$event_end_date          = $post_metas['event_end_date_select'][0];
				$event_time_start_select = $post_metas['event_time_start_select'][0];
				$event_time_end_select   = $post_metas['event_time_end_select'][0];
				$dffmain_event_location  = $post_metas['dffmain_event_location'][0]; 
				$events_overview         = $post_metas['events_overview'][0];
				$dffmain_events_agenda   = $post_metas['dffmain_events_agenda'][0];

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
							$arabic_attendee_data['event_name'][]       = $dffmain_post_title;
							$arabic_attendee_data['date'][]             = $event_date_ar;
							$arabic_attendee_data['time_frame'][]       = $event_time_start_select . ' إلى ' . $event_time_end_select;
							$arabic_attendee_data['location'][]         = $dffmain_event_location;
							$arabic_attendee_data['a_event_detail'][]   = $events_overview . '<br><br>' . $dffmain_events_agenda;

							$event_details_ar = $events_overview . '<br><br>' . $dffmain_events_agenda;

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

						$events_arabic_event_reminder = str_replace( $english_time_names, $arabic_time_names, $events_arabic_event_reminder );

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
		wp_reset_postdata();
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

		wp_die();
	}

}