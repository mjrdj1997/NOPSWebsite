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

/* ------------------------------------------------------------------
 * Contact form: store every submission (so nothing is lost even if
 * email delivery isn't configured yet) and email the site owner.
 * ------------------------------------------------------------------ */
function nops_register_inquiry_cpt() {
    register_post_type('nops_inquiry', [
        'labels'    => ['name' => 'Inquiries', 'singular_name' => 'Inquiry'],
        'public'    => false,
        'show_ui'   => true,
        'menu_icon' => 'dashicons-email-alt',
        'supports'  => ['title', 'editor'],
    ]);
}
add_action('init', 'nops_register_inquiry_cpt');

function nops_handle_contact() {
    // Honeypot: silently accept bots without doing anything.
    if (!empty($_POST['nops_website'])) { wp_safe_redirect(home_url('/contact/?sent=1')); exit; }
    if (!isset($_POST['nops_nonce']) || !wp_verify_nonce($_POST['nops_nonce'], 'nops_contact')) {
        wp_safe_redirect(home_url('/contact/?err=1')); exit;
    }
    $first    = sanitize_text_field($_POST['first_name'] ?? '');
    $last     = sanitize_text_field($_POST['last_name'] ?? '');
    $email    = sanitize_email($_POST['email'] ?? '');
    $phone    = sanitize_text_field($_POST['phone'] ?? '');
    $interest = sanitize_text_field($_POST['interest'] ?? '');
    $message  = sanitize_textarea_field($_POST['message'] ?? '');
    $name     = trim("$first $last");
    if ($name === '' || !is_email($email)) { wp_safe_redirect(home_url('/contact/?err=1')); exit; }

    $body = "New website inquiry\n\n"
          . "Name: $name\nEmail: $email\nPhone: $phone\nInterested in: $interest\n\n"
          . "Message:\n$message\n";

    // Persist a copy in wp-admin -> Inquiries.
    wp_insert_post([
        'post_type'    => 'nops_inquiry',
        'post_status'  => 'private',
        'post_title'   => "$name — $interest",
        'post_content' => $body,
    ]);

    // Email the owner (recipient filterable; defaults to the WP admin email).
    $to      = apply_filters('nops_contact_recipient', get_option('admin_email'));
    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    wp_mail($to, "Website inquiry from $name", $body, $headers);

    wp_safe_redirect(home_url('/contact/?sent=1'));
    exit;
}
add_action('admin_post_nops_contact', 'nops_handle_contact');
add_action('admin_post_nopriv_nops_contact', 'nops_handle_contact');

/* ------------------------------------------------------------------
 * SEO: per-page meta description, canonical, Open Graph, Twitter card.
 * (WordPress core already outputs <title> via title-tag support.)
 * ------------------------------------------------------------------ */
function nops_meta_description() {
    if (is_front_page()) {
        return 'Boutique New Orleans real estate brokerage founded in 2007 by Kari Ayala. White-glove service for buyers, sellers & investors — historic homes and local expertise. Call 504-473-5969.';
    }
    if (is_page('buy'))         return 'Find and win the right New Orleans home with boutique, white-glove buyer representation from Kari Ayala. Search MLS listings and start your home search.';
    if (is_page('sell'))        return 'Sell your New Orleans home for the most the market will bear — strategic pricing, standout marketing, and skilled negotiation from Kari Ayala.';
    if (is_page('communities')) return 'Explore New Orleans neighborhoods — Garden District, Uptown, French Quarter, Marigny, Bywater, Lakeview, Mid-City and more — with local expert Kari Ayala.';
    if (is_page('about'))       return 'Kari Kramer Ayala founded New Orleans Property Services in 2007. A Fulbright Scholar and lifelong New Orleanian delivering white-glove boutique real estate.';
    if (is_page('contact'))     return 'Contact Kari Ayala at New Orleans Property Services. Call 504-473-5969 or send a message to start buying, selling, or investing in New Orleans real estate.';
    if (is_home())              return 'Local real estate insight, neighborhood guides, and New Orleans market updates from boutique broker Kari Ayala.';
    if (is_singular())          return wp_strip_all_tags(get_the_excerpt());
    return get_bloginfo('description');
}

function nops_seo_meta() {
    $desc  = trim(preg_replace('/\s+/', ' ', nops_meta_description()));
    $title = wp_get_document_title();
    $url   = home_url(add_query_arg([], $GLOBALS['wp']->request ?? ''));
    $img   = get_theme_file_uri('assets/logo.jpg');
    echo "\n<meta name=\"description\" content=\"" . esc_attr($desc) . "\">\n";
    echo '<link rel="canonical" href="' . esc_url($url) . "\">\n";
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:site_name" content="New Orleans Property Services">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . "\">\n";
    echo '<meta property="og:description" content="' . esc_attr($desc) . "\">\n";
    echo '<meta property="og:url" content="' . esc_url($url) . "\">\n";
    echo '<meta property="og:image" content="' . esc_url($img) . "\">\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action('wp_head', 'nops_seo_meta', 1);

function nops_local_schema() {
    $data = [
        '@context'   => 'https://schema.org',
        '@type'      => 'RealEstateAgent',
        'name'       => 'New Orleans Property Services',
        'image'      => get_theme_file_uri('assets/logo.jpg'),
        'url'        => home_url('/'),
        'telephone'  => '+1-504-473-5969',
        'priceRange' => '$$',
        'areaServed' => 'New Orleans, LA',
        'address'    => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => '2801 St. Charles Ave, Unit 111B',
            'addressLocality' => 'New Orleans',
            'addressRegion'   => 'LA',
            'postalCode'      => '70115',
            'addressCountry'  => 'US',
        ],
        'founder'    => ['@type' => 'Person', 'name' => 'Kari Kramer Ayala'],
        'sameAs'     => [
            'https://www.instagram.com/nolakari1/',
            'https://www.linkedin.com/in/kari-ayala-2705446/',
        ],
    ];
    echo "\n<script type=\"application/ld+json\">" . wp_json_encode($data) . "</script>\n";
}
add_action('wp_head', 'nops_local_schema', 20);
