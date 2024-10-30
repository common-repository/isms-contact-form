<div id="isms-contact">
    <h3>Create new form</h3>
    <div class="isms-divider"></div>
        <div class="row">
            <div class="col-md-12 mt-2">
                <form id="add-new-contact">
                    <input type="text"  class="form-control"  placeholder="Enter title here" id="title" name="title" >
                    <input type="hidden" name="action" value="add_form">
					
					<div id="fields-holder">
						<input type="hidden" name="fields[1][field_type]" 	value="text">
						<input type="hidden" name="fields[1][field_name]" 	value="fname">
						<input type="hidden" name="fields[1][is_required]" 	value="1"> 

						<input type="hidden" name="fields[2][field_type]" 	value="text">
						<input type="hidden" name="fields[2][field_name]" 	value="lname"> 
						<input type="hidden" name="fields[2][is_required]" 	value="1"> 
						
						<input type="hidden" name="fields[3][field_type]" 	value="email">
						<input type="hidden" name="fields[3][field_name]" 	value="email"> 
						<input type="hidden" name="fields[3][is_required]" 	value="1"> 
						
						<input type="hidden" name="fields[4][field_type]" 	value="text">
						<input type="hidden" name="fields[4][field_name]" 	value="subject"> 
						<input type="hidden" name="fields[4][is_required]" 	value="1"> 
						
						
						<input type="hidden" name="fields[5][field_type]" 	value="teaxtarea">
						<input type="hidden" name="fields[5][field_name]" 	value="message"> 
						<input type="hidden" name="fields[5][is_required]" 	value="1"> 
						
					</div>
					
                    <div class="isms-response-holder isms-hidden"></div>
                    <div class="mt-2">
                        <ul>
                            <li class="active"><a href="javascript:void(0);" id="form-a" open-div="form-data-holder">Form</a></li>
                            <li><a href="javascript:void(0);" id="mail-a" open-div="mail-data-holder">Mail</a></li>
                            <li><a href="javascript:void(0);" id="message-a" open-div="message-data-holder">Message</a></li>
                        </ul>
                        <div id="form-data-holder" class="data-holder">
                            <h3 class="contact-label">Form</h3>
                            
                            <div id="isms-tag-generator-holder">
                                <a class="isms-tag-generator" id="text-generator" data-name="Text" data-inputtype="text">text</a>
                                <a class="isms-tag-generator" id="email-generator" data-name="Email" data-inputtype="email">email</a>
                                <a class="isms-tag-generator" id="url-generator" data-name="Url" data-inputtype="url">URL</a>
                                <a class="isms-tag-generator" id="tel-generator" data-name="Tel" data-inputtype="tel">tel</a>
                                <a class="isms-tag-generator" id="number-generator" data-name="Number" data-inputtype="number">number</a>
                                <a class="isms-tag-generator" id="date-generator" data-name="Date" data-inputtype="date">date</a>
                                <a class="isms-tag-generator" id="textarea-generator" data-name="Textarea" data-inputtype="textarea">text area</a>
                                <a class="isms-tag-generator" id="checkbox-generator" data-name="Checkboxes" data-inputtype="checkbox">checkboxes</a>
                                <a class="isms-tag-generator" id="radio-generator" data-name="Radio Buttons" data-inputtype="radio">radio buttons</a>
                                <a class="isms-tag-generator" id="accept-generator" data-name="Acceptance" data-inputtype="acceptance">acceptance</a>
                                <a class="isms-tag-generator" id="file-generator" data-name="File" data-inputtype="file">file</a>
                                <a class="isms-tag-generator" id="sumit-generator" data-name="Submit" data-inputtype="submit">submit</a>
                            </div>
                            <input type="hidden" id="addedfields" name="addedfields" value="fname,lname,email,subject,message">
                            <div id="modal-holder">
                                <div class="modal fade" id="isms-tag-generator-modal" tabindex="-1" role="dialog" aria-labelledby="ismsModalLabel" aria-hidden="true">

                                    <form id="isms-tag-form">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="ismsModalLabel"></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group row" id="fieldrequired-holder">
                                                        <label for="fieldrequired" class="col-sm-4 col-form-label">Field Type</label>
                                                        <div class="col-sm-8">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="fieldrequired">
                                                                <label class="form-check-label" for="fieldrequired">
                                                                    Required Field
                                                                </label>    
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" class="form-control" id="inputtype">
                                                    <div class="form-group row">
                                                        <label for="fieldname" class="col-sm-4 col-form-label">Name</label>
                                                        <div class="col-sm-8">
                                                          <input type="text"  class="form-control" id="fieldname">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row"id="fielddefault-holder">
                                                        <label for="fielddefault" class="col-sm-4 col-form-label">Default Value</label>
                                                        <div class="col-sm-8">
                                                          <input type="text"  class="form-control" id="fielddefault">
                                                          <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="useplaceholder">
                                                                <label class="form-check-label" for="useplaceholder">
                                                                    Use as placeholder of the field
                                                                </label>    
                                                            </div>
                                                        </div>
                                                    </div>
													   <div class="form-group row d-none" id="fieldacceptance-holder">
                                                        <label for="fieldCondition" class="col-sm-4 col-form-label">Condition</label>
                                                        <div class="col-sm-8">
                                                          <input type="text"  class="form-control" id="fieldCondition">
															<div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="conditionoptional" checked>
                                                                <label class="form-check-label" for="conditionoptional">
                                                                    Make this checkbox optional
                                                                </label>    
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row d-none" id="fieldlabel-holder">
                                                        <label for="fieldLabel" class="col-sm-4 col-form-label">Label</label>
                                                        <div class="col-sm-8">
                                                          <input type="text"  class="form-control" id="fieldLabel">
                                                        </div>
                                                    </div>  

                                                    <div class="form-group row d-none" id="fieldNumMin-holder">
                                                        <label for="fieldNumMin" class="col-sm-4 col-form-label">Min</label>
                                                        <div class="col-sm-8">
                                                          <input type="number"  min="1" class="form-control" id="fieldNumMin">
                                                        </div>
                                                    </div>  

                                                    <div class="form-group row d-none" id="fieldNumMax-holder">
                                                        <label for="fieldNumMax" class="col-sm-4 col-form-label">Max</label>
                                                        <div class="col-sm-8">
                                                          <input type="number"  min="1" class="form-control" id="fieldNumMax">
                                                        </div>
                                                    </div>  

                                                    <div class="form-group row d-none" id="fieldfileaccept-holder">
                                                        <label for="fieldfileaccept" class="col-sm-4 col-form-label">Acceptable file types</label>
                                                        <div class="col-sm-8">
                                                          <input type="text"  class="form-control" id="fieldfileaccept">
															example format 'gif','png','jpg'
                                                        </div>
                                                    </div> 
													<div class="form-group row d-none" id="fieldfilelimit-holder">
                                                        <label for="fieldfilelimit" class="col-sm-4 col-form-label">File size limit (bytes)</label>
                                                        <div class="col-sm-8">
                                                          <input type="number"  class="form-control" id="fieldfilelimit" required>
                                                        </div>
                                                    </div>  

                                                    <div class="form-group row">
                                                        <label for="fieldID" class="col-sm-4 col-form-label">ID Attribue</label>
                                                        <div class="col-sm-8">
                                                          <input type="text"  class="form-control" id="fieldID">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="fieldClass" class="col-sm-4 col-form-label">Class Attribue</label>
                                                        <div class="col-sm-8">
                                                          <input type="text"  class="form-control" id="fieldClass" value="form-control">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" readonly value='[isms-field]' id="isms-generated-tag" aria-describedby="isms-contact-insert-tag">
                                                        <div class="input-group-append">
                                                            <button class="btn  btn-primary" type="button" id="isms-contact-insert-tag">Insert tag</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

<textarea id="form-data" name="form-data" rows="20">
<label for="fname">Firstname</label>
[isms-field ty="text" fn="fname" dv="First name" ph="First name" fid="fname" fc="forn-control" fr="true" ]

<label for="lname">Lastname</label>
[isms-field ty="text" fn="lname" dv="Last name" ph="Last name" fid="lname" fc="forn-control" fr="true" ]

<label for="email">Email address</label>
[isms-field ty="email" fn="email" dv="Enter email" ph="Enter email" fid="email" fc="forn-control" fr="true" ]

<label for="subject">Subject</label>
[isms-field ty="text" fn="subject" dv="Subject" ph="Subject" fid="subject" fc="forn-control" fr="true" ]

<label for="message">Message</label>
[isms-field ty="textarea" fn="message" fid="message" fc="forn-control" fr="true" ]

[isms-field ty="submit" fl="Submit" fn="formsubmit" id="formsubmit" fc="forn-control"]
</textarea>
                       
                        </div>
                        <div id="mail-data-holder" class="data-holder isms-hidden">
                            <h3 class="contact-label">Mail</h3>
                            <p> the following fields, you can use these mail-tags:</p>

                            <span class="isms-mail-tags">[isms_fname]</span>
                            <span class="isms-mail-tags">[isms_lname]</span>
                            <span class="isms-mail-tags">[isms_email]</span>
                            <span class="isms-mail-tags">[isms_subject]</span>
                            <span class="isms-mail-tags">[isms_message]</span>
                           
                            <?php $user = wp_get_current_user(); ?>
                            <table>
                                <tr>
                                    <td class="label">To</td>
                                    <td><input type="email" id="email-to" name="email-to" value="<?php esc_html_e($user->user_email); ?>"></td>
                                </tr>
                                <tr>
                                    <td class="label">From</td>
                                    <td><input type="email" id="email-from"  name="email-from"value="<?php esc_html_e($user->user_email); ?>"></td>
                                </tr>
                                <tr>
                                    <td class="label">Subject</td>
                                    <td><input type="text" id="email-subject"  name="email-subject"value='<?php esc_html_e(get_bloginfo()); ?> [isms_subject]'></td>
                                </tr>
                                <tr>
                                    <td class="label">Additional headers</td>
                                    <td><textarea id="email-headers" name="email-headers"  rows="5">Reply-To: [isms_email]</textarea></td>
                                </tr>
                                <tr>
                                    <td class="label">Message</td>
                                    <td> 
 <textarea id="email-body" name="email-body" rows="25">From: [isms_fname] [isms_lname] 
Email: [isms_email]
	 
Subject: [isms_subject]

Message Body:
 [isms_message]

-- 
This e-mail was sent from a contact form on <?php _e(get_bloginfo()); ?> (<?php _e(get_site_url());?>)
 </textarea></td>
                                </tr>
								<tr>
                                    <td class="label"></td>
                                    <td><input type="checkbox" id="html-format" name="html-format" value="1">Use HTML content type</td>
                                </tr>
                            </table>
                        </div>

                        <div id="message-data-holder" class="data-holder isms-hidden">
                            <h3 class="contact-label">Message</h3>
                                <div class="form-group row">
                                    <label class="col-form-label">Mobile number invalid</label>
                                    <input type="text"  class="form-control" name="mobile_invalid" value="Mobile number is invalid.">
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label">Sender's message was sent successfully</label>
                                    <input type="text"  class="form-control" name="sent_successfully" value="Thank you for your message. It has been sent.">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Sender's message failed to send</label>
                                    <input type="text"  class="form-control" name="failed_to_send" value="There was an error trying to send your message. Please try again later.">
                                </div>
<!--  
                                <div class="form-group row">
                                    <label class="col-form-label">Validation errors occurred</label>
                                    <input type="text"  class="form-control" name="errors_occurred" value="One or more fields have an error. Please check and try again.">
                                </div> -->

                                <div class="form-group row">
                                    <label class="col-form-label">Submission was referred to as spam</label>
                                    <input type="text"  class="form-control" name="referred_to_as_spam" value="There was an error trying to send your message. Please try again later.">
                                </div>

                                <!--<div class="form-group row">
                                    <label class="col-form-label">There are terms that the sender must accept</label>
                                    <input type="text"  class="form-control" name="sender_must_accept" value="You must accept the terms and conditions before sending your message.">
                                </div>
    							<!--  
                                <div class="form-group row">
                                    <label class="col-form-label">There is a field that the sender must fill in</label>
                                    <input type="text"  class="form-control" name="sender_must_fill_in" value="The field is required.">
                                </div>
								
                                <div class="form-group row">
                                    <label class="col-form-label">There is a field with input that is longer than the maximum allowed length</label>
                                    <input type="text"  class="form-control" name="max_allowed_length" value="The field is too long.">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">There is a field with input that is shorter than the minimum allowed length</label>
                                    <input type="text"  class="form-control" name="min_allowed_length" value="The field is too short.">
                                </div>
								
                                <div class="form-group row">
                                    <label class="col-form-label">Date format that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="date_invalid" value="The date format is incorrect.">
                                </div>
								
                                <div class="form-group row">
                                    <label class="col-form-label">Date is earlier than minimum limit</label>
                                    <input type="text"  class="form-control" name="date_min_limit" value="The date is before the earliest one allowed.">
                                </div>

                                <div class="form-group row">
                                    <label fclass="col-form-label">Date is later than maximum limit</label>
                                    <input type="text"  class="form-control" name="date_max_limit" value="The date is after the latest one allowed.">
                                </div>-->

                                <div class="form-group row">
                                    <label class="col-form-label">Uploading a file fails for any reason</label>
                                    <input type="text"  class="form-control" name="upload_error" value="There was an unknown error uploading the file.">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Uploaded file is not allowed for file type</label>
                                    <input type="text"  class="form-control" name="upload_file_type_error" value="You are not allowed to upload files of this type.">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Uploaded file is too large</label>
                                    <input type="text"  class="form-control" name="upload_file_too_large" value="The file is too big.">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Uploading a file fails for PHP error</label>
                                        <input type="text"  class="form-control" name="upload_php_error" value="There was an error uploading the file.">
                                </div>

                              <!--  <div class="form-group row">
                                    <label class="col-form-label">Number format that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="invalid_number" value="The number format is invalid.">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Number is smaller than minimum limit</label>
                                        <input type="text"  class="form-control" name="number_min_limit" value="The number is smaller than the minimum allowed.">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Number is larger than maximum limit</label>
                                        <input type="text"  class="form-control" name="number_max_limit" value="The number is larger than the maximum allowed.">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Sender doesn't enter the correct answer to the quiz</label>
                                    <input type="text"  class="form-control" name="incorrect_quiz" value="The answer to the quiz is incorrect.">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Email address that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="email_invalid" value="The e-mail address entered is invalid.">
                                </div>

                      			  <div class="form-group row">
                                    <label class="col-form-label">URL that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="url_invalid" value="The URL is invalid.">
                                </div>-->
                                <div class="form-group row">
                                    <label  class="col-form-label">Telephone number that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="tel_invalid" value="The telephone number is invalid.">
                                </div>
                        </div>

                        <?php  submit_button(); ?>
                         
                  
                </form>
            </div>
        </div>
</div>

