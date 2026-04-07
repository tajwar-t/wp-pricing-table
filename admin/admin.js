/* BitBirds Pricing Table – Admin JS */
jQuery(function ($) {
    'use strict';

    /* ============================================================
       Feature rows – sortable + add/remove
       ============================================================ */
    var $list = $('#bbpt-features-list');
    var featureIndex = $list.find('.bbpt-feature-row').length;

    if ($list.length) {
        $list.sortable({ handle: '.bbpt-drag-handle', axis: 'y', update: reindexFeatures });
    }

    $(document).on('click', '.bbpt-add-feature', function () {
        var template = $('#bbpt-feature-template').html();
        template = template.replace(/__INDEX__/g, featureIndex);
        $list.append(template);
        featureIndex++;
        reindexFeatures();
    });

    $(document).on('click', '.bbpt-remove-feature', function () {
        $(this).closest('.bbpt-feature-row').remove();
        reindexFeatures();
    });

    function reindexFeatures() {
        $list.find('.bbpt-feature-row').each(function (i) {
            $(this).find('[name^="bbpt_features["]').each(function () {
                $(this).attr('name', $(this).attr('name').replace(/bbpt_features\[\d+\]/, 'bbpt_features[' + i + ']'));
            });
        });
    }

    /* ============================================================
       FIX 1: Update icon preview when type or fa_icon changes
       ============================================================ */
    function updateIconPreview($row) {
        var iconType = $row.find('.bbpt-icon-type-select').val();
        var faIcon   = $row.find('.bbpt-fa-icon-select').val();
        var $preview = $row.find('.bbpt-feat-icon-preview');

        // Rebuild FA class
        $preview.attr('class', 'bbpt-feat-icon-preview fas ' + faIcon + ' ' +
            (iconType === 'exclude' ? 'bbpt-icon--exclude' : 'bbpt-icon--include'));
    }

    $(document).on('change', '.bbpt-icon-type-select, .bbpt-fa-icon-select', function () {
        updateIconPreview($(this).closest('.bbpt-feature-row'));
    });

    // Init previews on load
    $list.find('.bbpt-feature-row').each(function () { updateIconPreview($(this)); });

    /* ============================================================
       Button type toggle
       ============================================================ */
    function toggleBtnFields() {
        var val = $('[name="bbpt_btn_type"]').val();
        if (val === 'whatsapp') {
            $('.bbpt-btn-url-field').hide();
            $('.bbpt-btn-wa-field').show();
        } else if (val === 'none') {
            $('.bbpt-btn-url-field').hide();
            $('.bbpt-btn-wa-field').hide();
        } else {
            $('.bbpt-btn-url-field').show();
            $('.bbpt-btn-wa-field').hide();
        }
    }
    $('[name="bbpt_btn_type"]').on('change', toggleBtnFields);
    toggleBtnFields();

    /* ============================================================
       Color scheme picker – highlight active
       ============================================================ */
    $(document).on('change', '.bbpt-scheme-picker input[type="radio"]', function () {
        $(this).closest('.bbpt-scheme-picker').find('.bbpt-scheme-option').removeClass('active');
        $(this).closest('.bbpt-scheme-option').addClass('active');

        // FIX 3: show/hide custom color panel
        if ($(this).val() === 'custom') {
            $('.bbpt-custom-colors-wrap').show();
        } else {
            $('.bbpt-custom-colors-wrap').hide();
        }
    });

    /* ============================================================
       FIX 3: Color picker – sync native ↔ hex text + live preview
       ============================================================ */
    function updateCustomPreview() {
        var hs = $('[name="bbpt_custom_header_start"]').val() || '#f5a623';
        var he = $('[name="bbpt_custom_header_end"]').val()   || '#f0b429';
        var ht = $('[name="bbpt_custom_header_text"]').val()  || '#1a1a2e';
        var bs = $('[name="bbpt_custom_btn_start"]').val()    || '#f5a623';
        var be = $('[name="bbpt_custom_btn_end"]').val()      || '#e09610';
        var bt = $('[name="bbpt_custom_btn_text"]').val()     || '#1a1a2e';

        $('.bbpt-preview-header-swatch').css({
            'background': 'linear-gradient(135deg,' + hs + ',' + he + ')',
            'color': ht
        });
        $('.bbpt-preview-btn-swatch').css({
            'background': 'linear-gradient(135deg,' + bs + ',' + be + ')',
            'color': bt
        });
    }

    // Native color → hex text
    $(document).on('input change', '.bbpt-color-native', function () {
        var val = $(this).val();
        $(this).siblings('.bbpt-color-hex').val(val);
        updateCustomPreview();
    });

    // Hex text → native color (on valid 7-char hex)
    $(document).on('input', '.bbpt-color-hex', function () {
        var val = $(this).val();
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            $(this).siblings('.bbpt-color-native').val(val);
        }
        updateCustomPreview();
    });

    updateCustomPreview();

    /* ============================================================
       Group – table selector
       ============================================================ */
    $('#bbpt-add-table-btn').on('click', function () {
        var $sel = $('#bbpt-table-add-select');
        var tid  = $sel.val();
        var text = $sel.find('option:selected').text();
        if (!tid) return;

        if ($('#bbpt-group-tables [data-id="' + tid + '"]').length) {
            alert('This table is already added.');
            return;
        }

        var $li = $(
            '<li data-id="' + tid + '">' +
            '<span class="bbpt-drag-handle dashicons dashicons-menu"></span>' +
            '<span class="bbpt-table-title">' + $('<div>').text(text).html() + '</span>' +
            '<input type="hidden" name="bbpt_table_ids[]" value="' + tid + '" />' +
            '<button type="button" class="button-link bbpt-remove-table-row"><span class="dashicons dashicons-trash"></span></button>' +
            '</li>'
        );
        $('#bbpt-group-tables').append($li);
        $sel.val('');
    });

    $(document).on('click', '.bbpt-remove-table-row', function () { $(this).closest('li').remove(); });

    if ($('#bbpt-group-tables').length) {
        $('#bbpt-group-tables').sortable({ handle: '.bbpt-drag-handle', axis: 'y' });
    }

    /* ============================================================
       Copy shortcode
       ============================================================ */
    $(document).on('click', '.bbpt-copy-shortcode', function () {
        var $btn = $(this);
        var sc   = $btn.data('sc');
        var orig = $btn.text();

        if (navigator.clipboard) {
            navigator.clipboard.writeText(sc).then(function () { flash(); });
        } else {
            var $tmp = $('<input>').val(sc).appendTo('body').select();
            document.execCommand('copy');
            $tmp.remove();
            flash();
        }

        function flash() {
            $btn.text('Copied!');
            setTimeout(function () { $btn.text(orig); }, 1800);
        }
    });

    /* Prevent Enter in feature inputs from submitting form */
    $(document).on('keydown', '.bbpt-feature-row input', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('.bbpt-add-feature').trigger('click'); }
    });
});
