<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns all available color schemes.
 * Each scheme: [ label, header_bg (gradient), header_text, btn_bg, btn_text, btn_shadow_color ]
 */
function bbpt_get_color_schemes() {
    return [
        'amber'  => [
            'label'        => __( 'Amber (Default)', 'bitbirds-pricing-table' ),
            'header_start' => '#f5a623',
            'header_end'   => '#f0b429',
            'header_text'  => '#1a1a2e',
            'btn_start'    => '#f5a623',
            'btn_end'      => '#e09610',
            'btn_text'     => '#1a1a2e',
            'btn_shadow'   => 'rgba(245,166,35,0.4)',
        ],
        'blue'   => [
            'label'        => __( 'Ocean Blue', 'bitbirds-pricing-table' ),
            'header_start' => '#1976d2',
            'header_end'   => '#42a5f5',
            'header_text'  => '#ffffff',
            'btn_start'    => '#1976d2',
            'btn_end'      => '#0d47a1',
            'btn_text'     => '#ffffff',
            'btn_shadow'   => 'rgba(25,118,210,0.4)',
        ],
        'green'  => [
            'label'        => __( 'Forest Green', 'bitbirds-pricing-table' ),
            'header_start' => '#2e7d32',
            'header_end'   => '#66bb6a',
            'header_text'  => '#ffffff',
            'btn_start'    => '#2e7d32',
            'btn_end'      => '#1b5e20',
            'btn_text'     => '#ffffff',
            'btn_shadow'   => 'rgba(46,125,50,0.4)',
        ],
        'purple' => [
            'label'        => __( 'Royal Purple', 'bitbirds-pricing-table' ),
            'header_start' => '#6a1b9a',
            'header_end'   => '#ab47bc',
            'header_text'  => '#ffffff',
            'btn_start'    => '#6a1b9a',
            'btn_end'      => '#4a148c',
            'btn_text'     => '#ffffff',
            'btn_shadow'   => 'rgba(106,27,154,0.4)',
        ],
        'red'    => [
            'label'        => __( 'Crimson Red', 'bitbirds-pricing-table' ),
            'header_start' => '#c62828',
            'header_end'   => '#ef5350',
            'header_text'  => '#ffffff',
            'btn_start'    => '#c62828',
            'btn_end'      => '#b71c1c',
            'btn_text'     => '#ffffff',
            'btn_shadow'   => 'rgba(198,40,40,0.4)',
        ],
        'bronze' => [
            'label'        => __( 'Bronze', 'bitbirds-pricing-table' ),
            'header_start' => '#cd7f32',
            'header_end'   => '#e8a265',
            'header_text'  => '#ffffff',
            'btn_start'    => '#cd7f32',
            'btn_end'      => '#a85e1a',
            'btn_text'     => '#ffffff',
            'btn_shadow'   => 'rgba(205,127,50,0.4)',
        ],
        'silver' => [
            'label'        => __( 'Silver', 'bitbirds-pricing-table' ),
            'header_start' => '#8e9eab',
            'header_end'   => '#c8d6df',
            'header_text'  => '#1a1a2e',
            'btn_start'    => '#8e9eab',
            'btn_end'      => '#6b7f8e',
            'btn_text'     => '#ffffff',
            'btn_shadow'   => 'rgba(142,158,171,0.4)',
        ],
        'gold'   => [
            'label'        => __( 'Gold', 'bitbirds-pricing-table' ),
            'header_start' => '#f5a623',
            'header_end'   => '#f0d060',
            'header_text'  => '#1a1a2e',
            'btn_start'    => '#f5a623',
            'btn_end'      => '#e09610',
            'btn_text'     => '#1a1a2e',
            'btn_shadow'   => 'rgba(245,166,35,0.4)',
        ],
        'dark'   => [
            'label'        => __( 'Dark', 'bitbirds-pricing-table' ),
            'header_start' => '#1a1a2e',
            'header_end'   => '#16213e',
            'header_text'  => '#f5a623',
            'btn_start'    => '#1a1a2e',
            'btn_end'      => '#0f0f1a',
            'btn_text'     => '#f5a623',
            'btn_shadow'   => 'rgba(26,26,46,0.4)',
        ],
        'teal'   => [
            'label'        => __( 'Teal', 'bitbirds-pricing-table' ),
            'header_start' => '#00695c',
            'header_end'   => '#26a69a',
            'header_text'  => '#ffffff',
            'btn_start'    => '#00695c',
            'btn_end'      => '#004d40',
            'btn_text'     => '#ffffff',
            'btn_shadow'   => 'rgba(0,105,92,0.4)',
        ],
    ];
}

/**
 * Returns inline CSS variables for a given scheme key.
 */
function bbpt_scheme_inline_style( $scheme_key ) {
    $schemes = bbpt_get_color_schemes();
    $s = isset( $schemes[ $scheme_key ] ) ? $schemes[ $scheme_key ] : $schemes['amber'];
    return sprintf(
        '--bbpt-header-start:%s;--bbpt-header-end:%s;--bbpt-header-text:%s;--bbpt-btn-start:%s;--bbpt-btn-end:%s;--bbpt-btn-text:%s;--bbpt-btn-shadow:%s;',
        esc_attr( $s['header_start'] ),
        esc_attr( $s['header_end'] ),
        esc_attr( $s['header_text'] ),
        esc_attr( $s['btn_start'] ),
        esc_attr( $s['btn_end'] ),
        esc_attr( $s['btn_text'] ),
        esc_attr( $s['btn_shadow'] )
    );
}
