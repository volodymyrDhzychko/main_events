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

    $post_metas = get_post_meta( $post_id );

    $html  = '';
    $html .= $post_metas['events_overview'][0];
    $html .= '<h3>Event Agenda</h3>';
    $html .= $post_metas['dffmain_events_agenda'][0];

    $meta_values = [
        'event_location'         => $post_metas['dffmain_event_location'][0],
        'event_cost_name'        => $post_metas['event_cost_name'][0],
        'event_date_select'      => $post_metas['event_date_select'][0],
        'event_google_map_input' => $post_metas['event_google_map_input'][0],
        'event_slug'             => get_post_field( 'post_name', $post_id ),
        'eid'                    => get_current_blog_id() . $post_id
    ];
    if( isset( $post_metas['event_end_date_select'][0] ) && !empty( $post_metas['event_end_date_select'][0] ) ) {
        $meta_values['event_end_date_select'] = $post_metas['event_end_date_select'][0];
    } else {
        $meta_values['event_time_start_select'] = $post_metas['event_time_start_select'][0];
        $meta_values['event_time_end_select']   = $post_metas['event_time_end_select'][0];
    }
    
    $add_to_post = [
        'ID'           => $post_id,
        'post_content' => $html,
        'meta_input'=> $meta_values
    ];
    wp_update_post( $add_to_post );

    /**TODO cron??? */
    update_post_meta( $post_id, 'upcoming', 'yes' );
}