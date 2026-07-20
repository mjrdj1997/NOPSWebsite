<?php
/*
 * Template Name: Neighborhood Landing
 * Hyper-local "[Neighborhood] Homes for Sale" landing page. Content lives in the
 * page's editor (post_content); this template adds the hero, a neighborhood photo,
 * and the search / concierge / contact calls to action.
 */
if (!defined('ABSPATH')) exit;
get_header();

$slug = get_queried_object()->post_name;
$imgmap = [
    'garden-district-homes-for-sale' => 'gardendistrict-nops.jpg',
    'uptown-homes-for-sale'          => 'uptown-nops.jpg',
    'french-quarter-homes-for-sale'  => 'french-quarter-nops.jpg',
    'marigny-homes-for-sale'         => 'marigny-nops.jpg',
    'bywater-homes-for-sale'         => 'bywater-cottage.jpg',
    'lakeview-homes-for-sale'        => 'lakeview-nops.jpg',
    'mid-city-homes-for-sale'        => 'midcity-nops.jpg',
    'irish-channel-homes-for-sale'   => 'shotgun-01.jpg',
];
$imgfile = isset($imgmap[$slug]) ? $imgmap[$slug] : 'gd-2008.jpg';
$img  = get_theme_file_uri('assets/nola/' . $imgfile);
$name = get_the_title();

// Map each landing page to the canonical neighborhood key so the "Search
// listings" button lands on ZIP-filtered results (matching the homepage/Buy
// quick-search), not a blank search form.
$hoodmap = [
    'garden-district-homes-for-sale' => 'Garden District',
    'uptown-homes-for-sale'          => 'Uptown',
    'french-quarter-homes-for-sale'  => 'French Quarter',
    'marigny-homes-for-sale'         => 'Marigny',
    'bywater-homes-for-sale'         => 'Bywater',
    'lakeview-homes-for-sale'        => 'Lakeview',
    'mid-city-homes-for-sale'        => 'Mid-City',
    'irish-channel-homes-for-sale'   => 'Irish Channel',
];
$hood_zips = isset($hoodmap[$slug]) ? nops_hood_to_zips($hoodmap[$slug]) : [];
$search_url = $hood_zips
    ? nops_listing_url(['zip_code' => implode(',', $hood_zips)])
    : nops_listing_url();
?>
<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <a href="<?php echo esc_url(home_url('/communities/')); ?>">Communities</a> / <?php echo esc_html($name); ?></div>
  <h1><?php the_title(); ?></h1>
</div></section>

<section class="section"><div class="container" style="max-width:840px">
  <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($name . ', New Orleans'); ?>" style="width:100%;height:auto;border-radius:14px;margin-bottom:32px" loading="lazy">

  <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>

  <div style="display:flex;flex-wrap:wrap;gap:12px;margin:34px 0 8px">
    <a class="btn btn--gold" href="<?php echo esc_url($search_url); ?>" style="color:#fff">Search <?php echo esc_html($name); ?> listings</a>
    <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/')); ?>#ai-concierge">Ask the AI concierge</a>
  </div>
  <p style="color:var(--muted);margin-top:20px">Thinking about buying or selling in <?php echo esc_html($name); ?>? <a href="<?php echo esc_url(home_url('/contact/')); ?>">Talk with Kari</a> — you'll work directly with a local broker who knows the neighborhood.</p>
</div></section>

<?php get_footer();
