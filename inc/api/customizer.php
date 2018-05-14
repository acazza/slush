<?php
/**
 * Slush Wordpress Customizer
 * All custom sections, settings, and controls for the customizer
 * @link https://codex.wordpress.org/Theme_Customization_API
 * @package com.soundlush.slush.v1
 */



/**
 * create sections, settings and controls for the customizer
 * @param  object  |  $wp_customize  |  instance of the WP_Customize_Manager class
 * @since 1.0.0
 */

function wpsl_customize_register( $wp_customize )
{

     //hero section
     $wp_customize->add_section( 'wpsl_custom_hero' , array(
        'title'      => __( 'Hero', 'slush' ),
        'priority'   => 30,
    ));

    //headline setting and control
    $wp_customize->add_setting( 'wpsl_custom_hero_headline' , array(
        'default'   => 'Create professional sounding music',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control( 'wpsl_custom_hero_headline_ctrl', array(
        'label'      => __( 'Headline Text', 'slush' ),
        'type'       => 'text',
        'section'    => 'wpsl_custom_hero',
        'settings'   => 'wpsl_custom_hero_headline',
    ));

    //lead setting and control
    $wp_customize->add_setting( 'wpsl_custom_hero_lead' , array(
        'default'   => 'Our mission is to teach you how',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control( 'wpsl_custom_hero_lead_ctrl', array(
        'label'      => __( 'Lead Text', 'slush' ),
        'type'       => 'textarea',
        'section'    => 'wpsl_custom_hero',
        'settings'   => 'wpsl_custom_hero_lead',
    ));
}
add_action( 'customize_register', 'wpsl_customize_register' );
