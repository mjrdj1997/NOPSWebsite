<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="/">Home</a> / Buy</div>
  <p class="eyebrow" style="color:var(--brass-soft)">Buyer Representation</p>
  <h1>Find the home that fits your life.</h1>
  <p class="lead">From your first tour to the closing table, we help you find — and win — the right New Orleans home with clear-eyed local guidance.</p>
</div></section>

<!-- Search — real Buying Buddy / MLS quick search (same as homepage) -->
<div class="container" style="margin-top:-36px">
  <?php nops_search_bar(false); ?>
</div>


<!-- Process -->
<section class="section tone-cream"><div class="container">
  <div class="center reveal"><p class="eyebrow">The Buyer Journey</p><h2>What working together looks like</h2></div>
  <div class="grid grid--2 reveal" style="margin-top:48px;gap:40px">
    <div class="steps">
      <div class="step"><div class="step__no"></div><div><h3>Get Pre-Approved</h3><p>We connect you with trusted local lenders so you shop with confidence and a clear budget.</p></div></div>
      <div class="step"><div class="step__no"></div><div><h3>Define Your Search</h3><p>Neighborhoods, must-haves, and dealbreakers — we translate your wish list into a focused search.</p></div></div>
    </div>
    <div class="steps">
      <div class="step"><div class="step__no"></div><div><h3>Tour &amp; Evaluate</h3><p>We tour together, and Kari reads each home's condition, value, and neighborhood honestly.</p></div></div>
      <div class="step"><div class="step__no"></div><div><h3>Offer &amp; Close</h3><p>Sharp negotiation and careful management of inspections, appraisal, and closing details.</p></div></div>
    </div>
  </div>
</div></section>

<section class="section"><div class="container"><div class="cta-band reveal">
  <p class="eyebrow" style="justify-content:center;color:var(--brass-soft)">Start Your Search</p>
  <h2>Let's find your New Orleans home.</h2>
  <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:24px"><a href="/contact/" class="btn btn--gold">Start My Home Search →</a><a href="tel:5044735969" class="btn btn--outline">📞 504-473-5969</a></div>
</div></div></section>

<?php get_footer(); ?>
