//Menu 
jQuery(document).ready(function ($) {


  $(document).on("click", ".toggle-password", function () {
    const input = $(this).siblings(".password-field");
    const type = input.attr("type") === "password" ? "text" : "password";
    input.attr("type", type);
  
    // Change icon
    $(this).toggleClass('pressed')
  });
	
	
  // Add close button dynamically when menu opens (only once)
       if ($('#menuClose').length === 0) {
        $('.menu-wrap').append('<button id="menuClose" class="menu-close">✕</button>');
    }
  // Toggle mobile menu
  $('#menuToggle').click(function () {
   
    $('.menu-wrap').toggleClass('active');
  });
// Close menu when clicking the dynamically added button
$(document).on('click', '#menuClose', function(e) {
  e.stopPropagation();
  e.preventDefault();
  $('.menu-wrap').removeClass('active');
});

	
	
  Fancybox.bind("[data-fancybox]", {
    animated: true,
    dragToClose: true,
    Toolbar: {
      display: ["close"],
    }
  });


 
 



  $('.filter-sidebar').append('<span class="close-filter">×</span>');
  $('.course-list-filter').click(function () {

    $('.filter-sidebar').toggleClass('active');

  });
  // Close sidebar on cross click
  $(document).on('click', '.close-filter', function () {
    $('.filter-sidebar').removeClass('active');
  });



  $(".testimonials-carousel").owlCarousel({
    loop: true,
    margin: 30,
    nav: true,
    dots: false,

    responsive: {
      0: {
        items: 1
      },
      768: {
        items: 2
      },
      1024: {
        items: 3
      }
    }
  });
  $(".courses-carousel").owlCarousel({
    loop: true,
    margin: 25,
    nav: true,
    dots: false,
    autoplay: false,
    responsive: {
      0: {
        items: 1
      },
      768: {
        items: 2
      },
      992: {
        items: 3
      },
      1200: {
        items: 4
      }
    }
  });

  // Custom navigation
  $('#nextBtn').click(function () {
    owl.trigger('next.owl.carousel');
  });

  $('#prevBtn').click(function () {
    owl.trigger('prev.owl.carousel');
  });
  $(window).on('scroll', function () {
    if ($(this).scrollTop() > 50) {
      $('header').addClass('scrolled');
    } else {
      $('header').removeClass('scrolled');
    }
  });



    // Toggle accordion
    $(".filter-header").on("click", function () {
      $(this).parent(".filter-section").toggleClass("collapsed");
    });

    // Toggle sections


    // Toggle sections
  // Scrollspy Logic
  var sections = $('.col-blk');
  var navLinks = $('.policy-list ul li');

  $(window).on('scroll', function () {
      var scrollPos = $(document).scrollTop() + 150; // offset for sticky sidebar

      sections.each(function () {
          var top = $(this).offset().top;
          var bottom = top + $(this).outerHeight();
          var id = $(this).attr('id');

          if (scrollPos >= top && scrollPos <= bottom) {
              navLinks.removeClass('active');
              $('.policy-list ul li a[href="#' + id + '"]').parent().addClass('active');
          }
      });
  });

  // Smooth Scroll on Click
  $('.policy-list a').on('click', function (e) {
      e.preventDefault();
      var target = $($(this).attr('href'));

      $('html, body').animate({
          scrollTop: target.offset().top - 120
      }, 500);
  });

});



