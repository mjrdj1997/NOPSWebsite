<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <?php the_title(); ?></div>
  <h1><?php the_title(); ?></h1>
</div></section>

<section class="section"><div class="container">
  <?php while (have_posts()) : the_post(); ?>
    <?php the_content(); ?>
  <?php endwhile; ?>
</div></section>

<?php get_footer(); ?>
