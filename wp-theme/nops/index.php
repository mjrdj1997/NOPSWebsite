<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / Journal</div>
  <p class="eyebrow" style="color:var(--brass-soft)">New Orleans Journal</p>
  <h1><?php echo is_home() ? 'Local insight, straight from the streets.' : esc_html(get_the_archive_title()); ?></h1>
  <p class="lead">Neighborhood guides, buying and selling know-how, and honest market updates &mdash; from someone who actually knows New Orleans.</p>
</div></section>

<section class="section"><div class="container">
  <?php if (have_posts()) : ?>
  <div class="grid grid--3">
    <?php while (have_posts()) : the_post(); ?>
    <article class="card post">
      <?php if (has_post_thumbnail()) : ?>
      <div class="post__media" style="background-image:url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>')"></div>
      <?php endif; ?>
      <div class="post__body">
        <span class="post__cat"><?php echo get_the_category_list(', '); ?></span>
        <a href="<?php the_permalink(); ?>" class="post__title" style="display:block"><?php the_title(); ?></a>
        <p class="post__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
        <div class="post__meta"><span><?php echo esc_html(get_the_date()); ?></span><span class="post__read"><?php the_time('M j'); ?></span></div>
      </div>
    </article>
    <?php endwhile; ?>
  </div>
  <div style="margin-top:40px"><?php the_posts_pagination(['mid_size' => 1]); ?></div>
  <?php else : ?>
  <p class="lead">Kari's New Orleans Journal is coming soon.</p>
  <?php endif; ?>
</div></section>

<?php get_footer(); ?>
