<?php if (!defined('ABSPATH')) exit; ?>

<footer class="site-footer"><div class="container">
  <div class="footer-grid">
    <div class="footer-brand">
      <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><img class="brand__logo brand__logo--footer" src="<?php echo esc_url(get_theme_file_uri('assets/logo.png')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"></a>
      <p>A boutique New Orleans brokerage delivering white-glove service to buyers, sellers, and investors since 2007.</p>
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
