<?php
/**
 * Slush Frontend Enqueue - javascript & css files
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 * @package com.soundlush.slush.v1
 */

function wpsl_load_scripts()
{
    wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/css/fa-svg-with-js.css', array(), '5.0.9', 'all' );
    wp_enqueue_style( 'slush', get_template_directory_uri() . '/css/slush.min.css', array(), '1.0.0', 'all' );

    wp_enqueue_script( 'fontawesome', get_template_directory_uri() . '/js/fontawesome-all.min.js', array('jquery'), '5.0.9', true );
    wp_enqueue_script( 'slush', get_template_directory_uri() . '/js/slush.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'retina', get_template_directory_uri() . '/js/retina.min.js', array(), '1.3.0', true );

    wp_localize_script( 'slush', 'ajax_slush', array(
        'ajax_url'   => admin_url( 'admin-ajax.php' ),
        'ajax_nonce' => wp_create_nonce( 'frontend_nonce' )
        //'post_id'    => $post->ID,
        //'user_id'    => get_current_user_id()
    ));
}
add_action( 'wp_enqueue_scripts', 'wpsl_load_scripts' );
