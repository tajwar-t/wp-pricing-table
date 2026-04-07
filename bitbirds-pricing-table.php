<?php
/**
 * Plugin Name: BitBirds Pricing Table
 * Plugin URI:  https://bitbirds.com/
 * Description: Create beautiful pricing tables and display them anywhere using shortcodes. Supports multiple tables per shortcode, color schemes, WhatsApp links, and more.
 * Version:     1.0.0
 * Author:      BitBirds
 * Author URI:  https://bitbirds.com/
 * License:     GPL-2.0+
 * Text Domain: bitbirds-pricing-table
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BBPT_VERSION',     '1.0.1' );
define( 'BBPT_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'BBPT_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
define( 'BBPT_POST_TYPE',   'bbpt_pricing_table' );
define( 'BBPT_GROUP_TYPE',  'bbpt_table_group' );

require_once BBPT_PLUGIN_DIR . 'includes/post-types.php';
require_once BBPT_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once BBPT_PLUGIN_DIR . 'includes/shortcode.php';
require_once BBPT_PLUGIN_DIR . 'includes/color-schemes.php';
require_once BBPT_PLUGIN_DIR . 'admin/admin-page.php';

/* ---------- Activation ---------- */
register_activation_hook( __FILE__, 'bbpt_activate' );
function bbpt_activate() {
    bbpt_register_post_types();
    flush_rewrite_rules();
}

/* ---------- Assets ---------- */
add_action( 'wp_enqueue_scripts', 'bbpt_frontend_assets' );
function bbpt_frontend_assets() {
    wp_enqueue_style(
        'bbpt-public',
        BBPT_PLUGIN_URL . 'public/css/bbpt-public.css',
        [],
        BBPT_VERSION
    );
    wp_enqueue_script(
        'bbpt-public',
        BBPT_PLUGIN_URL . 'public/js/bbpt-public.js',
        [],
        BBPT_VERSION,
        true
    );
}

add_action( 'admin_enqueue_scripts', 'bbpt_admin_assets' );
function bbpt_admin_assets( $hook ) {
    $allowed_hooks = [ 'post.php', 'post-new.php', 'edit.php', 'toplevel_page_bbpt-groups' ];
    if ( ! in_array( $hook, $allowed_hooks ) ) return;

    wp_enqueue_style(  'bbpt-admin', BBPT_PLUGIN_URL . 'admin/admin.css', [], BBPT_VERSION );
    wp_enqueue_script( 'bbpt-admin', BBPT_PLUGIN_URL . 'admin/admin.js',  [ 'jquery', 'jquery-ui-sortable' ], BBPT_VERSION, true );
    wp_localize_script( 'bbpt-admin', 'bbpt_admin', [
        'nonce' => wp_create_nonce( 'bbpt_admin_nonce' ),
        'ajax_url' => admin_url( 'admin-ajax.php' ),
    ]);
}

/* ---------- AJAX: get tables for group builder ---------- */
add_action( 'wp_ajax_bbpt_get_tables', 'bbpt_ajax_get_tables' );
function bbpt_ajax_get_tables() {
    check_ajax_referer( 'bbpt_admin_nonce', 'nonce' );
    $tables = get_posts([ 'post_type' => BBPT_POST_TYPE, 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ]);
    $out = [];
    foreach ( $tables as $t ) {
        $out[] = [ 'id' => $t->ID, 'title' => $t->post_title ];
    }
    wp_send_json_success( $out );
}
