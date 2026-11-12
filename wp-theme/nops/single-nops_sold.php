<?php
/* One recently sold home. See nops_register_sold_cpt() for what these pages are and aren't. */
if (!defined('ABSPATH')) exit;
get_header();
$id = get_queried_object_id();
$m  = nops_sold_meta($id);
$review = nops_sold_review($m, $id);
?>
<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <a href="<?php echo esc_url(get_post_type_archive_link('nops_sold')); ?>">Recently Sold</a><?php if ($m['hood_url']) : ?> / <a href="<?php echo esc_url($m['hood_url']); ?>"><?php echo esc_html($m['hood']); ?></a><?php endif; ?></div>
  <p class="eyebrow" style="color:var(--brass-soft)">Sold<?php echo $m['sold_label'] ? ' &middot; ' . esc_html($m['sold_label']) : ''; ?></p>
  <h1><?php echo esc_html($m['street']); ?></h1>
  <p class="lead"><?php echo esc_html($m['city'] . ', LA ' . $m['zip']); ?></p>
</div></section>

<section class="section"><div class="container" style="max-width:900px">
  <?php if (has_post_thumbnail($id)) : ?>
    <div class="listing-hero"><?php echo get_the_post_thumbnail($id, 'large', ['alt' => $m['street'] . ', ' . $m['city']]); ?></div>
  <?php endif; ?>

  <dl class="listing-facts">
    <div><dt>Status</dt><dd>Sold</dd></div>
    <?php if ($m['sold_label']) : ?><div><dt>Sold</dt><dd><?php echo esc_html($m['sold_label']); ?></dd></div><?php endif; ?>
    <?php if ($m['beds']) : ?><div><dt>Bedrooms</dt><dd><?php echo esc_html($m['beds']); ?></dd></div><?php endif; ?>
    <?php if ($m['baths']) : ?><div><dt>Bathrooms</dt><dd><?php echo esc_html($m['baths']); ?></dd></div><?php endif; ?>
    <?php if ($m['sqft']) : ?><div><dt>Living area</dt><dd><?php echo esc_html(number_format($m['sqft'])); ?> sq ft</dd></div><?php endif; ?>
    <?php if ($m['hood']) : ?><div><dt>Area</dt><dd><?php echo esc_html($m['hood']); ?></dd></div><?php endif; ?>
  </dl>

  <p class="lead"><?php echo esc_html(nops_sold_summary($m)); ?></p>

  <?php while (have_posts()) : the_post(); if (trim(get_the_content()) !== '') : ?>
    <div class="post-content"><?php the_content(); ?></div>
  <?php endif; endwhile; ?>

  <?php if ($review) : ?>
    <div style="margin:40px 0 0"><p class="eyebrow">From a seller we worked with</p><?php nops_review_card($review); ?></div>
  <?php endif; ?>

  <div class="card" style="padding:30px;margin-top:40px">
    <h2 style="margin-top:0;font-size:1.6rem">Thinking of selling<?php echo $m['hood'] ? ' in ' . esc_html($m['hood']) : ''; ?>?</h2>
    <p>Kari can tell you what homes like yours are selling for right now, and what it would take to get yours sold.</p>
    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:18px">
      <a class="btn btn--gold" href="tel:5044735969" style="color:#fff">Call Kari: 504-473-5969</a>
      <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/sell/')); ?>">How Kari sells homes</a>
      <?php if ($m['hood_url']) : ?><a class="btn btn--ghost" href="<?php echo esc_url($m['hood_url']); ?>"><?php echo esc_html($m['hood']); ?> homes for sale</a><?php endif; ?>
    </div>
  </div>
  <p style="color:var(--muted);margin-top:20px;font-size:.9rem">Sale handled by New Orleans Property Services, LLC. Details from public sale records; information deemed reliable but not guaranteed.</p>
</div></section>
<?php get_footer(); ?>
