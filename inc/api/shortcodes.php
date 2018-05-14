<?php
/**
 * Slush Shortcodes
 * All theme shortcodes to be used in posts
 * @link https://codex.wordpress.org/Shortcode_API
 * @package com.soundlush.slush.v1
 */



/**
 * button shortcode
 * @example [button slug="articles" type="primary" outlined=true]Click me[/button]
 * @since 1.0.0
 */

function wpsl_shortcode_button( $atts, $content = null )
{
    //get the attributes
    $atts = shortcode_atts(
        array(
            'slug'      => '#',
            'type'      => 'default',
            'outlined'  => false
        ),
        $atts,
        'button'
    );

    $outlined = ( $atts['outlined'] ) ? ' btn-outlined' : '';

    //return html
    $output = '<a href="' . get_home_url() . '/' . $atts['slug'] . '/" class="btn btn-' . $atts['type'] . $outlined . '">';
    $output .= $content;
    $output .= '</a>';
    return $output;
}
add_shortcode( 'button', 'wpsl_shortcode_button' );



/**
 * cards per row shortcode
 * @example [card cards_per_row=3 icon="link" title="Conectivity"]We are the best ones[/card]
 * @since 1.0.0
 */

function wpsl_shortcode_card( $atts, $content = null )
{
    //get the attributes
    $atts = shortcode_atts(
        array(
            'cards_per_row' => 3,   //Flex ???
            'icon'          => '', //use Fontawesome 5 class
            'title'         => '',
        ),
        $atts,
        'card'
    );
    $width = ( 100 / $atts['cards_per_row'] );

    //return html
    $output .= '<div class = "individual-card" width = "' . $width . '%">';
    $output .= '<div class = "card-icon"><i class="' . $atts['icon'] . '></i></div>';
    $output .= '<h4>' . $atts['title'] . '</h4>';
    $output .= '<p>' . $content . '</p>';
    $output .= '</div> <!-- .individual-card --> ';
    return $output;
}
add_shortcode( 'card', 'wpsl_shortcode_card' );



/**
 * back to the top shortcode
 * @example [backtotop]Back to Top[/backtotop]
 * @since 1.0.0
 */

function wpsl_shortcode_backtotop( $atts, $content = null )
{
    empty( $content ) ? 'Back to Top' : $content;

    //return html
    $output = '<a href="#" class="wpsl-back-to-top">' . $content . '</a>';
    return $output;
}
add_shortcode( 'backtotop', 'wpsl_shortcode_backtotop' );
