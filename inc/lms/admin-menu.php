<?php




/**
 * [wcr_shortcode description]
 * @param  array  pid     product id from short code
 * @return content        shortcode content if user bought product
 */

 //[wcr pid="72"]
 //This content only show for users that bought product with the id #72
 //[/wcr]

function wcr_shortcode( $atts = [], $content = null, $tag = '' )
{
    //normalize attribute keys, lowercase
    $atts = array_change_key_case( ( array ) $atts, CASE_LOWER );

    //start output
    $output = '';

    //start box
    $output .= '<div class="wcr-box">';

    $current_user = wp_get_current_user();

    if( current_user_can( 'administrator' ) || wc_customer_bought_product( $current_user->email, $current_user->ID, $atts['pid'] ) )
    {
        //enclosing tags
        if( !is_null( $content ) )
        {
            //secure output by executing the_content filter hook on $content
            $output .= apply_filters( 'the_content', $content );
        }

    }
    else
    {
        //User doesn't bought this product and not an administator
    }

    //end box
    $output .= '</div>';

    //return output
    return $output;
}


//add_action( 'parent_file', 'menu_highlight' );
//function menu_highlight( $parent_file ) {
//    global $current_screen;
//
//    $taxonomy = $current_screen->taxonomy;
//    if( $taxonomy == 'unit' || $taxonomy == 'quiz_pool' ) {
//        $parent_file = 'soundlush';
//    }
//
//    return $parent_file;
//}






function soundlush_theme_page()
{
  //Insert Instruction Guide Markup Here
  echo '
      <h1>Soundlush Theme Guideline</h1>

      <h2>Creating Courses </h2>
      <ol>
        <li> Create Courses and assign it to a WooCommerce Product</li>
        <li> Create Units and assign it to a Course</li>
        <li> Create Lessons and assigned it a Unit</li>
      </ol>

      <h2>Create Quizzes</h2>
      <ol>
        <li> Create Questions and assign it to a Question Pool</li>
        <li> Use the shortcode [quiz] on a Lesson</li>
      </ol>

      <h2>Theme Shortcodes</h2>
      <p> Example: </p>
      <code>
      [wcr pid="72"]
          This content only show for users that bought product with the id #72
      [/wcr]
      </code>
  ';

}


function soundlush_create_menupages()
{
    //https://developer.wordpress.org/reference/functions/add_menu_page/

    add_menu_page(
        'Slush LMS',                    //Page title
        'Slush LMS',                    //Menu title
        'manage_options',               //Capability
        'soundlush_lms',                //Slug
        'soundlush_theme_page',         //Function name
        'dashicons-welcome-learn-more', //Slug
        30                              //Order
    );

    //https://developer.wordpress.org/reference/functions/add_submenu_page/

    add_submenu_page(
        'soundlush_lms',                //Parent slug
        'Courses',                      //Page title
        'Courses',                      //Menu title
        'manage_options',               //Capability
        'edit.php?post_type=course',    //Slug
        false                           //Function
    );

    add_submenu_page(
        'soundlush_lms',
        'Units',
        'Units',
        'manage_options',
        'edit-tags.php?taxonomy=unit',
        false
    );

    add_submenu_page(
        'soundlush_lms',
        'Lessons',
        'Lessons',
        'manage_options',
        'edit.php?post_type=lesson',
        false
    );

    add_submenu_page(
        'soundlush_lms',
        'Submissions',
        'Submissions',
        'manage_options',
        'edit.php?post_type=submission',
        false
    );

    add_submenu_page(
        'soundlush_lms',
        'Quizzes',
        'Quizzes',
        'manage_options',
        'edit.php?post_type=quiz',
        false
    );

    add_submenu_page(
        'soundlush_lms',
        'Question Pools',
        'Question Pools',
        'manage_options',
        'edit-tags.php?taxonomy=quiz_pool',
        false
    );

    add_submenu_page(
        'soundlush_lms',
        'Questions',
        'Questions',
        'manage_options',
        'edit.php?post_type=question',
        false
    );
}
add_action( 'admin_menu', 'soundlush_create_menupages' );




function soundlush_menu_active()
{
    global $parent_file, $post_type, $taxonomy;

    if( $post_type == 'course' || $post_type == 'lesson' || $post_type == 'submission' || $post_type == 'quiz' || $post_type == 'question')
    {
        $parent_file = 'soundlush_lms';
    }

    if( $taxonomy == 'unit' || $taxonomy == 'quiz_pool' )
    {
        $parent_file = 'soundlush_lms';
    }
}
add_action( 'admin_head', 'soundlush_menu_active' );
