<?php if (!defined('ABSPATH')) exit; ?>

<footer class="site-footer"><div class="container">
  <div class="footer-grid">
    <div class="footer-brand">
      <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><img class="brand__logo brand__logo--footer" src="<?php echo esc_url(get_theme_file_uri('assets/logo.png')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"></a>
      <p>A boutique New Orleans brokerage delivering white-glove service to buyers, sellers, and investors since 2007.</p>
      <div class="footer-social">
        <a href="https://www.instagram.com/nolakari1/" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a>
        <a href="https://www.facebook.com/neworleanspropertyservices" target="_blank" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V6h-3c-1.7 0-3 1.3-3 3v2H8v3h3v6h3v-6h3l1-3h-4V9c0-.6.4-1 1-1Z"/></svg></a>
        <a href="https://www.linkedin.com/in/kari-ayala-2705446/" target="_blank" rel="noopener" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.5 8A1.5 1.5 0 1 0 6.5 5a1.5 1.5 0 0 0 0 3ZM5 10h3v9H5zM10 10h3v1.3c.5-.8 1.6-1.5 3-1.5 2.3 0 3 1.5 3 3.8V19h-3v-4.6c0-1.1-.4-1.8-1.4-1.8-1 0-1.6.7-1.6 1.8V19h-3z"/></svg></a>
      </div>
    </div>
    <div><h4>Explore</h4><ul class="footer-links">
      <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
      <li><a href="<?php echo esc_url(home_url('/buy/')); ?>">Buy a Home</a></li>
      <li><a href="<?php echo esc_url(home_url('/sell/')); ?>">Sell a Home</a></li>
      <li><a href="<?php echo esc_url(home_url('/communities/')); ?>">Communities</a></li>
      <li><a href="<?php echo esc_url(home_url('/journal/')); ?>">Journal</a></li>
      <li><a href="<?php echo esc_url(home_url('/about/')); ?>">About Kari</a></li>
    </ul></div>
    <div><h4>Services</h4><ul class="footer-links">
      <li><a href="<?php echo esc_url(home_url('/buy/')); ?>">Buyer Representation</a></li>
      <li><a href="<?php echo esc_url(home_url('/sell/')); ?>">Seller Representation</a></li>
      <li><a href="<?php echo esc_url(home_url('/buy/')); ?>">Investment Property</a></li>
      <li><a href="<?php echo esc_url(home_url('/communities/')); ?>">Historic Homes</a></li>
      <li><a href="<?php echo esc_url(home_url('/sell/')); ?>">Home Valuation</a></li>
    </ul></div>
    <div><h4>Contact</h4><ul class="footer-links">
      <li><a href="tel:5044735969">504-473-5969</a></li>
      <li><a href="mailto:kari@neworleanspropertyservices.com">kari@neworleanspropertyservices.com</a></li>
      <li>2801 St. Charles Ave, Unit 111B<br>New Orleans, LA 70115</li>
    </ul></div>
  </div>
  <div class="footer-bottom">
    <span>&copy; <?php echo esc_html(date('Y')); ?> New Orleans Property Services, LLC. All rights reserved.</span>
    <span>Licensed Real Estate Brokerage &middot; Equal Housing Opportunity &#127968;</span>
  </div>
</div></footer>

<?php wp_footer(); ?>
</body>
</html>
