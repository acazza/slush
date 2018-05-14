<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package com.soundlush.slush.v1
 */
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id-"main" class="site-main" role="main">
    <div class="container">
      <?php
      if( have_posts() ):
        while( have_posts() ): the_post();

          wpsl_save_post_views( get_the_ID() );

          $post_type = get_post_type();
          //get_template_part( 'template-parts/single', get_post_format() );
          get_template_part( 'template-parts/single', $post_type );?>
          <section class="article-navigation">
            <?php
            $nav = new SlushCustomPostNav();
            $nav->get_custom_post_nav( $post_type );
            ?>

          </section>
          <?php if( comments_open() ): ?>
            <?php comments_template();?>
          <?php endif;
        endwhile;
      endif;
      ?>
    </div> <!-- .container -->



  <main>
<div> <!-- #primary -->

<?php get_footer(); ?>
