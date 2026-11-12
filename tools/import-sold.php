<?php
/**
 * Create "Sold Homes" (nops_sold) DRAFTS from the Zillow past-sales archive.
 *
 *   wp eval-file import-sold.php /path/to/past-sales.csv [limit] [--publish]
 *
 * Only seller-side sales (side "Seller" or "Buyer/Seller") inside New Orleans
 * (ZIP 701xx), newest first. Buyer-side sales never get a page: see
 * nops_register_sold_cpt() in the theme for why. Idempotent: a sale whose
 * Zillow ID (nops_zpid) already has a post is skipped, so re-running with a
 * bigger limit only adds the next ones. Drafts by default; Kari publishes.
 */
if (!defined('WP_CLI') || !WP_CLI) { exit; }

$csv     = $args[0] ?? '';
$limit   = isset($args[1]) && ctype_digit($args[1]) ? (int) $args[1] : 15;
$status  = in_array('--publish', $args, true) ? 'publish' : 'draft';
if (!is_readable($csv)) WP_CLI::error("Can't read CSV: $csv");

$fh = fopen($csv, 'r');
$head = fgetcsv($fh);
$head[0] = preg_replace('/^\xEF\xBB\xBF/', '', $head[0]);
$rows = [];
while (($r = fgetcsv($fh)) !== false) {
    if (count($r) !== count($head)) continue;
    $rows[] = array_combine($head, $r);
}
fclose($fh);

$sales = array_filter($rows, function ($r) {
    return in_array($r['side'], ['Seller', 'Buyer/Seller'], true)
        && preg_match('/^(.+), New Orleans, LA (701\d\d)$/', trim($r['address']))
        && preg_match('/^\d{4}-\d{2}-\d{2}$/', $r['sold_date'])
        && $r['zpid'] !== '';
});
usort($sales, function ($a, $b) { return strcmp($b['sold_date'], $a['sold_date']); });

$clean = function ($v) { $v = trim($v); return ($v === '--' || $v === '') ? '' : $v; };
$made = 0; $skipped = 0; $out = [];
foreach ($sales as $s) {
    if ($made >= $limit) break;
    $exists = get_posts(['post_type' => 'nops_sold', 'post_status' => 'any', 'fields' => 'ids',
                         'meta_key' => 'nops_zpid', 'meta_value' => $s['zpid'], 'numberposts' => 1]);
    if ($exists) { $skipped++; continue; }

    preg_match('/^(.+), New Orleans, LA (701\d\d)$/', trim($s['address']), $a);
    $street = $a[1]; $zip = $a[2];
    $id = wp_insert_post([
        'post_type'   => 'nops_sold',
        'post_status' => $status,
        'post_title'  => $street,
        'post_name'   => sanitize_title($street),
        'post_date'   => $s['sold_date'] . ' 12:00:00',
        'post_content'=> '',
        'meta_input'  => [
            'nops_street' => $street, 'nops_city' => 'New Orleans', 'nops_zip' => $zip,
            'nops_sold_date' => $s['sold_date'], 'nops_side' => $s['side'],
            'nops_beds' => $clean($s['beds']), 'nops_baths' => $clean($s['baths']),
            'nops_sqft' => preg_replace('/\D/', '', $clean($s['sqft'])),
            'nops_zpid' => $s['zpid'], 'nops_source' => 'zillow-archive-2026-11-04',
        ],
    ], true);
    if (is_wp_error($id)) { WP_CLI::warning("$street: " . $id->get_error_message()); continue; }
    $made++;
    $out[] = ['ID' => $id, 'sold' => $s['sold_date'], 'address' => $street, 'zip' => $zip,
              'area' => nops_zip_to_hood($zip), 'url' => get_permalink($id)];
}
if ($out) WP_CLI\Utils\format_items('table', $out, ['ID', 'sold', 'address', 'zip', 'area', 'url']);
WP_CLI::success("Created $made $status sold-home posts; skipped $skipped already imported.");
