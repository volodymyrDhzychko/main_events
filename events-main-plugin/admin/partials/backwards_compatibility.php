<?php
/**
 * Helper functions for backwards compatibility
 *
 * @link       https://usoftware.co/
 * @since      1.0.0
 *
 * @package    Events_Main_Plugin
 * @subpackage Events_Main_Plugin/admin/partials
 */

function backwards_compatibility_events( $post_id ) {

    $post_data  = get_post( $post_id );
    $post_metas = get_post_meta( $post_id );

    $html  = '';
    $html .= get_post_meta( $post_id, 'events_overview' );
    $html .= '<h3>Event Agenda</h3>';
    $html .= get_post_meta( $post_id, 'dffmain_events_agenda' );

    
    $add_to_post = [
        'ID'           => $post_id,
        'post_content' => $html,
    ];
    wp_update_post( $add_to_post );


    update_post_meta( $post_id, 'event_location', get_post_meta( $post_id, 'dffmain_event_location') );
    update_post_meta( $post_id, 'event_cost_name', $post_metas['event_cost_name'][0] );
    update_post_meta( $post_id, 'event_date_select', $post_metas['event_date_select'][0] );
    update_post_meta( $post_id, 'event_google_map_input', $post_metas['event_google_map_input'][0] );
    update_post_meta( $post_id, 'event_slug', get_post_field( 'post_name', $post_id ) );
    update_post_meta( $post_id, 'eid', $post_id );

    /**TODO cron??? */
    update_post_meta( $post_id, 'upcoming', 'yes' );

    if( isset( $post_metas['event_end_date_select'][0] ) && !empty( $post_metas['event_end_date_select'][0] ) ) {
        update_post_meta( $post_id, 'event_end_date_select', $post_metas['event_end_date_select'][0] );
    } else {
        update_post_meta( $post_id, 'event_time_start_select', $post_metas['event_time_start_select'][0] );
        update_post_meta( $post_id, 'event_time_end_select', $post_metas['event_time_end_select'][0] );
    }
}