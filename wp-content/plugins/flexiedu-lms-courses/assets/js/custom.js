jQuery(document).ready(function ($) {
  var ajaxUrl = flexiedu_functions.ajax_url;
  var siteURL = flexiedu_functions.siteURL;

  $(".add_course_data").on("click", function (e) {
    e.preventDefault();
    var autorId = $(this).attr("data-author-id");
    $(this).text("adding..");

    $.ajax({
      url: ajaxUrl,
      type: "POST",
      data: {
        action: "create_course",
        autorId: autorId,
      },
      success: function (res) {
        if (res.success) {
          // Title
          console.log(res);
          var courseId = res.data.course_id;

          window.location.href = `${siteURL}/course-builder/${courseId}/`;
        } else {
          alert(res.data || "Failed to load");
        }
      },
    });
  });

  jQuery(document).on("click", ".request-enroll", function ($) {
    let btn = jQuery(this);

    let course_id = btn.data("course");
    let org_id = btn.data("org");

    if (btn.hasClass("requested")) return;

    btn.prop("disabled", true).text("Sending...");

    jQuery.ajax({
      url: ajaxUrl,
      type: "POST",
      data: {
        action: "flexi_request_enrollment",
        course_id: course_id,
        org_id: org_id,
      },
      success: function (res) {
        if (res.success) {
          btn
            .addClass("requested")
            .removeClass("btn-primary")
            .addClass("btn-secondary")
            .text("Requested");
        } else {
          btn.prop("disabled", false).text(res.data || "Try Again");
        }
      },
    });
  });

  $(document).on("click", ".page_title-wrap .toggleMenu", function(e) {
  e.preventDefault();
  $(".dashboard-content-wrapper").toggleClass("hideSidebar");
   setTimeout(function() {
    $(".banner-slider").trigger("refresh.owl.carousel");
  }, 300); // wait for layout change
});

});
