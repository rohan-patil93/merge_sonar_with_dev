  'use strict';
  $(document).ready(function() {

      // $('#date,#datejoin').bootstrapMaterialDatePicker({
      //        time: false,
      //        clearButton: true
      //    });
      //  $("#example-date-inputS").bootstrapMaterialDatePicker({
      //                time: false,
      //                clearButton: true
      //            });

      $("#basic-forms").steps({
          headerTag: "h3",
          bodyTag: "fieldset",
          transitionEffect: "slideLeft",
          autoFocus: true
      });
      $("#verticle-wizard").steps({
          headerTag: "h3",
          bodyTag: "fieldset",
          transitionEffect: "slide",
          stepsOrientation: "vertical",
          autoFocus: true
      });

      $("#design-wizard").steps({
          headerTag: "h3",
          bodyTag: "fieldset",
          transitionEffect: "slideLeft",
          autoFocus: true
      });




      var form = $("#example-advanced-form").show();

      form.steps({
          headerTag: "h3",
          bodyTag: "fieldset",
          transitionEffect: "slideLeft",
          onStepChanging: function(event, currentIndex, newIndex) {

              // Allways allow previous action even if the current form is not valid!
              if (currentIndex > newIndex) {
                  return true;
              }
              // Forbid next action on "Warning" step if the user is to young
              if (newIndex === 3 && Number($("#age-2").val()) < 18) {
                  return false;
              }
              // Needed in some cases if the user went back (clean up)
              if (currentIndex < newIndex) {
                  // To remove error styles
                  form.find(".body:eq(" + newIndex + ") label.error").remove();
                  form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
              }
              form.validate().settings.ignore = ":disabled,:hidden";
              return form.valid();
          },
          onStepChanged: function(event, currentIndex, priorIndex) {

              // Used to skip the "Warning" step if the user is old enough.
              if (currentIndex === 2 && Number($("#age-2").val()) >= 18) {
                  form.steps("next");
              }
              // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
              if (currentIndex === 2 && priorIndex === 3) {
                  form.steps("previous");
              }
          },
          onFinishing: function(event, currentIndex) {
// console.log(event);
              form.validate().settings.ignore = ":disabled";
              return form.valid();
          },
          onFinished: function(event, currentIndex) {
              alert("Submitted!");
              $('.content input[type="text"]').val('');
              $('.content input[type="email"]').val('');
              $('.content input[type="password"]').val('');
          }
      }).validate({
        
          errorPlacement: function errorPlacement(error, element) {

              element.before(error);
          },
          rules: {
              confirm: {
                  equalTo: "#password-2"
              }
          }
      });

 var formsl = $("#example-advanced-form_new").show();

      formsl.steps({
          headerTag: "h3",
          bodyTag: "fieldset",
          transitionEffect: "slide",
          stepsOrientation: "vertical",
          autoFocus: true,
          onStepChanging: function(event, currentIndex, newIndex) {
              // Allways allow previous action even if the current form is not valid!
              if (currentIndex > newIndex) {
                  return true;
              }
              // Forbid next action on "Warning" step if the user is to young
              if (newIndex === 3 && Number($("#age-2").val()) < 18) {
                  return false;
              }
              // Needed in some cases if the user went back (clean up)
              if (currentIndex < newIndex) {
                  // To remove error styles
                  formsl.find(".body:eq(" + newIndex + ") label.error").remove();
                  formsl.find(".body:eq(" + newIndex + ") .error").removeClass("error");
              }
              formsl.validate().settings.ignore = ":disabled,:hidden";
              return formsl.valid();
          },
          onStepChanged: function(event, currentIndex, priorIndex) {

              // Used to skip the "Warning" step if the user is old enough.
              if (currentIndex === 2 && Number($("#age-2").val()) >= 18) {
                  formsl.steps("next");
              }
              // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
              if (currentIndex === 2 && priorIndex === 3) {
                  formsl.steps("previous");
              }
          },
          onFinishing: function(event, currentIndex) {
// console.log(event);
              formsl.validate().settings.ignore = ":disabled";
              return formsl.valid();
          },
          onFinished: function(event, currentIndex) {
            formsl.submit();


             // alert("Submitted!");
              // $('.content input[type="text"]').val('');
              // $('.content input[type="email"]').val('');
              // $('.content input[type="password"]').val('');
          }
      }).validate({
        
          errorPlacement: function errorPlacement(error, element) {

              element.before(error);
          },
          rules: {
              confirm: {
                  equalTo: "#password-2"
              }
          }
      });

//employee insert form
      var form22 = $("#example-advanced-create-employee").show();
      form22.steps({
          headerTag: "h3",
          bodyTag: "fieldset",
          transitionEffect: "slideLeft",
          onStepChanging: function(event, currentIndex, newIndex) {
              // Allways allow previous action even if the current form is not valid!
              if (currentIndex > newIndex) {
                  return true;
              }
              // Forbid next action on "Warning" step if the user is to young
              if (newIndex === 3 && Number($("#age-2").val()) < 18) {
                  return false;
              }
              // Needed in some cases if the user went back (clean up)
              if (currentIndex < newIndex) {
                  // To remove error styles
                  form22.find(".body:eq(" + newIndex + ") label.error").remove();
                  form22.find(".body:eq(" + newIndex + ") .error").removeClass("error");
              }
              form22.validate().settings.ignore = ":disabled,:hidden";
              return form22.valid();
          },
          onStepChanged: function(event, currentIndex, priorIndex) {

              // Used to skip the "Warning" step if the user is old enough.
              if (currentIndex === 2 && Number($("#age-2").val()) >= 18) {
                  form22.steps("next");
              }
              // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
              if (currentIndex === 2 && priorIndex === 3) {
                  form22.steps("previous");
              }
          },
          onFinishing: function(event, currentIndex) {
              form22.validate().settings.ignore = ":disabled";
              return form22.valid();
          },
          onFinished: function(event, currentIndex) {
                var formvalue =  $("#example-advanced-create-employee").serialize();
                    jQuery.ajax({
                     url: "views/custom_insert_employee_details_popup.php",
                     data: formvalue,
                     type: "POST",
                     success:function(data){
                      console.log(data);
                      $('#dynamic_popupxxl').html(data);
                      $('#kt_modal_dynamic_popupxxl').modal("show");
                    }
                  });
          }
      }).validate({ 
          errorPlacement: function errorPlacement(error, element) {
              element.after(error);
          },
          rules: {
              confirm: {
                  equalTo: "#password-2"
              }
          }
      });

//End employee insert form


var formvendor = $("#example-advanced-form-vendorsetup").show();

      formvendor.steps({
          // headerTag: "h3",
          // bodyTag: "fieldset",
          // transitionEffect: "slideLeft",
          headerTag: "h3",
          bodyTag: "fieldset",
          transitionEffect: "slide",
          stepsOrientation: "vertical",
          onStepChanging: function(event, currentIndex, newIndex) {

              // Allways allow previous action even if the current form is not valid!
              if (currentIndex > newIndex) {
                  return true;
              }
              // Forbid next action on "Warning" step if the user is to young
              if (newIndex === 3 && Number($("#age-2").val()) < 18) {
                  return false;
              }
              // Needed in some cases if the user went back (clean up)
              if (currentIndex < newIndex) {
                  // To remove error styles
                  formvendor.find(".body:eq(" + newIndex + ") label.error").remove();
                  formvendor.find(".body:eq(" + newIndex + ") .error").removeClass("error");
              }
              formvendor.validate().settings.ignore = ":disabled,:hidden";
              return formvendor.valid();
          },
          onStepChanged: function(event, currentIndex, priorIndex) {

              // Used to skip the "Warning" step if the user is old enough.
              if (currentIndex === 2 && Number($("#age-2").val()) >= 18) {
                  formvendor.steps("next");
              }
              // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
              if (currentIndex === 2 && priorIndex === 3) {
                  formvendor.steps("previous");
              }
          },
          onFinishing: function(event, currentIndex) {
// console.log(event);
              formvendor.validate().settings.ignore = ":disabled";
              return formvendor.valid();
          },
          onFinished: function(event, currentIndex) {
              // $('.content input[type="text"]').val('');
              // $('.content input[type="email"]').val('');
              // $('.content input[type="password"]').val('');
              formvendor.submit();
          }
      }).validate({
        
          errorPlacement: function errorPlacement(error, element) {
             element.after(error);
          },
          rules: {
              confirm: {
                  equalTo: "#password-2"
              }
          }
      });




  });
