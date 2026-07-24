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

/* ------------------------------------------------------------------
 * Contact-form anti-spam. Layered, no third-party service required:
 *   1. honeypot            — naive bots
 *   2. signed form token   — instant submits, day-old replayed forms, and
 *                            re-posting one page load over and over
 *   3. per-IP rate limit   — turns a flood into at most a trickle
 *   4. content scoring     — link spam, non-English blasts, and the
 *                            "ChristophervenGM"-style generated names
 * A submission that scores as spam is still SAVED to Inquiries (title
 * prefixed [SPAM]) so a false positive is never lost, but it sends NO email
 * to Kari and NO auto-reply to the submitted address — auto-replying to a
 * forged address is backscatter and damages the sending domain's reputation.
 * ------------------------------------------------------------------ */
function nops_form_token_hash($t) {
    return hash_hmac('sha256', 'nops_contact|' . $t, wp_salt('nonce'));
}

/** Hidden fields stamping when this form was rendered. Echo inside every form. */
function nops_form_token_fields() {
    $t = time();
    return '<input type="hidden" name="nops_t" value="' . esc_attr($t) . '">'
         . '<input type="hidden" name="nops_k" value="' . esc_attr(nops_form_token_hash($t)) . '">';
}

/** '' when the token is good, otherwise a short reason. Counts each use. */
function nops_form_token_problem() {
    $t = isset($_POST['nops_t']) ? absint($_POST['nops_t']) : 0;
    $k = isset($_POST['nops_k']) ? sanitize_text_field(wp_unslash($_POST['nops_k'])) : '';
    if (!$t || !$k || !hash_equals(nops_form_token_hash($t), $k)) return 'bad-token';
    $age = time() - $t;
    if ($age < 4) return 'too-fast';                       // no human fills this in 4s
    if ($age > 12 * HOUR_IN_SECONDS) return 'stale-form';
    $key  = 'nops_tok_' . md5($k);
    $used = (int) get_transient($key);
    if ($used >= 3) return 'token-reuse';                  // one page load, many posts
    set_transient($key, $used + 1, 12 * HOUR_IN_SECONDS);
    return '';
}

/** True once this IP passes 3 submissions an hour or 8 a day. */
function nops_contact_rate_exceeded($ip) {
    $hk = 'nops_cf_h_' . md5($ip);
    $dk = 'nops_cf_d_' . md5($ip);
    $h  = (int) get_transient($hk);
    $d  = (int) get_transient($dk);
    if ($h >= 3 || $d >= 8) return true;
    set_transient($hk, $h + 1, HOUR_IN_SECONDS);
    set_transient($dk, $d + 1, DAY_IN_SECONDS);
    return false;
}

/** Weighted spam signals for one submission. Total >= 4 is treated as spam. */
function nops_spam_signals($f) {
    $out  = [];
    $name = $f['name'];
    $msg  = $f['message'];
    $blob = $name . ' ' . $msg;

    // Real inquiries almost never contain links; spam almost always does.
    $links = preg_match_all('#(https?://|www\.)#i', $msg);
    if ($links >= 1) $out['links'] = 3;
    if ($links >= 3) $out['many-links'] = 3;
    if (preg_match('#\[url|\[/url\]|<a\s|</a>#i', $blob)) $out['markup'] = 4;

    // Generated names: "ChristophervenGM", "JosephTycleZG".
    if (preg_match('/[a-z]{2}[A-Z]{2}/', $name)) $out['name-pattern'] = 5;
    if ($f['first'] !== '' && strcasecmp($f['first'], $f['last']) === 0) $out['name-repeat'] = 2;

    // Blasts in another language/script — Kari's market writes in English.
    $chars = function_exists('mb_strlen') ? mb_strlen($msg, 'UTF-8') : strlen($msg);
    if ($chars >= 10) {
        $ascii = strlen(preg_replace('/[^\x00-\x7F]/u', '', $msg));
        if (($chars - $ascii) / $chars > 0.08) $out['non-english'] = 4;
    }

    $spammy = '/\b(seo services?|backlinks?|link building|guest post|sponsored post|write for us|'
            . 'crypto(currency)?|bitcoin|casino|viagra|escort|web visitors into leads|'
            . 'ericjonesmyemail|blastleadgeneration|rank your (web)?site|increase your traffic|'
            . 'digital marketing agency|telegram)\b/i';
    if (preg_match($spammy, $blob)) $out['spam-phrase'] = 4;

    return $out;
}

function nops_handle_contact() {
    $ip = nops_client_ip();

    // Honeypot: silently accept bots without doing anything.
    if (!empty($_POST['nops_website'])) { wp_safe_redirect(home_url('/contact/?sent=1')); exit; }
    if (!isset($_POST['nops_nonce']) || !wp_verify_nonce($_POST['nops_nonce'], 'nops_contact')) {
        wp_safe_redirect(home_url('/contact/?err=1')); exit;
    }
    // Flood control: over the limit is dropped outright (not even stored).
    if (nops_contact_rate_exceeded($ip)) { wp_safe_redirect(home_url('/contact/?sent=1')); exit; }

    $first    = sanitize_text_field($_POST['first_name'] ?? '');
    $last     = sanitize_text_field($_POST['last_name'] ?? '');
    $email    = sanitize_email($_POST['email'] ?? '');
    $phone    = sanitize_text_field($_POST['phone'] ?? '');
    $interest = sanitize_text_field($_POST['interest'] ?? '');
    $message  = sanitize_textarea_field($_POST['message'] ?? '');
    $name     = trim("$first $last");
    if ($name === '' || !is_email($email)) { wp_safe_redirect(home_url('/contact/?err=1')); exit; }

    // Score it: bad/replayed form tokens count as a strong signal too.
    $signals = nops_spam_signals(['name' => $name, 'first' => $first, 'last' => $last, 'message' => $message]);
    $problem = nops_form_token_problem();
    if ($problem !== '') $signals[$problem] = 5;
    $score   = array_sum($signals);
    $is_spam = $score >= 4;

    $body = "New website inquiry\n\n"
          . "Name: $name\nEmail: $email\nPhone: $phone\nInterested in: $interest\n\n"
          . "Message:\n$message\n";

    // Persist a copy in wp-admin -> Inquiries (spam included, so nothing is lost).
    wp_insert_post([
        'post_type'    => 'nops_inquiry',
        'post_status'  => 'private',
        'post_title'   => ($is_spam ? '[SPAM] ' : '') . "$name — $interest",
        'post_content' => $is_spam
            ? $body . "\n---\nFlagged as spam (score $score: " . implode(', ', array_keys($signals)) . ") from $ip.\nNo email was sent. If this is a real person, reply to them directly."
            : $body,
        'meta_input'   => [
            '_nops_ip'      => $ip,
            '_nops_score'   => $score,
            '_nops_signals' => implode(', ', array_keys($signals)),
        ],
    ]);

    // Spam stops here: no notification, and no auto-reply to a forged address.
    if ($is_spam) { wp_safe_redirect(home_url('/contact/?sent=1')); exit; }

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

/**
 * Apache serves this site directly — there is no trusted proxy in front of it,
 * so X-Forwarded-For is attacker-controlled and must NOT be honoured: a bot
 * could rotate that header to walk straight past every per-IP rate limit.
 */
function nops_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
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

/* ------------------------------------------------------------------
 * NOLA neighborhood <-> ZIP conversion (single source of truth).
 *
 * WHY: GSREIN's Neighborhood/sub_area MLS field is sparse and inconsistent
 * (e.g. "Uptown"/"Mid-City" return nothing; "Garden District" coexists with the
 * typo "Garden Districe The"). ZIP (Buying Buddy field `zip_code`) is clean and
 * complete, so we filter by ZIP and let neighborhood names map to their ZIP set.
 * A neighborhood spans several ZIPs and a ZIP spans several neighborhoods, so the
 * map is deliberately many-to-many; hood->zips returns the SET to OR-filter on.
 * ------------------------------------------------------------------ */
function nops_nola_hood_zips() {
    return [
        'Garden District'           => ['70130', '70115'],
        'Lower Garden District'     => ['70130'],
        'Uptown'                    => ['70115', '70118'],
        'Carrollton'                => ['70118'],
        'Irish Channel'             => ['70115', '70130'],
        'French Quarter'            => ['70116', '70130'],
        'Marigny'                   => ['70116', '70117'],
        'Bywater'                   => ['70117'],
        'Marigny / Bywater'         => ['70117', '70116'],
        'Treme'                     => ['70116', '70119'],
        'Mid-City'                  => ['70119'],
        'Lakeview'                  => ['70124'],
        'Gentilly'                  => ['70122'],
        'Algiers Point'             => ['70114'],
        'Warehouse District'        => ['70130'],
        'Central Business District' => ['70112', '70130'],
    ];
}

/** Neighborhood name -> array of ZIPs (case-insensitive). [] if unknown. */
function nops_hood_to_zips($name) {
    $map = nops_nola_hood_zips();
    if (isset($map[$name])) return $map[$name];
    foreach ($map as $hood => $zips) {
        if (strcasecmp($hood, $name) === 0) return $zips;
    }
    return [];
}

/** ZIP -> the best-known single neighborhood label (for display/reverse lookup). '' if unknown. */
function nops_zip_to_hood($zip) {
    $zip = substr(preg_replace('/\D/', '', (string) $zip), 0, 5);
    $primary = [
        '70112' => 'Central Business District', '70113' => 'Central City',
        '70114' => 'Algiers Point', '70115' => 'Uptown', '70116' => 'French Quarter',
        '70117' => 'Bywater', '70118' => 'Uptown', '70119' => 'Mid-City',
        '70122' => 'Gentilly', '70124' => 'Lakeview',
        '70130' => 'Garden District', '70131' => 'Algiers Point',
    ];
    return $primary[$zip] ?? '';
}

/**
 * Single source of truth for Buying Buddy IDX hand-off URLs. Two distinct pages:
 *   /listing-results/ = [mbb_widget data-type="ListingResults"] — the results grid;
 *                       the ONLY page that filters. It reads real MLS filter fields
 *                       from the URL when `data-filter=bb` is present (this is the
 *                       exact format Buying Buddy's own search form emits).
 *   /listing-search/  = [mbb_widget data-type="SearchForm"] — just the form.
 * Pass an assoc array of MLS filter fields (zip_code, price_min, price_max,
 * bedrooms_total_min, baths_total_min, ...); empty array => the browse/search form.
 */
function nops_listing_url($fields = []) {
    $fields = array_filter((array) $fields, function ($v) { return $v !== '' && $v !== null; });
    if (!$fields) return home_url('/listing-search/');
    $query = array_merge(['data-filter' => 'bb', 'mls_id' => 'la248'], $fields);
    return home_url('/listing-results/') . '?' . http_build_query($query);
}

function nops_concierge_search_url($c) {
    $f = [];
    if (!empty($c['price_min'])) $f['price_min']          = (int) $c['price_min'];
    if (!empty($c['price_max'])) $f['price_max']          = (int) $c['price_max'];
    if (!empty($c['beds_min']))  $f['bedrooms_total_min'] = (int) $c['beds_min'];
    if (!empty($c['baths_min'])) $f['baths_total_min']    = (int) $c['baths_min'];
    // Neighborhood filtering runs on ZIP (see nops_hood_to_zips) — GSREIN's
    // neighborhood field is unreliable, ZIP is clean. Collect the ZIP set.
    if (!empty($c['neighborhoods']) && is_array($c['neighborhoods'])) {
        $zips = [];
        foreach ($c['neighborhoods'] as $n) {
            $zips = array_merge($zips, nops_hood_to_zips($n));
        }
        $zips = array_values(array_unique($zips));
        if ($zips) $f['zip_code'] = implode(',', $zips);
    }
    return nops_listing_url($f);
}

/**
 * The NOPS quick-search bar (neighborhood + ZIP + max price -> Buying Buddy
 * results). Reused on the homepage AND injected onto the Buying Buddy
 * search/results pages, where BB's own search UI is hidden (see main.css) so
 * every visitor funnels through our search. $wrap adds the .container (homepage);
 * pass false when the surrounding template already provides one (BB pages).
 */
function nops_search_bar($wrap = true) {
    $results  = esc_url(home_url('/listing-results/'));
    $search   = esc_url(home_url('/listing-search/'));
    $headline = ['Garden District', 'Uptown', 'French Quarter', 'Marigny / Bywater', 'Lakeview', 'Mid-City'];
    if ($wrap) echo '<div class="container">';
    ?>
    <form class="searchbar" id="mls-quicksearch" method="get" action="<?php echo $search; ?>" data-results-url="<?php echo $results; ?>">
      <div class="field">
        <label for="qs-nbhd">Neighborhood</label>
        <select id="qs-nbhd" name="nbhd">
          <option value="">Any area</option>
          <?php foreach ($headline as $hood) : ?>
            <option value="<?php echo esc_attr($hood); ?>"><?php echo esc_html($hood); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="qs-zip">or ZIP code</label>
        <input id="qs-zip" name="zip" type="text" inputmode="numeric" autocomplete="postal-code" placeholder="e.g. 70130" pattern="[0-9 ,]*">
      </div>
      <div class="field">
        <label for="qs-type">Type</label>
        <?php // values are real GSREIN property_subtype strings (from the live BB feed) ?>
        <select id="qs-type" name="ptype">
          <option value="">All homes</option>
          <option value="detached,single family residence">Single-Family</option>
          <option value="condominium">Condo</option>
          <option value="townhouse,attached">Townhouse</option>
          <option value="duplex,triplex,quadruplex">Multi-Family</option>
        </select>
      </div>
      <div class="field">
        <label for="qs-beds">Beds</label>
        <select id="qs-beds" name="beds">
          <option value="">Any</option>
          <option value="1">1+</option>
          <option value="2">2+</option>
          <option value="3">3+</option>
          <option value="4">4+</option>
          <option value="5">5+</option>
        </select>
      </div>
      <div class="field">
        <label for="qs-baths">Baths</label>
        <select id="qs-baths" name="baths">
          <option value="">Any</option>
          <option value="1">1+</option>
          <option value="2">2+</option>
          <option value="3">3+</option>
          <option value="4">4+</option>
        </select>
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
    <?php if ($wrap) echo '</div>'; ?>
    <script>
    (function () {
      var form = document.getElementById('mls-quicksearch');
      if (!form) return;
      var HOOD_ZIPS = <?php echo wp_json_encode(nops_nola_hood_zips()); ?>;
      var RESULTS = form.dataset.resultsUrl;
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var zips = [];
        var nbhd = form.querySelector('#qs-nbhd').value;
        if (nbhd && HOOD_ZIPS[nbhd]) zips = zips.concat(HOOD_ZIPS[nbhd]);
        var typed = (form.querySelector('#qs-zip').value.match(/\d{5}/g)) || [];
        zips = zips.concat(typed);
        zips = zips.filter(function (z, i) { return zips.indexOf(z) === i; });
        var params = {};
        if (zips.length) params.zip_code = zips.join(',');
        var type = form.querySelector('#qs-type').value;
        if (type) params.property_subtype = type;
        var beds = form.querySelector('#qs-beds').value;
        if (beds) params.bedrooms_total_min = beds;
        var baths = form.querySelector('#qs-baths').value;
        if (baths) params.baths_total_min = baths;
        var price = form.querySelector('#qs-price').value;
        if (price) { var p = price.split(':'); params[p[0]] = p[1]; }
        var keys = Object.keys(params);
        if (!keys.length) { window.location.href = RESULTS; return; }
        var qs = ['data-filter=bb', 'mls_id=la248'];
        keys.forEach(function (k) { qs.push(encodeURIComponent(k) + '=' + encodeURIComponent(params[k])); });
        window.location.href = RESULTS + '?' + qs.join('&');
      });
    })();
    </script>
    <?php
}

/**
 * Funnel visitors through our search: on the Buying Buddy search + results pages,
 * inject the NOPS search bar above the content. BB's native SearchForm and the
 * results "refine" bar are hidden in CSS (#MBBv3_SearchForm / form[refine-search]).
 */
add_filter('the_content', 'nops_inject_search_on_idx_pages');
function nops_inject_search_on_idx_pages($content) {
    if (!is_page(['listing-search', 'listing-results']) || !in_the_loop() || !is_main_query()) {
        return $content;
    }
    ob_start();
    echo '<div class="idx-quicksearch">';
    nops_search_bar(false);
    echo '</div>';
    return ob_get_clean() . $content;
}

/**
 * Buying Buddy renders its widgets inside OPEN Shadow DOM, so the theme stylesheet
 * cannot reach its search/refine/header chrome (that's why CSS alone never hid it).
 * On the IDX pages, pierce every open shadow root and inject a hide-style into it,
 * re-applying after BB's async renders. Listings (mbb-results / mbb-galleryitem /
 * mbb-listitem / mbb-resultsmap) are deliberately NOT hidden.
 */
add_action('wp_footer', 'nops_hide_bb_chrome_script', 99);
function nops_hide_bb_chrome_script() {
    if (!is_page(['listing-search', 'listing-results'])) return;
    ?>
    <script>
    (function () {
      var HIDE = [
        '#MBBv3_SearchForm', 'form[refine-search]', 'mbb-results-header', 'mbb-search-form',
        'mbb-search-form-nls', 'mbb-quick-search', 'mbb-areasearch', 'mbb-search-criteria',
        'mbb-criteria-badge', 'mbb-filter'
      ].join(',') + '{display:none !important;}';

      function ensureStyle(root) {
        var host = (root === document) ? document.head : root;
        if (!host || !host.querySelector) return;
        if (host.querySelector('style[data-nops-hide]')) return; // still present after re-render
        var s = document.createElement('style');
        s.setAttribute('data-nops-hide', '1');
        s.textContent = HIDE;
        host.appendChild(s);
      }
      function walk(node) {
        var els = node.querySelectorAll ? node.querySelectorAll('*') : [];
        for (var i = 0; i < els.length; i++) {
          if (els[i].shadowRoot) { ensureStyle(els[i].shadowRoot); walk(els[i].shadowRoot); }
        }
      }
      function run() { ensureStyle(document); walk(document); }

      run();
      var ticks = 0, iv = setInterval(function () { run(); if (++ticks > 50) clearInterval(iv); }, 300); // ~15s
      try { new MutationObserver(run).observe(document.documentElement, { childList: true, subtree: true }); } catch (e) {}
    })();
    </script>
    <?php
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
