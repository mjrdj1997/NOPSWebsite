<?php
/*
 * Template Name: Spanish Page
 * Pages in Spanish under /es/. Copy lives in the editor; the excerpt is the
 * hero subtitle and meta description. /es/vender/ gets the home value form,
 * every other Spanish page gets the contact form, both in Spanish.
 * Language tag and hreflang pairing: nops_is_spanish() / nops_hreflang().
 */
if (!defined('ABSPATH')) exit;
get_header();
$uri   = get_page_uri(get_queried_object());
$pairs = nops_es_pairs();
$en    = isset($pairs[$uri]) ? home_url($pairs[$uri]) : home_url('/');
$type  = $uri === 'es/vender' ? 'valuation' : 'contact';
?>
<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="<?php echo esc_url(home_url('/es/')); ?>">Inicio en español</a><?php if ($uri !== 'es') : ?> / <?php echo esc_html(get_the_title()); ?><?php endif; ?> &middot; <a href="<?php echo esc_url($en); ?>" hreflang="en" lang="en">English</a></div>
  <p class="eyebrow" style="color:var(--brass-soft)">Hablamos español</p>
  <h1><?php the_title(); ?></h1>
  <?php if (has_excerpt()) : ?><p class="lead"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
</div></section>

<section class="section"><div class="container">
  <div class="contact-grid">
    <div class="post-content">
      <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
      <p style="margin-top:28px">
        <a class="btn btn--gold" href="tel:5044735969" style="color:#fff">Llame a Kari: 504-473-5969</a>
      </p>
    </div>
    <div>
      <?php echo nops_lead_form($type, 'es', $type === 'valuation'
          ? ['heading' => '¿Cuánto vale su casa?', 'intro' => 'Kari revisará su casa y las ventas recientes de la zona, y le responderá personalmente.']
          : ['heading' => 'Escríbale a Kari', 'intro' => 'Cuéntenos qué busca. Kari le responderá personalmente, en español.']); ?>
    </div>
  </div>
</div></section>
<?php get_footer(); ?>
