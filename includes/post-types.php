<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'bbpt_register_post_types' );
function bbpt_register_post_types() {

    /* ---- Individual Pricing Table ---- */
    register_post_type( BBPT_POST_TYPE, [
        'labels' => [
            'name'               => __( 'Pricing Tables', 'bitbirds-pricing-table' ),
            'singular_name'      => __( 'Pricing Table', 'bitbirds-pricing-table' ),
            'add_new'            => __( 'Add New Table', 'bitbirds-pricing-table' ),
            'add_new_item'       => __( 'Add New Pricing Table', 'bitbirds-pricing-table' ),
            'edit_item'          => __( 'Edit Pricing Table', 'bitbirds-pricing-table' ),
            'new_item'           => __( 'New Pricing Table', 'bitbirds-pricing-table' ),
            'view_item'          => __( 'View Pricing Table', 'bitbirds-pricing-table' ),
            'search_items'       => __( 'Search Pricing Tables', 'bitbirds-pricing-table' ),
            'not_found'          => __( 'No Pricing Tables found', 'bitbirds-pricing-table' ),
            'not_found_in_trash' => __( 'No Pricing Tables found in Trash', 'bitbirds-pricing-table' ),
            'menu_name'          => __( 'Pricing Tables', 'bitbirds-pricing-table' ),
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => 'bbpt-groups',
        'supports'     => [ 'title' ],
        'has_archive'  => false,
        'rewrite'      => false,
    ]);

    /* ---- Table Group (shortcode container) ---- */
    register_post_type( BBPT_GROUP_TYPE, [
        'labels' => [
            'name'               => __( 'Table Groups', 'bitbirds-pricing-table' ),
            'singular_name'      => __( 'Table Group', 'bitbirds-pricing-table' ),
            'add_new'            => __( 'Add New Group', 'bitbirds-pricing-table' ),
            'add_new_item'       => __( 'Add New Table Group', 'bitbirds-pricing-table' ),
            'edit_item'          => __( 'Edit Table Group', 'bitbirds-pricing-table' ),
            'new_item'           => __( 'New Table Group', 'bitbirds-pricing-table' ),
            'search_items'       => __( 'Search Groups', 'bitbirds-pricing-table' ),
            'not_found'          => __( 'No Groups found', 'bitbirds-pricing-table' ),
            'not_found_in_trash' => __( 'No Groups found in Trash', 'bitbirds-pricing-table' ),
            'menu_name'          => __( 'Pricing Tables', 'bitbirds-pricing-table' ),
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => 'bbpt-groups',
        'supports'     => [ 'title' ],
        'has_archive'  => false,
        'rewrite'      => false,
    ]);
}
