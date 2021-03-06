<?php
/**
 * The template for displaying the content of Single Post
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.slush.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'slush-single' ); ?>>

  <header class="entry-header">
    <?php the_title( '<h2 class="entry-title">', '</h2>') ?>

    <div class="entry-meta">
      <?php echo wpsl_get_post_meta(); ?>
    </div>
  </header>

  <div class="entry-content">
    <?php if( wpsl_get_attachment() ): ?>
      <div class="standard-featured background-image" style="background-image: url( <?php echo wpsl_get_attachment(); ?> );"></div>

    <?php endif ?>
    <?php the_content(); ?>
  </div> <!-- .entry-content -->

  <footer class="entry-footer">
    <?php echo wpsl_get_post_footer(); ?>
    <hr>
  </footer>

</article>
