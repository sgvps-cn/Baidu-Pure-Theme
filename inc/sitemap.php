<?php
/**
 * Native Sitemap Generator for Baidu Pure Theme
 * Supports XML and HTML formats with caching.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * 1. Add Rewrite Rules
 */
function baidu_pure_sitemap_init() {
	add_rewrite_rule( '^sitemap\.xml$', 'index.php?baidu_sitemap=xml', 'top' );
	add_rewrite_rule( '^sitemap\.html$', 'index.php?baidu_sitemap=html', 'top' );
}
add_action( 'init', 'baidu_pure_sitemap_init' );

/**
 * 2. Register Query Variable
 */
function baidu_pure_sitemap_query_vars( $vars ) {
	$vars[] = 'baidu_sitemap';
	return $vars;
}
add_filter( 'query_vars', 'baidu_pure_sitemap_query_vars' );

/**
 * 3. Template Redirect
 */
function baidu_pure_sitemap_template_redirect() {
	$type = get_query_var( 'baidu_sitemap' );
	if ( empty( $type ) ) {
		return;
	}

	if ( 'xml' === $type ) {
		baidu_pure_generate_sitemap_xml();
		exit;
	} elseif ( 'html' === $type ) {
		baidu_pure_generate_sitemap_html();
		exit;
	}
}
add_action( 'template_redirect', 'baidu_pure_sitemap_template_redirect' );

/**
 * 4. Generate XML Sitemap
 */
function baidu_pure_generate_sitemap_xml() {
	$cache_key = 'baidu_pure_sitemap_xml';
	$xml       = get_transient( $cache_key );

	if ( false === $xml ) {
		$posts = get_posts( array(
			'numberposts' => 1000,
			'orderby'     => 'modified',
			'post_status' => 'publish',
			'post_type'   => array( 'post', 'page' ),
		) );

		$xml  = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		// Home
		$xml .= '<url>';
		$xml .= '<loc>' . home_url( '/' ) . '</loc>';
		$xml .= '<lastmod>' . date( 'Y-m-d\TH:i:s+00:00' ) . '</lastmod>';
		$xml .= '<changefreq>daily</changefreq>';
		$xml .= '<priority>1.0</priority>';
		$xml .= '</url>';

		foreach ( $posts as $post ) {
			$xml .= '<url>';
			$xml .= '<loc>' . get_permalink( $post ) . '</loc>';
			$xml .= '<lastmod>' . get_the_modified_date( 'Y-m-d\TH:i:s+00:00', $post ) . '</lastmod>';
			$xml .= '<changefreq>weekly</changefreq>';
			$xml .= '<priority>0.8</priority>';
			$xml .= '</url>';
		}

		// Terms (Categories & Tags)
		$terms = get_terms( array(
			'taxonomy'   => array( 'category', 'post_tag' ),
			'hide_empty' => true,
		) );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$xml .= '<url>';
				$xml .= '<loc>' . get_term_link( $term ) . '</loc>';
				$xml .= '<changefreq>weekly</changefreq>';
				$xml .= '<priority>0.6</priority>';
				$xml .= '</url>';
			}
		}

		$xml .= '</urlset>';

		set_transient( $cache_key, $xml, 12 * HOUR_IN_SECONDS );
	}

	header( 'Content-Type: text/xml; charset=utf-8' );
	echo $xml;
}

/**
 * 5. Generate HTML Sitemap
 */
function baidu_pure_generate_sitemap_html() {
	$cache_key = 'baidu_pure_sitemap_html';
	$html      = get_transient( $cache_key );

	if ( false === $html ) {
		$posts = get_posts( array(
			'numberposts' => 1000,
			'orderby'     => 'date',
			'post_status' => 'publish',
			'post_type'   => array( 'post', 'page' ),
		) );

		$html  = '<!DOCTYPE html>';
		$html .= '<html><head>';
		$html .= '<title>Site Map - ' . get_bloginfo( 'name' ) . '</title>';
		$html .= '<meta charset="UTF-8">';
		$html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
		$html .= '<style>body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;line-height:1.6;max-width:800px;margin:20px auto;padding:0 20px;color:#333;} h1{border-bottom:1px solid #eee;padding-bottom:10px;} ul{list-style:none;padding:0;} li{margin-bottom:5px;} a{text-decoration:none;color:#0066cc;} a:hover{text-decoration:underline;}</style>';
		$html .= '</head><body>';
		$html .= '<h1>Site Map</h1>';
		
		$html .= '<h2>Pages</h2>';
		$html .= '<ul>';
		$html .= '<li><a href="' . home_url( '/' ) . '">Home</a></li>';
		$html .= '</ul>';

		$html .= '<h2>Latest Posts</h2>';
		$html .= '<ul>';
		foreach ( $posts as $post ) {
			$html .= '<li><a href="' . get_permalink( $post ) . '">' . get_the_title( $post ) . '</a> <small>(' . get_the_date( '', $post ) . ')</small></li>';
		}
		$html .= '</ul>';
		
		// Terms
		$categories = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => true ) );
		if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
			$html .= '<h2>Categories</h2><ul>';
			foreach ( $categories as $cat ) {
				$html .= '<li><a href="' . get_term_link( $cat ) . '">' . $cat->name . '</a></li>';
			}
			$html .= '</ul>';
		}

		$tags = get_terms( array( 'taxonomy' => 'post_tag', 'hide_empty' => true ) );
		if ( ! is_wp_error( $tags ) && ! empty( $tags ) ) {
			$html .= '<h2>Tags</h2><ul>';
			foreach ( $tags as $tag ) {
				$html .= '<li><a href="' . get_term_link( $tag ) . '">' . $tag->name . '</a></li>';
			}
			$html .= '</ul>';
		}

		$html .= '</body></html>';

		set_transient( $cache_key, $html, 12 * HOUR_IN_SECONDS );
	}

	header( 'Content-Type: text/html; charset=utf-8' );
	echo $html;
}
