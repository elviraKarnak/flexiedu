document.addEventListener("DOMContentLoaded", function () {
    const drawerIcon = document.getElementById("drawerIcon");
    const drawerContent = document.getElementById("drawerContent");

    if(drawerIcon){
        drawerIcon.addEventListener("click", function () {
            if (drawerContent.style.display === "none") {
                drawerContent.style.display = "block";
            } else {
                drawerContent.style.display = "none";
            }
        });
    }
    


    // show and hide
    const showAll = document.getElementById("show-all");
    const showLess = document.getElementById("show-less");
    const hiddenItems = document.querySelectorAll("#hidden-item");
    if(showAll){
        showAll.addEventListener("click", function () {
            hiddenItems.forEach(item => {
                item.style.display = 'flex';
            });
            showAll.style.display ="none";
            showLess.style.display ="flex";
        });
    }
    
    if(showLess){
        showLess.addEventListener("click", function () {
            hiddenItems.forEach(item => {
                item.style.display = 'none';
            });
            showAll.style.display ="flex";
            showLess.style.display ="none";
    
        });
    }
    

    function saveAdminSetting(key, value, successCallback) {
        jQuery.ajax({
            url: ir_loc.ajax_url,
            data: {
                action: 'update_admin_settings',
                key: key,
                value: value,
            },
            type: 'POST',
            success: function (data) {
                console.log(key);
                console.log(value);
                if(key === 'ir_disable_backend_dashboard'){
                    if(value === 0){
                        jQuery('.ir-disable-backend').hide();
                    }
                    else{
                        jQuery('.ir-disable-backend').show();
                        jQuery('.ir-flex-js').css('display', 'flex');
                    }
                }
                if (typeof successCallback === 'function') {
                    successCallback();
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

    function hide_disable_backend(key, value){
        if(key === 'ir_disable_backend_dashboard'){
            if(value === 0){
                jQuery('.ir-disable-backend').hide();
            }
            else{
                jQuery('.ir-disable-backend').show();
                jQuery('.ir-flex-js').css('display', 'flex');
            }
        }
    }

    jQuery('.dashboard-menu-item input[type=checkbox], .ir-ajax.ir-switch > input[type=checkbox], .dashboard-settings-frontend-instructor-dashboard .checkbox-label > input[type=checkbox]').on('change', function (e) {
        var key = jQuery(this).attr('name');
        var value = jQuery(this)[0].checked ? 1 : 0;
        hide_disable_backend(key, value);
    })

    jQuery('.edit-appearance-btn').on('click', function (e) {
        jQuery('.dashboard-selected-page').hide();
        jQuery('.ir-hide-on-click').hide();
        jQuery('.ir-appearance-fd').show();
    })

    jQuery('.ir-back-gd-settings').on('click', function (e) {
        jQuery('.dashboard-selected-page').show();
        jQuery('.ir-hide-on-click').show();
        jQuery('.ir-appearance-fd').hide();
    })
    
});