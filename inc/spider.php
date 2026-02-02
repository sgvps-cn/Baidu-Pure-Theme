<?php
/**
 * Baidu Spider Optimization Module
 * 
 * Detects Baidu Spider and optimizes page load by removing:
 * - Comments
 * - Heartbeat API
 * - WP Emojis
 * - Non-essential scripts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Detect Baidu Spider
 * 
 * @return bool
 */
function baidu_pure_is_spider() {
	$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	return ( stripos( $user_agent, 'Baiduspider' ) !== false );
}

/**
 * Apply Optimizations for Spider
 */
function baidu_pure_spider_optimize_init() {
	if ( baidu_pure_is_spider() ) {
		// Debug Header
		if ( ! headers_sent() ) {
			header( 'X-Baidu-Spider-Optimization: Active' );
		}

		// 1. Disable Comments
		add_filter( 'comments_open', '__return_false', 10, 2 );
		add_filter( 'pings_open', '__return_false', 10, 2 );

		// 2. Disable Heartbeat
		wp_deregister_script( 'heartbeat' );

		// 3. Disable Emojis
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	}
}
add_action( 'init', 'baidu_pure_spider_optimize_init' );

/**
 * Dequeue Scripts for Spider
 */
function baidu_pure_spider_dequeue_scripts() {
	if ( baidu_pure_is_spider() ) {
		wp_dequeue_script( 'comment-reply' );
		// We keep styles to prevent layout shifts (Cloaking risk)
	}
}
add_action( 'wp_enqueue_scripts', 'baidu_pure_spider_dequeue_scripts', 100 );
