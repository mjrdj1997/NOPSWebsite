<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<!-- ===== Hero ===== -->
<section class="hero">
  <div class="container hero__grid">
    <div class="hero__text reveal">
      <p class="eyebrow">New Orleans · Since 1998</p>
      <h1>Your home in the city we know by heart.</h1>
      <p class="lead">A boutique brokerage delivering white-glove service to buyers, sellers, and investors — with an intimate knowledge of every street, corner, and historic block of New Orleans.</p>
      <div class="hero__actions">
        <a href="/buy/" class="btn btn--gold">Find Your Home →</a>
        <a href="/sell/" class="btn btn--ghost">Get a Home Valuation</a>
      </div>
      <div class="hero__stats">
        <div class="stat"><div class="stat__num">337+</div><div class="stat__label">Families Served</div></div>
        <div class="stat"><div class="stat__num">$125M+</div><div class="stat__label">In Home Sales</div></div>
        <div class="stat"><div class="stat__num">25+</div><div class="stat__label">Years in NOLA</div></div>
        <div class="stat"><div class="stat__num">5.0★</div><div class="stat__label">Client Rating</div></div>
      </div>
    </div>
    <div class="hero__media reveal">
      <img src="<?php echo esc_url(get_theme_file_uri('assets/nola/gd-mansion.jpg')); ?>">
      <div class="hero__media-badge"><span class="fleur">⚜</span><span><b>Est. 2007</b><span>Boutique &amp; locally owned</span></span></div>
    </div>
  </div>
</section>

<!-- ===== Search bar (maps to IDX search) ===== -->
<div class="container">
  <form class="searchbar" data-demo>
    <div class="field">
      <label>Neighborhood</label>
      <select><option>Any area</option><option>Garden District</option><option>Uptown</option><option>French Quarter</option><option>Marigny / Bywater</option><option>Lakeview</option><option>Mid-City</option></select>
    </div>
    <div class="field">
      <label>Type</label>
      <select><option>All homes</option><option>Single-family</option><option>Historic / Creole</option><option>Condo</option><option>Multi-family</option><option>Investment</option></select>
    </div>
    <div class="field">
      <label>Max Price</label>
      <select><option>No max</option><option>$400k</option><option>$600k</option><option>$800k</option><option>$1M+</option></select>
    </div>
    <button class="btn btn--gold" type="submit">Search MLS</button>
  </form>
</div>


<!-- ===== Services ===== -->
<section class="section tone-cream">
  <div class="container">
    <div class="center reveal">
      <p class="eyebrow">How We Help</p>
      <h2>Full-service guidance, start to finish</h2>
    </div>
    <div class="grid grid--3 reveal" style="margin-top:48px">
      <div class="service">
        <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9.5 21v-6h5v6"/></svg></div>
        <h3>Buying a Home</h3>
        <p>From first tour to closing table, we help you find and win the right home — with sharp local insight on value, condition, and neighborhood fit.</p>
      </div>
      <div class="service">
        <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 13 12 4l9 9"/><path d="M6 21V12h12v9"/><path d="M12 8v4M10 10h4"/></svg></div>
        <h3>Selling a Home</h3>
        <p>Strategic pricing, standout marketing, and skilled negotiation to sell your home for the most the market will bear — with as little friction as possible.</p>
      </div>
      <div class="service">
        <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 21h18"/><path d="M4 21V9l5-3 5 3v12"/><path d="M14 21V13l4-2 2 1v9"/></svg></div>
        <h3>Investment Property</h3>
        <p>Rental analysis, short-term rental guidance, and off-market opportunities for investors building a portfolio in a one-of-a-kind market.</p>
      </div>
      <div class="service">
        <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3 4 6v6c0 5 3.5 7.5 8 9 4.5-1.5 8-4 8-9V6z"/><path d="m9 12 2 2 4-4"/></svg></div>
        <h3>Historic Homes</h3>
        <p>Deep expertise with New Orleans' shotguns, Creole cottages, and Victorian doubles — including renovation, restoration, and preservation considerations.</p>
      </div>
      <div class="service">
        <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5 8 12 8 12s8-7 8-12a8 8 0 0 0-8-8Z"/></svg></div>
        <h3>Relocation</h3>
        <p>Moving to New Orleans? We orient you to the neighborhoods, schools, and rhythms of the city so your first home here truly feels like home.</p>
      </div>
      <div class="service">
        <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 4h16v12H4z"/><path d="M4 9h16"/><path d="M8 20h8M12 16v4"/></svg></div>
        <h3>Market Valuation</h3>
        <p>A precise, data-backed opinion of value for your home or prospective purchase — grounded in real comparable sales, not an algorithm.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== Market pulse ===== -->
<section class="section tone-paper2">
  <div class="container">
    <div class="center reveal">
      <p class="eyebrow">Market Pulse</p>
      <h2>The New Orleans market at a glance</h2>
      <p class="lead center">A current snapshot of the New Orleans market.</p>
    </div>
    <div class="grid grid--4 reveal" style="margin-top:44px">
      <div class="mstat"><div class="n">$354K</div><div class="l">Median Sale Price</div><div class="d up">5.7% YoY</div></div>
      <div class="mstat"><div class="n">75</div><div class="l">Median Days on Market</div><div class="d dn">1 day faster</div></div>
      <div class="mstat"><div class="n">$212</div><div class="l">Median $ / Sq Ft</div><div class="d dn">0.5% YoY</div></div>
      <div class="mstat"><div class="n">1,072</div><div class="l">Homes Sold &middot; May</div><div class="d up">15% YoY</div></div>
    </div>
    <p class="center" style="color:var(--muted);font-size:.82rem;margin-top:22px">New Orleans &middot; 3 months ending May 2026. Source: <a href="https://www.redfin.com/city/14233/LA/New-Orleans/housing-market" target="_blank" rel="noopener">Redfin</a>.</p>
  </div>
</section>

<!-- ===== About split ===== -->
<section class="section">
  <div class="container">
    <div class="split">
      <div class="split__media reveal">
        <img src="<?php echo esc_url(get_theme_file_uri('assets/kari.png')); ?>" alt="Kari Ayala, founder of New Orleans Property Services">
        <div class="split__badge"><div class="num">Est. 2007</div><div class="lbl">Founded &amp; independently owned in New Orleans</div></div>
      </div>
      <div class="reveal">
        <p class="eyebrow">Meet Kari Ayala</p>
        <h2>A local's knowledge, a professional's care.</h2>
        <p class="lead">Kari Kramer Ayala has been part of the New Orleans real estate market since 1998 and founded New Orleans Property Services in 2007. What began as a one-agent practice has grown into a trusted boutique brokerage built on professionalism, discretion, and genuine care.</p>
        <p>A Fulbright Scholar with graduate training from Tulane, Kari brings rigor and warmth to every transaction — and an intimate familiarity with the character of each neighborhood. Deeply rooted in the community, she co-founded the International School of Louisiana and serves as President of the Krewe of Dolly.</p>
        <ul class="check">
          <li>Licensed REALTOR® since 2004 · 25+ years of local market experience</li>
          <li>337+ families &amp; individuals guided home</li>
          <li>Boutique, high-touch service — you work directly with Kari</li>
        </ul>
        <div style="margin-top:30px"><a href="/about/" class="btn btn--dark">Read Kari's Story →</a></div>
      </div>
    </div>
  </div>
</section>

<!-- ===== In the community ===== -->
<section class="section tone-cream">
  <div class="container">
    <div class="split">
      <div class="split__media reveal"><img src="<?php echo esc_url(get_theme_file_uri('assets/nola/foti-mansion.jpg')); ?>" alt="An elegant historic New Orleans mansion"></div>
      <div class="reveal">
        <p class="eyebrow">In the Community</p>
        <h2>Rooted in the life of the city.</h2>
        <p class="lead">Kari's connection to New Orleans runs deeper than real estate — and it's exactly what makes her guidance more than transactional.</p>
        <p>She co-founded the International School of Louisiana, a public charter offering full-immersion programs in Spanish and French, and serves as President of the Krewe of Dolly, a nonprofit supporting Dolly Parton's Imagination Library.</p>
        <ul class="pills">
          <li><b>Co-Founder</b> · Intl. School of Louisiana</li>
          <li><b>President</b> · Krewe of Dolly</li>
          <li><b>Supporter</b> · Imagination Library</li>
          <li><b>Fulbright</b> Scholar</li>
        </ul>
        <div style="margin-top:28px"><a href="/about/" class="btn btn--dark">More About Kari →</a></div>
      </div>
    </div>
  </div>
</section>

<!-- ===== Why choose (dark) ===== -->
<section class="section tone-dark">
  <div class="container">
    <div class="center reveal">
      <p class="eyebrow">The Difference</p>
      <h2>Why clients choose a boutique brokerage</h2>
      <p class="lead center">Big enough to get it done, small enough to truly know you.</p>
    </div>
    <div class="grid grid--4 reveal" style="margin-top:48px">
      <div class="value"><div class="num">01</div><h3>Direct Access</h3><p>You work with Kari herself — not a rotating team. One relationship, start to finish.</p></div>
      <div class="value"><div class="num">02</div><h3>Hyper-Local</h3><p>Street-by-street knowledge of value, history, flood, and character across the city.</p></div>
      <div class="value"><div class="num">03</div><h3>White-Glove Service</h3><p>Discreet, attentive, and thorough — the standard of care our clients return to.</p></div>
      <div class="value"><div class="num">04</div><h3>Proven Results</h3><p>$125M+ closed and 337+ families served across more than two decades.</p></div>
    </div>
  </div>
</section>

<!-- ===== Neighborhoods ===== -->
<section class="section tone-paper2">
  <div class="container">
    <div class="center reveal">
      <p class="eyebrow">Explore the City</p>
      <h2>Neighborhoods we call home</h2>
    </div>
    <div class="grid grid--4 reveal" style="margin-top:48px">
      <a class="hood" href="/communities/" style="background-image:linear-gradient(135deg,rgba(26,24,22,.2),rgba(26,24,22,.3)),url('<?php echo esc_url(get_theme_file_uri('assets/nola/gd-2008.jpg')); ?>')"><span class="hood__label"><h3>Garden District</h3><span>Historic mansions &amp; oaks</span></span></a>
      <a class="hood" href="/communities/" style="background-image:linear-gradient(135deg,rgba(26,24,22,.2),rgba(26,24,22,.3)),url('<?php echo esc_url(get_theme_file_uri('assets/nola/streetcar.jpg')); ?>')"><span class="hood__label"><h3>Uptown</h3><span>Streetcars &amp; Magazine St.</span></span></a>
      <a class="hood" href="/communities/" style="background-image:linear-gradient(135deg,rgba(26,24,22,.2),rgba(26,24,22,.3)),url('<?php echo esc_url(get_theme_file_uri('assets/nola/fq-balcony.jpg')); ?>')"><span class="hood__label"><h3>French Quarter</h3><span>Iron galleries &amp; courtyards</span></span></a>
      <a class="hood" href="/communities/" style="background-image:linear-gradient(135deg,rgba(26,24,22,.2),rgba(26,24,22,.3)),url('<?php echo esc_url(get_theme_file_uri('assets/nola/marigny-cottage.jpg')); ?>')"><span class="hood__label"><h3>Marigny / Bywater</h3><span>Creole cottages &amp; color</span></span></a>
    </div>
    <div class="center" style="margin-top:40px"><a href="/communities/" class="btn btn--ghost">Explore All Neighborhoods</a></div>
  </div>
</section>

<!-- ===== Instagram ===== -->
<section class="section">
  <div class="container">
    <div class="center reveal">
      <p class="eyebrow">Follow Along</p>
      <h2>@nolakari1 on Instagram</h2>
      <p class="lead center">New listings, sold celebrations, and a local's view of New Orleans — pulled straight from Instagram so the site stays current.</p>
    </div>
    <div class="ig-grid reveal" style="margin-top:40px">
      <a href="#" class="ig-tile" style="background-image:url('<?php echo esc_url(get_theme_file_uri('assets/nola/hoffman-mansion.jpg')); ?>')"></a>
      <a href="#" class="ig-tile" style="background-image:url('<?php echo esc_url(get_theme_file_uri('assets/nola/bywater-cottage.jpg')); ?>')"></a>
      <a href="#" class="ig-tile" style="background-image:url('<?php echo esc_url(get_theme_file_uri('assets/nola/lakeview.jpg')); ?>')"></a>
      <a href="#" class="ig-tile" style="background-image:url('<?php echo esc_url(get_theme_file_uri('assets/nola/midcity.jpg')); ?>')"></a>
      <a href="#" class="ig-tile" style="background-image:url('<?php echo esc_url(get_theme_file_uri('assets/nola/shotgun-01.jpg')); ?>')"></a>
      <a href="#" class="ig-tile" style="background-image:url('<?php echo esc_url(get_theme_file_uri('assets/nola/lanaux-mansion.jpg')); ?>')"></a>
    </div>
    <div class="center" style="margin-top:34px"><a href="#" class="btn btn--ghost">Follow @nolakari1</a></div>
  </div>
</section>

<!-- ===== NOLA Journal ===== -->
<section class="section tone-cream">
  <div class="container">
    <div class="reveal" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px">
      <div><p class="eyebrow">New Orleans Journal</p><h2>Local insight &amp; market notes</h2></div>
      <a href="/journal/" class="btn btn--ghost">Read the Journal →</a>
    </div>
    <div class="grid grid--3 reveal" style="margin-top:44px">
      <?php
      $nops_journal = new WP_Query(['post_type' => 'post', 'posts_per_page' => 3, 'ignore_sticky_posts' => true]);
      if ($nops_journal->have_posts()) :
        while ($nops_journal->have_posts()) : $nops_journal->the_post();
          $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
          $cats  = get_the_category();
          $mins  = max(1, (int) round(str_word_count(wp_strip_all_tags(get_the_content())) / 200));
      ?>
      <article class="card post">
        <?php if ($thumb) : ?><div class="post__media" style="background-image:url('<?php echo esc_url($thumb); ?>')"></div><?php endif; ?>
        <div class="post__body">
          <?php if ($cats) : ?><span class="post__cat"><?php echo esc_html($cats[0]->name); ?></span><?php endif; ?>
          <a href="<?php the_permalink(); ?>" class="post__title" style="display:block"><?php the_title(); ?></a>
          <p class="post__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
          <div class="post__meta"><span><?php echo esc_html(get_the_date('M j, Y')); ?></span><span class="post__read"><?php echo (int) $mins; ?> min read</span></div>
        </div>
      </article>
      <?php endwhile; wp_reset_postdata(); endif; ?>
    </div>
  </div>
</section>

<!-- ===== Process ===== -->
<section class="section">
  <div class="container">
    <div class="split">
      <div class="reveal">
        <p class="eyebrow">The Process</p>
        <h2>Simple, transparent, and on your side.</h2>
        <p class="lead">Whether you're buying or selling, you'll always know exactly where you stand and what comes next.</p>
        <div style="margin-top:28px"><a href="/contact/" class="btn btn--gold">Start a Conversation →</a></div>
      </div>
      <div class="steps reveal">
        <div class="step"><div class="step__no"></div><div><h3>Discovery</h3><p>We start with a conversation about your goals, timeline, and the life you want your home to fit.</p></div></div>
        <div class="step"><div class="step__no"></div><div><h3>Strategy</h3><p>A tailored plan — a targeted search for buyers, or pricing and marketing for sellers.</p></div></div>
        <div class="step"><div class="step__no"></div><div><h3>Negotiation</h3><p>Skilled, level-headed representation to protect your interests and your bottom line.</p></div></div>
        <div class="step"><div class="step__no"></div><div><h3>Closing &amp; Beyond</h3><p>We manage the details to the finish line — and stay a resource long after the keys change hands.</p></div></div>
      </div>
    </div>
  </div>
</section>

<!-- ===== Testimonials ===== -->
<section class="section tone-cream">
  <div class="container">
    <div class="center reveal">
      <p class="eyebrow">Client Stories</p>
      <h2>Trusted across the city</h2>
    </div>
    <div class="grid grid--3 reveal" style="margin-top:48px">
      <div class="quote">
        <div class="quote__stars">★★★★★</div>
        <p>"Kari knows New Orleans like no one else. She guided us to the perfect historic home and made a complex purchase feel effortless."</p>
        <div class="quote__by"><span class="quote__avatar">MR</span><span><span class="quote__name">Marie &amp; Robert D.</span><br><span class="quote__loc">Garden District</span></span></div>
      </div>
      <div class="quote">
        <div class="quote__stars">★★★★★</div>
        <p>"Truly white-glove service. Our home sold above asking in under two weeks. Kari's pricing and marketing were spot on."</p>
        <div class="quote__by"><span class="quote__avatar">JT</span><span><span class="quote__name">James T.</span><br><span class="quote__loc">Uptown</span></span></div>
      </div>
      <div class="quote">
        <div class="quote__stars">★★★★★</div>
        <p>"As an out-of-state investor I relied entirely on Kari's judgment — and she delivered a fantastic rental property with real upside."</p>
        <div class="quote__by"><span class="quote__avatar">AL</span><span><span class="quote__name">Alicia L.</span><br><span class="quote__loc">Marigny</span></span></div>
      </div>
    </div>
  </div>
</section>

<!-- ===== CTA ===== -->
<section class="section">
  <div class="container">
    <div class="cta-band reveal">
      <p class="eyebrow" style="justify-content:center;color:var(--brass-soft)">Let's Talk</p>
      <h2>Ready to make your next move?</h2>
      <p class="lead center" style="color:#c9d2d8">Whether you're buying, selling, or investing, a short conversation is the best place to start.</p>
      <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:26px">
        <a href="tel:5044735969" class="btn btn--gold">📞 Call 504-473-5969</a>
        <a href="/contact/" class="btn btn--outline">Send a Message</a>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
