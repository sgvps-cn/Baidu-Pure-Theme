<?php
/**
 * Baidu Daily Push Module (Cron Job)
 * 
 * Handles daily automatic pushing of random posts to Baidu to maintain active quota usage.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Schedule Cron Event
 */
function baidu_pure_schedule_daily_push() {
    if ( ! wp_next_scheduled( 'baidu_pure_daily_push_event' ) ) {
        wp_schedule_event( time(), 'daily', 'baidu_pure_daily_push_event' );
    }
}
add_action( 'init', 'baidu_pure_schedule_daily_push' );

/**
 * 2. Main Push Logic
 */
function baidu_pure_daily_push() {
    $api_token = get_option( 'baidu_api_token' );
    $site_url  = get_option( 'baidu_site_url' );

    if ( ! $api_token || ! $site_url ) {
        error_log( 'Baidu Daily Push Skipped: Missing API Token or Site URL.' );
        return;
    }

    // Query 20 random published posts
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 20,
        'orderby'        => 'rand',
    );
    $query = new WP_Query( $args );
    $urls = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $urls[] = get_permalink();
        }
        wp_reset_postdata();
    }

    if ( empty( $urls ) ) {
        error_log( 'Baidu Daily Push Skipped: No posts found.' );
        return;
    }

    // Send to Baidu
    $api_url = "http://data.zz.baidu.com/urls?site=$site_url&token=$api_token"; 
    
    $response = wp_remote_post( $api_url, array(
        'headers' => array( 'Content-Type' => 'text/plain' ),
        'body'    => implode( "\n", $urls )
    ) );

    // Log Results
    if ( is_wp_error( $response ) ) {
        error_log( 'Baidu Daily Push Network Error: ' . $response->get_error_message() );
    } else {
        $body = wp_remote_retrieve_body( $response );
        error_log( 'Baidu Daily Push Result: ' . $body );
    }
}
add_action( 'baidu_pure_daily_push_event', 'baidu_pure_daily_push' );

/**
 * 3. Manual Trigger Handler (Run Now)
 */
function baidu_pure_handle_manual_push() {
    if ( isset( $_GET['baidu_run_daily_push'] ) && check_admin_referer( 'baidu_manual_push' ) ) {
        baidu_pure_daily_push();
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>每日推送已触发！请检查 debug.log 查看推送结果。</p></div>';
        });
    }
}
add_action( 'admin_init', 'baidu_pure_handle_manual_push' );
