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
    $cssv = filemtime(get_theme_file_path('assets/main.css'));
    $jsv  = filemtime(get_theme_file_path('assets/main.js'));
    wp_enqueue_style('nops-main', get_theme_file_uri('assets/main.css'), [], $cssv);
    wp_enqueue_script('nops-main', get_theme_file_uri('assets/main.js'), [], $jsv, true);
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
        '/'                => 'Home',
        '/listing-search/' => 'Search',
        '/buy/'            => 'Buy',
        '/sell/'           => 'Sell',
        '/communities/'    => 'Communities',
        '/journal/'        => 'Journal',
        '/about/'          => 'About',
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

    // Branded HTML auto-reply / confirmation to the submitter.
    $greet = $first !== '' ? esc_html($first) : 'there';
    // Hard-code the real domain (over HTTPS) so the link text and destination match a
    // legitimate domain. Deriving this from home_url() yields the bare server IP until DNS
    // cutover, and a friendly-text-hiding-a-raw-IP link is a textbook phishing signal that
    // gets the whole message quarantined/deferred by spam filters (esp. Microsoft 365).
    $site  = 'https://neworleanspropertyservices.com/';
    $ack_html = <<<HTML
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f0e8;padding:24px 0;margin:0;font-family:Arial,Helvetica,sans-serif">
  <tr><td align="center">
    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e8e1d4">
      <tr><td align="center" style="background:#1a1816;padding:28px 24px">
        <div style="font-size:26px;color:#cd8c38;line-height:1;margin-bottom:6px">&#9884;</div>
        <div style="font-family:Georgia,'Times New Roman',serif;font-size:22px;color:#ffffff;letter-spacing:.5px">New Orleans <span style="color:#cd8c38">Property Services</span></div>
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:10px;color:#a49c92;letter-spacing:2.5px;text-transform:uppercase;margin-top:7px">Boutique Real Estate &middot; Est. 2007</div>
      </td></tr>
      <tr><td style="padding:34px 40px 6px">
        <h1 style="margin:0 0 16px;font-family:Georgia,'Times New Roman',serif;font-size:24px;line-height:1.25;color:#1a1816;font-weight:normal">Thank you, {$greet}.</h1>
        <p style="margin:0 0 16px;font-size:16px;line-height:1.6;color:#403c38">I've received your message and will get back to you <strong>personally</strong> &mdash; usually within one business day.</p>
        <p style="margin:0 0 26px;font-size:16px;line-height:1.6;color:#403c38">If it's time-sensitive, feel free to call or text me directly at <a href="tel:5044735969" style="color:#cd8c38;text-decoration:none;font-weight:bold">504-473-5969</a>.</p>
        <table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="background:#cd8c38;border-radius:40px">
          <a href="tel:5044735969" style="display:inline-block;padding:13px 30px;color:#ffffff;text-decoration:none;font-size:15px;font-weight:bold">Call 504-473-5969</a>
        </td></tr></table>
      </td></tr>
      <tr><td style="padding:26px 40px 34px">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td style="border-top:1px solid #e8e1d4;padding-top:22px">
          <p style="margin:0;font-family:Georgia,serif;font-size:18px;color:#1a1816">Kari Ayala</p>
          <p style="margin:3px 0 0;font-size:12px;color:#6b6560;text-transform:uppercase;letter-spacing:1.5px">Broker &amp; Owner &middot; New Orleans Property Services</p>
          <p style="margin:14px 0 0;font-size:14px;color:#403c38"><a href="tel:5044735969" style="color:#403c38;text-decoration:none">504-473-5969</a> &nbsp;&middot;&nbsp; <a href="{$site}" style="color:#cd8c38;text-decoration:none">neworleanspropertyservices.com</a></p>
        </td></tr></table>
      </td></tr>
      <tr><td align="center" style="background:#1a1816;padding:16px 24px">
        <p style="margin:0;font-size:11px;color:#a49c92;line-height:1.6">2801 St. Charles Ave, Unit 111B, New Orleans, LA 70115<br>Licensed Real Estate Brokerage &middot; Equal Housing Opportunity</p>
      </td></tr>
    </table>
  </td></tr>
</table>
HTML;
    $ack_headers = ['Content-Type: text/html; charset=UTF-8', 'Reply-To: ' . $to];
    wp_mail($email, 'Thanks for reaching out — New Orleans Property Services', $ack_html, $ack_headers);

    wp_safe_redirect(home_url('/contact/?sent=1'));
    exit;
}
add_action('admin_post_nops_contact', 'nops_handle_contact');
add_action('admin_post_nopriv_nops_contact', 'nops_handle_contact');

/* ------------------------------------------------------------------
 * AI Home Concierge — natural-language home search powered by the
 * Anthropic API. Public REST endpoint (nops/v1/concierge) calls Claude
 * server-side (key in wp-config, never exposed to the browser), returns a
 * warm reply in Kari's voice + structured criteria mapped to a Buying
 * Buddy IDX search. Fair-Housing guardrails are enforced in the prompt.
 * ------------------------------------------------------------------ */
add_action('rest_api_init', function () {
    register_rest_route('nops/v1', '/concierge', [
        'methods'             => 'POST',
        'callback'            => 'nops_concierge_handler',
        'permission_callback' => '__return_true',
    ]);
});

function nops_client_ip() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function nops_concierge_handler(WP_REST_Request $req) {
    // CSRF: require a valid REST nonce issued to the page.
    if (!wp_verify_nonce($req->get_header('X-WP-Nonce'), 'wp_rest')) {
        return new WP_REST_Response(['error' => 'Your session expired — please refresh the page and try again.'], 403);
    }
    $p = $req->get_json_params();
    if (!empty($p['website'])) { // honeypot
        return new WP_REST_Response(['error' => 'Request could not be processed.'], 400);
    }
    $text = trim((string) ($p['q'] ?? ''));
    if ($text === '') {
        return new WP_REST_Response(['error' => 'Please describe the home you have in mind.'], 400);
    }
    if (mb_strlen($text) > 600) $text = mb_substr($text, 0, 600);

    // Per-IP rate limit: 8 requests/hour.
    $rk  = 'nops_conc_rl_' . md5(nops_client_ip());
    $cnt = (int) get_transient($rk);
    if ($cnt >= 8) {
        return new WP_REST_Response(['error' => "You've reached the concierge limit for now. Please try again later, or call Kari directly at 504-473-5969."], 429);
    }
    // Global monthly call ceiling (cost guard).
    $bk   = 'nops_conc_month_' . gmdate('Y_m');
    $mcnt = (int) get_option($bk, 0);
    if ($mcnt >= 3000) {
        return new WP_REST_Response(['error' => 'Our AI concierge is resting for the moment — please use the search above or contact Kari directly.'], 503);
    }

    $result = nops_concierge_generate($text);
    if (is_wp_error($result)) {
        return new WP_REST_Response(['error' => $result->get_error_message()], 502);
    }

    // Count only successful calls.
    set_transient($rk, $cnt + 1, HOUR_IN_SECONDS);
    update_option($bk, $mcnt + 1, false);

    return new WP_REST_Response($result, 200);
}

/**
 * Core: call Claude with the visitor's request; return an array of
 * [summary, followup, criteria, search_url] or a WP_Error.
 */
function nops_concierge_generate($text) {
    $key = defined('ANTHROPIC_API_KEY') ? ANTHROPIC_API_KEY : '';
    if (!$key) return new WP_Error('noconfig', 'The concierge is not configured yet.');

    $tool = [
        'name'         => 'present_home_search',
        'description'  => "Return a warm reply in Kari's voice plus the structured home-search criteria you extracted.",
        'input_schema' => [
            'type'       => 'object',
            'properties' => [
                'summary'           => ['type' => 'string', 'description' => "A warm, gracious 2-4 sentence reply in Kari's voice that reflects back what the visitor is looking for and invites next steps. Never state specific listings, prices, or availability."],
                'neighborhoods'     => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Matched New Orleans neighborhoods, only from the allowed list in the system prompt.'],
                'price_min'         => ['type' => 'integer'],
                'price_max'         => ['type' => 'integer'],
                'beds_min'          => ['type' => 'integer'],
                'baths_min'         => ['type' => 'number'],
                'property_type'     => ['type' => 'string', 'enum' => ['any', 'single-family', 'historic', 'condo', 'townhouse', 'multi-family', 'investment']],
                'features'          => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Objective, property-related features requested (e.g. courtyard, off-street parking, renovated kitchen, pool).'],
                'followup_question' => ['type' => 'string', 'description' => 'One short clarifying question if the request is too vague to search; otherwise an empty string.'],
            ],
            'required'   => ['summary'],
        ],
    ];

    $body = [
        'model'       => 'claude-haiku-4-5-20251001',
        'max_tokens'  => 800,
        'system'      => nops_concierge_system_prompt(),
        'tools'       => [$tool],
        'tool_choice' => ['type' => 'tool', 'name' => 'present_home_search'],
        'messages'    => [['role' => 'user', 'content' => $text]],
    ];

    $resp = wp_remote_post('https://api.anthropic.com/v1/messages', [
        'headers' => [
            'x-api-key'         => $key,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ],
        'body'    => wp_json_encode($body),
        'timeout' => 30,
    ]);

    if (is_wp_error($resp)) {
        return new WP_Error('net', 'The concierge is momentarily unavailable. Please try the search above.');
    }
    $code = wp_remote_retrieve_response_code($resp);
    $data = json_decode(wp_remote_retrieve_body($resp), true);
    if ($code !== 200 || empty($data['content'])) {
        return new WP_Error('api', 'The concierge is momentarily unavailable. Please try the search above.');
    }

    $criteria = null;
    foreach ($data['content'] as $block) {
        if (($block['type'] ?? '') === 'tool_use' && ($block['name'] ?? '') === 'present_home_search') {
            $criteria = $block['input'];
            break;
        }
    }
    if (!is_array($criteria) || empty($criteria['summary'])) {
        return new WP_Error('parse', 'Sorry — I could not quite parse that. Try naming the area, budget, and size (for example, a 3-bed in Mid-City under 500k).');
    }

    return [
        'summary'    => (string) $criteria['summary'],
        'followup'   => (string) ($criteria['followup_question'] ?? ''),
        'criteria'   => $criteria,
        'search_url' => nops_concierge_search_url($criteria),
    ];
}

function nops_concierge_system_prompt() {
    return <<<'PROMPT'
You are the AI home concierge for New Orleans Property Services, the boutique brokerage of Kari Ayala (a New Orleans REALTOR since 1998; firm established 2007). Your job is to warmly help a website visitor describe the New Orleans home they want and translate it into a home search.

VOICE: gracious, warm, concise, white-glove — like a trusted local friend who knows every block of New Orleans. Write the summary in the first person on behalf of Kari's team ("we"/"I"), 2 to 4 sentences. Do NOT invent or state specific listings, addresses, prices, or availability — you are setting up a search, not quoting inventory.

FAIR HOUSING — STRICT AND NON-NEGOTIABLE: You must comply with the U.S. Fair Housing Act. NEVER steer, rank, or make recommendations based on — or on proxies for — race, color, religion, national origin, sex, familial status, or disability. Do NOT describe or compare neighborhoods by safety or crime, "good" or "bad" schools, religion or houses of worship, or the kinds of people who live there, and do not fulfill such requests. If a visitor asks for any of that, gently decline that part and redirect to objective criteria you CAN help with: location and geography, price, size (beds/baths), home style, and property features (such as walkability to a named place, a courtyard, or off-street parking). Keep everything about the property and objective geography.

NEIGHBORHOODS: Only use New Orleans-area neighborhood names from this list: Garden District, Lower Garden District, Uptown, Carrollton, Irish Channel, French Quarter, Marigny, Bywater, Treme, Mid-City, Lakeview, Gentilly, Algiers Point, Warehouse District, Central Business District. If the visitor names a place outside greater New Orleans, gently note that New Orleans Property Services focuses on the New Orleans area and steer back.

Always respond by calling the present_home_search tool with a friendly summary and whatever criteria you can extract. If the request is too vague to search, still provide a warm summary and set followup_question to one short clarifying question.
PROMPT;
}

/**
 * Single source of truth for Buying Buddy IDX hand-off URLs. Two distinct pages:
 *   /listing-results/  = [mbb_widget data-type="ListingResults"] — the results
 *                        grid; this is the ONLY page that reads ?filter=.
 *   /listing-search/   = [mbb_widget data-type="SearchForm"]    — just the form,
 *                        which ignores ?filter=.
 * So a criteria-carrying search MUST target /listing-results/?filter=..., while a
 * bare "browse listings" link goes to the search form. The quick-search form and
 * the AI concierge both route through here so they can never drift apart again.
 */
function nops_listing_url($filter = '') {
    if ($filter) return home_url('/listing-results/') . '?filter=' . $filter;
    return home_url('/listing-search/');
}

function nops_concierge_search_url($c) {
    $f = [];
    if (!empty($c['price_min'])) $f[] = 'price_min:' . (int) $c['price_min'];
    if (!empty($c['price_max'])) $f[] = 'price_max:' . (int) $c['price_max'];
    if (!empty($c['beds_min']))  $f[] = 'bedrooms_total_min:' . (int) $c['beds_min'];
    if (!empty($c['baths_min'])) $f[] = 'baths_total_min:' . (int) $c['baths_min'];
    // Buying Buddy reads its filter from ?filter=key:value+key:value on the results page.
    // Only universal numeric keys are mapped here; neighborhood/property-type mapping needs
    // GSREIN area/type codes and is added once the live feed is on (currently pending approval).
    return nops_listing_url($f ? implode('+', $f) : '');
}

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
        '@id'        => home_url('/#agent'),
        'name'       => 'New Orleans Property Services',
        'image'      => get_theme_file_uri('assets/logo.jpg'),
        'logo'       => get_theme_file_uri('assets/logo.jpg'),
        'url'        => home_url('/'),
        'telephone'  => '+1-504-473-5969',
        'priceRange' => '$$',
        'areaServed' => [
            ['@type' => 'City', 'name' => 'New Orleans, LA'],
            'Garden District', 'Uptown New Orleans', 'French Quarter', 'Marigny',
            'Bywater', 'Lakeview', 'Mid-City', 'Irish Channel', 'Warehouse District',
        ],
        'address'    => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => '2801 St. Charles Ave, Unit 111B',
            'addressLocality' => 'New Orleans',
            'addressRegion'   => 'LA',
            'postalCode'      => '70115',
            'addressCountry'  => 'US',
        ],
        'geo'        => ['@type' => 'GeoCoordinates', 'latitude' => 29.9268, 'longitude' => -90.0870],
        'founder'    => ['@type' => 'Person', 'name' => 'Kari Kramer Ayala'],
        'sameAs'     => [
            'https://www.instagram.com/nolakari1/',
            'https://www.facebook.com/neworleanspropertyservices',
            'https://www.linkedin.com/in/kari-ayala-2705446/',
            'https://www.zillow.com/profile/nolakari',
        ],
    ];
    echo "\n<script type=\"application/ld+json\">" . wp_json_encode($data) . "</script>\n";
}
add_action('wp_head', 'nops_local_schema', 20);

/* Article (BlogPosting) schema on single Journal posts. */
function nops_article_schema() {
    if (!is_singular('post')) return;
    $post = get_queried_object();
    $img  = get_the_post_thumbnail_url($post, 'full');
    if (!$img) $img = get_theme_file_uri('assets/logo.jpg');
    $data = [
        '@context'         => 'https://schema.org',
        '@type'            => 'BlogPosting',
        'headline'         => get_the_title($post),
        'description'      => wp_strip_all_tags(get_the_excerpt($post)),
        'image'            => $img,
        'datePublished'    => get_the_date('c', $post),
        'dateModified'     => get_the_modified_date('c', $post),
        'author'           => ['@type' => 'Person', 'name' => 'Kari Kramer Ayala', 'url' => home_url('/about/')],
        'publisher'        => [
            '@type' => 'Organization',
            'name'  => 'New Orleans Property Services',
            'logo'  => ['@type' => 'ImageObject', 'url' => get_theme_file_uri('assets/logo.jpg')],
        ],
        'mainEntityOfPage' => get_permalink($post),
    ];
    echo "\n<script type=\"application/ld+json\">" . wp_json_encode($data) . "</script>\n";
}
add_action('wp_head', 'nops_article_schema', 21);

/* BreadcrumbList schema on inner pages/posts. */
function nops_breadcrumb_schema() {
    if (is_front_page()) return;
    $items = [['name' => 'Home', 'url' => home_url('/')]];
    if (is_singular('post')) {
        $items[] = ['name' => 'Journal', 'url' => home_url('/journal/')];
        $items[] = ['name' => get_the_title(), 'url' => get_permalink()];
    } elseif (is_page()) {
        $items[] = ['name' => get_the_title(), 'url' => get_permalink()];
    } elseif (is_home()) {
        $items[] = ['name' => 'Journal', 'url' => home_url('/journal/')];
    } else {
        return;
    }
    $list = [];
    foreach ($items as $i => $it) {
        $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $it['name'], 'item' => $it['url']];
    }
    $data = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list];
    echo "\n<script type=\"application/ld+json\">" . wp_json_encode($data) . "</script>\n";
}
add_action('wp_head', 'nops_breadcrumb_schema', 22);

/* ------------------------------------------------------------------
 * Google Analytics 4. Outputs the gtag snippet only when a valid
 * Measurement ID is stored in the `nops_ga4_id` option, and never
 * for logged-in editors/admins (keeps their visits out of the data).
 * Enable with:  wp option update nops_ga4_id G-XXXXXXXXXX
 * ------------------------------------------------------------------ */
function nops_analytics() {
    $id = get_option('nops_ga4_id', '');
    if (!$id || !preg_match('/^G-[A-Z0-9]+$/', $id)) return;
    if (is_user_logged_in() && current_user_can('edit_posts')) return;
    ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($id); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo esc_js($id); ?>');
</script>
<?php
}
add_action('wp_head', 'nops_analytics', 5);
