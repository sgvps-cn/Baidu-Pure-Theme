<?php
/**
 * Image SEO Optimization Module
 * 
 * Automatically fills missing Alt text in images with Post Title.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter 'the_content' to inject Alt text into inline images
 */
function baidu_pure_auto_image_alt( $content ) {
    if ( ! is_singular() ) {
        return $content; 
    }

    global $post;
    $title = get_the_title( $post->ID );
    $title = esc_attr( $title );

    // Regex to find img tags
    if ( preg_match_all( '/<img[^>]+>/i', $content, $matches ) ) {
        foreach ( $matches[0] as $img_tag ) {
            $new_img_tag = $img_tag;
            
            // Case 1: No alt attribute at all
            if ( ! preg_match( '/alt=["\']/', $img_tag ) ) {
                $injection = ' alt="' . $title . '"';
                $new_img_tag = preg_replace( '/(\/?>)$/', $injection . '$1', $img_tag );
            } 
            // Case 2: Empty alt attribute (alt="" or alt='')
            elseif ( preg_match( '/alt=["\']\s*["\']/', $img_tag ) ) {
                $new_img_tag = preg_replace( '/alt=["\']\s*["\']/', 'alt="' . $title . '"', $img_tag );
            }
            
            if ( $new_img_tag !== $img_tag ) {
                $content = str_replace( $img_tag, $new_img_tag, $content );
            }
        }
    }

    return $content;
}
add_filter( 'the_content', 'baidu_pure_auto_image_alt' );

/**
 * Filter attachment attributes (Featured Images, etc)
 */
function baidu_pure_attachment_image_alt( $attr, $attachment, $size ) {
    if ( empty( $attr['alt'] ) ) {
        global $post;
        if ( $post && ! empty( $post->post_title ) ) {
             $attr['alt'] = $post->post_title;
        } else {
             $attr['alt'] = get_the_title( $attachment->ID );
        }
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'baidu_pure_attachment_image_alt', 10, 3 );
