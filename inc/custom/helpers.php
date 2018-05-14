<?php
/**
 * Slush Custom Post Helpers
 * Custom helper function
 * @package com.soundlush.slush.v1
 */

if( !class_exists( 'SlushHelpers' ) )
{
    class SlushHelpers
    {
        /**
         * flush rewrite rules for custom post types on theme (de)activation.
         * @since 1.0.0
         */

        public static function activate()
        {
            add_action( 'after_switch_theme', 'flush_rewrite_rules' );
        }



        /**
         * set and unifies the use of textdomain
         * @since 1.0.0
         */

        public static function get_textdomain()
        {
            return 'slush';
        }



        /**
         * convert to first caps and replace undercores with spaces
         * @example $name = self::beautify( $string );
         * @since 1.0.0
         */

        public static function beautify( $string )
        {
            //return ucwords( str_replace( '_', ' ', $string ) );
            return ucwords( strtolower( str_replace( '-', ' ', str_replace( '_', ' ', $string ) ) ) );
        }



        /**
         * convert to small caps and replace spaces with undercores
         * @example $name = self::uglify( $string );
         * @since 1.0.0
         */

        public static function uglify( $string )
        {
            //return strtolower( str_replace( ' ', '_', $string ) );
            return strtolower( str_replace( ' ', '_', str_replace( '-', '_', $string ) ) );
        }



        /**
         * generate plural form
         * @example $plural = self::pluralize( $string )
         * @since 1.0.0
         */

        public static function pluralize( $string )
        {
            $last = $string[strlen( $string ) - 1];

            switch( $last )
            {
                case 'y': //convert y to ies
                    $cut = substr( $string, 0, -1 );
                    $plural = $cut . 'ies';
                    break;

                case 'z': //repeat last consonant and attach es
                    $plural = $string . 'zes';
                    break;

                default: //just attach an s
                    $plural = $string . 's';
                    break;
            }
            return $plural;
        }



        /**
         * checks if current post is from a specific Post Type
         * @since 1.0.0
         */

        public static function isPosttype( $type )
        {
            global $wp_query;

            if( $type == get_post_type( $wp_query->post->ID ) )
            {
                return true;
            }

            return false;
        }


    }
}
