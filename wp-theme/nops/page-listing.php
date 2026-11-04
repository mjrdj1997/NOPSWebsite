<?php
/*
 * Template Name: Listing Page
 * A page of our own for one of Kari's listings: a clean address URL with original
 * copy, so it can compete for its address and live on as a "recently sold" page.
 * Facts come from nops_* post meta; the story goes in the page editor.
 */
if (!defined('ABSPATH')) exit;
get_header();
$l = nops_listing_meta(get_the_ID());
?>
<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <?php if ($l['hood_url']) : ?><a href="<?php echo esc_url(home_url($l['hood_url'])); ?>"><?php echo esc_html($l['hood']); ?></a> / <?php endif; ?><?php echo esc_html($l['street']); ?></div>
  <h1><?php echo esc_html($l['address']); ?></h1>
</div></section>

<section class="section"><div class="container" style="max-width:900px">
  <?php if ($l['photos']) : ?>
    <img src="<?php echo esc_url($l['photos'][0]); ?>" alt="<?php echo esc_attr($l['address']); ?>" class="listing-hero">
  <?php endif; ?>

  <dl class="listing-facts">
    <div><dt>Status</dt><dd><?php echo esc_html($l['status']); ?></dd></div>
    <?php if ($l['price']) : ?><div><dt>Price</dt><dd>$<?php echo esc_html(number_format($l['price'])); ?></dd></div><?php endif; ?>
    <?php if ($l['beds']) : ?><div><dt>Bedrooms</dt><dd><?php echo esc_html($l['beds']); ?></dd></div><?php endif; ?>
    <?php if ($l['baths']) : ?><div><dt>Bathrooms</dt><dd><?php echo esc_html($l['baths']); ?></dd></div><?php endif; ?>
    <?php if ($l['living']) : ?><div><dt>Living area</dt><dd><?php echo esc_html(number_format((int) $l['living'])); ?> sq ft</dd></div><?php endif; ?>
    <?php if ($l['year']) : ?><div><dt>Built</dt><dd><?php echo esc_html($l['year']); ?></dd></div><?php endif; ?>
    <?php if ($l['style']) : ?><div><dt>Style</dt><dd><?php echo esc_html($l['style']); ?></dd></div><?php endif; ?>
    <?php if ($l['mls']) : ?><div><dt>MLS#</dt><dd><?php echo esc_html($l['mls']); ?></dd></div><?php endif; ?>
  </dl>

  <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>

  <?php if (count($l['photos']) > 1) : ?>
    <div class="listing-gallery">
      <?php foreach (array_slice($l['photos'], 1) as $i => $src) : ?>
        <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($l['address'] . ', photo ' . ($i + 2)); ?>" loading="lazy">
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div style="display:flex;flex-wrap:wrap;gap:12px;margin:34px 0 8px">
    <a class="btn btn--gold" href="tel:5044735969" style="color:#fff">Call Kari: 504-473-5969</a>
    <?php if ($l['idx_url']) : ?><a class="btn btn--ghost" href="<?php echo esc_url(home_url($l['idx_url'])); ?>">All photos &amp; full MLS details</a><?php endif; ?>
    <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/contact/')); ?>">Send a message</a>
  </div>
  <p style="color:var(--muted);margin-top:20px;font-size:.9rem">Listed by Kari Ayala, New Orleans Property Services, LLC. Information deemed reliable but not guaranteed; buyers should verify all details independently.</p>
</div></section>
<?php get_footer(); ?>
