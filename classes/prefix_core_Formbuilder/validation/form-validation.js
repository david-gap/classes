/**
 * javascript/jQuery extension for the formbuilder
 *
 * @author      David Voglgsang
 * @version     1.0
 *
 */


/*==================================================================================
  Functions
==================================================================================*/
$(function(){


    /* Global Settings
    /------------------------*/
    // email validation
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;


    /* validation function
    /------------------------*/
    function ValidateForm( parent, validation ) {
      var invalid = 0,
          activeDiv = '.form-builder-container[data-validate="' + parent + '"]';

      // check all required fields
      $(activeDiv + ' form span[data-validation="true"]').each(function(){
        // get needed vars for validation
        var type = $(this).data('valtype');

        // validate input by type
        if(type === "text"){

          var value = $(this).find('input').val();

        } else if(type === "email"){

          var email = $(this).find('input').val();
          if( !emailReg.test( email ) || email == "") {
            var value = "";
          } else {
            var value = email;
          }

        } else if(type === "radio"){

          if($(this).find("input").is(':checked')){
            var value = $(this).find("input:checked").val();
          } else {
            var value = "";
          }

        } else if(type === "select"){

          var selected = $(this).find("select :selected").text();
          if($(this).find("select :selected").is(':disabled')){
            var value = "";
          } else {
            var value = selected;
          }

        } else if(type === "checkbox"){

          if($(this).find("input").is(':checked')){
            var value = $(this).find('input:checked').map(function(_, el) {return $(el).val();}).get();
          } else {
            var value = "";
          }

        } else if(type === "textarea"){

          var value = $(this).find('textarea').val();

        }

        // add class if input is invalid
        if(value === ''){
          $(this).addClass('required');
          invalid++;
        } else {
          $(this).removeClass('required');
        }
      });

      // validate google reCAPTCHA V2
      if ($( "#g-recaptcha" ).length && grecaptcha.getResponse() === '') {
        $("#g-recaptcha").addClass('required');
      } else {
        $("#g-recaptcha").removeClass('required');
      }

      // run results
      if(validation === "live" && invalid <= 0){
        // live validation - no input is invalid
        $(activeDiv).find('input[type="submit"]').prop('disabled', false);
      } else if(validation === "live" && invalid > 0){
        // live validation - inputs are invalid
        $(activeDiv).find('input[type="submit"]').prop('disabled', true);
      } else if(invalid > 0){
        // one of the input is invalid
        event.preventDefault();
      } else {
        // formular is valid
      }
    }


    /* run validation by input change
    /------------------------*/
    $(document).on('input change', '.form-builder-container input, .form-builder-container textarea, .form-builder-container select', function (event) {
      var parent = $(this).parents('.form-builder-container').data('validate'),
          validation = $(this).parents('.form-builder-container').data('valtype');
      // if live validation is active - run validation
      if(parent && parent !== "" && validation && validation === "live"){
        ValidateForm( parent, validation );
      }
    });


    /* run validation on submit
    /------------------------*/
    $(function(){
      $(document).on('click', '.form-builder-container input[type="submit"]', function (event) {
        var parent = $(this).parents('.form-builder-container').data('validate'),
            validation = $(this).parents('.form-builder-container').data('valtype'),
            types = ['js','live'];
        // if live validation is active - run validation
        if(parent && parent !== "" && validation && types.includes(validation)){
          ValidateForm( parent, validation );
        }
      });
    });


});
