<?php
/**
 * Slush Custom Functions
 * Custom procedural functions
 * @package com.soundlush.slush.v1
 */



/**
 * Update Edit Form
 * allow files to be included in forms
 * @version 1.0
 */

function wpsl_update_edit_form()
{
    echo ' enctype="multipart/form-data"';
}
add_action( 'post_edit_form_tag', 'wpsl_update_edit_form' );





function soundlush_posted_meta()
{
    $posted_on  = get_the_date();
    $posted_in  = '';

    $categories = get_the_category();
    $separator  = ', ';

    $i = 1;

    if( !empty( $categories ) )
    {
        foreach( $categories as $category )
        {
            if( $i > 1 )
            {
                $posted_in .= $separator;
            }

            $posted_in .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" alt="' . esc_attr( 'View all posts in%s', $category->name ) . '">' . esc_html( $category->name ) . '</a>';

            $i++;
        }

    }

    return '<span class="posted-on">' . $posted_on . '</span> | <span class="posted-in">' . $posted_in . '</span>';
}




function soundlush_posted_footer()
{
    $tags     = get_the_tag_list('<div class="tags-list">', ' ', '</div>');
    $tag_icon = '<i class="fas fa-tag fa-lg"></i>';

    $comment_icon = '<i class="fas fa-comment-dots fa-lg"></i>';
    $comments_num = get_comments_number();

    if( comments_open() )
    {
        if( $comments_num == 0 )
        {
            $comments = __( 'No Comments' );
        }
        elseif( $comments_num > 1 )
        {
            $comments = $comments_num . ' ' . __( 'Comments' );
        }
        else
        {
            $comments = __( '1 Comment' );
        }

        $comments = '<a href="' . get_comments_link() . '">' . $comments . $comment_icon . '</a>';
    }
    else
    {
        $comments = __( 'Comments are closed' );
    };

    return '<div class="post-footer-container"><div class="tag-container">' . $tag_icon . $tags . '</div><div class="comment-container">' . $comments . '</div></div>';
}




//Standard & Image Post Format

function soundlush_get_attachment( $num = 1 )
{
    $output = '';

    if( has_post_thumbnail() && $num == 1)
    {
        $output = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
    }
    else
    {
        $attachments = get_posts( array(
            'post_type'      => 'attachment',
            'posts_per_page' => $num,
            'post_parent'    => get_the_ID()
        ));

        if( $attachments && $num == 1 )
        {
            foreach( $attachments as $attachment )
            {
                $output = wp_get_attachment_url( $attachment->ID );
            }
        }
        elseif( $attachments && $num > 1)
        {
            $output = $attachments;
        }

        wp_reset_postdata();
    }

    return $output;
}




//Audio & Video Post Format

function soundlush_get_embedded_media( $type = array() )
{
    $content = do_shortcode( apply_filters( 'the_content', get_the_content() ) );
    $embed   = get_media_embedded_in_content( $content, $type );

    if( in_array( 'audio', $type ) )
    {
        $output = str_replace( '?visual=true', '?visual=false', $embed[0] );
    }
    else
    {
        $output = $embed[0];
    }

    return $output;
}




//Link Post Format

function soundlush_grab_url()
{
    if( !preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/i', get_the_content(), $links ) )
    {
        return false;
    }
    else
    {
        return esc_url_raw( $links[1] );
    }
}



//Post Navigation Section

function soundlush_get_post_navigation()
{
    require( get_template_directory() . '/inc/templates/soundlush-post-nav.php' );
}



//Comment Navigation Section

function soundlush_get_comment_navigation()
{
    if( get_comment_pages_count() > 1 && get_option( 'page_comments' ) )
    {
        require( get_template_directory() . '/inc/templates/soundlush-comment-nav.php' );
    }
}



//Share Post Section

function soundlush_share_post( $content )
{
    if( is_singular( 'post' ) )
    {
        $title     = get_the_title();
        $permalink = get_permalink();

        $twitter   = 'https://twitter.com/intent/tweet?text=Hey! Read this: ' . $title . '&amp;url=' . $permalink . '&amp;via=@soundlush ';
        $facebook  = 'https://facebook.com/sharer/sharer.php?u=' . $permalink;

        $content .= '<div class="share-post"><h4>Share this</h4>';
        $content .= '<ul class="share-social-media">';
        $content .= '<li><a class="share-button" href="' . $twitter . '" target="_blank" rel="nofollow"><i class="fab fa-twitter fa-lg" data-fa-transform="up-1.4"></i></a></li>';
        $content .= '<li><a class="share-button" href="' . $facebook . '" target="_blank" rel="nofollow"><i class="fab fa-facebook-f fa-lg" data-fa-transform="up-1.6"></i></a></li>';
        $content .= '</ul></div> <!-- .share_post --> ';

        return $content;
    }
    else
    {
        return $content;
    }
}
add_filter( 'the_content', 'soundlush_share_post' );




//Related Posts Section

function soundlush_related_posts( $content )
{
    if( is_single() )
    {
        global $post;
        $original_post = $post;
        $tags = wp_get_post_tags($post->ID);

        if( $tags )
        {
            $tag_ids = array();
            foreach( $tags as $tag ) $tag_ids[] = $tag->term_id;

            $args=array(
                'tag__in'             => $tag_ids,
                'post__not_in'        => array($post->ID),
                'posts_per_page'      => 4, //Number of related posts to display.
                'ignore_sticky_posts' => 1
            );

            $query = new wp_query( $args );

            if( $query->have_posts() )
            {
                $content .= '<div class="related-posts">';
                $content .= '<h4>Related Posts</h4>';

                while( $query->have_posts() )
                {
                    $query->the_post();
                    $post_id = get_the_ID();

                    $content .= '<div class="related-thumbnail">';
                    $content .= '<a href="' . get_the_permalink() . '">';
                    $content .= '<img src="' . soundlush_get_attachment() . '" height="100" width="150" >';
                    $content .= get_the_title();
                    $content .= '</a></div>';
                }

                $content .= '</div>';
            }
        }

        $post = $original_post;
        wp_reset_query();
        return $content;
    }
    else
    {
        return $content;
    }
}
add_filter( 'the_content', 'soundlush_related_posts' );




//Latest Posts Section

function soundlush_get_latest_posts( $number_posts = 1 )
{

    $lastest_posts = wp_get_recent_posts( array(
        'numberposts' => $number_posts,
        'orderby'     => 'post_date',
        'order'       => 'DESC',
        'post_type'   => 'post'
    ));

    $output = '<ul>';

    foreach( $lastest_posts as $lastest )
    {
        $output .= '<li><a href="' . get_permalink($lastest["ID"]) . '">' . $lastest["post_title"] . '</a></li> ';
    }

    $output .= '</ul>';

    wp_reset_query();

    return $output;
}




//Featured Posts Section

function soundlush_get_featured_posts( $num_posts = 3 )
{
    //Get latest post
    $lastest= wp_get_recent_posts( array(
        'numberposts' => 1,
        'orderby'     => 'post_date',
        'order'       => 'DESC',
        'post_type'   => 'post'
    ));

    $sticky = get_option( 'sticky_posts' );

    $args = array(
        'posts_per_page'      => $num_posts,
  	    'post__in'            => $sticky,
        'post__not_in'        => $lastest, //Exclude latest post, if sticky, already in display
        'orderby'             => 'post_date',
        'order'               => 'DESC',
  	    'ignore_sticky_posts' => 1
    );

    $query = new WP_Query( $args );

    if( $query->have_posts() )
    {
        $output  = '<div class="featured-posts">';
        $output .= '<h4>Featured Posts</h4>';

        while( $query->have_posts() )
        {
            $query->the_post();
            $post_id = get_the_ID();

            $output .= '<div class="related-thumbnail">';
            $output .= '<a href="' . get_the_permalink() . '">';
            $output .= '<img src="' . soundlush_get_attachment() . '" height="100" width="150" >';
            $output .= get_the_title();
            $output .= '</a></div>';
        }

        $output .= '</div>';
    }

    wp_reset_query();
    return $output;
}




//Verify if user already bought a course (Course Page)
function soundlush_check_purchase( $product )
{
    $current_user = wp_get_current_user();

    //make sure WooCommerce is active and determine if customer has bought product
    if( is_woocommerce_activated() && wc_customer_bought_product( $current_user->email, $current_user->ID, $product ) )
    {
    	  echo __( 'Product already purchased.', 'slush' );
        return true;
    }
    else
    {
        return false;
    }
}




if( !function_exists( 'is_woocommerce_activated' ) )
{
    function is_woocommerce_activated()
    {
        if( class_exists( 'woocommerce' ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
};
