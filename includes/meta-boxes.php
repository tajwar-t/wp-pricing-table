<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   META BOXES – Individual Pricing Table
   ============================================================ */
add_action( 'add_meta_boxes', 'bbpt_add_meta_boxes' );
function bbpt_add_meta_boxes() {
    add_meta_box( 'bbpt_table_data', __( 'Pricing Table Settings', 'bitbirds-pricing-table' ), 'bbpt_table_data_cb', BBPT_POST_TYPE, 'normal', 'high' );
    add_meta_box( 'bbpt_shortcode_info', __( 'Shortcode', 'bitbirds-pricing-table' ), 'bbpt_table_shortcode_cb', BBPT_POST_TYPE, 'side' );
    add_meta_box( 'bbpt_group_data', __( 'Group Settings & Tables', 'bitbirds-pricing-table' ), 'bbpt_group_data_cb', BBPT_GROUP_TYPE, 'normal', 'high' );
    add_meta_box( 'bbpt_group_shortcode', __( 'Shortcode', 'bitbirds-pricing-table' ), 'bbpt_group_shortcode_cb', BBPT_GROUP_TYPE, 'side' );
}

/* ---- Individual Table Meta Box ---- */
function bbpt_table_data_cb( $post ) {
    wp_nonce_field( 'bbpt_save_table', 'bbpt_table_nonce' );
    $d = bbpt_get_table_meta( $post->ID );
    $schemes = bbpt_get_color_schemes();
    ?>
    <div class="bbpt-meta-wrap">
        <div class="bbpt-field">
            <label><?php _e( 'Table Title', 'bitbirds-pricing-table' ); ?> <span class="required">*</span></label>
            <input type="text" name="bbpt_table_title" value="<?php echo esc_attr( $d['table_title'] ); ?>" placeholder="e.g. Pro Plan" />
            <p class="description"><?php _e( 'Displayed on the card. The post title above is for internal admin use only.', 'bitbirds-pricing-table' ); ?></p>
        </div>

        <div class="bbpt-field-row">
            <div class="bbpt-field">
                <label><?php _e( 'Price', 'bitbirds-pricing-table' ); ?> <span class="required">*</span></label>
                <input type="text" name="bbpt_price" value="<?php echo esc_attr( $d['price'] ); ?>" placeholder="e.g. 5000" />
            </div>
            <div class="bbpt-field">
                <label><?php _e( 'Currency Symbol', 'bitbirds-pricing-table' ); ?></label>
                <input type="text" name="bbpt_currency" value="<?php echo esc_attr( $d['currency'] ); ?>" placeholder="৳" style="max-width:80px;" />
            </div>
            <div class="bbpt-field">
                <label><?php _e( 'Price Period (optional)', 'bitbirds-pricing-table' ); ?></label>
                <input type="text" name="bbpt_period" value="<?php echo esc_attr( $d['period'] ); ?>" placeholder="/mo" style="max-width:120px;" />
            </div>
        </div>

        <div class="bbpt-field">
            <label><?php _e( 'Description (optional)', 'bitbirds-pricing-table' ); ?></label>
            <textarea name="bbpt_description" rows="2" placeholder="Short description..."><?php echo esc_textarea( $d['description'] ); ?></textarea>
        </div>

        <div class="bbpt-field-row">
            <div class="bbpt-field">
                <label><input type="checkbox" name="bbpt_featured" value="1" <?php checked( $d['featured'], '1' ); ?> /> <?php _e( 'Mark as Featured (ribbon badge)', 'bitbirds-pricing-table' ); ?></label>
            </div>
            <div class="bbpt-field">
                <label><?php _e( 'Badge Text', 'bitbirds-pricing-table' ); ?></label>
                <input type="text" name="bbpt_badge_text" value="<?php echo esc_attr( $d['badge_text'] ); ?>" placeholder="Recommended" style="max-width:160px;" />
            </div>
        </div>

        <div class="bbpt-field">
            <label><?php _e( 'Features List Title', 'bitbirds-pricing-table' ); ?></label>
            <input type="text" name="bbpt_list_title" value="<?php echo esc_attr( $d['list_title'] ); ?>" placeholder="Top Features" />
        </div>

        <!-- FIX 1: Feature rows with per-item icon selector -->
        <div class="bbpt-field">
            <label><?php _e( 'Feature Items', 'bitbirds-pricing-table' ); ?></label>
            <p class="description"><?php _e( 'Set icon type, choose an icon, enter text. Drag to reorder.', 'bitbirds-pricing-table' ); ?></p>
            <div id="bbpt-features-list" class="bbpt-sortable">
                <?php if ( ! empty( $d['features'] ) ) { foreach ( $d['features'] as $i => $feat ) { bbpt_render_feature_row( $i, $feat ); } } ?>
            </div>
            <button type="button" class="button bbpt-add-feature"><?php _e( '+ Add Feature', 'bitbirds-pricing-table' ); ?></button>
        </div>

        <div class="bbpt-section-title"><?php _e( 'Call-to-Action Button', 'bitbirds-pricing-table' ); ?></div>
        <div class="bbpt-field-row">
            <div class="bbpt-field">
                <label><?php _e( 'Button Text', 'bitbirds-pricing-table' ); ?></label>
                <input type="text" name="bbpt_btn_text" value="<?php echo esc_attr( $d['btn_text'] ); ?>" placeholder="Buy Now" />
            </div>
            <div class="bbpt-field">
                <label><?php _e( 'Button Type', 'bitbirds-pricing-table' ); ?></label>
                <select name="bbpt_btn_type">
                    <option value="url"      <?php selected( $d['btn_type'], 'url' ); ?>><?php _e( 'Regular Link', 'bitbirds-pricing-table' ); ?></option>
                    <option value="whatsapp" <?php selected( $d['btn_type'], 'whatsapp' ); ?>><?php _e( 'WhatsApp', 'bitbirds-pricing-table' ); ?></option>
                    <option value="none"     <?php selected( $d['btn_type'], 'none' ); ?>><?php _e( 'No Button', 'bitbirds-pricing-table' ); ?></option>
                </select>
            </div>
        </div>

        <!-- FIX 2: type="text" accepts any value, not just URLs -->
        <div class="bbpt-field bbpt-btn-url-field" <?php echo ( $d['btn_type'] === 'whatsapp' || $d['btn_type'] === 'none' ) ? 'style="display:none"' : ''; ?>>
            <label><?php _e( 'Button Link / URL', 'bitbirds-pricing-table' ); ?></label>
            <input type="text" name="bbpt_btn_url" value="<?php echo esc_attr( $d['btn_url'] ); ?>" placeholder="https://... or #section or tel:+880... or /page" />
            <p class="description"><?php _e( 'Any value: full URL, relative path, anchor (#section), tel:, mailto:, etc.', 'bitbirds-pricing-table' ); ?></p>
        </div>

        <div class="bbpt-field bbpt-btn-wa-field" <?php echo $d['btn_type'] !== 'whatsapp' ? 'style="display:none"' : ''; ?>>
            <label><?php _e( 'WhatsApp Number (with country code, no +)', 'bitbirds-pricing-table' ); ?></label>
            <input type="text" name="bbpt_wa_number" value="<?php echo esc_attr( $d['wa_number'] ); ?>" placeholder="8801XXXXXXXXX" />
            <label style="margin-top:8px;"><?php _e( 'Pre-filled Message', 'bitbirds-pricing-table' ); ?></label>
            <input type="text" name="bbpt_wa_message" value="<?php echo esc_attr( $d['wa_message'] ); ?>" placeholder="Hi, I'm interested in this plan..." />
        </div>

        <div class="bbpt-field">
            <label><input type="checkbox" name="bbpt_btn_new_tab" value="1" <?php checked( $d['btn_new_tab'], '1' ); ?> /> <?php _e( 'Open in new tab', 'bitbirds-pricing-table' ); ?></label>
        </div>

        <!-- FIX 3: Color scheme with custom color picker -->
        <div class="bbpt-section-title"><?php _e( 'Color Scheme', 'bitbirds-pricing-table' ); ?></div>
        <div class="bbpt-field bbpt-scheme-picker">
            <?php foreach ( $schemes as $key => $scheme ) : ?>
                <label class="bbpt-scheme-option <?php echo $d['color_scheme'] === $key ? 'active' : ''; ?>">
                    <input type="radio" name="bbpt_color_scheme" value="<?php echo esc_attr( $key ); ?>" <?php checked( $d['color_scheme'], $key ); ?> />
                    <span class="bbpt-scheme-swatch" style="background:linear-gradient(135deg,<?php echo esc_attr($scheme['header_start']); ?>,<?php echo esc_attr($scheme['header_end']); ?>);"></span>
                    <span class="bbpt-scheme-label"><?php echo esc_html( $scheme['label'] ); ?></span>
                </label>
            <?php endforeach; ?>
            <label class="bbpt-scheme-option bbpt-scheme-option--custom <?php echo $d['color_scheme'] === 'custom' ? 'active' : ''; ?>">
                <input type="radio" name="bbpt_color_scheme" value="custom" <?php checked( $d['color_scheme'], 'custom' ); ?> />
                <span class="bbpt-scheme-swatch" style="background:conic-gradient(red,yellow,lime,aqua,blue,magenta,red);"></span>
                <span class="bbpt-scheme-label"><?php _e( 'Custom', 'bitbirds-pricing-table' ); ?></span>
            </label>
        </div>

        <div class="bbpt-custom-colors-wrap" <?php echo $d['color_scheme'] !== 'custom' ? 'style="display:none"' : ''; ?>>
            <div class="bbpt-field-row bbpt-custom-colors">
                <?php
                $color_fields_def = [
                    'custom_header_start' => [ __( 'Header Gradient Start', 'bitbirds-pricing-table' ), '#f5a623' ],
                    'custom_header_end'   => [ __( 'Header Gradient End',   'bitbirds-pricing-table' ), '#f0b429' ],
                    'custom_header_text'  => [ __( 'Header Text Color',     'bitbirds-pricing-table' ), '#1a1a2e' ],
                    'custom_btn_start'    => [ __( 'Button Gradient Start', 'bitbirds-pricing-table' ), '#f5a623' ],
                    'custom_btn_end'      => [ __( 'Button Gradient End',   'bitbirds-pricing-table' ), '#e09610' ],
                    'custom_btn_text'     => [ __( 'Button Text Color',     'bitbirds-pricing-table' ), '#1a1a2e' ],
                ];
                foreach ( $color_fields_def as $field_key => [ $label, $default ] ) :
                    $val = $d[ $field_key ] ?: $default;
                ?>
                <div class="bbpt-field bbpt-color-field">
                    <label><?php echo esc_html( $label ); ?></label>
                    <div class="bbpt-color-input-wrap">
                        <input type="color" class="bbpt-color-native" data-target="bbpt_<?php echo $field_key; ?>" value="<?php echo esc_attr( $val ); ?>" />
                        <input type="text"  class="bbpt-color-hex" name="bbpt_<?php echo $field_key; ?>" value="<?php echo esc_attr( $val ); ?>" maxlength="7" placeholder="<?php echo esc_attr( $default ); ?>" />
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="bbpt-custom-preview">
                <span><?php _e( 'Preview:', 'bitbirds-pricing-table' ); ?></span>
                <span class="bbpt-preview-header-swatch"><?php _e( 'Header', 'bitbirds-pricing-table' ); ?></span>
                <span class="bbpt-preview-btn-swatch"><?php _e( 'Button', 'bitbirds-pricing-table' ); ?></span>
            </div>
        </div>

    </div>

    <script type="text/html" id="bbpt-feature-template">
        <?php bbpt_render_feature_row( '__INDEX__', [ 'text' => '', 'icon' => 'include', 'fa_icon' => 'fa-check' ] ); ?>
    </script>
    <?php
}

/* FIX 1: Feature row with individual icon type + FA icon select */
function bbpt_render_feature_row( $index, $feat ) {
    $icon    = isset( $feat['icon'] )    ? $feat['icon']    : 'include';
    $text    = isset( $feat['text'] )    ? $feat['text']    : '';
    $fa_icon = isset( $feat['fa_icon'] ) ? $feat['fa_icon'] : 'fa-check';

    $include_icons = [
        'fa-check' => '✓ Check', 'fa-check-circle' => '✓ Check Circle', 'fa-check-square' => '✓ Check Square',
        'fa-star' => '★ Star', 'fa-bolt' => '⚡ Bolt', 'fa-shield-alt' => '🛡 Shield',
        'fa-infinity' => '∞ Infinity', 'fa-server' => '⬛ Server', 'fa-lock' => '🔒 Lock',
        'fa-globe' => '🌐 Globe', 'fa-cloud' => '☁ Cloud', 'fa-database' => '🗄 Database',
        'fa-rocket' => '🚀 Rocket', 'fa-headset' => '🎧 Support', 'fa-certificate' => '🏅 Certificate',
        'fa-tachometer-alt' => '⚡ Speed', 'fa-wifi' => '📶 Wifi', 'fa-envelope' => '✉ Email',
        'fa-hdd' => '💾 Storage', 'fa-microchip' => '🔧 CPU',
    ];
    $exclude_icons = [
        'fa-times' => '✗ Times', 'fa-times-circle' => '✗ Times Circle',
        'fa-ban' => '⊘ Ban', 'fa-minus' => '− Minus', 'fa-minus-circle' => '− Minus Circle',
    ];
    $icon_color_class = $icon === 'exclude' ? 'bbpt-icon--exclude' : 'bbpt-icon--include';
    ?>
    <div class="bbpt-feature-row">
        <span class="bbpt-drag-handle dashicons dashicons-menu"></span>
        <select name="bbpt_features[<?php echo $index; ?>][icon]" class="bbpt-icon-type-select" style="width:105px;flex-shrink:0;">
            <option value="include" <?php selected( $icon, 'include' ); ?>><?php _e( '✓ Include', 'bitbirds-pricing-table' ); ?></option>
            <option value="exclude" <?php selected( $icon, 'exclude' ); ?>><?php _e( '✗ Exclude', 'bitbirds-pricing-table' ); ?></option>
        </select>
        <select name="bbpt_features[<?php echo $index; ?>][fa_icon]" class="bbpt-fa-icon-select" style="width:155px;flex-shrink:0;">
            <optgroup label="<?php esc_attr_e( 'Include Icons', 'bitbirds-pricing-table' ); ?>">
                <?php foreach ( $include_icons as $cls => $lbl ) : ?><option value="<?php echo esc_attr($cls); ?>" <?php selected( $fa_icon, $cls ); ?>><?php echo esc_html($lbl); ?></option><?php endforeach; ?>
            </optgroup>
            <optgroup label="<?php esc_attr_e( 'Exclude Icons', 'bitbirds-pricing-table' ); ?>">
                <?php foreach ( $exclude_icons as $cls => $lbl ) : ?><option value="<?php echo esc_attr($cls); ?>" <?php selected( $fa_icon, $cls ); ?>><?php echo esc_html($lbl); ?></option><?php endforeach; ?>
            </optgroup>
        </select>
        <span class="bbpt-feat-icon-preview fas <?php echo esc_attr($fa_icon); ?> <?php echo $icon_color_class; ?>"></span>
        <input type="text" name="bbpt_features[<?php echo $index; ?>][text]" value="<?php echo esc_attr( $text ); ?>" placeholder="<?php esc_attr_e( 'Feature text...', 'bitbirds-pricing-table' ); ?>" />
        <button type="button" class="button-link bbpt-remove-feature"><span class="dashicons dashicons-trash"></span></button>
    </div>
    <?php
}

function bbpt_table_shortcode_cb( $post ) {
    if ( $post->post_status === 'publish' ) { ?>
        <p><?php _e( 'Single table shortcode:', 'bitbirds-pricing-table' ); ?></p>
        <code class="bbpt-shortcode-box">[bbpt_table id="<?php echo $post->ID; ?>"]</code>
        <button type="button" class="button bbpt-copy-shortcode" data-sc='[bbpt_table id="<?php echo $post->ID; ?>"]'><?php _e( 'Copy Shortcode', 'bitbirds-pricing-table' ); ?></button>
    <?php } else {
        echo '<p>' . __( 'Publish to get a shortcode.', 'bitbirds-pricing-table' ) . '</p>';
    }
}

/* ---- Group Meta Box ---- */
function bbpt_group_data_cb( $post ) {
    wp_nonce_field( 'bbpt_save_group', 'bbpt_group_nonce' );
    $gd = bbpt_get_group_meta( $post->ID );
    $all_tables = get_posts([ 'post_type' => BBPT_POST_TYPE, 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ]);
    $schemes = bbpt_get_color_schemes();
    ?>
    <div class="bbpt-meta-wrap">

        <div class="bbpt-field-row">
            <div class="bbpt-field">
                <label><?php _e( 'Section Title (optional)', 'bitbirds-pricing-table' ); ?></label>
                <input type="text" name="bbpt_section_title" value="<?php echo esc_attr( $gd['section_title'] ); ?>" placeholder="Pricing" />
            </div>
        </div>

        <!-- FIX 4 + 5: free-form columns + gap -->
        <div class="bbpt-field-row">
            <div class="bbpt-field">
                <label><?php _e( 'Grid Columns', 'bitbirds-pricing-table' ); ?></label>
                <input type="number" name="bbpt_columns" value="<?php echo esc_attr( $gd['columns'] ); ?>" min="1" max="12" step="1" style="max-width:80px;" />
                <p class="description"><?php _e( 'Columns on desktop (1–12).', 'bitbirds-pricing-table' ); ?></p>
            </div>
            <div class="bbpt-field">
                <label><?php _e( 'Gap Between Cards', 'bitbirds-pricing-table' ); ?></label>
                <div style="display:flex;align-items:center;gap:6px;">
                    <input type="number" name="bbpt_gap" value="<?php echo esc_attr( $gd['gap'] ); ?>" min="0" max="200" step="1" style="max-width:80px;" />
                    <span>px</span>
                </div>
                <p class="description"><?php _e( '0 = flush (no gap).', 'bitbirds-pricing-table' ); ?></p>
            </div>
        </div>

        <div class="bbpt-field">
            <label><?php _e( 'Default Color Scheme (overrides all tables in this group)', 'bitbirds-pricing-table' ); ?></label>
            <div class="bbpt-scheme-picker">
                <label class="bbpt-scheme-option <?php echo $gd['default_scheme'] === '' ? 'active' : ''; ?>">
                    <input type="radio" name="bbpt_default_scheme" value="" <?php checked( $gd['default_scheme'], '' ); ?> />
                    <span class="bbpt-scheme-swatch" style="background:#ccc;"></span>
                    <span class="bbpt-scheme-label"><?php _e( 'Per-table', 'bitbirds-pricing-table' ); ?></span>
                </label>
                <?php foreach ( $schemes as $key => $scheme ) : ?>
                    <label class="bbpt-scheme-option <?php echo $gd['default_scheme'] === $key ? 'active' : ''; ?>">
                        <input type="radio" name="bbpt_default_scheme" value="<?php echo esc_attr( $key ); ?>" <?php checked( $gd['default_scheme'], $key ); ?> />
                        <span class="bbpt-scheme-swatch" style="background:linear-gradient(135deg,<?php echo esc_attr($scheme['header_start']); ?>,<?php echo esc_attr($scheme['header_end']); ?>);"></span>
                        <span class="bbpt-scheme-label"><?php echo esc_html( $scheme['label'] ); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bbpt-field">
            <label><?php _e( 'Tables in this Group', 'bitbirds-pricing-table' ); ?></label>
            <p class="description"><?php _e( 'Add tables and drag to reorder.', 'bitbirds-pricing-table' ); ?></p>
            <div class="bbpt-table-selector">
                <select id="bbpt-table-add-select">
                    <option value=""><?php _e( '— Select a table to add —', 'bitbirds-pricing-table' ); ?></option>
                    <?php foreach ( $all_tables as $t ) : ?><option value="<?php echo $t->ID; ?>"><?php echo esc_html( $t->post_title ); ?></option><?php endforeach; ?>
                </select>
                <button type="button" class="button" id="bbpt-add-table-btn"><?php _e( '+ Add', 'bitbirds-pricing-table' ); ?></button>
            </div>
            <ul id="bbpt-group-tables" class="bbpt-sortable bbpt-group-list">
                <?php
                if ( ! empty( $gd['table_ids'] ) ) {
                    foreach ( $gd['table_ids'] as $tid ) {
                        $title = get_the_title( $tid );
                        if ( ! $title ) continue;
                        echo '<li data-id="' . esc_attr( $tid ) . '">';
                        echo '<span class="bbpt-drag-handle dashicons dashicons-menu"></span>';
                        echo '<span class="bbpt-table-title">' . esc_html( $title ) . '</span>';
                        echo '<input type="hidden" name="bbpt_table_ids[]" value="' . esc_attr( $tid ) . '" />';
                        echo '<button type="button" class="button-link bbpt-remove-table-row"><span class="dashicons dashicons-trash"></span></button>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>

    </div>
    <?php
}

function bbpt_group_shortcode_cb( $post ) {
    if ( $post->post_status === 'publish' ) { ?>
        <p><?php _e( 'Use this shortcode anywhere:', 'bitbirds-pricing-table' ); ?></p>
        <code class="bbpt-shortcode-box">[bbpt_group id="<?php echo $post->ID; ?>"]</code>
        <button type="button" class="button bbpt-copy-shortcode" data-sc='[bbpt_group id="<?php echo $post->ID; ?>"]'><?php _e( 'Copy Shortcode', 'bitbirds-pricing-table' ); ?></button>
    <?php } else {
        echo '<p>' . __( 'Publish to get a shortcode.', 'bitbirds-pricing-table' ) . '</p>';
    }
}

/* ============================================================
   SAVE
   ============================================================ */
add_action( 'save_post', 'bbpt_save_table_meta', 10, 2 );
function bbpt_save_table_meta( $post_id, $post ) {
    if ( ! isset( $_POST['bbpt_table_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['bbpt_table_nonce'], 'bbpt_save_table' ) ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( $post->post_type !== BBPT_POST_TYPE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // FIX 2: btn_url is plain text – not forced through esc_url
    $text_fields = [ 'table_title', 'price', 'currency', 'period', 'badge_text', 'list_title',
                 'btn_text', 'btn_type', 'btn_url', 'wa_number', 'wa_message', 'color_scheme' ];
    foreach ( $text_fields as $f ) {
        if ( isset( $_POST[ 'bbpt_' . $f ] ) ) {
            update_post_meta( $post_id, '_bbpt_' . $f, sanitize_text_field( $_POST[ 'bbpt_' . $f ] ) );
        }
    }

    if ( isset( $_POST['bbpt_description'] ) ) {
        update_post_meta( $post_id, '_bbpt_description', sanitize_textarea_field( $_POST['bbpt_description'] ) );
    }

    update_post_meta( $post_id, '_bbpt_featured',    isset( $_POST['bbpt_featured'] )    ? '1' : '0' );
    update_post_meta( $post_id, '_bbpt_btn_new_tab', isset( $_POST['bbpt_btn_new_tab'] ) ? '1' : '0' );

    // FIX 3: custom colors
    $color_keys = [ 'custom_header_start', 'custom_header_end', 'custom_header_text',
                    'custom_btn_start', 'custom_btn_end', 'custom_btn_text' ];
    foreach ( $color_keys as $ck ) {
        if ( isset( $_POST[ 'bbpt_' . $ck ] ) ) {
            $hex = sanitize_hex_color( $_POST[ 'bbpt_' . $ck ] );
            update_post_meta( $post_id, '_bbpt_' . $ck, $hex ?: '' );
        }
    }

    // FIX 1: features now save fa_icon
    $features = [];
    if ( ! empty( $_POST['bbpt_features'] ) && is_array( $_POST['bbpt_features'] ) ) {
        foreach ( $_POST['bbpt_features'] as $feat ) {
            if ( ! empty( $feat['text'] ) ) {
                $features[] = [
                    'icon'    => in_array( $feat['icon'] ?? '', [ 'include', 'exclude' ] ) ? $feat['icon'] : 'include',
                    'fa_icon' => isset( $feat['fa_icon'] ) ? sanitize_html_class( $feat['fa_icon'] ) : 'fa-check',
                    'text'    => sanitize_text_field( $feat['text'] ),
                ];
            }
        }
    }
    update_post_meta( $post_id, '_bbpt_features', $features );
}

add_action( 'save_post', 'bbpt_save_group_meta', 10, 2 );
function bbpt_save_group_meta( $post_id, $post ) {
    if ( ! isset( $_POST['bbpt_group_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['bbpt_group_nonce'], 'bbpt_save_group' ) ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( $post->post_type !== BBPT_GROUP_TYPE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // FIX 5: free-form columns 1–12
    update_post_meta( $post_id, '_bbpt_columns', max( 1, min( 12, intval( $_POST['bbpt_columns'] ?? 4 ) ) ) );
    // FIX 4: gap in px
    update_post_meta( $post_id, '_bbpt_gap', max( 0, min( 200, intval( $_POST['bbpt_gap'] ?? 0 ) ) ) );
    update_post_meta( $post_id, '_bbpt_section_title',  sanitize_text_field( $_POST['bbpt_section_title']  ?? '' ) );
    update_post_meta( $post_id, '_bbpt_default_scheme', sanitize_text_field( $_POST['bbpt_default_scheme'] ?? '' ) );

    $ids = [];
    if ( ! empty( $_POST['bbpt_table_ids'] ) ) {
        foreach ( (array) $_POST['bbpt_table_ids'] as $tid ) { $ids[] = intval( $tid ); }
    }
    update_post_meta( $post_id, '_bbpt_table_ids', $ids );
}

/* ============================================================
   HELPERS
   ============================================================ */
function bbpt_get_table_meta( $post_id ) {
    return [
        'table_title'         => get_post_meta( $post_id, '_bbpt_table_title', true ),
        'price'               => get_post_meta( $post_id, '_bbpt_price',               true ),
        'currency'            => get_post_meta( $post_id, '_bbpt_currency',            true ) ?: '৳',
        'period'              => get_post_meta( $post_id, '_bbpt_period',              true ),
        'description'         => get_post_meta( $post_id, '_bbpt_description',         true ),
        'featured'            => get_post_meta( $post_id, '_bbpt_featured',            true ),
        'badge_text'          => get_post_meta( $post_id, '_bbpt_badge_text',          true ) ?: 'Recommended',
        'list_title'          => get_post_meta( $post_id, '_bbpt_list_title',          true ) ?: 'Top Features',
        'features'            => get_post_meta( $post_id, '_bbpt_features',            true ) ?: [],
        'btn_text'            => get_post_meta( $post_id, '_bbpt_btn_text',            true ) ?: 'Buy Now',
        'btn_type'            => get_post_meta( $post_id, '_bbpt_btn_type',            true ) ?: 'url',
        'btn_url'             => get_post_meta( $post_id, '_bbpt_btn_url',             true ), // FIX 2: raw
        'wa_number'           => get_post_meta( $post_id, '_bbpt_wa_number',           true ),
        'wa_message'          => get_post_meta( $post_id, '_bbpt_wa_message',          true ),
        'btn_new_tab'         => get_post_meta( $post_id, '_bbpt_btn_new_tab',         true ),
        'color_scheme'        => get_post_meta( $post_id, '_bbpt_color_scheme',        true ) ?: 'amber',
        // FIX 3
        'custom_header_start' => get_post_meta( $post_id, '_bbpt_custom_header_start', true ) ?: '#f5a623',
        'custom_header_end'   => get_post_meta( $post_id, '_bbpt_custom_header_end',   true ) ?: '#f0b429',
        'custom_header_text'  => get_post_meta( $post_id, '_bbpt_custom_header_text',  true ) ?: '#1a1a2e',
        'custom_btn_start'    => get_post_meta( $post_id, '_bbpt_custom_btn_start',    true ) ?: '#f5a623',
        'custom_btn_end'      => get_post_meta( $post_id, '_bbpt_custom_btn_end',      true ) ?: '#e09610',
        'custom_btn_text'     => get_post_meta( $post_id, '_bbpt_custom_btn_text',     true ) ?: '#1a1a2e',
    ];
}

function bbpt_get_group_meta( $post_id ) {
    return [
        'columns'        => get_post_meta( $post_id, '_bbpt_columns',        true ) ?: 4,
        'gap'            => get_post_meta( $post_id, '_bbpt_gap',            true ),   // FIX 4
        'section_title'  => get_post_meta( $post_id, '_bbpt_section_title',  true ),
        'default_scheme' => get_post_meta( $post_id, '_bbpt_default_scheme', true ),
        'table_ids'      => get_post_meta( $post_id, '_bbpt_table_ids',      true ) ?: [],
    ];
}
