<?php
/**
 * The template for displaying the content of Gallery Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.slush.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-format-gallery' ); ?>>

  <header class="entry-header">

    <?php
    if( wpsl_get_attachment() ):
      $attachments = wpsl_get_attachment(5);
    endif
    ?>

    <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>') ?>

    <div class="entry-meta">
      <?php echo wpsl_get_post_meta(); ?>
    </div>
  </header>

  <div class="entry-content">

    <div class="entry-excerpt"><?php the_excerpt(); ?></div>
    <div class="button-container">
      <a href="<?php the_permalink(); ?>" class="btn btn-default btn-outlined"><?php _e( 'Read More' ); ?></a>
    </div> <!-- .button-container -->

  </div> <!-- .entry-content -->

  <footer class="entry-footer">
    <?php echo wpsl_get_post_footer(); ?>
    <hr>
  </footer>

</article>
