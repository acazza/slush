<?php
/**
 * The template for displaying the content of Course Post Type
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.slush.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'slush-single-course' ); ?>>

    <header class="entry-header">
      <?php the_title( '<h2 class="entry-title">', '</h2>') ?>
    </header>

    <div class="entry-content">
      <?php if( soundlush_get_attachment() ): ?>
        <div class="standard-featured background-image" style="background-image: url( <?php echo soundlush_get_attachment(); ?> );"></div>

      <?php endif ?>
      <?php the_content(); ?>
    </div> <!-- .entry-content -->


    <?php


      //Check if( class_exists( 'WooCommerce' ) )
      //Check if user has purchased the product
      $product = get_post_meta( $post->ID, 'wsl_course_product_id', true );

      if( isset( $product ) )
      {
          if( soundlush_check_purchase( $product ) )
          {
              //TODO show personal options & data ...
              echo '<a class="btn btn-default" href=#>' . __( 'GO TO LESSONS', 'slush' ) . '</a>';
              echo '<a class="btn btn-default" href=#>' . __( 'RETAKE COURSE', 'slush' ) . '</a>';

          } else {

              $_product = wc_get_product(  $product );
              echo $_product->get_price();
              echo '<a class="btn btn-default" href="' . get_permalink( $product ) . '">' . __( 'BUY NOW', 'slush' ) . '</a>';

          }
      }

      //Instructor
      $instructor = get_the_author_meta('ID'); ;
      if( isset( $instructor ) )
      {
          echo __( 'Instructor: ', 'slush' );
          echo get_avatar( $instructor ); //, $size, $default, $alt, $args );
          echo '<p>' . get_the_author_meta( 'display_name' ) . '</p>';
          echo '<p>' . nl2br( get_the_author_meta( 'user_description' ) ) . '</p>';
      }


      echo '<div class="wsl-course-syllabus">';

      //Course Syllabus
      echo '<h3>' . __( 'Course Syllabus', 'slush' ) . '</h3>';


      //Description
      $description = get_post_meta( $post->ID, 'wsl_course_description', true );

      if( isset( $description ) )
      {
          echo '<h4>'. __( 'Description', 'slush' ) . '</h4>';
          $description = htmlspecialchars_decode($description);
          $description = wpautop( $description );
          echo $description;
      }


      //Objectives
      $objectives = get_post_meta( $post->ID, 'wsl_course_objectives', true );

      if( isset( $objectives ) )
      {
          echo '<h4>'. __( 'Objectives', 'slush' ) . '</h4>';
          $objectives = htmlspecialchars_decode($objectives);
          $objectives = wpautop( $objectives );
          echo $objectives;
      }


      //Learning Outcomes
      $outcomes = get_post_meta( $post->ID, 'wsl_course_learning_outcomes', true );

      if( isset( $outcomes ) )
      {
          echo '<h4>'. __( 'Learning Outcomes', 'slush' ) . '</h4>';
          $outcomes = htmlspecialchars_decode($outcomes);
          $outcomes = wpautop( $outcomes );
          echo $outcomes;
      }


      //Activities
      $activities = get_post_meta( $post->ID, 'wsl_course_activities', true );

      if( isset( $activities ) )
      {
          echo '<h4>'. __( 'Activities', 'slush' ) . '</h4>';
          $activities = htmlspecialchars_decode($activities);
          $activities = wpautop( $activities );
          echo $activities;
      }

      //Course Outline
      //soundlush_generate_course_index();
    ?>

    <footer class="entry-footer">
      <hr>
    </footer>

</article>
