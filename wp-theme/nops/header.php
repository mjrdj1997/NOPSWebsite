<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?php echo esc_url(get_theme_file_uri('assets/logo.png')); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div class="topbar"><div class="container">
  <div class="topbar__left">
    <span>&#128222; <a href="tel:5044735969">504-473-5969</a></span>
    <span>&#9993; <a href="mailto:kari@neworleanspropertyservices.com">kari@neworleanspropertyservices.com</a></span>
    <span>&#128205; 2801 St. Charles Ave, Unit 111B, New Orleans, LA 70115</span>
  </div>
  <div class="topbar__social"><a href="https://www.instagram.com/nolakari1/" target="_blank" rel="noopener">Instagram</a><a href="https://www.facebook.com/neworleanspropertyservices" target="_blank" rel="noopener">Facebook</a><a href="https://www.linkedin.com/in/kari-ayala-2705446/" target="_blank" rel="noopener">LinkedIn</a></div>
</div></div>

<header class="site-header"><div class="container">
  <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
    <img class="brand__logo" src="<?php echo esc_url(get_theme_file_uri('assets/logo-dark.png')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
  </a>
  <nav class="nav" id="nav"><?php nops_primary_nav(); ?></nav>
  <button class="nav-toggle" aria-label="Menu"><span></span><span></span><span></span></button>
</div></header>
