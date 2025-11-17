//Menu 
jQuery(document).ready(function($) {

   
        // Toggle mobile menu
        $('#menuToggle').click(function() {
          $(this).toggleClass('active');
          $('#menu').toggleClass('active');
        });

       
  
        // Mobile submenu toggle
        if ($(window).width() <= 992) {
          $('.has-submenu').click(function(e) {
            e.preventDefault();
            $(this).toggleClass('active');
            $(this).next('.submenu').toggleClass('active');
          });
        }
  
        // Handle window resize
        $(window).resize(function() {
          if ($(window).width() > 992) {
            $('#menu').removeClass('active');
            $('#menuToggle').removeClass('active');
            $('.submenu').removeClass('active');
            $('.has-submenu').removeClass('active');
          } else {
            // Re-bind click events for mobile
            $('.has-submenu').off('click').click(function(e) {
              e.preventDefault();
              $(this).toggleClass('active');
              $(this).next('.submenu').toggleClass('active');
            });
          }
        });
  
        // Close menu when clicking on a non-submenu link in mobile view
        $('.menu > li > a:not(.has-submenu)').click(function() {
          if ($(window).width() <= 992) {
            $('#menu').removeClass('active');
            $('#menuToggle').removeClass('active');
          }
        });
        $('.filter-sidebar').append('<span class="close-filter">×</span>');
        $('.course-list-filter').click(function() {
          
          $('.filter-sidebar').toggleClass('active');
         
        });
// Close sidebar on cross click
$(document).on('click', '.close-filter', function() {
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
    $('#nextBtn').click(function() {
        owl.trigger('next.owl.carousel');
    });
    
    $('#prevBtn').click(function() {
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
 
      
});
