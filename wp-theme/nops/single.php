<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <a href="<?php echo esc_url(home_url('/journal/')); ?>">Journal</a></div>
  <p class="eyebrow" style="color:var(--brass-soft)"><?php echo get_the_category_list(', '); ?></p>
  <h1><?php the_title(); ?></h1>
  <p class="lead"><?php echo esc_html(get_the_date()); ?> &middot; By <?php the_author(); ?></p>
</div></section>

<section class="section"><div class="container" style="max-width:760px">
  <?php while (have_posts()) : the_post(); ?>
    <?php if (has_post_thumbnail()) : ?>
      <div style="border-radius:14px;overflow:hidden;margin-bottom:32px"><?php the_post_thumbnail('large'); ?></div>
    <?php endif; ?>
    <div class="post-content"><?php the_content(); ?></div>
  <?php endwhile; ?>
  <p style="margin-top:40px"><a href="<?php echo esc_url(home_url('/journal/')); ?>" class="btn btn--ghost">&larr; Back to the Journal</a></p>
</div></section>

<?php get_footer(); ?>
