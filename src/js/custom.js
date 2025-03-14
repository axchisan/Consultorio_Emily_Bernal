(function ($) {

  "use strict";

    // PRE LOADER
    $(window).load(function(){
      $('.preloader').fadeOut(1000); // set duration in brackets    
    });


    //Navigation Section
    $('.navbar-collapse a').on('click',function(){
      $(".navbar-collapse").collapse('hide');
    });


    // Owl Carousel
    $('.owl-carousel').owlCarousel({
      animateOut: 'fadeOut',
      items:1,
      loop:true,
      autoplay:true,
    })


    // PARALLAX EFFECT
    $.stellar();  


    // SMOOTHSCROLL
    $(function() {
      $('.navbar-default a, #home a, footer a').on('click', function(event) {
        var $anchor = $(this);
          $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top - 49
          }, 1000);
            event.preventDefault();
      });
    });  


    // WOW ANIMATION
    new WOW({ mobile: false }).init();

})(jQuery);



document.addEventListener("DOMContentLoaded", function() {
  var horaInput = document.getElementById("hora");
  var horaDisplay = document.getElementById("hora-display");
  var errorDiv = document.getElementById("hora-error");

  function convertTo12Hour(time24) {
      if (!time24) return "";
      var [hours, minutes] = time24.split(":");
      hours = parseInt(hours);
      var period = hours >= 12 ? "PM" : "AM";
      var hours12 = hours % 12 || 12;
      return `${hours12}:${minutes} ${period}`;
  }

  horaInput.addEventListener("change", function() {
      var time24 = this.value;
      horaDisplay.textContent = convertTo12Hour(time24);

      if (!time24) {
          this.setCustomValidity("Por favor, seleccione una hora.");
          errorDiv.style.display = "block";
          errorDiv.textContent = "Por favor, seleccione una hora.";
          return;
      }

      var hours = parseInt(time24.split(":")[0]);
      var minutes = parseInt(time24.split(":")[1]);
      var timeInMinutes = hours * 60 + minutes;

      var morningStart = 8 * 60 + 30;
      var morningEnd = 12 * 60;
      var afternoonStart = 14 * 60;
      var afternoonEnd = 18 * 60;

      if (
          (timeInMinutes >= morningStart && timeInMinutes <= morningEnd) ||
          (timeInMinutes >= afternoonStart && timeInMinutes <= afternoonEnd)
      ) {
          this.setCustomValidity("");
          errorDiv.style.display = "none";
      } else {
          this.setCustomValidity("Por favor, seleccione una hora entre 8:30-12:00 o 02:00-06:00.");
          errorDiv.style.display = "block";
          errorDiv.textContent = "Horario no disponible. Seleccione una hora entre 8:30-12:00 o 02:00-06:00.";
      }
  });

  function validateForm() {
      var time24 = horaInput.value;

      if (!time24) {
          errorDiv.style.display = "block";
          errorDiv.textContent = "Por favor, seleccione una hora para la cita.";
          return false;
      }

      var hours = parseInt(time24.split(":")[0]);
      var minutes = parseInt(time24.split(":")[1]);
      var timeInMinutes = hours * 60 + minutes;

      var morningStart = 8 * 60 + 30;
      var morningEnd = 12 * 60;
      var afternoonStart = 14 * 60;
      var afternoonEnd = 18 * 60;

      if (
          (timeInMinutes >= morningStart && timeInMinutes <= morningEnd) ||
          (timeInMinutes >= afternoonStart && timeInMinutes <= afternoonEnd)
      ) {
          errorDiv.style.display = "none";
          return true;
      } else {
          errorDiv.style.display = "block";
          errorDiv.textContent = "Horario no disponible. Seleccione una hora entre 8:30-12:00 o 02:00-06:00.";
          return false;
      }
  }
});