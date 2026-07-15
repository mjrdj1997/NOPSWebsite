<?php if (!defined('ABSPATH')) exit; get_header(); ?>

<section class="page-hero"><div class="container">
  <div class="crumbs"><a href="/">Home</a> / Contact</div>
  <p class="eyebrow" style="color:var(--brass-soft)">Get in Touch</p>
  <h1>Let's start a conversation.</h1>
  <p class="lead">Buying, selling, investing, or just exploring — reach out and Kari will get back to you personally.</p>
</div></section>

<section class="section"><div class="container">
  <div class="contact-grid">
    <div class="reveal">
      <p class="eyebrow">Contact Details</p>
      <h2 style="margin-bottom:8px">We'd love to hear from you.</h2>
      <p class="lead" style="font-size:1.05rem">You'll work directly with Kari — no call centers, no runaround.</p>
      <div style="margin-top:20px">
        <div class="info-row"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5c0 8 7 15 15 15l1.5-3.5-4-1.5-1.5 2c-3-1.5-5-3.5-6.5-6.5l2-1.5-1.5-4z"/></svg></span><div><h4>Phone</h4><a href="tel:5044735969">504-473-5969</a></div></div>
        <div class="info-row"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><div><h4>Email</h4><a href="mailto:kari@neworleanspropertyservices.com">kari@neworleanspropertyservices.com</a></div></div>
        <div class="info-row"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg></span><div><h4>Office</h4><p>2801 St. Charles Ave, Unit 111B<br>New Orleans, LA 70115</p></div></div>
        <div class="info-row"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><div><h4>Hours</h4><p>Mon–Sat, 9am–6pm · By appointment</p></div></div>
      </div>
    </div>

    <div class="reveal">
      <div class="card" style="padding:36px 34px">
        <h3 style="margin-bottom:6px">Send a message</h3>
        <p style="color:var(--muted);font-size:.95rem;margin-bottom:22px">Tell us a little about what you're looking for.</p>
        <?php if (isset($_GET['sent'])) : ?>
          <p class="form-note" style="color:#2f7d4f;font-weight:600;background:#eef7f0;border:1px solid #cde8d5;padding:14px 16px;border-radius:8px;margin-bottom:18px">Thank you — your message has been sent. Kari will get back to you personally.</p>
        <?php elseif (isset($_GET['err'])) : ?>
          <p class="form-note" style="color:#b3402f;font-weight:600;background:#fbeeec;border:1px solid #f0cfc9;padding:14px 16px;border-radius:8px;margin-bottom:18px">Sorry — something went wrong. Please call 504-473-5969 or email us directly.</p>
        <?php endif; ?>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
          <input type="hidden" name="action" value="nops_contact">
          <?php wp_nonce_field('nops_contact', 'nops_nonce'); ?>
          <div style="position:absolute;left:-9999px" aria-hidden="true"><label>Leave this blank<input type="text" name="nops_website" tabindex="-1" autocomplete="off"></label></div>
          <div class="form-row">
            <div><label class="fl">First name</label><input type="text" name="first_name" required placeholder="Jane"></div>
            <div><label class="fl">Last name</label><input type="text" name="last_name" required placeholder="Doe"></div>
          </div>
          <div class="form-row">
            <div><label class="fl">Email</label><input type="email" name="email" required placeholder="jane@email.com"></div>
            <div><label class="fl">Phone</label><input type="tel" name="phone" placeholder="(504) 000-0000"></div>
          </div>
          <div><label class="fl">I'm interested in</label>
            <select name="interest"><option>Buying a home</option><option>Selling a home</option><option>Investment property</option><option>A home valuation</option><option>Relocation to New Orleans</option><option>Just have a question</option></select>
          </div>
          <div><label class="fl">Message</label><textarea name="message" placeholder="Tell us what you have in mind..."></textarea></div>
          <button class="btn btn--gold" type="submit" style="justify-content:center">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</div></section>

<?php get_footer(); ?>
