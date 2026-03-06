/* cspell:ignore pallete // ignoring misspelled words that we can't change now. */

(function ($) {
    display_active_tab();
    // Tabs.
    $('.ir-frontend-dashboard-settings .ir-tabs-nav a').on('click', function () {
        // Check for active
        $('.ir-tabs-nav li').removeClass('active');
        $(this).parent().addClass('active');

        // Display active tab
        let currentTab = $(this).attr('href');
        $('.ir-tabs-content > div').hide();
        if ('view_settings' === currentTab) {
            $(currentTab).css('display', 'flex');
        } else {
            $(currentTab).show();
        }
        return false;
    });

    // Setup Frontend Dashboard.
    $('#ir_create_frontend_dashboard').on('click', function () {
        const $this = $(this);
        $this.prop('disabled', true);

        const enable_fcc = $('#ir_enable_frontend_dashboard').prop('checked');
        const add_dash_link = $('#wdm_id_ir_dash_pri_menu').prop('checked');
        const login_redirect = $('#wdm_login_redirect').prop('checked');
        const disable_backend = $('#ir_disable_backend_dashboard').prop('checked');
        const nonce = $('#ir_view_nonce').val();

        $.ajax({
            url: ir_fd_loc.ajax_url,
            method: 'post',
            dataType: 'json',
            timeout: 30000,
            data: {
                action: 'create_frontend_dashboard',
                ir_nonce: ir_loc.frontend_dashboard_view_settings_nonce,
                enable_fcc,
                add_dash_link,
                login_redirect,
                disable_backend,

            },
            beforeSend: function () {

            },
            complete: function () {
                $this.prop('disabled', false);
            },
            success: function (response) {
                if ('success' === response.status) {
                    if('1'=== ir_loc.is_gutenberg_enabled){
                        window.location.href = response.edit_url;
                    }else{
                        window.location.href = response.edit_url+ '?action=ir_launch_setup';
                    }
                } else if('error' === response.status) {
					alert(response.message);
                }
            }
        })
    })

    $('.ir-color-patterns .ir-color-scheme').on('click', function () {
        const $this = $(this);

        // Remove previous active styling.
        $('.ir-color-patterns .ir-color-scheme.ir-active-color')
            .removeClass('ir-active-color')
            .find('.ir-color-scheme-name span.dashicons-yes-alt')
            .addClass('ir-hide');
        $('.ir-custom-color-pattern').hide();

        // Add new active styling.
        $this.addClass('ir-active-color');
        $this.find('.ir-color-scheme-name span.dashicons-yes-alt').removeClass('ir-hide');
        $this.find('input').prop('checked', true);
        if ('custom' === $this.find('input').val()) {
            $('.ir-custom-color-pattern').show();
        } else {
            // Apply current preset to custom palette.
            update_custom_pallete(ir_fd_loc.dashboard_colors[$this.find('input').val()]);
        }


    })

    $('.ir-advanced-color-title span.dashicons').on('click', function () {
        const $this = $(this);
        if ($this.hasClass('dashicons-arrow-down-alt2')) {
            $this.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
        } else {
            $this.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        }
        $('.ir-advanced-colors').slideToggle();
    })

    $('.ir-setting-title-right span.dashicons').on('click', function () {
        const $this = $(this);
        if ($this.hasClass('dashicons-arrow-down-alt2')) {
            $this.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
        } else {
            $this.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        }
        $('.ir-settings-accordion-content').slideToggle();
    })

    function update_custom_pallete(pallete) {
        $('#ir_frontend_appearance_custom_primary').val(pallete.primary);
        $('#ir_frontend_appearance_custom_accent').val(pallete.sidebar_active_bg);
        $('#ir_frontend_appearance_custom_background').val(pallete.page_bg);
        $('#ir_frontend_appearance_custom_headings').val(pallete.headings);
        $('#ir_frontend_appearance_custom_text').val(pallete.text);
        $('#ir_frontend_appearance_custom_border').val(pallete.border);
        $('#ir_frontend_appearance_custom_side_bg').val(pallete.sidebar_bg);
        $('#ir_frontend_appearance_custom_side_mt').val(pallete.sidebar_menu);
        $('#ir_frontend_appearance_custom_text_light').val(pallete.text_light);
        $('#ir_frontend_appearance_custom_text_ex_light').val(pallete.text_extra_light);
        $('#ir_frontend_appearance_custom_text_primary_btn').val(pallete.primary_btn_text);
    }

    function display_active_tab() {
        $('section.ir-tabs-content div.ir-tab:not(.active)').css('display', 'none');
        // Display active tab
        let currentTab = $('div.ir-frontend-dashboard-settings ul.ir-tabs-nav > li.active a').attr('href');
        $('.ir-tabs-content > div').hide();
        if ('view_settings' === currentTab) {
            $(currentTab).css('display', 'flex');
        } else {
            $(currentTab).show();
        }
    }
})(jQuery);
