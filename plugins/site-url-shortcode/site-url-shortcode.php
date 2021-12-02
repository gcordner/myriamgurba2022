<?php
/*
Plugin Name: Site URL Shortcode
Plugin URI: https://wordpress.org/plugins/site-url-shortcode/
Description: Use the <strong>[site-url]</strong> shortcode anywhere throughout your site in place of the wordpress site url.
Version: 1.02
Author: Robert Bellamy
Text Domain: site-url-shortcode
License: Public Domain
*/

if (!is_admin()) {
    /* This action hook ensures compatibility with cache plugins
     * (called immediately after wp_cache_postload) */
    add_action('plugins_loaded', function() {
        ob_start('site_url_shortcode_ob_callback');
    });
    
    // Cleanup
    add_action('shutdown', function() {
        ob_end_flush();
    });
}

function site_url_shortcode_ob_callback($html) {
    // Don't bother looking for shortcode if not a wordpress page (e.g. binary)
    if (!preg_match('/(<\/html>|<\/rss>|<\/feed>|<\/urlset|<\?xml)/i', $html )) {
        return $html;
    }
	
    $html = apply_filters('site_url_shortcode_pre', $html);
    
    // Site url
    $siteUrl = site_url();
    $siteUrlNoProtocol = preg_replace('%^((.*?)//)%', '', $siteUrl);

    /* First replace instances of [site-url] preceded by a protocl
       with protocol-less site url, preserving the specified protocol */
    $html = str_replace('//[site-url]', sprintf('//%s', $siteUrlNoProtocol), $html);

    // Then replace standalone [site-url] with the full site url with protocol
    $html = str_replace('[site-url]', $siteUrl, $html);

    return apply_filters('site_url_shortcode', $html);
}
