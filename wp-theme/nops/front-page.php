<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<!-- ===== Hero ===== -->
<section class="hero">
  <div class="container hero__grid">
    <div class="hero__text reveal">
      <p class="eyebrow">New Orleans Property Services · Since 2007</p>
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
  <?php
    // Neighborhood filtering runs on ZIP (GSREIN's neighborhood field is unreliable;
    // ZIP is clean). The dropdown maps each neighborhood to its ZIP set via the same
    // canonical PHP map the concierge uses (nops_nola_hood_zips), and a ZIP box lets
    // users enter ZIPs directly. Submit builds Buying Buddy's real results URL:
    //   /listing-results/?data-filter=bb&mls_id=la248&zip_code=70130,70115&price_max=...
  ?>
  <form class="searchbar" id="mls-quicksearch" method="get" action="<?php echo esc_url(home_url('/listing-search/')); ?>" data-results-url="<?php echo esc_url(home_url('/listing-results/')); ?>">
    <div class="field">
      <label for="qs-nbhd">Neighborhood</label>
      <select id="qs-nbhd" name="nbhd">
        <option value="">Any area</option>
        <?php foreach (array_keys(nops_nola_hood_zips()) as $hood) :
          // Show the six headline neighborhoods in the quick bar (full list still maps).
          if (!in_array($hood, ['Garden District','Uptown','French Quarter','Marigny / Bywater','Lakeview','Mid-City'], true)) continue; ?>
          <option value="<?php echo esc_attr($hood); ?>"><?php echo esc_html($hood); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label for="qs-zip">or ZIP code</label>
      <input id="qs-zip" name="zip" type="text" inputmode="numeric" autocomplete="postal-code" placeholder="e.g. 70130" pattern="[0-9 ,]*">
    </div>
    <div class="field">
      <label for="qs-price">Max Price</label>
      <select id="qs-price" name="price">
        <option value="">No max</option>
        <option value="price_max:400000">$400k</option>
        <option value="price_max:600000">$600k</option>
        <option value="price_max:800000">$800k</option>
        <option value="price_min:1000000">$1M+</option>
      </select>
    </div>
    <button class="btn btn--gold" type="submit">Search MLS</button>
  </form>
</div>
<script>
(function () {
  var form = document.getElementById('mls-quicksearch');
  if (!form) return;

  // Same neighborhood -> ZIP set the server helper uses (single source of truth).
  var HOOD_ZIPS = <?php echo wp_json_encode(nops_nola_hood_zips()); ?>;
  var RESULTS = form.dataset.resultsUrl;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var zips = [];
    // Neighborhood -> its ZIP set
    var nbhd = form.querySelector('#qs-nbhd').value;
    if (nbhd && HOOD_ZIPS[nbhd]) zips = zips.concat(HOOD_ZIPS[nbhd]);
    // Direct ZIP entry (accepts "70130", "70115 70118", "70115,70118")
    var typed = (form.querySelector('#qs-zip').value.match(/\d{5}/g)) || [];
    zips = zips.concat(typed);
    zips = zips.filter(function (z, i) { return zips.indexOf(z) === i; }); // dedupe

    var params = {};
    if (zips.length) params.zip_code = zips.join(',');
    var price = form.querySelector('#qs-price').value; // 'price_max:NNNN' or 'price_min:NNNN'
    if (price) { var p = price.split(':'); params[p[0]] = p[1]; }

    var keys = Object.keys(params);
    if (!keys.length) { window.location.href = RESULTS; return; } // browse all
    var qs = ['data-filter=bb', 'mls_id=la248'];
    keys.forEach(function (k) { qs.push(encodeURIComponent(k) + '=' + encodeURIComponent(params[k])); });
    window.location.href = RESULTS + '?' + qs.join('&');
  });
})();
</script>


<!-- ===== Featured Listings (IDX) ===== -->
<section class="section" id="featured-listings">
  <div class="container">
    <div class="center reveal">
      <p class="eyebrow">Now on the Market</p>
      <h2>Featured New Orleans listings</h2>
      <p class="lead center">A selection of current listings, pulled live from the MLS. <a href="<?php echo esc_url(nops_listing_url()); ?>">Search every home for sale &rarr;</a></p>
    </div>
    <div class="reveal" style="margin-top:40px">
      <?php echo do_shortcode('[mbb_widget data-type="FeaturedGallery"]'); ?>
    </div>
  </div>
</section>


<!-- ===== AI Home Concierge ===== -->
<section class="section tone-paper2" id="ai-concierge">
  <div class="container" style="max-width:840px">
    <div class="center reveal">
      <p class="eyebrow">AI Home Concierge</p>
      <h2>Tell us what you're looking for.</h2>
      <p class="lead center">Describe your ideal New Orleans home in your own words. Kari's concierge will point you to matching listings — and can set up a personal shortlist.</p>
    </div>
    <div class="reveal" style="margin-top:30px">
      <form id="conc-form" class="conc-form" autocomplete="off">
        <label for="conc-q" class="fl" style="display:block;margin-bottom:8px">Describe your ideal home <span style="color:var(--muted,#6b6560);font-weight:400">— or tap the mic to speak</span></label>
        <div class="conc-input">
          <textarea id="conc-q" rows="3" maxlength="600" required placeholder="e.g. A 3-bedroom historic home in the Marigny or Bywater under $600k, with a courtyard and off-street parking, walkable to coffee."></textarea>
          <button type="button" id="conc-mic" class="conc-mic" hidden aria-pressed="false" aria-label="Speak your search" title="Speak your search">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M6 11a6 6 0 0 0 12 0"/><path d="M12 17v3"/></svg>
          </button>
        </div>
        <div style="position:absolute;left:-9999px" aria-hidden="true"><input type="text" id="conc-hp" tabindex="-1" autocomplete="off"></div>
        <button type="submit" id="conc-go" class="btn btn--gold" style="justify-content:center;margin-top:14px">Find my matches</button>
      </form>
      <div id="conc-result" class="conc-result" aria-live="polite" hidden></div>
      <p class="conc-fineprint">Kari's AI concierge helps with home criteria only — location, price, size, style, and features. In keeping with Fair Housing law, it doesn't advise on neighborhoods by demographics, schools, or safety; for that guidance, <a href="/contact/">talk with Kari directly</a>.</p>
    </div>
  </div>
</section>
<style>
#ai-concierge .conc-form textarea{width:100%;padding:14px 16px;border:1px solid var(--line,#e8e1d4);border-radius:10px;font:inherit;font-size:1rem;background:#fff;color:var(--ink,#1a1816);resize:none;box-sizing:border-box}
#ai-concierge .conc-form textarea:focus{outline:none;border-color:var(--brass,#cd8c38);box-shadow:0 0 0 3px rgba(205,140,56,.15)}
#ai-concierge .conc-result{margin-top:24px;background:#fff;border:1px solid var(--line,#e8e1d4);border-radius:12px;padding:24px 26px;scroll-margin-top:110px}
#ai-concierge .conc-result h3{margin:0 0 10px;font-family:Georgia,serif;font-size:1.2rem;color:var(--ink,#1a1816)}
#ai-concierge .conc-summary{font-size:1.05rem;line-height:1.6;color:#403c38;margin:0 0 18px}
#ai-concierge .conc-tags{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 20px}
#ai-concierge .conc-tag{font-size:.8rem;background:#f4f0e8;border:1px solid #e8e1d4;color:#6b6560;padding:5px 11px;border-radius:20px}
#ai-concierge .conc-actions{display:flex;flex-wrap:wrap;gap:12px}
#ai-concierge .conc-lead{margin-top:20px;border-top:1px solid var(--line,#e8e1d4);padding-top:20px}
#ai-concierge .conc-lead .form-row{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px}
#ai-concierge .conc-lead input{flex:1 1 180px;padding:11px 14px;border:1px solid var(--line,#e8e1d4);border-radius:8px;font:inherit;box-sizing:border-box}
#ai-concierge .conc-fineprint{color:var(--muted,#6b6560);font-size:.78rem;line-height:1.5;text-align:center;margin-top:16px}
#ai-concierge .conc-spin{display:inline-block;width:15px;height:15px;border:2px solid rgba(255,255,255,.5);border-top-color:#fff;border-radius:50%;animation:concspin .7s linear infinite;vertical-align:-2px;margin-right:8px}
@keyframes concspin{to{transform:rotate(360deg)}}
#ai-concierge .conc-input{position:relative}
#ai-concierge .conc-input textarea{padding-right:62px}
#ai-concierge .conc-mic{position:absolute;right:12px;bottom:12px;width:44px;height:44px;padding:0;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--brass,#cd8c38);background:#fff;color:var(--brass,#cd8c38);cursor:pointer;transition:background .15s,color .15s}
#ai-concierge .conc-mic:hover{background:var(--brass,#cd8c38);color:#fff}
#ai-concierge .conc-mic.listening{background:#c0392b;border-color:#c0392b;color:#fff;animation:concpulsebtn 1.25s ease-in-out infinite}
@keyframes concpulsebtn{0%{box-shadow:0 0 0 0 rgba(192,57,43,.45)}70%,100%{box-shadow:0 0 0 10px rgba(192,57,43,0)}}
</style>
<script>
(function(){
  var cfg={
    endpoint:  <?php echo wp_json_encode( esc_url_raw( rest_url('nops/v1/concierge') ) ); ?>,
    nonce:     <?php echo wp_json_encode( wp_create_nonce('wp_rest') ); ?>,
    leadUrl:   <?php echo wp_json_encode( esc_url_raw( admin_url('admin-post.php') ) ); ?>,
    leadNonce: <?php echo wp_json_encode( wp_create_nonce('nops_contact') ); ?>
  };
  var form=document.getElementById('conc-form'); if(!form) return;
  var q=document.getElementById('conc-q'), hp=document.getElementById('conc-hp'),
      go=document.getElementById('conc-go'), out=document.getElementById('conc-result');
  var lastQuery='', lastCriteria=null, lastSummary='';
  function el(t,c,x){var e=document.createElement(t); if(c)e.className=c; if(x!=null)e.textContent=x; return e;}
  function show(){ out.hidden=false; out.scrollIntoView({behavior:'smooth',block:'center'}); }

  // Voice input (Web Speech API) — progressive enhancement; button stays hidden where unsupported.
  var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  var mic = document.getElementById('conc-mic');
  if (SR && mic) {
    mic.hidden = false;
    var rec = new SR(); rec.lang='en-US'; rec.interimResults=true; rec.continuous=false; rec.maxAlternatives=1;
    var listening=false, baseText='', micTxt=mic.querySelector('.conc-mic-txt');
    function setListening(on){ listening=on; mic.classList.toggle('listening',on); mic.setAttribute('aria-pressed',on?'true':'false'); mic.title = on?'Listening… tap to stop':'Speak your search'; if(micTxt) micTxt.textContent = on?'Listening… tap to stop':'Speak your search'; }
    mic.addEventListener('click',function(){ if(listening){ rec.stop(); return; } baseText = q.value ? q.value.replace(/\s+$/,'')+' ' : ''; try{ rec.start(); }catch(e){} });
    rec.onstart=function(){ setListening(true); q.focus(); };
    rec.onend=function(){ setListening(false); };
    rec.onerror=function(e){ setListening(false); if(e&&e.error==='not-allowed'&&micTxt){ micTxt.textContent='Mic blocked — allow access'; setTimeout(function(){ if(micTxt) micTxt.textContent='Speak your search'; },2600); } };
    rec.onresult=function(ev){ var interim='',final=''; for(var i=ev.resultIndex;i<ev.results.length;i++){ var t=ev.results[i][0].transcript; if(ev.results[i].isFinal) final+=t; else interim+=t; } q.value=(baseText+final+interim).slice(0,600); if(final) baseText=(baseText+final).replace(/\s+$/,'')+' '; };
  }

  form.addEventListener('submit',function(ev){
    ev.preventDefault();
    if(hp.value) return;
    var text=q.value.trim(); if(!text) return;
    lastQuery=text; go.disabled=true; go.innerHTML='<span class="conc-spin"></span>Thinking…';
    out.hidden=true; out.innerHTML='';
    fetch(cfg.endpoint,{method:'POST',headers:{'Content-Type':'application/json','X-WP-Nonce':cfg.nonce},body:JSON.stringify({q:text,website:hp.value})})
      .then(function(r){return r.json().then(function(j){return{ok:r.ok,j:j};});})
      .then(function(res){
        go.disabled=false; go.textContent='Find my matches';
        if(!res.ok||!res.j||res.j.error){ err((res.j&&res.j.error)||'Something went wrong. Please try again.'); return; }
        render(res.j);
      })
      .catch(function(){ go.disabled=false; go.textContent='Find my matches'; err('The concierge is momentarily unavailable. Please try the search above.'); });
  });

  function err(m){ out.innerHTML=''; out.appendChild(el('p','conc-summary',m)); show(); }

  function render(j){
    lastCriteria=j.criteria||{}; lastSummary=j.summary||'';
    out.innerHTML='';
    out.appendChild(el('h3',null,'Here’s what I’m hearing'));
    out.appendChild(el('p','conc-summary', j.summary||''));
    var c=j.criteria||{}, tags=[];
    if(c.neighborhoods&&c.neighborhoods.length) tags=tags.concat(c.neighborhoods);
    if(c.price_max) tags.push('Up to $'+Number(c.price_max).toLocaleString());
    else if(c.price_min) tags.push('From $'+Number(c.price_min).toLocaleString());
    if(c.beds_min) tags.push(c.beds_min+'+ bd');
    if(c.baths_min) tags.push(c.baths_min+'+ ba');
    if(c.property_type&&c.property_type!=='any') tags.push(c.property_type);
    if(c.features&&c.features.length) tags=tags.concat(c.features);
    if(tags.length){ var tw=el('div','conc-tags'); tags.forEach(function(t){ tw.appendChild(el('span','conc-tag',t)); }); out.appendChild(tw); }
    if(j.followup) out.appendChild(el('p','conc-summary', j.followup));
    var act=el('div','conc-actions');
    if(j.search_url){ var a=el('a','btn btn--gold','View matching listings'); a.href=j.search_url; a.style.color='#fff'; act.appendChild(a); }
    var lb=el('button','btn btn--ghost','Have Kari hand-pick a shortlist'); lb.type='button'; act.appendChild(lb);
    out.appendChild(act);
    lb.addEventListener('click',function(){ lb.style.display='none'; out.appendChild(lead()); });
    show();
  }

  function lead(){
    var w=el('div','conc-lead');
    w.appendChild(el('p','conc-summary','Leave your details and Kari will personally put together a shortlist for you.'));
    var r1=el('div','form-row'), fn=el('input'), ln=el('input');
    fn.placeholder='First name'; fn.required=true; ln.placeholder='Last name';
    r1.appendChild(fn); r1.appendChild(ln); w.appendChild(r1);
    var r2=el('div','form-row'), em=el('input'), ph=el('input');
    em.type='email'; em.placeholder='Email'; em.required=true; ph.type='tel'; ph.placeholder='Phone (optional)';
    r2.appendChild(em); r2.appendChild(ph); w.appendChild(r2);
    var send=el('button','btn btn--gold','Send my request'); send.type='button'; send.style.color='#fff'; w.appendChild(send);
    var note=el('p','conc-summary'); note.style.margin='12px 0 0'; w.appendChild(note);
    send.addEventListener('click',function(){
      if(!fn.value.trim()||!em.value.trim()){ note.textContent='Please add your first name and email.'; return; }
      send.disabled=true; send.innerHTML='<span class="conc-spin"></span>Sending…';
      var msg='AI Concierge request:\n\n"'+lastQuery+'"\n\nConcierge summary:\n'+lastSummary+'\n\nParsed criteria:\n'+JSON.stringify(lastCriteria,null,2);
      var fd=new FormData();
      fd.append('action','nops_contact'); fd.append('nops_nonce',cfg.leadNonce); fd.append('nops_website','');
      fd.append('first_name',fn.value); fd.append('last_name',ln.value); fd.append('email',em.value); fd.append('phone',ph.value);
      fd.append('interest','AI Concierge — shortlist request'); fd.append('message',msg);
      fetch(cfg.leadUrl,{method:'POST',body:fd,redirect:'manual'})
        .then(function(r){
          if(r.type==='opaqueredirect'||r.ok||r.status===0){
            send.style.display='none'; r1.style.display='none'; r2.style.display='none';
            note.textContent='Thank you — your request is on its way to Kari. She’ll be in touch personally, usually within one business day.';
          } else { send.disabled=false; send.textContent='Send my request'; note.textContent='Sorry — that didn’t go through. Please call 504-473-5969.'; }
        })
        .catch(function(){ send.style.display='none'; r1.style.display='none'; r2.style.display='none'; note.textContent='Thank you — your request is on its way to Kari.'; });
    });
    return w;
  }
})();
</script>


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
      <div class="split__media reveal"><img src="<?php echo esc_url(get_theme_file_uri('assets/nola/bordeaux-porch.jpg')); ?>" alt="A restored Victorian New Orleans home"></div>
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
      <a class="hood" href="/garden-district-homes-for-sale/" style="background-image:linear-gradient(135deg,rgba(26,24,22,.2),rgba(26,24,22,.3)),url('<?php echo esc_url(get_theme_file_uri('assets/nola/gardendistrict-nops.jpg')); ?>')"><span class="hood__label"><h3>Garden District</h3><span>Historic mansions &amp; oaks</span></span></a>
      <a class="hood" href="/uptown-homes-for-sale/" style="background-image:linear-gradient(135deg,rgba(26,24,22,.2),rgba(26,24,22,.3)),url('<?php echo esc_url(get_theme_file_uri('assets/nola/uptown-nops.jpg')); ?>')"><span class="hood__label"><h3>Uptown</h3><span>Streetcars &amp; Magazine St.</span></span></a>
      <a class="hood" href="/communities/" style="background-image:linear-gradient(135deg,rgba(26,24,22,.2),rgba(26,24,22,.3)),url('<?php echo esc_url(get_theme_file_uri('assets/nola/french-quarter-nops.jpg')); ?>')"><span class="hood__label"><h3>French Quarter</h3><span>Iron galleries &amp; courtyards</span></span></a>
      <a class="hood" href="/marigny-homes-for-sale/" style="background-image:linear-gradient(135deg,rgba(26,24,22,.2),rgba(26,24,22,.3)),url('<?php echo esc_url(get_theme_file_uri('assets/nola/marigny-nops.jpg')); ?>')"><span class="hood__label"><h3>Marigny / Bywater</h3><span>Creole cottages &amp; color</span></span></a>
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
    <div class="ig-feed" style="margin-top:40px">
      <?php echo do_shortcode('[instagram-feed feed="1"]'); ?>
    </div>
    <div class="center" style="margin-top:34px"><a href="https://www.instagram.com/nolakari1/" target="_blank" rel="noopener" class="btn btn--ghost">Follow @nolakari1</a></div>
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
    <div class="reveal" style="margin-top:48px">
      <?php echo do_shortcode('[trustindex no-registration=zillow]'); ?>
    </div>
    <div class="center" style="margin-top:40px"><a href="https://www.zillow.com/profile/nolakari" target="_blank" rel="noopener" class="btn btn--ghost">★ Read Kari's reviews on Zillow →</a></div>
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
