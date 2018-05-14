<?php

/**
 * ==============================
 *  POSTTYPE: Course
 * ==============================
 */


$options = array(
    'hierarchical' => true,
    'show_in_menu' => false,
    'supports'     => array( 'title', 'editor', 'author','thumbnail', 'excerpt' )
);

$course = new SlushPostType('course', $options);

if( class_exists( 'WooCommerce' ) )
{
    $course->customfields()->add( array(
        'id'        => 'wpsl_course_product',
        'title'     => __( 'Associate with WooCommerce Product' ),
        'fields'    => array(
            array(
                'label'     => 'Product',
                'desc'      => 'WooCommerce Plugin MUST be installed and activated.',
                'id'        => 'wpsl_course_product_id',
                'type'      => 'relation',
                'posttype'  => 'product',
                'required'  => false
            ),
        )
    ));
}

$course->customfields()->add( array(
    'id'        => 'wpsl_course_syllabus',
    'title'     => __( 'Course Syllabus' ),
    'fields'    => array(
        array(
            'label'     => 'Description',
            'desc'      => 'Provide a short, pithy statement which informs a student about the subject matter, approach, breadth, and applicability of the course (about 80 words maximum).',
            'id'        => 'wpsl_course_description',
            'type'      => 'editor',
            'required'  => false
        ),
        array(
            'label'     => 'Objectives',
            'desc'      => 'Provide clear and concise statements that describe what students are expected to learn by the end of the course.',
            'id'        => 'wpsl_course_objectives',
            'type'      => 'editor',
            'required'  => false
        ),
        array(
            'label'     => 'Learning Outcomes',
            'desc'      => 'Provide statements that describe the knowledge or skills students should acquire by the end of the course.',
            'id'        => 'wpsl_course_learning_outcomes',
            'type'      => 'editor',
            'required'  => false
        ),
        array(
            'label'     => 'Activities',
            'desc'      => 'Provide a brief description of all exercises, assignments, projects, and more that allow students to apply their learning and practice their mastery of material from a unit or course.',
            'id'        => 'wpsl_course_activities',
            'type'      => 'editor',
            'required'  => false
        ),
    )
));


$course->register();




 /**
  * ==============================
  *  POSTTYPE: Lesson
  * ==============================
  */

$options = array(
    'hierarchical' => false,
    'show_in_menu' => false,
    'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' )
);

$lesson = new SlushPostType( 'lesson', $options );

$lesson->taxonomy( 'unit' );

$lesson->register();


//function add_parent( $parent, $child ){
//
//    add_action( 'add_meta_boxes', function() use( $parent, $child ){
//
//        $box_id    = 'wpsl-'. $child .'-parent';
//        $box_title = SlushHelpers::beautify( $parent );
//
//        add_meta_box(
//            $box_id,                      //meta-box id
//            $box_title,                   //meta-box title
//            'create_attributes_meta_box', //callback
//            $child,                       //screen (post-type)
//            'side',                       //context
//            'high',                       //priority
//            $parent                       //callback args
//        );
//    });
//}
//
//
//
//function create_attributes_meta_box( $post, $parent )
//{
//  	$post_type_object = get_post_type_object( $post->post_type );
//
//  	$pages = wp_dropdown_pages( array(
//        'post_type' => $parent,
//        'selected'  => $post->post_parent,
//        'name'      => 'parent_id',
//        'show_option_none' => __( '(no parent)' ),
//        'sort_column'=> 'menu_order, post_title',
//        'echo' => 0
//    ));
//
//  	if( !empty( $pages ) ) {
//  		  echo $pages;
//  	}
//}



/**
 * ==============================
 *  TAXONOMY: Unit
 * ==============================
 * @since 1.0.0
 */

$unit = new SlushTaxonomy( 'unit' );

$unit->register();



/**
 * ==============================
 *  CUSTOM FUNCTION: Mark Lesson as Viewed
 * ==============================
 */

function soundlush_mark_as_viewed()
{
    if( is_user_logged_in() && is_singular() )
    {
        $user     = wp_get_current_user();
        $post_id  = get_the_id();

        //TODO maybe use custom field istead of parent?
        //$metakey    = '_wpsl_lesson_viewed_' . $post->post_parent;
        $metakey  = '_wpsl_lesson_viewed_' . '247';

        $usermeta = get_user_meta( $user->ID, $metakey, false );

        if( isset( $usermeta[0] ) )
        {
          $meta = $usermeta[0];
          $meta[] = $post_id;
        }
        else
        {
          $meta[0] = $post_id;
        }

        $meta = array_unique($meta);

        update_user_meta($user->ID, $metakey, $meta);
    }
}



/**
 * ==============================
 *  SHORTCODE: Mark Lesson as Complete
 * ==============================
 */

//TODO NO SHORTCODE, USE FILTER HOOK AT THE BOTTOM OF LESSON POST, ALSO GIVE OPTION TO NOT SHOW?? ON EXERCISES AND QUIZZES, SUBMISSION == COMPLETE

add_shortcode( 'markcomplete', 'soundlush_create_markcomplete_btn' );

function soundlush_create_markcomplete_btn( $atts, $content = null )
{
    //Get the attributes
    $atts = shortcode_atts(
        array(),
        $atts,
        'markcomplete'
    );

    if( is_user_logged_in() && is_singular() ) //and user has product and user have not upload it yet
    {
        $html = '<button type="submit" id="soundlush_markcomplete_btn" class="btn btn-accent" data-id="'.get_the_id().'" data-user="'.get_current_user_id().'" >Mark as Complete</button>';

        return $html;
    }
    else
    {
        return;
    }
}



/**
 * ==============================
 *  AJAX CALLBACK: Mark Lesson as Complete
 * ==============================
 */

add_action( 'wp_ajax_nopriv_mark_as_completed', 'mark_as_completed' );
add_action( 'wp_ajax_mark_as_completed', 'mark_as_completed' );

function mark_as_completed()
{
    //check nonce before doing anything
    check_ajax_referer( 'frontend_nonce', 'nonce' );

    $user_id    = wp_strip_all_tags( $_POST['user'] );
    $post_id    = wp_strip_all_tags( $_POST['post'] );

    //TODO maybe use custom field istead of parent?
    //$metakey    = '_wpsl_lesson_completed_' . $post->post_parent;
    $metakey    = '_wpsl_lesson_completed_' . '247';

    $usermeta = get_user_meta( $user_id, $metakey, false );

    if( isset( $usermeta[0] ) )
    {
        $meta = $usermeta[0];
        $meta[] = $post_id;
    }
    else
    {
        $meta[0] = $post_id;
    }

    $meta = array_unique($meta);

    update_user_meta($user_id, $metakey, $meta);

    die;
}
