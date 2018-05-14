<?php
/**
 * Slush Custom Walker Radio Taxonomy Class
 * Extend Walker and substitute taxonomy metabox with radio buttons
 * @link https://codex.wordpress.org/Class_Reference/Walker
 * @package com.soundlush.slush.v1
 */

if( !class_exists('SlushWalkerRadioTaxonomy' ) )
{
    class SlushWalkerRadioTaxonomy extends Walker
    {
        /**
         * What the class handles
         * @var string
         */

        public $tree_type = 'category';

        /**
         * Database fields to use.
         * @var array
         */

        public $db_fields = array( 'parent'=>'parent', 'id'=>'term_id' );



        /**
        * start item list (<ul>)
        * @link https://developer.wordpress.org/reference/classes/walker/start_lvl/
        * @param string  |  $output  |  append additional content
        * @param int     |  $depth   |  depth of the item
        * @param array   |  $args    |  array of arguments
        * @since 1.0.0
        */

        function start_lvl( &$output, $depth = 0, $args = array() )
        {
            $indent  = str_repeat( "\t", $depth );
            $output .= "$indent<ul class='children'>\n";
        }



        /**
        * end item list (</ul>)
        * @link https://developer.wordpress.org/reference/classes/Walker/end_lvl/
        * @param string  |  $output  |  append additional content
        * @param int     |  $depth   |  depth of the item
        * @param array   |  $args    |  array of arguments
        * @since 1.0.0
        */

        function end_lvl( &$output, $depth = 0, $args = array() )
        {
            $indent  = str_repeat( "\t", $depth );
            $output .= "$indent</ul>\n";
        }



        /**
        * start item list elements (<li>)
        * @link https://developer.wordpress.org/reference/classes/walker/start_el/
        * @param string  |  $output             |  append additional content
        * @param object  |  $category           |  data object
        * @param int     |  $depth              |  depth of the item
        * @param array   |  $args               |  array of arguments
        * @param int     |  $current_object_id  |  current object id
        * @since 1.0.0
        */

        function start_el( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 )
        {
            extract( $args );
            if( empty( $taxonomy ) )
            {
                $taxonomy = 'category';
            }

            if( $taxonomy == 'category' )
            {
                $name = 'post_category';
            }
            else
            {
                $name = 'tax_input[' . $taxonomy . ']';
            }

            $output .= "\n<ul>";
            $output .= "<li id='{$taxonomy}-{$category->term_id}'>";
            $output .= '<label class="selectit"><input value="' . $category->term_id . '" type="radio" name="'.$name.'" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . ' />'. esc_html( apply_filters('the_category', $category->name ));
            $output .= '</label></li></ul>';
        }



        /**
        * end item list elements (</li>)
        * @link https://developer.wordpress.org/reference/classes/walker/end_el/
        * @param string  |  $output    |  append additional content
        * @param object  |  $category  |  data object
        * @param int     |  $depth     |  depth of the item
        * @param array   |  $args      |  array of arguments
        * @since 1.0.0
        */

        function end_el( &$output, $category, $depth = 0, $args = array() )
        {
            $output .= "</li>\n";
        }
    }
}
