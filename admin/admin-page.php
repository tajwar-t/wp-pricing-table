<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'bbpt_admin_menu' );
function bbpt_admin_menu() {
    add_menu_page(
        __( 'Pricing Tables', 'bitbirds-pricing-table' ),
        __( 'Pricing Tables', 'bitbirds-pricing-table' ),
        'edit_posts',
        'bbpt-groups',
        'bbpt_admin_dashboard',
        'dashicons-grid-view',
        30
    );

    add_submenu_page(
        'bbpt-groups',
        __( 'Table Groups', 'bitbirds-pricing-table' ),
        __( 'Table Groups', 'bitbirds-pricing-table' ),
        'edit_posts',
        'edit.php?post_type=bbpt_table_group'
    );

    add_submenu_page(
        'bbpt-groups',
        __( 'All Tables', 'bitbirds-pricing-table' ),
        __( 'All Tables', 'bitbirds-pricing-table' ),
        'edit_posts',
        'edit.php?post_type=bbpt_pricing_table'
    );

    add_submenu_page(
        'bbpt-groups',
        __( 'Add New Table', 'bitbirds-pricing-table' ),
        __( 'Add New Table', 'bitbirds-pricing-table' ),
        'edit_posts',
        'post-new.php?post_type=bbpt_pricing_table'
    );

    add_submenu_page(
        'bbpt-groups',
        __( 'Add New Group', 'bitbirds-pricing-table' ),
        __( 'Add New Group', 'bitbirds-pricing-table' ),
        'edit_posts',
        'post-new.php?post_type=bbpt_table_group'
    );
}

function bbpt_admin_dashboard() {
    $tables = wp_count_posts( BBPT_POST_TYPE );
    $groups = wp_count_posts( BBPT_GROUP_TYPE );
    ?>
    <div class="wrap bbpt-dashboard">
        <h1><?php _e( 'BitBirds Pricing Tables', 'bitbirds-pricing-table' ); ?></h1>

        <div class="bbpt-dash-cards">
            <div class="bbpt-dash-card">
                <span class="dashicons dashicons-grid-view"></span>
                <div>
                    <strong><?php echo (int) $tables->publish; ?></strong>
                    <span><?php _e( 'Pricing Tables', 'bitbirds-pricing-table' ); ?></span>
                </div>
                <a href="<?php echo admin_url( 'edit.php?post_type=' . BBPT_POST_TYPE ); ?>" class="button"><?php _e( 'Manage', 'bitbirds-pricing-table' ); ?></a>
            </div>
            <div class="bbpt-dash-card">
                <span class="dashicons dashicons-layout"></span>
                <div>
                    <strong><?php echo (int) $groups->publish; ?></strong>
                    <span><?php _e( 'Table Groups', 'bitbirds-pricing-table' ); ?></span>
                </div>
                <a href="<?php echo admin_url( 'edit.php?post_type=' . BBPT_GROUP_TYPE ); ?>" class="button"><?php _e( 'Manage', 'bitbirds-pricing-table' ); ?></a>
            </div>
        </div>

        <div class="bbpt-dash-actions">
            <a href="<?php echo admin_url( 'post-new.php?post_type=' . BBPT_POST_TYPE ); ?>" class="button button-primary button-hero">
                <?php _e( '+ New Pricing Table', 'bitbirds-pricing-table' ); ?>
            </a>
            <a href="<?php echo admin_url( 'post-new.php?post_type=' . BBPT_GROUP_TYPE ); ?>" class="button button-secondary button-hero">
                <?php _e( '+ New Table Group', 'bitbirds-pricing-table' ); ?>
            </a>
        </div>

        <div class="bbpt-dash-help">
            <h2><?php _e( 'How to Use', 'bitbirds-pricing-table' ); ?></h2>
            <ol>
                <li><?php _e( '<strong>Create Pricing Tables</strong> — Add one table per plan (e.g. Bronze, Silver, Gold). Set price, features, button, and color.', 'bitbirds-pricing-table' ); ?></li>
                <li><?php _e( '<strong>Create a Table Group</strong> — Add multiple tables to a group, set grid columns, and optionally override the color scheme for all tables.', 'bitbirds-pricing-table' ); ?></li>
                <li><?php _e( '<strong>Copy the Shortcode</strong> — Paste <code>[bbpt_group id="X"]</code> into any page or post.', 'bitbirds-pricing-table' ); ?></li>
                <li><?php _e( '<strong>Single table</strong> — Use <code>[bbpt_table id="X"]</code> to embed one table anywhere. Add <code>scheme="gold"</code> to override the color.', 'bitbirds-pricing-table' ); ?></li>
            </ol>
            <h3><?php _e( 'Available Color Schemes', 'bitbirds-pricing-table' ); ?></h3>
            <div class="bbpt-scheme-preview-list">
                <?php
                $schemes = bbpt_get_color_schemes();
                foreach ( $schemes as $key => $s ) {
                    echo '<span class="bbpt-scheme-preview-chip" style="background:linear-gradient(135deg,' . esc_attr($s['header_start']) . ',' . esc_attr($s['header_end']) . ');color:' . esc_attr($s['header_text']) . ';">' . esc_html( $key ) . '</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}

/* ---------- Custom columns for post list ---------- */
add_filter( 'manage_' . BBPT_POST_TYPE . '_posts_columns', 'bbpt_table_columns' );
function bbpt_table_columns( $cols ) {
    $new = [];
    foreach ( $cols as $k => $v ) {
        $new[ $k ] = $v;
        if ( $k === 'title' ) {
            $new['bbpt_price']  = __( 'Price', 'bitbirds-pricing-table' );
            $new['bbpt_scheme'] = __( 'Color', 'bitbirds-pricing-table' );
            $new['bbpt_sc']     = __( 'Shortcode', 'bitbirds-pricing-table' );
        }
    }
    return $new;
}

add_action( 'manage_' . BBPT_POST_TYPE . '_posts_custom_column', 'bbpt_table_column_data', 10, 2 );
function bbpt_table_column_data( $col, $post_id ) {
    $d = bbpt_get_table_meta( $post_id );
    if ( $col === 'bbpt_price' ) {
        echo esc_html( $d['currency'] ) . esc_html( $d['price'] );
        if ( $d['period'] ) echo '<small>' . esc_html( $d['period'] ) . '</small>';
    }
    if ( $col === 'bbpt_scheme' ) {
        $schemes = bbpt_get_color_schemes();
        $s = $schemes[ $d['color_scheme'] ] ?? $schemes['amber'];
        echo '<span style="display:inline-block;width:18px;height:18px;border-radius:50%;background:linear-gradient(135deg,' . esc_attr($s['header_start']) . ',' . esc_attr($s['header_end']) . ');vertical-align:middle;margin-right:4px;"></span>';
        echo esc_html( $d['color_scheme'] );
    }
    if ( $col === 'bbpt_sc' ) {
        echo '<code>[bbpt_table id="' . $post_id . '"]</code>';
    }
}

add_filter( 'manage_' . BBPT_GROUP_TYPE . '_posts_columns', 'bbpt_group_columns' );
function bbpt_group_columns( $cols ) {
    $new = [];
    foreach ( $cols as $k => $v ) {
        $new[ $k ] = $v;
        if ( $k === 'title' ) {
            $new['bbpt_tables'] = __( 'Tables', 'bitbirds-pricing-table' );
            $new['bbpt_cols']   = __( 'Columns', 'bitbirds-pricing-table' );
            $new['bbpt_sc']     = __( 'Shortcode', 'bitbirds-pricing-table' );
        }
    }
    return $new;
}

add_action( 'manage_' . BBPT_GROUP_TYPE . '_posts_custom_column', 'bbpt_group_column_data', 10, 2 );
function bbpt_group_column_data( $col, $post_id ) {
    $gd = bbpt_get_group_meta( $post_id );
    if ( $col === 'bbpt_tables' ) echo count( (array) $gd['table_ids'] );
    if ( $col === 'bbpt_cols' )   echo intval( $gd['columns'] );
    if ( $col === 'bbpt_sc' )     echo '<code>[bbpt_group id="' . $post_id . '"]</code>';
}
