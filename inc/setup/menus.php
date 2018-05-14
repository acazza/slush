<?php
/**
 * Slush Register Navigation Menus
 *
 * @link https://codex.wordpress.org/Function_Reference/register_nav_menus
 * @package com.soundlush.slush.v1
 */

function wpsl_register_nav_menu()
{
    register_nav_menu( 'primary', 'Header Navigation Menu' );
    register_nav_menu( 'secondary', 'Footer Navigation Menu' );
}

add_action( 'after_setup_theme', 'wpsl_register_nav_menu' );
