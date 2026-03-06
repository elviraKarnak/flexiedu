/* cspell:ignore pallete // ignoring misspelled words that we can't change now. */

(function ($) {
    $('section.ir-tabs-content div.ir-tab:not(.active)').css('display', 'none');
    $('section.ir-tabs-content div.ir-tab.active').css('display', 'flex');
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

    $('.ir-show-appearance').on('click', function () {
        $('body').addClass('ir-appearance');
    })

    $('.ir-hide-appearance').on('click', function () {
        $('body').removeClass('ir-appearance');
    })



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
            url: ir_loc.ajax_url,
            method: 'post',
            dataType: 'json',
            timeout: 30000,
            data: {
                action: 'create_frontend_dashboard',
                ir_nonce: nonce,
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
                    console.log(response);
                    window.location.href = response.edit_url;
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
        $('#ir_frontend_course_creator_custom_primary').val(pallete.primary);
        $('#ir_frontend_course_creator_custom_accent').val(pallete.sidebar_active_bg);
        $('#ir_frontend_course_creator_custom_background').val(pallete.page_bg);
        $('#ir_frontend_course_creator_custom_headings').val(pallete.headings);
        $('#ir_frontend_course_creator_custom_text').val(pallete.text);
        $('#ir_frontend_course_creator_custom_border').val(pallete.border);
        $('#ir_frontend_course_creator_custom_side_bg').val(pallete.sidebar_bg);
        $('#ir_frontend_course_creator_custom_side_mt').val(pallete.sidebar_menu);
        $('#ir_frontend_course_creator_custom_text_light').val(pallete.text_light);
        $('#ir_frontend_course_creator_custom_text_ex_light').val(pallete.text_extra_light);
        $('#ir_frontend_course_creator_custom_text_primary_btn').val(pallete.primary_btn_text);
    }
    function saveAdminSetting(key, value) {
        jQuery.ajax({
            url: ir_loc.ajax_url,
            data: {
                action: 'update_admin_settings',
                key: key,
                value: value,
            },
            type: 'POST',
            success: function (data) {
                console.log('success');
                console.log(value);
                if(key === 'ir_enable_frontend_dashboard'){
                    if(value === 1){
                        jQuery('.ir-fcc-section-setting ').show();
                        jQuery('.ir-backend-creation').show();
                    }
                    else{
                        jQuery('.ir-fcc-section-setting ').hide();
                        jQuery('.ir-backend-creation').hide();
                    }
                }

            },
            error: function (data) {
            showNotification({
                title: sprintf(__("Something wrong!", "wdm_instructor_role")),
                message: sprintf(
                __("This user has successfully been added as an instructor", "wdm_instructor_role")
                ),
                color: 'red'
            });
            }
        })
    }

    function hide_frontend_course_creation(key, value){
        if(key === 'ir_enable_frontend_dashboard'){
            if(value === 1){
                jQuery('.ir-fcc-section-setting ').show();
                jQuery('.ir-backend-creation').show();
            }
            else{
                jQuery('.ir-fcc-section-setting ').hide();
                jQuery('.ir-backend-creation').hide();
            }
        }
    }

    jQuery('.ir-ajax-fcc.ir-switch > input[type=checkbox]').on('change', function (e) {
        var key = jQuery(this).attr('name');
        var value = jQuery(this)[0].checked ? 1 : 0;
        hide_frontend_course_creation(key, value);
    })
})(jQuery);
