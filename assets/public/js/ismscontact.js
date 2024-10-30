window.addEventListener('load', function () {
    var formID = jQuery(".isms-contact-form").attr('id');
	
  	jQuery('<input type="hidden" id="isms-contact-country-code" name="isms_contact_country_code" value="60"/><input type="tel"  class="form-control" id="mobilefield" name="mobilefield" required>Capcha:<br/><input name="captcha-input" type="text" class="captcha-input" required><br/><input type="text" name="session-captcha" id="session-captcha" readonly/><br/><br/>').insertBefore('#'+formID+' input[type="submit"]'); 
    jQuery('#session-captcha').val(jQuery('#generated-captcha').val());
    jQuery('#session-captcha').bind("cut copy paste",function(e) {
        e.preventDefault();
    });
    jQuery('#generated-captcha').bind("cut copy paste",function(e) {
        e.preventDefault();
    });
    
   
  	function validateForm() {
        var isValid = true;
      
        jQuery('#'+formID+' input').each(function() {
            if(jQuery(this).attr('required')){
                if(jQuery(this).val() === ''){
                    isValid = false;
                }
            }
        });
        return isValid;
    }

    jQuery(document).on('click', '.isms-contact-form ul#country-listbox li', function() {
        jQuery('.isms-contact-form #isms-contact-country-code').val(jQuery(this).attr('data-dial-code'));
        jQuery('.isms-contact-form input[name="isms_contact_mobilefield_hidden"]').val('+'+jQuery(this).attr('data-dial-code')+jQuery(mobilefield).val());
                
    });


     if(jQuery('.isms-contact-form #mobilefield').length) {
  		var input = document.querySelector("#mobilefield");
  		
        window.intlTelInput(input, {
            //allowDropdown: false,
            // autoHideDialCode: false,
            //autoPlaceholder: "off",
            // dropdownContainer: document.body,
            // excludeCountries: ["us"],
            // formatOnDisplay: false,
            //geoIpLookup: function (callback) {
            //   $.get("http://ipinfo.io", function () {
            //   }, "jsonp").always(function (resp) {
            //       var countryCode = (resp && resp.country) ? resp.country : "";
            //      callback(countryCode);
            //   });
            // },
            hiddenInput: "isms_contact_mobilefield_hidden",

            // initialCountry: "auto",
            // localizedCountries: { 'de': 'Deutschland' },
            // nationalMode: false,
            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
            placeholderNumberType: "MOBILE",
            preferredCountries: ['my', 'jp'],
            separateDialCode: true,
            utilsScript: ismscontactScript.pluginsUrl + "../assets/prefix/js/utils.js?1581331045115",
        });

        jQuery(".isms-contact-form #mobilefield").keyup(function () {
            jQuery(this).val(jQuery(this).val().replace(/^0+/, ''));
            jQuery('.isms-contact-form input[name="isms_contact_mobilefield_hidden"]').val('+'+jQuery('.isms-contact-form #isms-contact-country-code').val()+jQuery(this).val().replace(/^0+/, ''));

        });


        jQuery('#'+formID+' input[type="submit"]').click(function(event){
			var original_val = jQuery(this).val();
			
			
        	if(document.querySelector('form#'+formID).checkValidity()){
				
                if(validateForm()) {
                   	event.preventDefault();
					if(original_val == "Submit") {
						jQuery(this).val("Submitting...");
					}else if(original_val == "Send") {
						jQuery(this).val("Submitting...");
					}
					jQuery('.isms-response-holder').removeClass('isms-bg-success');
					jQuery('.isms-response-holder').removeClass('isms-bg-danger');
                    jQuery('.isms-response-holder').fadeOut('slow');	
                  	var myform = document.getElementById(formID);
   					var fd = new FormData(myform );
                   
                    jQuery.ajax({
                    	url: isms_contact_public_ajax.ajaxurl,
                       	data: fd,

				        processData: false,
				        contentType: false,

				        type: 'POST',
                            success:function(data) {
								console.log(original_val);
								jQuery('#'+formID+' input[type="submit"]').val(original_val);
                            	console.log(data);
                                if(parseFloat(data.status)) {
                                    jQuery('.isms-response-holder').addClass('isms-bg-success');
                                    jQuery('.isms-response-holder').html(data.message);
                                    jQuery('.isms-response-holder').fadeIn('slow');
                                }else{
                                    jQuery('.isms-response-holder').removeClass('isms-bg-success');
                                    jQuery('.isms-response-holder').addClass('isms-bg-danger');
                                    jQuery('.isms-response-holder').html(data.message);
                                    jQuery('.isms-response-holder').fadeIn('slow');
                                   
                                }

                            },
                            error: function(errorThrown){
                                console.log(errorThrown);
                             }
                        });
                    }
                }
        });	
        
    }

});