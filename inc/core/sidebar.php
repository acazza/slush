<?php
/**
 * Soundlush Register Widget Areas
 * All sidebar areas to be available in the theme
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 * @package com.soundlush.slush.v1
 */



/**
* register theme sidebars
* @since 1.0.0
*/

function wpsl_register_sidebar()
{
    register_sidebar(
        array(
            'name'          => esc_html( 'Footer Col-1', 'slush' ),
            'id'            => 'wpsl-footer-col-1',
            'description'   => __( 'Footer Col 1', 'slush' ),
            'before_widget' => '<section id="%1$s" class="wpls-footer-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h4 class="wpsl-footer-widget-title">',
            'after_title'   => '</h4>',
        )
    );

    register_sidebar(
        array(
            'name'          => esc_html( 'Footer Col-2', 'slush' ),
            'id'            => 'wpsl-footer-col-2',
            'description'   => __( 'Footer Col 2', 'slush' ),
            'before_widget' => '<section id="%1$s" class="wpsl-footer-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h4 class="wpsl-footer-widget-title">',
            'after_title'   => '</h4>',
        )
    );

    register_sidebar(
        array(
            'name'          => esc_html( 'Footer Col-3', 'slush' ),
            'id'            => 'wpsl-footer-col-3',
            'description'   => __( 'Footer Col 3', 'slush' ),
            'before_widget' => '<section id="%1$s" class="wpsl-footer-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h4 class="wpsl-footer-widget-title">',
            'after_title'   => '</h4>',
        )
    );

    register_sidebar(
        array(
            'name'          => esc_html( 'Footer Bottom', 'slush' ),
            'id'            => 'wpsl-footer-bottom',
            'description'   => __( 'Footer Bottom', 'slush' ),
            'before_widget' => '<section id="%1$s" class="wpsl-footer-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h4 class="wpsl-footer-widget-title">',
            'after_title'   => '</h4>',
        )
    );
}
add_action( 'widgets_init', 'wpsl_register_sidebar' );
