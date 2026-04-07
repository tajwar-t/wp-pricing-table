<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   SHORTCODES
   ============================================================ */

// [bbpt_group id="123"]
add_shortcode( 'bbpt_group', 'bbpt_shortcode_group' );
function bbpt_shortcode_group( $atts ) {
    $atts = shortcode_atts( [ 'id' => 0 ], $atts, 'bbpt_group' );
    $group_id = intval( $atts['id'] );
    if ( ! $group_id ) return '';

    $gd = bbpt_get_group_meta( $group_id );
    if ( empty( $gd['table_ids'] ) ) return '';

    ob_start();

    $cols        = max( 1, min( 12, intval( $gd['columns'] ) ) );
    $gap         = isset( $gd['gap'] ) && $gd['gap'] !== '' ? intval( $gd['gap'] ) : 0;  // FIX 4
    $title       = $gd['section_title'];
    $def_scheme  = $gd['default_scheme'];

    // FIX 4+5: inline grid style with dynamic gap and columns
    $grid_style = sprintf(
        'display:grid;grid-template-columns:repeat(%d,minmax(0,1fr));gap:%dpx;align-items:start;',
        $cols,
        $gap
    );

    echo '<div class="bbpt-section">';

    if ( $title ) {
        echo '<h2 class="bbpt-section-heading">' . esc_html( $title ) . '</h2>';
    }

    echo '<div class="bbpt-grid bbpt-grid--dynamic" style="' . esc_attr( $grid_style ) . '">';

    foreach ( $gd['table_ids'] as $tid ) {
        $post = get_post( $tid );
        if ( ! $post || $post->post_status !== 'publish' ) continue;
        $override_scheme = $def_scheme !== '' ? $def_scheme : null;
        echo bbpt_render_single_table( $tid, $override_scheme );
    }

    echo '</div>'; // .bbpt-grid
    echo '</div>'; // .bbpt-section

    return ob_get_clean();
}

// [bbpt_table id="123" scheme="gold"]
add_shortcode( 'bbpt_table', 'bbpt_shortcode_single' );
function bbpt_shortcode_single( $atts ) {
    $atts = shortcode_atts( [ 'id' => 0, 'scheme' => '' ], $atts, 'bbpt_table' );
    $id = intval( $atts['id'] );
    if ( ! $id ) return '';
    $override = $atts['scheme'] !== '' ? sanitize_text_field( $atts['scheme'] ) : null;

    ob_start();
    echo '<div class="bbpt-section">';
    echo '<div class="bbpt-grid bbpt-grid--dynamic" style="display:grid;grid-template-columns:minmax(0,420px);justify-content:center;">';
    echo bbpt_render_single_table( $id, $override );
    echo '</div>';
    echo '</div>';
    return ob_get_clean();
}

/* ============================================================
   RENDER – single table card
   ============================================================ */
function bbpt_render_single_table( $post_id, $override_scheme = null ) {
    $d     = bbpt_get_table_meta( $post_id );
    $stored_title = get_post_meta( $post_id, '_bbpt_table_title', true );
    $title        = $stored_title ?: get_the_title( $post_id );

    // Resolve color scheme
    $scheme_key = ( $override_scheme !== null ) ? $override_scheme : $d['color_scheme'];

    // FIX 3: support 'custom' scheme using the table's own saved colors
    if ( $scheme_key === 'custom' ) {
        $inline_style = sprintf(
            '--bbpt-header-start:%s;--bbpt-header-end:%s;--bbpt-header-text:%s;--bbpt-btn-start:%s;--bbpt-btn-end:%s;--bbpt-btn-text:%s;--bbpt-btn-shadow:rgba(0,0,0,0.25);',
            esc_attr( $d['custom_header_start'] ?: '#f5a623' ),
            esc_attr( $d['custom_header_end']   ?: '#f0b429' ),
            esc_attr( $d['custom_header_text']  ?: '#1a1a2e' ),
            esc_attr( $d['custom_btn_start']    ?: '#f5a623' ),
            esc_attr( $d['custom_btn_end']      ?: '#e09610' ),
            esc_attr( $d['custom_btn_text']     ?: '#1a1a2e' )
        );
    } else {
        $schemes = bbpt_get_color_schemes();
        if ( ! isset( $schemes[ $scheme_key ] ) ) $scheme_key = 'amber';
        $inline_style = bbpt_scheme_inline_style( $scheme_key );
    }

    // FIX 2: btn_url is stored as raw text – output as esc_attr, not esc_url
    $btn_href = '';
    if ( $d['btn_type'] === 'url' ) {
        $btn_href = $d['btn_url']; // kept as-is; output via esc_attr on the href
    } elseif ( $d['btn_type'] === 'whatsapp' ) {
        $wa_num   = preg_replace( '/[^0-9]/', '', $d['wa_number'] );
        $wa_msg   = rawurlencode( $d['wa_message'] );
        $btn_href = 'https://wa.me/' . $wa_num . ( $wa_msg ? '?text=' . $wa_msg : '' );
    }

    $target         = $d['btn_new_tab'] === '1' ? ' target="_blank" rel="noopener noreferrer"' : '';
    $featured_class = $d['featured'] === '1' ? ' bbpt-card--featured' : '';

    ob_start();
    ?>
    <div class="bbpt-card<?php echo $featured_class; ?>" style="<?php echo $inline_style; ?>">

        <?php if ( $d['featured'] === '1' ) : ?>
            <span class="bbpt-badge"><?php echo esc_html( $d['badge_text'] ); ?></span>
        <?php endif; ?>

        <div class="bbpt-card__header">
            <h2 class="bbpt-card__title"><?php echo esc_html( $title ); ?></h2>
            <div class="bbpt-card__price">
                <?php if ( $d['currency'] ) : ?><span class="bbpt-currency"><?php echo esc_html( $d['currency'] ); ?></span><?php endif; ?>
                <span class="bbpt-amount"><?php echo esc_html( $d['price'] ); ?></span>
                <?php if ( $d['period'] ) : ?><span class="bbpt-period"><?php echo esc_html( $d['period'] ); ?></span><?php endif; ?>
            </div>
        </div>

        <div class="bbpt-card__body">
            <?php if ( $d['description'] ) : ?>
                <p class="bbpt-card__desc"><?php echo esc_html( $d['description'] ); ?></p>
            <?php endif; ?>

            <?php if ( $d['list_title'] ) : ?>
                <div class="bbpt-features-title"><?php echo esc_html( $d['list_title'] ); ?></div>
            <?php endif; ?>

            <?php if ( ! empty( $d['features'] ) ) : ?>
                <ul class="bbpt-features">
                    <?php foreach ( $d['features'] as $feat ) :
                        $is_exclude  = ( $feat['icon'] === 'exclude' );
                        // FIX 1: use the per-item fa_icon, fallback to fa-check / fa-times
                        $fa_icon     = isset( $feat['fa_icon'] ) && $feat['fa_icon']
                                        ? $feat['fa_icon']
                                        : ( $is_exclude ? 'fa-times' : 'fa-check' );
                        $icon_class  = $is_exclude ? 'bbpt-icon--exclude' : 'bbpt-icon--include';
                        $text_class  = $is_exclude ? 'bbpt-feat-text--exclude' : 'bbpt-feat-text--include';
                    ?>
                        <li class="bbpt-feat-item">
                            <i class="bbpt-feat-icon fas <?php echo esc_attr( $fa_icon ); ?> <?php echo $icon_class; ?>" aria-hidden="true"></i>
                            <span class="bbpt-feat-text <?php echo $text_class; ?>"><?php echo esc_html( $feat['text'] ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <?php if ( $d['btn_type'] !== 'none' && $d['btn_text'] && $btn_href ) : ?>
            <div class="bbpt-card__cta">
                <?php if ( $d['btn_type'] === 'whatsapp' ) : ?>
                    <a href="<?php echo esc_attr( $btn_href ); ?>" class="bbpt-btn bbpt-btn--wa"<?php echo $target; ?>>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="bbpt-wa-icon" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.558 4.12 1.533 5.847L.057 23.5l5.787-1.517A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.817 9.817 0 01-5.003-1.368l-.36-.213-3.433.9.917-3.346-.233-.376A9.818 9.818 0 012.182 12C2.182 6.574 6.574 2.182 12 2.182S21.818 6.574 21.818 12 17.426 21.818 12 21.818z"/></svg>
                        <?php echo esc_html( $d['btn_text'] ); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_attr( $btn_href ); ?>" class="bbpt-btn"<?php echo $target; ?>><?php echo esc_html( $d['btn_text'] ); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
    <?php
    return ob_get_clean();
}
