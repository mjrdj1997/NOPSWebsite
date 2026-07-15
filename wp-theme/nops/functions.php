<?php
if (!defined('ABSPATH')) exit;

/**
 * New Orleans Property Services — theme setup
 */
function nops_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus([
        'primary' => 'Primary Navigation',
    ]);
}
add_action('after_setup_theme', 'nops_setup');

/**
 * Enqueue fonts, the approved stylesheet, and the interaction JS.
 */
function nops_assets() {
    $ver = wp_get_theme()->get('Version');
    wp_enqueue_style(
        'nops-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );
    wp_enqueue_style('nops-main', get_theme_file_uri('assets/main.css'), [], $ver);
    wp_enqueue_script('nops-main', get_theme_file_uri('assets/main.js'), [], $ver, true);
}
add_action('wp_enqueue_scripts', 'nops_assets');

/**
 * Static fallback nav — renders the approved menu as plain <a> tags
 * (matches the CSS) until a WordPress menu is assigned to "primary".
 * The Contact item is styled as the gold pill button.
 */
function nops_primary_nav() {
    if (has_nav_menu('primary')) {
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'walker'         => new NOPS_Link_Walker(),
            'fallback_cb'    => 'nops_fallback_nav',
        ]);
    } else {
        nops_fallback_nav();
    }
}

function nops_fallback_nav() {
    $items = [
        '/'             => 'Home',
        '/buy/'         => 'Buy',
        '/sell/'        => 'Sell',
        '/communities/' => 'Communities',
        '/journal/'     => 'Journal',
        '/about/'       => 'About',
    ];
    $current = untrailingslashit(home_url(add_query_arg([], $GLOBALS['wp']->request ?? '')));
    foreach ($items as $path => $label) {
        $url    = home_url($path);
        $active = (untrailingslashit($url) === $current) ? ' class="active"' : '';
        printf('<a href="%s"%s>%s</a>', esc_url($url), $active, esc_html($label));
    }
    printf('<a href="%s" class="btn btn--gold" style="color:#fff">Contact</a>', esc_url(home_url('/contact/')));
}

/**
 * Walker that outputs bare <a> elements (no <ul>/<li>) so an admin-managed
 * menu matches the flat flex nav in the design.
 */
class NOPS_Link_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {}
    function end_lvl(&$output, $depth = 0, $args = null) {}
    function end_el(&$output, $item, $depth = 0, $args = null) {}
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $is_current = in_array('current-menu-item', $classes, true);
        $is_contact = stripos($item->title, 'contact') !== false;
        $attr  = $is_contact ? ' class="btn btn--gold" style="color:#fff"' : ($is_current ? ' class="active"' : '');
        $output .= sprintf('<a href="%s"%s>%s</a>', esc_url($item->url), $attr, esc_html($item->title));
    }
}
