// cspell:ignore ftable

jQuery(document).ready(function () {
  // to show hide add instructor screen
  jQuery(".add-new-instructor-btn").on("click", function (e) {
    e.preventDefault();
    jQuery(".manage-instructors-screen").hide();

    jQuery(".add-new-instructor").show();
  });

  jQuery(".back-to-manage-instructor").on("click", function (e) {
    e.preventDefault();
    jQuery(".manage-instructors-screen").show();

    jQuery(".add-new-instructor").hide();
  });

  // To show email form
  jQuery(document).on("click", "#wdm_pay_amount", function (e) {
    e.preventDefault();
    var total_paid = parseFloat(
      jQuery("#wdm_total_amount_paid").text().replace(/,/g, "")
    );
    jQuery("#wdm_total_amount_paid_price").attr("value", total_paid);
    var amount_paid = parseFloat(
      jQuery("#wdm_amount_paid").text().replace(/,/g, "")
    );
    jQuery("#wdm_amount_paid_price").attr("value", amount_paid);
    popup("popUpDiv");
  });

  jQuery("#wdm_pay_click").click(function (e) {
    e.preventDefault();
    var update_commission = jQuery(this);
    update_commission.parent().find(".wdm_ajax_loader").show();
    var total_paid = parseFloat(
      jQuery("#wdm_total_amount_paid_price").val().replace(/,/g, "")
    );
    var amount_paid = parseFloat(
      jQuery("#wdm_amount_paid_price").val().replace(/,/g, "")
    );
    var enter_amount = parseFloat(
      jQuery("#wdm_pay_amount_price").val().replace(/,/g, "")
    );
    var instructor_id = jQuery("#instructor_id").val();
    if (enter_amount == "" || enter_amount <= 0 || isNaN(enter_amount)) {
      alert(wdm_commission_data.enter_amount);
      update_commission.parent().find(".wdm_ajax_loader").hide();
      return false;
    }
    if (enter_amount > amount_paid) {
      alert(wdm_commission_data.enter_amount_less_than);
      update_commission.parent().find(".wdm_ajax_loader").hide();
      return false;
    }
    jQuery.ajax({
      method: "post",
      url: wdm_commission_data.ajax_url,
      dataType: "JSON",
      data: {
        action: "wdm_amount_paid_instructor",
        total_paid: total_paid,
        amount_tobe_paid: amount_paid, // cspell:disable-line
        enter_amount: enter_amount,
        instructor_id: instructor_id,
      },
      success: function (response) {
        jQuery.each(response, function (i, val) {
          switch (i) {
            case "error":
              alert(val);
              update_commission.parent().find(".wdm_ajax_loader").hide();
              break;
            case "success":
              jQuery("#wdm_total_amount_paid_price").attr(
                "value",
                val.total_paid
              );
              jQuery("#wdm_amount_paid_price").attr(
                "value",
                val.amount_tobe_paid // cspell:disable-line
              );
              jQuery("#wdm_pay_amount_price").attr("value", "");
              jQuery("#wdm_pay_amount_price").val("");
              jQuery("#wdm_total_amount_paid").text(val.total_paid);
              jQuery("#wdm_amount_paid").text(val.amount_tobe_paid); // cspell:disable-line
              update_commission.parent().find(".wdm_ajax_loader").hide();
              if (val.amount_tobe_paid == 0) { // cspell:disable-line
                jQuery("#wdm_pay_click").remove();
              }
              alert(wdm_commission_data.added_successfully);
              break;
          }
        });
      },
    });
  });
  var $ftable = jQuery(".footable");
  jQuery("#change-page-size").change(function (e) {
    e.preventDefault();
    var pageSize = jQuery(this).val();
    $ftable.data("page-size", pageSize);
    $ftable.trigger("footable_initialized");
  });
  jQuery(".DataTable").DataTable();
  jQuery(".footable").footable();

  // Commission Logs Tabs.
  jQuery(".ir-commission-logs-container .ir-tabs-nav a").on(
    "click",
    function () {
      // Check for active
      jQuery(".ir-tabs-nav li").removeClass("active");
      jQuery(this).parent().addClass("active");
      // Display active tab
      let currentTab = jQuery(this).attr("href");
      jQuery(".ir-tabs-content > div").css("display", "none");
      jQuery(currentTab).css("display", "block");
      // Added for proper working of fooTables.
      jQuery(".ir-commission-logs-container").trigger("resize");
      return false;
    }
  );
});
