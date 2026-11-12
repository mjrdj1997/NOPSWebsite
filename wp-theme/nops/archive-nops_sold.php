<?php
/* /sold/ — every published sold-home page, newest sale first. */
if (!defined('ABSPATH')) exit;
get_header();
?>
<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / Recently Sold</div>
  <h1>Recently sold in New Orleans</h1>
  <p class="lead">Homes New Orleans Property Services has sold for our clients, from Uptown and the Garden District to Mid-City and Lakeview.</p>
</div></section>

<section class="section"><div class="container">
  <?php if (have_posts()) : ?>
    <div class="grid grid--3">
      <?php while (have_posts()) : the_post(); $m = nops_sold_meta(get_the_ID()); ?>
      <article class="card post">
        <?php if (has_post_thumbnail()) : ?><div class="post__media" style="background-image:url('<?php echo esc_url(get_the_post_thumbnail_url(null, 'medium_large')); ?>')"></div><?php endif; ?>
        <div class="post__body">
          <span class="post__cat">Sold<?php echo $m['sold_label'] ? ' &middot; ' . esc_html($m['sold_label']) : ''; ?></span>
          <a href="<?php the_permalink(); ?>" class="post__title" style="display:block"><?php echo esc_html($m['street']); ?></a>
          <p class="post__excerpt"><?php echo esc_html(trim(($m['hood'] ? $m['hood'] . ' · ' : '') . nops_sold_facts_line($m), ' ·')); ?></p>
        </div>
      </article>
      <?php endwhile; ?>
    </div>
    <div style="margin-top:40px"><?php the_posts_pagination(['mid_size' => 1]); ?></div>
  <?php else : ?>
    <p class="lead">Sold homes will appear here soon.</p>
  <?php endif; ?>

  <div class="center" style="margin-top:50px">
    <p class="lead center">Thinking of selling? Kari can tell you what your home would sell for today.</p>
    <a class="btn btn--gold" href="tel:5044735969" style="color:#fff">Call Kari: 504-473-5969</a>
  </div>
</div></section>
<?php get_footer(); ?>
