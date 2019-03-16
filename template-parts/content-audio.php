<?php
/**
 * The template for displaying the content of Audio Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.slush.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-format-audio' ); ?>>

  <header class="entry-header">
    <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>') ?>

    <div class="entry-meta"><?php echo wpsl_get_post_meta(); ?></div>
  </header>

  <div class="entry-content"><?php echo wpsl_get_embedded_media( array( 'audio', 'iframe' ) ); ?></div>

  <footer class="entry-footer">
    <?php echo wpsl_get_post_footer(); ?>
    <hr>
  </footer>

</article>
