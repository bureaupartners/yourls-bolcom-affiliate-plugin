<?php
/*
Plugin Name: Bol.com Affiliate
Plugin URI: https://github.com/bureaupartners/yourls-bolcom-affiliate-plugin
Description: Add bol.com affiliate links
Version: 1.0
Author: Mark Hameetman <mark@bureau.partners>
Author URI: https://bureau.partners
 */

yourls_add_action('pre_redirect', 'bureaupartners_bol_affiliate');

function bureaupartners_bol_affiliate($args)
{
    $url = $args[0];
    if (preg_match('/^http(s)?:\\/\\/(www\\.)?bol.com+/ui', $url) == true) {
        $partner_id = yourls_get_option('bureaupartners_bol_affiliate_id');
        if (strlen($partner_id) === 0) {
            $partner_id = 1015523;
        }
        $url = 'https://partner.bol.com/click/click?p=2&t=url&s=' . $partner_id . '&f=TXL&url=' . urlencode($url) . '&name=bureau.partners';
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
        die();
    }
}

// Register our plugin admin page
yourls_add_action('plugins_loaded', 'bureaupartners_bol_affiliate_admin');
function bureaupartners_bol_affiliate_admin()
{
    yourls_register_plugin_page('bureaupartners_bol_affiliate', 'Bol.com affiliate', 'bureaupartners_bol_affiliate_admin_page');
}

// Display admin page
function bureaupartners_bol_affiliate_admin_page()
{

    // Check if a form was submitted
    if (isset($_POST['partner_id'])) {
        yourls_verify_nonce('bureaupartners_bol_affiliate');
        bureaupartners_bol_affiliate_admin_save();
    }

    // Get value from database
    $partner_id = yourls_get_option('bureaupartners_bol_affiliate_id');

    // Create nonce
    $nonce = yourls_create_nonce('bureaupartners_bol_affiliate');

    echo <<<HTML
		<h2>Bol.com affiliate</h2>
		<p>This plugin makes affiliate links of all bol.com links</p>
		<form method="post">
		<input type="hidden" name="nonce" value="$nonce" />
		<p><label for="test_option">Enter your Partner ID</label> <input type="text" id="partner_id" name="partner_id" value="$partner_id" /></p>
		<p><input type="submit" value="Update value" /></p>
		</form>

HTML;
}

function bureaupartners_bol_affiliate_admin_save()
{
    if ($_POST['partner_id']) {
        yourls_update_option('bureaupartners_bol_affiliate_id', intval($_POST['partner_id']));
    }
}
