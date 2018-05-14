<?php
/**
 * Slush Custom Post Types Navigation Class
 * Custom post navigation by posttype and user determined order
 * @link https://codex.wordpress.org/Function_Reference/get_previous_post
 * @link https://codex.wordpress.org/Function_Reference/get_next_post
 * @package com.soundlush.slush.v1
 */


if( !class_exists( 'SlushCustomPostNav' ) )
{
    class SlushCustomPostNav
    {
        /**
         * The PostType name
         * @var string
         */
        public $post_type_name;



        /**
        * output previous/next post navigation
        * @param string  |  $post_type  |  required  |  Post type name.
        * @since 1.0.0
        */

        public function get_custom_post_nav( $post_type )
        {

            $this->post_type_name = SlushHelpers::uglify( $post_type );

            if( is_singular( $this->post_type_name ) )
            {
              	global $post, $wpdb;

                //add filter only for custom post types
                if( $this->post_type_name != 'post' )
                {
                    add_filter( 'get_next_post_sort', array( &$this, 'filter_next_cpt_sort' ) );
                  	add_filter( 'get_next_post_where', array( &$this, 'filter_next_cpt_where' ) );

                  	add_filter( 'get_previous_post_sort',  array( &$this, 'filter_previous_cpt_sort' ) );
                  	add_filter( 'get_previous_post_where',  array( &$this, 'filter_previous_cpt_where' ) );
                }

              	$previous_post = get_previous_post();
              	$next_post     = get_next_post();

                //html output
              	echo '<div class="adjacent-entry-pagination pagination">';

              	if( $previous_post )
                {
              		  echo '<div class="pagination-previous"><a href="' .get_permalink( $previous_post->ID ). '">&laquo; ' .$previous_post->post_title. '</a></div>';
              	}
                else
                {
              		  echo '<div class="pagination-previous"><a href="' .get_post_type_archive_link( $this->post_type_name ). '">Back to Archive</a></div>';
              	}

              	if( $next_post )
                {
                		echo '<div class="pagination-next"><a href="' .get_permalink( $next_post->ID ). '">' .$next_post->post_title. ' &raquo;</a></div>';
              	}
                else
                {
                		echo '<div class="pagination-next"><a href="' .get_post_type_archive_link( $this->post_type_name ). '">Back to Archive</a></div>';
              	}

              	echo '</div>';
            }
        }



        /**
        * re-order post type by menu_order next post navigation
        * @param string   |  $sort  |  required  |  SQL ORDER BY syntax for next post navigation.
        * @return string  |  $sort  |  Updated SQL ORDER BY syntax for next post navigation.
        * @since 1.0.0
        */

        public function filter_next_cpt_sort( $sort )
        {
          $sort = 'ORDER BY p.menu_order ASC LIMIT 1';
          return $sort;
        }



        /**
        * filters post per parent for next post navigation
        * @param string   |  $where  |  required  |  SQL WHERE BY syntax for next post navigation.
        * @return string  |  $where  |  Updated SQL WHERE syntax for next post navigation.
        * @since 1.0.0
        */

        public function filter_next_cpt_where( $where )
        {
            global $post, $wpdb;

            $where = $wpdb->prepare( "WHERE p.menu_order > '%s' AND p.post_type =  '%s' AND p.post_status = 'publish' AND p.post_parent = '%s'",$post->menu_order, $post->post_type, $post->post_parent);

            return $where;
        }



        /**
        * re-order post type by menu_order previous post navigation
        * @param string   |  $sort  |  required  |  SQL ORDER BY syntax for previous post navigation.
        * @return string  |  $sort  |  Updated SQL ORDER BY syntax for previous post navigation.
        * @since 1.0.0
        */

        public function filter_previous_cpt_sort( $sort )
        {
            $sort = 'ORDER BY p.menu_order DESC LIMIT 1';
            return $sort;
        }



        /**
        * filters post per parent for previous post navigation
        * @param string   |  $where  |  required  |  SQL WHERE BY syntax for previous post navigation.
        * @return string  |  $where  |  Updated SQL WHERE syntax for previous post navigation.
        * @since 1.0.0
        */

        public function filter_previous_cpt_where($where)
        {
            global $post, $wpdb;

            $where = $wpdb->prepare( "WHERE p.menu_order < '%s' AND p.post_type = '%s' AND p.post_status = 'publish'AND p.post_parent = '%s'",$post->menu_order, $post->post_type, $post->post_parent);

            return $where;
        }
    }
}
