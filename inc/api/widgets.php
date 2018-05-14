<?php
/**
 * Slush Custom Widgets
 * All classes & custom functions for creation of theme widgets
 * @link https://codex.wordpress.org/Widgets_API
 * @package com.soundlush.slush.v1
 */



/**
 * Slush Popular Posts Widget
 * === === === === === === === === === === === ===
 * Retrieve the x more popular posts (based on view count)
 */



/**
 *  count post views and store info as post metadata
 *  @param int  |  $post_id  |  id of the current post
 *  @since 1.0.0
 */

function wpsl_save_post_views( $post_id )
{
    $meta_key = '_wpsl_post_views';
    $views = get_post_meta( $post_id, $meta_key, true );
    $count = empty( $views ) ? 0 : $views;
    $count++;

    update_post_meta( $post_id, $meta_key, $count );
}
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );




/**
 *  Slush Popular Posts Widget Class
 *  Extends WP_Widget and create Slush Popular Posts Widget
 *  @since 1.0.0
 */

class SlushPopularPostsWidget extends WP_Widget
{

    /**
     *  setup the widget name, description, etc.
     *  @since 1.0.0
     */

    public function __construct()
    {
        $widget_ops = array(
          'classname'   => 'wpsl-popular-posts',
          'description' => __( 'Custom Slush Popular Posts Widget', 'slush' ),
        );

        parent::__construct(
            'wpsl-popular-posts-widget',         //widget id
            __( 'Slush Popular Posts', slush ),  //widget title
            $widget_ops                          //options (class, description)
        );
    }



    /**
     *  outputs widget options form on the admin backend
     *  @param array  |  $instance  |  Previously saved values from database.
     *  @since 1.0.0
     */

    public function form( $instance )
    {
        //store widget title
        $title = !empty( $instance['title'] ) ? $instance['title'] : 'Popular Posts';

        //store number of posts to retrieve
        $total = !empty( $instance['total'] ) ? absint( $instance['total'] ) : 4;

        //html output for the backend
        $output  = '<p>';
        $output .= '<label for = "' . esc_attr( $this-> get_field_ID( 'title' ) ) . '">Title:</label>';
        $output .= '<input type="text" class="widefat" id="' . esc_attr( $this-> get_field_ID( 'title' ) ) . '" name = "' . esc_attr( $this-> get_field_name( 'title' ) ) . '" value="' . esc_attr( $title ) . '" >';
        $output .= '</p>';

        $output .= '<p>';
        $output .= '<label for = "' . esc_attr( $this-> get_field_ID( 'total' ) ) . '">Number of Posts:</label>';
        $output .= '<input type="number" class="widefat" id="' . esc_attr( $this-> get_field_ID( 'total' ) ) . '" name = "' . esc_attr( $this-> get_field_name( 'total' ) ) . '" value="' . esc_attr( $total ) . '" >';
        $output .= '</p>';

        echo $output;
    }



    /**
     *  sanitize widget form values as they are saved
     *  @param array  |  $new_instance  |  Values just sent to be saved.
	   *  @param array  |  $old_instance  |  Previously saved values from database.
	   *  @return array |  $instance      |  Updated safe values to be saved.
     *  @since 1.0.0
     */

    public function update( $new_instance, $old_instance )
    {
        $instance = array();
        $instance['title'] = !empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['total'] = !empty( $new_instance['total'] ) ? absint( strip_tags( $new_instance['total'] ) ) : 0;
        return $instance;
    }



    /**
     *  output the widget content on the frontend
     *  @param array  |  $args      |  Widget arguments.
	   *  @param array  |  $instance  |  Saved values from database.
     *  @since 1.0.0
     */

    public function widget( $args, $instance )
    {
        //define query params
        $total = absint( $instance['total'] );
        $posts_args = array(
          'post_type'       => 'post',
          'posts_per_page'  => $total,
          'meta_key'        => '_wpsl_post_views',
          'orderby'         => 'meta_value_num',
          'order'           => 'DESC'
        );

        //query posts
        $posts_query = new WP_Query( $posts_args );

        //html output for the front end
        echo $args['before_widget'];

        if( !empty( $instance['title'] ) )
        {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        if( $posts_query->have_posts() )
        {
            echo '<ul>';

            while( $posts_query->have_posts() )
            {
                $posts_query->the_post();
                 echo '<li><a href="' . get_the_permalink()  . '">' . get_the_title() . '</a></li>';
            }

            echo '</ul>';
        }

        echo $args['after_widget'];
    }
}

add_action('widgets_init', function(){
    register_widget('SlushPopularPostsWidget');
});





/**
 * Slush Social Media Widget
 * === === === === === === === === === === === ===
 * Display social media icons with profile links
 */



/**
 *  Slush Social Media Widget Class
 *  Extende WP_widget and create Slush Social Media Widget
 *  @since 1.0.0
 */

class SlushSocialWidget extends WP_Widget
{

    /**
     *  setup the widget name, description, etc.
     *  @since 1.0.0
     */

    public function __construct()
    {
        $widget_ops = array(
          'classname' => 'wpsl-social-media',
          'description' => __( 'Custom Slush Social Media Widget', slush ),
        );

        parent::__construct(
            'wpsl-social-media-widget',
            __( 'Slush Social Media', slush ),
            $widget_ops
        );
    }



    /**
     *  outputs widget options form on the admin backend
     *  @param array  |  $instance  |  Previously saved values from database.
     *  @since 1.0.0
     */

    public function form( $instance )
    {
        echo '<p>No options for this widget</p>';
    }



    /**
     *  outputs the widget content on the frontend
     *  @param array  |  $args      |  Widget arguments.
	   *  @param array  |  $instance  |  Saved values from database.
     *  @since 1.0.0
     */

    public function widget( $args, $instance )
    {
        echo $args['before_widget'];
        echo $args['after_widget'];
    }
}

add_action('widgets_init', function(){
    register_widget('SlushSocialWidget');
});
