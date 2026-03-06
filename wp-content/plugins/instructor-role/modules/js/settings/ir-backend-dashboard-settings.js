(function ($) {
    // Tabs.
    $('.ir-backend-dashboard-settings .ir-tabs-nav a').on('click', function () {
        // Check for active
        $('.ir-tabs-nav li').removeClass('active');
        $(this).parent().addClass('active');

        // Display active tab
        let currentTab = $(this).attr('href');
        $('.ir-tabs-content > div').css('display', 'none');
        $(currentTab).css('display', 'block');
        return false;
    });
})(jQuery)