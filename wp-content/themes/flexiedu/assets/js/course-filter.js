jQuery(document).ready(function($){

  console.log('Filter Initialized');

  $("#loader_coursefilter").hide();

  const ajaxUrl = flexiedu_filter_object.ajax_url;
  const ajaxNonce = flexiedu_filter_object.nonce;


    // Initialize noUiSlider
    const priceSlider = document.getElementById("priceSlider");
    noUiSlider.create(priceSlider, {
      start: [10, 100],
      connect: true,
      range: {
        min: 0,
        max: 5000,
      },
      step: 1,
    });

    const minPrice = $("#minPrice");
    const maxPrice = $("#maxPrice");

    // Update input fields on slider change
    priceSlider.noUiSlider.on("update", function (values, handle) {
      if (handle === 0) minPrice.val(Math.round(values[0]));
      if (handle === 1) maxPrice.val(Math.round(values[1]));
    });

      // Update slider when typing in inputs
    minPrice.on("change", function () {
      priceSlider.noUiSlider.set([this.value, null]);
    });

    maxPrice.on("change", function () {
      priceSlider.noUiSlider.set([null, this.value]);
    });

// ------------------------------------------------------------------------------------------------------

    // TRIGGER ON ANY CHANGE INSIDE FILTERS
    $('#course-filters').on("change", function (e) {

        let selectedCategories = $("input[name='course-categories']:checked")
            .map(function(){ return $(this).val().trim(); })
            .get();


        let selectedLevels = $("input[name='course-levels']:checked")
            .map(function(){ return $(this).val().trim(); })
            .get();

        let minPrice = $("#minPrice").val();
        let maxPrice = $("#maxPrice").val();

      
        let searchText = $(".filter-search").val();

        console.log("===== FILTER DATA =====");
        console.log("Categories:", selectedCategories);
        console.log("Levels:", selectedLevels);
        console.log("Price Range:", minPrice + " - " + maxPrice);
        console.log("Search Text:", searchText);
        console.log("=======================");

        $("#loader_coursefilter").show();

        $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: {
                action: "filter_courses",
                nonce: ajaxNonce, 
                categories: selectedCategories,
                levels: selectedLevels,
                min_price: minPrice,
                max_price: maxPrice,
                search: searchText
            },
            success: function (response) {
                $("#course-results").html(response);

                $("#loader_coursefilter").hide();
                
                $('.filter-sidebar').removeClass('active');
            }
        });




    });


// TRIGGER ON ANY CHANGE INSIDE FILTERS

      priceSlider.noUiSlider.on('change', function(values, handle) {
        
        console.log('Final values:', values);

        let selectedCategories = $("input[name='course-categories']:checked")
              .map(function(){ return $(this).val().trim(); })
              .get();

   
        let selectedLevels = $("input[name='course-levels']:checked")
              .map(function(){ return $(this).val().trim(); })
              .get();

        let searchText = $(".filter-search").val();


          console.log("===== FILTER DATA =====");
          console.log("Categories:", selectedCategories);
          console.log("Levels:", selectedLevels);
          console.log("Price Range:", values['0'] + " - " + values['1']);
          console.log("Search Text:", searchText);
          console.log("=======================");

               $("#loader_coursefilter").show();

          $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: {
                action: "filter_courses",
                nonce: ajaxNonce, 
                categories: selectedCategories,
                levels: selectedLevels,
                min_price: values['0'],
                max_price: values['1'],
                search: searchText
            },
            success: function (response) {
                $("#course-results").html(response);
                 $("#loader_coursefilter").hide();
                 $('.filter-sidebar').removeClass('active');
            }
        });

    });



    jQuery(document).on("change", "#course-sort", function () {
        let sortValue = jQuery(this).val();
        console.log("Selected Sort:", sortValue);

    });



    jQuery(document).on("click", "#load-more", function () {

        let button = $(this);
        let page = parseInt(button.attr('data-page')) + 1;

         let perPage = parseInt(button.data('perpage'));
         let total   = parseInt(button.data('total'));

         let selectedCategories = $("input[name='course-categories']:checked")
            .map(function(){ return $(this).val().trim(); })
            .get();


        let selectedLevels = $("input[name='course-levels']:checked")
            .map(function(){ return $(this).val().trim(); })
            .get();

        let minPrice = $("#minPrice").val();
        let maxPrice = $("#maxPrice").val();

      
        let searchText = $(".filter-search").val();

      $("#loader_coursefilter").show();

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: "filter_courses",
                nonce: ajaxNonce, 
                page: page,
                search: $('#search').val(),
                categories: selectedCategories,
                levels: selectedLevels,
                min_price: minPrice,
                max_price: maxPrice
            },
            beforeSend: function(){
                button.text('Loading...');

            },
            success: function(response){

              button.hide();

               $("#course-results").append(response);

                button.data('page', page);

                let shownNow = page * perPage;
                if (shownNow > total) shownNow = total;

                // Update text
                jQuery(".total-result").text(
                  "Showing 1-" + shownNow + " of " + total + " Results"
                );

                // Hide button on last page
                if (shownNow >= total) {
                    button.hide();
                }


                $("#loader_coursefilter").hide();
                $('.filter-sidebar').removeClass('active');
            }
        });

    });



});