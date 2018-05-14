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
      ?>
        <article class="soundlush-page">
          <header class="entry-header">
            <?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
          </header>
          <div class="entry-content">
            <?php the_content(); ?>
          </div><!-- .entry-content -->
        </article>
      <?php
        endwhile;
      endif;
      ?>
    </div> <!-- .container -->
  <main>
<div> <!-- #primary -->

<?php get_footer(); ?>
