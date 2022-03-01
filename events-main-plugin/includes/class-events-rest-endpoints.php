<?php

/**
 * Class for all Rest Endpoints
 *
 * @link       https://usoftware.co/
 * @since      1.0.0
 *
 * @package    Events_Rest_Endpoints
 * @subpackage Events_Rest_Endpoints/includes
 */

/** TODO rebuild for multisite 
 * CLASS IS TURNED OFF 
 * includes/class-events-main-plugin.php 84
*/
class Events_Rest_Endpoints {

	/**
	 * Class Constructor
	 */
	public function __construct() {

		/*
		 * Initialize the Rest End Point
		 */
		add_action( 'rest_api_init', array( $this, 'dff_rest_points' ) );
	}

	/**
	 * WP Rest End Points for Events.
	 *
	 * @since 1.0.0
	 */
	public function dff_rest_points() {

		/**
		 * Verify the Token & Domain
		 * wp-json/events/token
		 */
		register_rest_route(
			'events', '/token', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'dff_token_verify_api' ),
			)
		);

		/**
		 * Get Events.
		 * wp-json/events/
		 */
		register_rest_route(
			'events', 'pull', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'dff_get_events' ),
			)
		);

		/**
		 * Get Single Event by ID.
		 * wp-json/events/single
		 */
		register_rest_route(
			'events', 'single', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'dff_get_single_event' ),
			)
		);

		/**
		 * Get Event Categories.
		 * wp-json/events/cats
		 */
		register_rest_route(
			'events', '/cats', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'dff_get_cats' ),
			)
		);

		/**
		 * Get Event Categories.
		 * wp-json/events/tags
		 */
		register_rest_route(
			'events', '/tags', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'dff_get_tags' ),
			)
		);

		/**
		 * Flush Events Data.
		 * wp-json/events/flush
		 */
		register_rest_route(
			'events', '/flush', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'dff_flush_events' ),
			)
		);

	}

	/**
	 * Call back for Token Verify API.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool Verified or not.
	 */
	public function dff_token_verify_api( WP_REST_Request $request ) {

		$parameters = $request->get_params();

		$token  = isset( $parameters['key'] ) ? $parameters['key'] : '';
		$domain = isset( $parameters['domain'] ) ? $parameters['domain'] : '';

		$verified = $this->dff_token_verify( $token, $domain );

		return $verified;
	}

	/**
	 * Call back for Flush Events Data.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool Verified or not.
	 */
	public function dff_flush_events( WP_REST_Request $request ) {

		$parameters = $request->get_params();

		$key       = isset( $parameters['key'] ) ? $parameters['key'] : '';
		$post_type = isset( $parameters['post_type'] ) ? $parameters['post_type'] : '';

		if ( empty( $key ) ) {
			return "Key is missing!, please pass key value in a 'key' parameter!";
		}
		if ( empty( $post_type ) ) {
			return "Post type is missing! Please pass 'post_type' value in a parameter!";
		}

		if ( 'lkvn2@$%Fghlksmbf' === $key ) {
			$result = $this->dff_wipe_out_cpt( $post_type );
		}

		return "$result items removed successfully!";
	}

	/**
	 * @param string $post_type Post Type.
	 *
	 * @return bool|int Result of query.
	 */
	public function dff_wipe_out_cpt( $post_type ) {

		global $wpdb;

		$wipe_query = $wpdb->query(
			$wpdb->prepare( "DELETE a,b,c
			    FROM wp_posts a
			    LEFT JOIN wp_term_relationships b
			        ON (a.ID = b.object_id)
			    LEFT JOIN wp_postmeta c
			        ON (a.ID = c.post_id)
			    WHERE a.post_type = %s", $post_type ) );

		return $wipe_query;
	}

	/**
	 * Verify the Token & Domain.
	 *
	 * @return bool Verified or not.
	 */
	public function dff_token_verify( $token, $domain ) {

		// Guilty until proven.
		$verified = false;

		$token_added_sites = get_option( 'npm_added_child_sites' );

		if ( isset( $token_added_sites ) && ! empty( $token_added_sites ) ) {
			foreach ( $token_added_sites as $token_added_sites ) {
				if ( $token_added_sites['token'] === $token
					&& strpos( $domain, $token_added_sites['siteurl'] ) !== false ) {
					$verified = true;
				}
			}
		}

		return $verified;
	}

	/**
	 * Call back tp pull Events.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array Events Data.
	 */
	public function dff_get_events( WP_REST_Request $request ) {

		$data_missing = false;

		$parameters = $request->get_params();

		$lang         = isset( $parameters['lang'] ) ? $parameters['lang'] : 'en';
		$type         = isset( $parameters['type'] ) ? $parameters['type'] : '';
		$token        = isset( $parameters['key'] ) ? $parameters['key'] : '';
		$domain       = isset( $parameters['domain'] ) ? $parameters['domain'] : '';
		$child_eids   = isset( $parameters['eids'] ) ? explode( ',', $parameters['eids'] ) : '';
		$child_cats   = isset( $parameters['cats'] ) ? explode( ',', $parameters['cats'] ) : 'all';
		$child_tags   = isset( $parameters['tags'] ) ? explode( ',', $parameters['tags'] ) : 'all';
		$no_of_events = isset( $parameters['total'] ) && (int) $parameters['total'] > -1 ? (int) $parameters['total'] : '';
		$no_of_events = 'upcoming' === $type ? 0 : $no_of_events;

		// If this is not the first attempt.
		// There must be from and to dates in the request.
		if ( empty( $no_of_events ) && 0 !== $no_of_events ) {
			if ( isset( $parameters['fromDate'] ) ) {
				$separated_after_timestamp = $this->dff_get_separated_timestamp( $parameters['fromDate'] );
			} else {
				$data_missing = true;
			}

			if ( isset( $parameters['toDate'] ) ) {
				$separated_before_timestamp = $this->dff_get_separated_timestamp( $parameters['toDate'] );
			} else {
				$separated_before_timestamp = $this->dff_get_separated_timestamp( current_time( 'Y-m-d H:i:s' ) );
			}
		}

		$verified = $this->dff_token_verify( $token, $domain );

		// Guilty until proven.
		$events_data           = array();
		$events_data['status'] = 401;

		if ( true ) { /**$verified && false === $data_missing */

			$dffmain_meta = array();

			$dffmain_meta['dffmain_post_title']      = 'title';
			$dffmain_meta['events_overview']         = 'overview';
			$dffmain_meta['dffmain_events_agenda']   = 'agenda';
			$dffmain_meta['dffmain_event_location']  = 'location';

			/** TODO get data relevant to language from subdomain site */
			// if ( 'en' === $lang ) {
			$meta_names = $dffmain_meta;
			$title_name = 'dffmain_post_title';
			$cat_tax    = 'events_categories';
			$tag_tax    = 'events_tags';


			$args = array(
				'post_type'   => 'dffmain-events',
				'post_status' => array( 'publish', 'trash', 'cancelled' ),
			);

			// No of posts
			if ( ! empty( $no_of_events ) || 0 === $no_of_events ) {
				$no_of_posts            = 0 === $no_of_events ? -1 : $no_of_events;
				$args['posts_per_page'] = $no_of_posts;
				$args['orderby']        = 'modified';
				$args['order']          = 'DESC';
			}

			// If request has "no_of_events", the to and from dates
			// along with the "$child_eids" parameters will be dsicarded.
			// If request has NOT "$child_eids", get events with "tags" & "cats"
			// and send back response without any filter.
			// If request has "$child_eids", get events without "tags" & "cats"
			// and then filter by $child_eids, cats & tags.
			if ( empty( $child_eids ) ) {

				// tax query
				$args['tax_query'] = array( 'relation' => 'OR' );

				if ( 'all' !== $child_cats ) {
					$args['tax_query'][] = array(
						'taxonomy' => $cat_tax,
						'field'    => 'term_id',
						'terms'    => $child_cats,
					);
				}

				if ( 'all' !== $child_tags ) {
					$args['tax_query'][] = array(
						'taxonomy' => $tag_tax,
						'field'    => 'term_id',
						'terms'    => $child_tags,
					);
				}
			}

            // date query
			if ( empty( $no_of_events ) && 0 !== $no_of_events ) {
				$args['date_query'] = array();

				$args['date_query'][] = array(
					'column' => 'post_modified_gmt',
					array(
						'after'     => array(
							'year'  => $separated_after_timestamp['year'],
							'month' => $separated_after_timestamp['month'],
							'day'   => $separated_after_timestamp['day'],
						),
						'before'    => array(
							'year'  => $separated_before_timestamp['year'],
							'month' => $separated_before_timestamp['month'],
							'day'   => $separated_before_timestamp['day'],
						),
						'inclusive' => true,
					),
					array(
						'hour'    => $separated_before_timestamp['hour'],
						'minute'  => $separated_before_timestamp['min'],
						'second'  => $separated_before_timestamp['sec'],
						'compare' => '<=',
					),
					array(
						'hour'    => $separated_after_timestamp['hour'],
						'minute'  => $separated_after_timestamp['min'],
						'second'  => $separated_after_timestamp['sec'],
						'compare' => '>=',
					),

				);
			}

            if( 'upcoming' === $type ) {
			    $args['post_status'] = array( 'publish' );
            }
			/**TODO  unset($args['date_query']); */
			unset($args['date_query']);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
// return  $the_query;
				// Status Success.
				if ( isset( $the_query->posts ) ) {
					$events_data['status'] = 200;
				}

				$e_counts = 0;
				while ( $the_query->have_posts() ) {

					$the_query->the_post();
					$eid         = get_the_ID();
					$post_status = get_post_status();

					// Fetch Categories.
					$cats        = get_the_terms( $eid, $cat_tax );
					$parent_cats = array();
					if ( ! empty( $cats ) && false !== $cats ) {
						foreach ( $cats as $cat ) {
							$events_data['data'][ $e_counts ]['cats'][ $cat->term_id ]['name']   = $cat->name;
							$events_data['data'][ $e_counts ]['cats'][ $cat->term_id ]['parent'] = $cat->parent;
							$parent_cats[] = $cat->term_id;
						}
					}

					// Fetch Tags.
					$tags        = get_the_terms( $eid, $tag_tax );
					$parent_tags = array();
					if ( ! empty( $tags ) && false !== $tags ) {
						foreach ( $tags as $tag ) {
							$events_data['data'][ $e_counts ]['tags'][ $tag->term_id ]['name']   = $tag->name;
							$events_data['data'][ $e_counts ]['tags'][ $tag->term_id ]['parent'] = $tag->parent;
							$parent_tags[] = $tag->term_id;
						}
					}

                    // Update the 'upcoming' meta for future events.
                    if( 'upcoming' === $type ) {
                        $current_date = current_time('d F Y H:i:s');
                        $current_date = strtotime($current_date);

						$e_date = get_post_meta( $eid, 'event_date_select', true );
						$e_end_date = get_post_meta( $eid, 'event_end_date_select', true );

						if( isset( $e_end_date ) && !empty( $e_end_date ) ) {
							$e_date = $e_end_date;
						}
						
                        $e_date .= ' ' . get_post_meta( $eid, 'event_time_start_select', true );
                        $e_date = !empty($e_date) ? date('d F Y H:i:s', strtotime($e_date)) : '';
                        $e_date = !empty($e_date) ? strtotime($e_date) : '';

                        if ( $e_date < $current_date ) {
                            unset( $events_data['data'][$e_counts] );
                            continue;
                        }
                    }

					// Status of the post. (Added/Modified/Deleted)
					if ( 'publish' === $post_status ) {
						$status = 'modified';
						if ( isset( $parameters['fromDate'] ) ) {
							$created_on = get_the_date( 'Y-m-d H:i:s' );
							if ( $parameters['fromDate'] <= $created_on ) {
								$status = 'added';
							}
						}
					} else {
						$status = 'deleted';
					}

					// Filter events if $child_eids is not blank.
					if ( ! empty( $child_eids ) ) {

						// By default, the event is not eligible to be sent back.
						$eligible = 0;

						/**
						 * Events do not exist is child site and trashed from parent,
						 * (OR) Parent Events with 0 cats or tags are also not eligible.
						 */
						if (
							( ! in_array( $eid, $child_eids, false ) && 'deleted' === $status )
							|| ( 0 === count( $parent_cats ) && 0 === count( $parent_tags ) && 'deleted' !== $status )
						) {
							continue;

							/*
							 * If event exist in child site, and its status ar parent site
							 * is "deleted", event is eligible to proceed.
							 */
						} elseif ( in_array( $eid, $child_eids, false ) && 'deleted' === $status ) {
							$status = 'deleted';

						} else {

							/*
							 * Match cats & tags of Parent Event with the requested $cats & $tags,
							 * If even a single tag/cat matches with requests, prepare data to "update"
							 */

							if ( 'all' !== $child_cats && 0 !== count( $child_cats ) && 0 !== count( $parent_cats ) ) {
								$intersected_cats = array_intersect( $parent_cats, $child_cats );
								if ( 0 !== count( $intersected_cats ) ) {
									$eligible = 1;
								}
							} elseif ( 'all' === $child_cats ) {
								// If all cats are requested, then all new events should be sent back.
								$eligible = 1;
							}

							// Try to find matched tags.
							if ( 'all' !== $child_tags && 0 !== count( $child_tags ) && 0 !== count( $parent_tags ) ) {
								$intersected_tags = array_intersect( $parent_tags, $child_tags );
								if ( 0 !== count( $intersected_tags ) ) {
									$eligible = 1;
								}
							} elseif ( 'all' === $child_tags ) {
								// If all tags are requested, then all new events should be sent back.
								$eligible = 1;
							}

							/*
							 * If there is nothing matching, it means the cats/tags are removed,
							 * then prepare data to "delete"
							 */
							if ( in_array( $eid, $child_eids, false ) && 0 === $eligible ) {
								$status = 'deleted';
							}
						}
					}

					$metas = get_post_meta( $eid );

					// Skip if title is empty.
					$title = get_post_meta( $eid, $title_name, true );

					if ( 'deleted' === $status ) {
						// Only two values to pass if 'deleted'.
						$events_data['data'][ $e_counts ]['eid']    = $eid;
						$events_data['data'][ $e_counts ]['status'] = $status;
						$e_counts++;
					} elseif ( ! empty( $title ) ) {

						// Language Specific Fields.
						foreach ( $metas as $k => $v ) {

							// Skip hidden meta.
							if ( '_' === $k[0] ) {
								continue;
							}
							$k_name = isset( $meta_names[ $k ] ) ? $meta_names[ $k ] : '';
							if ( ! empty( $k_name ) ) {
								$events_data['data'][ $e_counts ][ $k_name ] = $v[0];
							}
						}

						$events_data['data'][ $e_counts ]['slug'] = get_post_field( 'post_name', $eid );

						// Common Fields.
						$events_data['data'][ $e_counts ]['eid']            = $eid;
						$events_data['data'][ $e_counts ]['status']         = $status;
						$events_data['data'][ $e_counts ]['internal_name']  = get_the_title();
						$events_data['data'][ $e_counts ]['cost']           = $metas['event_cost_name'][0];
						$events_data['data'][ $e_counts ]['date']           = $metas['event_date_select'][0];
						if( isset( $metas['event_end_date_select'][0] ) && !empty( $metas['event_end_date_select'][0] ) ) {
							$events_data['data'][ $e_counts ]['event_end_date_select'] = $metas['event_end_date_select'][0];
						} else {
							$events_data['data'][ $e_counts ]['starttime']      = $metas['event_time_start_select'][0];
							$events_data['data'][ $e_counts ]['endtime']        = $metas['event_time_end_select'][0];
						}

						$event_attendee_limit_count = get_post_meta( $eid, 'event_attendee_limit_count', true );
						if ( isset( $event_attendee_limit_count ) && ! empty( $event_attendee_limit_count ) ) {
							$args                     = array(
								'post_type'  => 'attendees',
								'meta_query' => array(
									array(
										'key'   => 'event_id',
										'value' => $eid,
									),
								),
							);
							$query                    = new WP_Query( $args );
							$found_posts              = $query->found_posts ? $query->found_posts : 0;
							$remaining_attendee_count = (int) $event_attendee_limit_count - (int) $found_posts;

							if ( 0 === $remaining_attendee_count ) {
								$events_data['data'][ $e_counts ]['remaining_seats']        = (int) $found_posts;
							} else {
								$events_data['data'][ $e_counts ]['remaining_seats']        = $remaining_attendee_count;
							}

						}

						$events_data['data'][ $e_counts ]['gmap']           = $metas['event_google_map_input'][0];
						$events_data['data'][ $e_counts ]['featured_photo'] = get_the_post_thumbnail_url( $eid, 'full' );
						$image_attributes                                   = wp_get_attachment_image_src( $metas['event_detail_img'][0], 'full' );
						$events_data['data'][ $e_counts ]['detail_photo']   = $image_attributes[0];

						// Increment.
						$e_counts++;
					}
				}
				$events_data['total_events'] = $e_counts;
			} else {
				$events_data['status'] = 'No Events updated';
			}

			// Restore original Post Data
			wp_reset_postdata();
		}

		return $events_data;
	}

	/**
	 * Date & Time Separator.
	 *
	 * @param string $timestamp The timestamp 'MM-DD-YYYY HH:MM:SS'.
	 *
	 * @return array Separated date and time values.
	 */
	public function dff_get_separated_timestamp( $timestamp ) {

		$separated = array();

		$date_ex = explode( ' ', $timestamp );

		$date_ex_inner      = $date_ex[0];
		$date_ex_inner      = explode( '-', $date_ex_inner );
		$separated['year']  = $date_ex_inner[0];
		$separated['month'] = $date_ex_inner[1];
		$separated['day']   = $date_ex_inner[2];

		$time_ex_inner     = isset( $date_ex[1] ) ? $date_ex[1] : '00:00:00';
		$time_ex_inner     = explode( ':', $time_ex_inner );
		$separated['hour'] = $time_ex_inner[0];
		$separated['min']  = $time_ex_inner[1];
		$separated['sec']  = $time_ex_inner[2];

		return $separated;

	}

	/**
	 * Call back tp pull Single Event.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array Single Events Data.
	 */
	public function dff_get_single_event( WP_REST_Request $request ) {

		$parameters = $request->get_params();

		$eid    = isset( $parameters['id'] ) ? $parameters['id'] : '';
		$lang   = isset( $parameters['lang'] ) ? $parameters['lang'] : 'en';
		$token  = isset( $parameters['key'] ) ? $parameters['key'] : '';
		$domain = isset( $parameters['domain'] ) ? $parameters['domain'] : '';

		$data_missing = empty( $eid ) ? true : false;

		$verified = $this->dff_token_verify( $token, $domain );

		// Guilty until proven.
		$events_data           = array();
		$events_data['status'] = 401;

		if ( $verified && false === $data_missing ) {

			// Parameter Names Map.
			$dffmain_meta = array();

			$dffmain_meta['dffmain_post_title']      = 'title';
			$dffmain_meta['events_overview']         = 'overview';
			$dffmain_meta['dffmain_events_agenda']   = 'agenda';
			$dffmain_meta['dffmain_event_location']  = 'location';

			/** TODO get data relevant to language from subdomain site */
			// if ( 'en' === $lang ) {
			$meta_names = $dffmain_meta;
			$title_name = 'dffmain_post_title';
			$cat_tax    = 'events_categories';
			$tag_tax    = 'events_tags';

			$args = array(
				'post_type' => 'dffmain-events',
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {

				// Status Success.
				if ( isset( $the_query->posts ) ) {
					$events_data['status'] = 200;
				}

				while ( $the_query->have_posts() ) {

					$the_query->the_post();
					$metas = get_post_meta( $eid );

					// Skip if title is empty.
					$title = get_post_meta( $eid, $title_name, true );

					if ( ! empty( $title ) ) {

						// Language Specific Fields.
						foreach ( $metas as $k => $v ) {

							// Skip hidden meta.
							if ( '_' === $k[0] ) {
								continue;
							}
							$k_name = $meta_names[ $k ];
							if ( ! empty( $k_name ) ) {
								$events_data[ $k_name ] = $v[0];
							}
						}

						// Common Fields.
						$events_data['id']             = $eid;
						$events_data['internal_name']  = get_the_title();
						$events_data['cost']           = $metas['event_cost_name'][0];
						$events_data['date']           = $metas['event_date_select'][0];

						if( isset( $metas['event_end_date_select'][0] ) && !empty( $metas['event_end_date_select'][0] ) ) {
							$events_data['event_end_date_select'] = $metas['event_end_date_select'][0];
						} else {
							$events_data['starttime']      = $metas['event_time_start_select'][0];
							$events_data['endtime']        = $metas['event_time_end_select'][0];
						}

						$event_attendee_limit_count = get_post_meta( $eid, 'event_attendee_limit_count', true );
						if ( isset( $event_attendee_limit_count ) && ! empty( $event_attendee_limit_count ) ) {
							$args                     = array(
								'post_type'  => 'attendees',
								'meta_query' => array(
									array(
										'key'   => 'event_id',
										'value' => $eid,
									),
								),
							);
							$query                    = new WP_Query( $args );
							$found_posts              = $query->found_posts ? $query->found_posts : 0;
							$remaining_attendee_count = (int) $event_attendee_limit_count - (int) $found_posts;

							if ( 0 === $remaining_attendee_count ) {
								$events_data['remaining_seats']        = (int) $found_posts;
							} else {
								$events_data['remaining_seats']        = $remaining_attendee_count;
							}

						}

						$events_data['gmap']           = $metas['event_google_map_input'][0];
						$events_data['featured_photo'] = get_the_post_thumbnail_url( $eid, 'full' );
						$image_attributes              = wp_get_attachment_image_src( $metas['event_detail_img'][0], 'full' );
						$events_data['detail_photo']   = $image_attributes[0];

						// Fetch Categories.
						$cats = get_the_terms( $eid, $cat_tax );
						if ( array( $cats ) && !empty( $cats ) ) {
							foreach ( $cats as $cat ) {
								$events_data['cats'][ $cat->term_id ] = $cat->name;
							}
						}
						// Fetch Tags.
						$tags = get_the_terms( $eid, $tag_tax );
						if ( array( $tags ) && !empty( $tags ) ) {
							foreach ( $tags as $tag ) {
								$events_data['tags'][ $tag->term_id ] = $tag->name;
							}
						}
					} else {
						$events_data['status'] = '404 - Title Not found';
					}
				}
			}

			// Restore original Post Data
			wp_reset_postdata();
		}

		return $events_data;
	}

	/**
	 * Call back to Get Event Categories.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array Categories IDs and Names.
	 */
	public function dff_get_cats( WP_REST_Request $request ) {

		// Guilty until proven.
		$cats_data           = array();
		$cats_data['status'] = 401;

		$parameters = $request->get_params();

		$lang   = isset( $parameters['lang'] ) ? $parameters['lang'] : 'en';
		$domain = isset( $parameters['domain'] ) ? $parameters['domain'] : '';
		$key    = isset( $parameters['key'] ) ? $parameters['key'] : '';

		$verified = $this->dff_token_verify( $key, $domain );
		if ( $verified ) {

			$taxonomy = 'events_categories';

			$categories = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);

			$cats_data['status']     = 200;
			$cats_data['total_cats'] = count( $categories );

			foreach ( $categories as $cat ) {
				$cats_data['data'][ $cat->term_id ]['name']        = $cat->name;
				$cats_data['data'][ $cat->term_id ]['description'] = $cat->description;
				$cats_data['data'][ $cat->term_id ]['parent']      = $cat->parent;
			}
		}

		return $cats_data;

	}

	/**
	 * Call back to Get Event Tags.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array Tags IDs and Names.
	 */
	public function dff_get_tags( WP_REST_Request $request ) {

		// Guilty until proven.
		$tags_data           = array();
		$tags_data['status'] = 401;

		$parameters = $request->get_params();

		$lang   = isset( $parameters['lang'] ) ? $parameters['lang'] : 'en';
		$domain = isset( $parameters['domain'] ) ? $parameters['domain'] : '';
		$key    = isset( $parameters['key'] ) ? $parameters['key'] : '';

		$verified = $this->dff_token_verify( $key, $domain );

		if ( $verified ) {

			$taxonomy = 'events_tags';

			$tag = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);

			$tags_data['status']     = 200;
			$tags_data['total_tags'] = count( $tag );
			foreach ( $tag as $tag ) {
				$tags_data['data'][ $tag->term_id ]['name']        = $tag->name;
				$tags_data['data'][ $tag->term_id ]['description'] = $tag->description;
				$tags_data['data'][ $tag->term_id ]['parent']      = $tag->parent;
			}
		}

		return $tags_data;

	}

}

new Events_Rest_Endpoints();
