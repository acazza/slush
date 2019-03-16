<?php
/**
 * The template for displaying the content of Image Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.slush.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-format-image' ); ?>>

  <header class="entry-header background-image" style="background-image: url( <?php echo wpsl_get_attachment(); ?> );">
    <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>') ?>

    <div class="entry-meta"><?php echo wpsl_get_post_meta(); ?></div>

    <div class="entry-excerpt image-caption"><?php the_excerpt(); ?></div>
  </header>

  <footer class="entry-footer">
    <?php echo wpsl_get_post_footer(); ?>
    <hr>
  </footer>

</article>
