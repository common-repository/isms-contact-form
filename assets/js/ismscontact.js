jQuery(document).ready(function($){

    if($('body').hasClass('isms-contact_page_isms-contact-new') || $('body').hasClass('isms-contact_page_isms-contact-update') ) {
        function StringSearch() {

            var SearchTerm = document.getElementById("addedfields").value;
            var TextSearch = document.getElementById("form-data").value;

            var fields_array = SearchTerm.split(',');
            var addedfields = "";

       
            for(var i = 0; i < fields_array.length; i++){

                if (SearchTerm.length > 0 && TextSearch.indexOf(fields_array[i]) > -1) {
                    addedfields+=","+fields_array[i];

                } else {
                    console.log(fields_array[i]+" Not found");
                }

                $('#addedfields').val(addedfields.substr(1));
            }
        } 

        $("#form-data").keyup(function () { StringSearch(); });
        StringSearch();
		
    }

    


    if($('body').hasClass('isms-contact_page_isms-contact-account')){
        console.log("test");
        var input = document.querySelector("#ismscontactphone");
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
            hiddenInput: "ismscontactphone",

            // initialCountry: "auto",
            // localizedCountries: { 'de': 'Deutschland' },
            // nationalMode: false,
            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
            placeholderNumberType: "MOBILE",
            preferredCountries: ['my', 'jp'],
            separateDialCode: true,
            utilsScript: ismscontactScript.pluginsUrl + "../assets/prefix/js/utils.js?1581331045115",
        });

        $("#ismscontactphone").keyup(function () {
            $(this).val($(this).val().replace(/^0+/, ''));
        });
    }

    $('#add-new-contact li a').click( function () {
        $('#add-new-contact li').removeClass("active");
        $('.data-holder').addClass('isms-hidden');

        var open_div = $(this).attr('open-div');
        $('#'+open_div).removeClass('isms-hidden');
        $(this).closest('li').addClass('active');

    });

    $('#update-contact li a').click( function () {
        $('#update-contact li').removeClass("active");
        $('.data-holder').addClass('isms-hidden');

        var open_div = $(this).attr('open-div');
        $('#'+open_div).removeClass('isms-hidden');
        $('#'+open_div).fadeIn('slow');
        $(this).closest('li').addClass('active');
    });

    $('#add-new-contact #submit').click(function(e){
        e.preventDefault();
        $(this).prop('disabled', true);
        var error = false;
        var form_title = $('#add-new-contact #title').val();

        if($('#add-new-contact #title').val() == "") {
            form_title = 'Untitled';
        }

        if($('#add-new-contact #form-data').val() == ""){
            error = true;
            $('.isms-response-holder').removeClass('isms-bg-success');
            $('.isms-response-holder').addClass('isms-bg-danger');
            $('.isms-response-holder').html("Form data is required!");
            $('.isms-response-holder').fadeIn('slow');        
        }

        if(!error) {
            $('.isms-response-holder').removeClass('isms-bg-danger');
            $('.isms-response-holder').fadeOut('slow');  

            var myform = document.getElementById('add-new-contact');
            var fd = new FormData(myform );

            $.ajax({
                url: ismscontactajaxurl.scriptismscontact,
                data: fd,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                        success:function(data) {
                            console.log(data);
					
                            if(parseFloat(data.status)) {
                                $('.isms-response-holder').addClass('isms-bg-success');
                                $('.isms-response-holder').html("Save Successfully!");
                                $('.isms-response-holder').fadeIn('slow');

                               setTimeout(function(){ window.location.replace('admin.php?page=isms-contact-update&id='+data.message); }, 2000);

                            }else {
                                $('.isms-response-holder').removeClass('isms-bg-success');
                                $('.isms-response-holder').addClass('isms-bg-danger');
                                $('.isms-response-holder').html(data.message);
                                $('.isms-response-holder').fadeIn('slow');
                                $('#add-new-contact #submit').prop('disabled', false);
                            }

                        },
                        error: function(errorThrown){
                            console.log(errorThrown);
                         }
                        });
            


            /*$.ajax({
                type : 'POST',
                url: ismscontactajaxurl.scriptismscontact,
                dataType: 'json',
                data : {
                    action          : 'add_form',
                    form_title      : form_title,
                    form_data       : $('#add-new-contact #form-data').val(),
                    addedfields     : $('#add-new-contact #addedfields').val(),
                    email_to        : $('#email-to').val(),
                    email_from      : $('#email-from').val(),
                    email_subject   : $('#email-subject').val(),
                    email_headers   : $('#email-headers').val(),
                    email_body      : $('#email-body').val()

                },
                success:function(data) {
                   console.log(data);
                    if(data) {
                        $('.isms-response-holder').addClass('isms-bg-success');
                        $('.isms-response-holder').html("Save Successfully!");
                        $('.isms-response-holder').fadeIn('slow');

                        setTimeout(function(){ window.location.replace('admin.php?page=isms-contact-update&id='+data); }, 2000);

                    }else {
                        $('.isms-response-holder').removeClass('isms-bg-success');
                        $('.isms-response-holder').addClass('isms-bg-danger');
                        $('.isms-response-holder').html("Failed to save!");
                        $('.isms-response-holder').fadeIn('slow');
                        $('#add-new-contact #submit').prop('disabled', false);
                    }
                },

                error: function(errorThrown){
                    console.log(errorThrown);
                }   
            });*/
        }
    });

    $('#update-contact #submit').click(function(e){
        e.preventDefault();
        var error = false;

        $(this).prop('disabled', true);

        var form_title = $('#update-contact #title').val();
        
        if($('#update-contact #title').val() == "") {
            form_title = 'Untitled';
        }

        if($('#update-contact #form-data').val() == ""){
            error = true;
            $('.isms-response-holder').removeClass('isms-bg-success');
            $('.isms-response-holder').addClass('isms-bg-danger');
            $('.isms-response-holder').html("Form data is required!");
            $('.isms-response-holder').fadeIn('slow');        
        }

        if(!error) {
            $('.isms-response-holder').removeClass('isms-bg-danger');
            $('.isms-response-holder').fadeOut('slow');   

            var myform = document.getElementById('update-contact');
            var fd = new FormData(myform );     

            $.ajax({
                url: ismscontactajaxurl.scriptismscontact,
                data: fd,
                    processData: false,
                    contentType: false,
                    type: 'POST',

                success:function(data) {
                     console.log(data);
                    if(parseFloat(data.status)) {
                        $('.isms-response-holder').addClass('isms-bg-success');
                        $('.isms-response-holder').html("Successfully updated!");
                        $('.isms-response-holder').fadeIn('slow');
                        setTimeout(function(){ window.location.replace('admin.php?page=isms-contact-update&id='+$('#update-contact #form-id').val()); }, 2000);

                    }else {
                        $('.isms-response-holder').removeClass('isms-bg-success');
                        $('.isms-response-holder').addClass('isms-bg-danger');
                        $('.isms-response-holder').html(data.message);
                        $('.isms-response-holder').fadeIn('slow');
                        $('#update-contact #submit').prop('disabled', false);
                    }
                },

                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });
        }
    });

    $(document).on('mouseenter','#dt-form-list td', function (event) {
        $( this ).find('ul').show();

    }).on('mouseleave','#dt-form-list td',  function(){
        $( this ).find('ul').hide();
    });

    $('.isms-tag-generator').click(function(){
        $('#fielddefault-holder').removeClass('d-none');
        $('#fieldrequired-holder').removeClass('d-none');
        $('#fielddefault-holder').removeClass('d-none');
        $('#fieldlabel-holder').addClass('d-none');
        $('#fieldfileaccept-holder').addClass('d-none');
        $('#fieldNumMax-holder').addClass('d-none');
        $('#fieldNumMin-holder').addClass('d-none');
		$('#fieldfilelimit-holder').addClass('d-none');

        var btntype     = $(this).attr('id');
        var btnname     = $(this).data('name');
        var inputtype   = $(this).data('inputtype');
		if(inputtype != 'acceptance') {
			 $('#fieldacceptance-holder').addClass('d-none');
		}
        if(inputtype == "checkbox" || inputtype =="radio" || inputtype == "file" || inputtype == 'acceptance' ) {
            $('#fielddefault-holder').addClass('d-none');
            $('#fieldlabel-holder').removeClass('d-none');
			
        }
		if(inputtype == 'acceptance') {
			 $('#fieldlabel-holder').addClass('d-none');
			 $('#fieldrequired-holder').addClass('d-none');
			   $('#fieldfilelimit-holder').addClass('d-none');
			 $('#fieldacceptance-holder').removeClass('d-none');
		}

        if(inputtype == "file"){
            $('#fieldlabel-holder').addClass('d-none');
            $('#fieldfilelimit-holder').removeClass('d-none');
            $('#fieldfileaccept-holder').removeClass('d-none');
        }

        if(inputtype == "submit") {
            $('#fieldrequired-holder').addClass('d-none');
            $('#fielddefault-holder').addClass('d-none');
            $('#fieldlabel-holder').removeClass('d-none');
        }

        if(inputtype == "number") {
            $('#fieldNumMax-holder').removeClass('d-none');
            $('#fieldNumMin-holder').removeClass('d-none');
        }

        var x = Math.floor((Math.random() * 1000) + 1);

        $('#fieldname').val('input-'+x);
		$('#inputtype').val(inputtype);
	
        $('#fieldClass').val('form-control');

        $('#ismsModalLabel').html("Form Tag Generator: "+btnname);
        $('#isms-tag-generator-modal').modal('show');

        generate_tag_shortcode();
    });

    $("#isms-tag-generator-modal #fieldrequired").click(function() {
        generate_tag_shortcode();
    }); 
	$("#isms-tag-generator-modal #conditionoptional").click(function() {
        generate_tag_shortcode();
		
    }); 

    $("#isms-tag-generator-modal #useplaceholder").click(function() {
        generate_tag_shortcode();
    }); 
    
    $("#isms-tag-generator-modal input").keyup(function(){
        generate_tag_shortcode();
    });

    $('#isms-tag-generator-modal .close').click(function() {
        $("#isms-tag-generator-modal input").val('');
        $("#isms-tag-generator-modal input[type='checkbox']").prop("checked",false);
    });

    function generate_tag_shortcode() {

        var required  = 'fr="true"';
        var inputtype = $('#inputtype').val();
		if(inputtype == 'acceptance'){
			 var shortcode = '[isms-field ty="checkbox" ';
		}else {
			var shortcode = '[isms-field ty="'+inputtype+'" ';
		}
        

        if($("#isms-tag-generator-modal #fieldrequired").prop("checked")){
           shortcode+='fr="true" ';
        }

        if($('#isms-tag-generator-modal #fieldname').val() != ""){
            shortcode+='fn="'+$('#isms-tag-generator-modal #fieldname').val()+'" ';
        }

        if(inputtype != 'submit' ){
            if($('#isms-tag-generator-modal #fielddefault').val() !=""){
                shortcode+='dv="'+$('#isms-tag-generator-modal #fielddefault').val()+'" ';

                if($("#isms-tag-generator-modal #useplaceholder").prop("checked")){
                    shortcode+='ph="'+$('#isms-tag-generator-modal #fielddefault').val()+'" ';
                }
            }
        }

        if(inputtype == 'number'){
            if($('#isms-tag-generator-modal #fieldNumMin').val() != "") {
                shortcode+='nmin="'+$('#isms-tag-generator-modal #fieldNumMin').val()+'" ';
            }
            if($('#isms-tag-generator-modal #fieldNumMax').val() != "") {
                shortcode+='nmax="'+$('#isms-tag-generator-modal #fieldNumMax').val()+'" ';
            }
        }

       // if(inputtype == 'file'){
           // if($('#isms-tag-generator-modal #fieldfileaccept').val() != "") {
               // shortcode+='ffa="'+$('#isms-tag-generator-modal #fieldfileaccept').val()+'" ';
           // }
       // }
		if(inputtype == 'acceptance'){
            if($("#isms-tag-generator-modal #conditionoptional").prop("checked")){
            	shortcode+='fl="'+$('#isms-tag-generator-modal #fieldCondition').val()+'(optional)" ';
             }else {
				 shortcode+='fl="'+$('#isms-tag-generator-modal #fieldCondition').val()+'" ';
				  shortcode+='fr="true" ';
				  
			}
        }


        if($('#isms-tag-generator-modal #fieldLabel').val() != "") {
            shortcode+='fl="'+$('#isms-tag-generator-modal #fieldLabel').val()+'" ';
        }
        
        if($('#isms-tag-generator-modal #fieldID').val() != ""){
            shortcode+='fid="'+$('#isms-tag-generator-modal #fieldID').val()+'" ';
        }

        if($('#isms-tag-generator-modal #fieldClass').val() != ""){
            shortcode+='fc="'+$('#isms-tag-generator-modal #fieldClass').val()+'" ';
        }
       
        shortcode+=']';
        
        $('#isms-tag-generator-modal #isms-generated-tag').val(shortcode);
    }

    $('#isms-contact-insert-tag').click(function(){
		
        var existing_fields = $('#addedfields').val();
        var field = $('#fieldname').val();
		var fieldarray = existing_fields.split(',');
		
		var field_count = fieldarray.length;
		var newcount = field_count + 1;
		var is_required = 0;
		
		if($("#isms-tag-generator-modal #fieldrequired").prop("checked")){ is_required = 1; }
		
		var fieldtype = $('#inputtype').val();
		
        if($('#inputtype') != 'submit') {

            if(existing_fields ==""){
                $('#addedfields').val(field);
            }else {
                $('#addedfields').val(existing_fields+=","+field);
            }
			
			var fieldsdata = '<input type="hidden" name="fields['+newcount+'][field_type]" value="'+$('#inputtype').val()+'">';
					fieldsdata +='<input type="hidden" name="fields['+newcount+'][field_name]" 	value="'+field+'">';
					fieldsdata +='<input type="hidden" name="fields['+newcount+'][is_required]" value="'+is_required+'">';
					if(fieldtype == 'file') {
						fieldsdata +='<input type="hidden" name="fields['+newcount+'][field_accept]" value="'+$('#fieldfileaccept').val()+'">'; 
						fieldsdata +='<input type="hidden" name="fields['+newcount+'][field_limit]" value="'+$('#fieldfilelimit').val()+'">'; 
					}
					if(fieldtype == 'number') {
						fieldsdata +='<input type="hidden" name="fields['+newcount+'][field_min]" value="'+$('#fieldNumMin').val()+'">'; 
						fieldsdata +='<input type="hidden" name="fields['+newcount+'][field_max]" value="'+$('#fieldNumMax').val()+'">'; 
					}
			
			$('#fields-holder').append(fieldsdata);
         }

        insertAtCaret('form-data',$('#isms-generated-tag').val());
        $('#isms-tag-generator-modal .close').trigger('click');
		
    });

    function insertAtCaret(areaId, text) {
        var txtarea = document.getElementById(areaId);

        if (!txtarea) {
            return;
        }

        var scrollPos = txtarea.scrollTop;
        var strPos = 0;
        var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
            "ff" : (document.selection ? "ie" : false));

        if (br == "ie") {
            txtarea.focus();
            var range = document.selection.createRange();
            range.moveStart('character', - txtarea.value.length);
            strPos = range.text.length;

        } else if (br == "ff") {
            strPos = txtarea.selectionStart;
        }

        var front = (txtarea.value).substring(0, strPos);
        var back = (txtarea.value).substring(strPos, txtarea.value.length);

        txtarea.value = front + text + back;
        strPos = strPos + text.length;

        if (br == "ie") {
            txtarea.focus();
            var ieRange = document.selection.createRange();

            ieRange.moveStart('character', -txtarea.value.length);
            ieRange.moveStart('character', strPos);
            ieRange.moveEnd('character', 0);
            ieRange.select();
        } else if (br == "ff") {
            txtarea.selectionStart = strPos;
            txtarea.selectionEnd = strPos;
            txtarea.focus();
        }

        txtarea.scrollTop = scrollPos;
    }


   
});