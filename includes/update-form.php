<div id="isms-contact" style="padding-right: 1rem">
    <h3>Edit Contact Form</h3>
    <div class="isms-divider"></div>

      <?php 
		$formid = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $form = $this->isms_contact_process->get_db_row(ISMS_CONTACT_FORM,'id',(int) $formid);
        $form_meta  = $this->isms_contact_process->get_db_row(ISMS_CONTACT_FORM_META,'form_id',(int) $formid);
        $form_messages = $this->isms_contact_process->get_db_row(ISMS_CONTACT_FORM_MESSAGE,'form_id',(int) $formid);
	 	$form_fields = $this->isms_contact_process->get_db_data_id(ISMS_CONTACT_FORM_FIELDS,'form_id',(int) $formid);

      ?>
        <div class="row">
            <div class="col-md-12 mt-2">
                <form id="update-contact">
                    <input type="text"  class="form-control"  placeholder="Enter title here" id="title" name="title" value="<?php esc_html_e( $form->title); ?>" >
                    <input type="hidden" id="form-id" name="form-id" value="<?php esc_html_e( (int) $formid); ?>">
                     <input type="hidden" name="action" value="update_form">
					<div id="fields-holder">
						<?php
						$index = 1;
						foreach($form_fields as $field) { ?>
							<input type="hidden" name="fields[<?php esc_html_e( $index);?>][field_type]" 	value="<?php esc_html_e( $field->field_type); ?>">
							<input type="hidden" name="fields[<?php esc_html_e( $index); ?>][field_name]" 	value="<?php esc_html_e( $field->field_name); ?>">
							<input type="hidden" name="fields[<?php esc_html_e( $index); ?>][is_required]" 	value="<?php esc_html_e( $field->is_required); ?>">
							<input type="hidden" name="fields[<?php esc_html_e( $index); ?>][field_accept]" value="<?php esc_html_e( $field->field_accept); ?>">  
							<input type="hidden" name="fields[<?php esc_html_e( $index); ?>][field_limit]" value="<?php esc_html_e( $field->field_limit); ?>">  
							<input type="hidden" name="fields[<?php esc_html_e( $index); ?>][field_min]" value="<?php esc_html_e( $field->field_min); ?>">  
							<input type="hidden" name="fields[<?php esc_html_e( $index); ?>][field_max]" value="<?php esc_html_e( $field->field_max); ?>">  
						
						<?php $index++; } ?>
						
						
						
					</div>
                    <br/>
                    <span class="note">Copy this shortcode and paste it into your post, page, or text widget content:</span><br/>
                    <span id="shortcode"><?php esc_html_e( $form->shortcode); ?></span>
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
                              <input type="hidden" id="addedfields" name="addedfields" value="<?php esc_html_e( $form_meta->fields); ?>">   
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
                                                        </div>
                                                    </div>  
													<div class="form-group row d-none" id="fieldfilelimit-holder">
                                                        <label for="fieldfilelimit" class="col-sm-4 col-form-label">File size limit (bytes)</label>
                                                        <div class="col-sm-8">
                                                          <input type="number"  class="form-control" id="fieldfilelimit">
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
                            <textarea id="form-data" name="form-data" rows="20"><?php _e(stripslashes(base64_decode($form->form_data))); ?></textarea>
                        </div>

                         <div id="mail-data-holder" class="data-holder isms-hidden">
                            <h3 class="contact-label">Mail</h3>
                            <p> the following fields, you can use these mail-tags:</p>
                            <?php 
                                $fields_array = explode(",", $form_meta->fields);
                                $user = wp_get_current_user();

                                foreach ($fields_array as $key => $field) { ?>
                                   <span class="isms-mail-tags">[isms_<?php esc_html_e($field); ?>]</span>
							 <?php
                                }
                             ?>
                            <table>
                                <tr>
                                    <td class="label">To</td>
                                    <td><input type="email" id="email-to" name="email-to" value="<?php esc_html_e( $form_meta->mail_to);?>"></td>
                                </tr>
                                <tr>
                                    <td class="label">From</td>
                                    <td><input type="text" id="email-from" name="email-from" value="<?php esc_html_e( $form_meta->mail_from);?>"></td>
                                </tr>
                                <tr>
                                    <td class="label">Subject</td>
                                    <td><input type="text" id="email-subject" name="email-subject" value="<?php esc_html_e( stripslashes(base64_decode($form_meta->mail_subject))); ?>"></td>
                                </tr>
                                <tr>
                                    <td class="label">Additional headers</td>
                                    <td><textarea id="email-headers" name="email-headers" rows="5"><?php esc_html_e( $form_meta->mail_additional_header);?></textarea></td>
                                </tr>
                                <tr>
                                    <td class="label">Message</td>
                                    <td> <textarea id="email-body" name="email-body" rows="25"><?php esc_html_e( stripslashes(base64_decode($form_meta->mail_body)));?>
                                    </textarea></td>
                                </tr>
								<tr>
                                    <td class="label"></td>
                                    <td><input type="checkbox" id="html-format" name="html-format" value="1" <?php if($form_meta->html_format == 1){ esc_html_e( "checked");}; ?> >Use HTML content type</td>
                                </tr>
                            </table>
                        </div>
                        <div id="message-data-holder" class="data-holder isms-hidden">
                            <h3 class="contact-label">Message</h3>
                                <div class="form-group row">
                                    <label class="col-form-label">Mobile number invalid</label>
                                    <input type="text"  class="form-control" name="mobile_invalid" value="<?php esc_html_e($form_messages->mobile_invalid);?>">
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label">Sender's message was sent successfully</label>
                                    <input type="text"  class="form-control" name="sent_successfully" value="<?php esc_html_e( $form_messages->sent_successfully);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Sender's message failed to send</label>
                                    <input type="text"  class="form-control" name="failed_to_send" value="<?php esc_html_e( $form_messages->failed_to_send);?>">
                                </div>
								<!--
                                <div class="form-group row">
                                    <label class="col-form-label">Validation errors occurred</label>
                                    <input type="text"  class="form-control" name="errors_occurred" value="<?php esc_html_e( $form_messages->errors_occurred);?>">
                                </div>-->

                                <div class="form-group row">
                                    <label class="col-form-label">Submission was referred to as spam</label>
                                    <input type="text"  class="form-control" name="referred_to_as_spam" value="<?php esc_html_e( $form_messages->referred_to_as_spam);?>">
                                </div>
								<!--
                                <div class="form-group row">
                                    <label class="col-form-label">There are terms that the sender must accept</label>
                                    <input type="text"  class="form-control" name="sender_must_accept" value="<?php esc_html_e( $form_messages->sender_must_accept);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">There is a field that the sender must fill in</label>
                                    <input type="text"  class="form-control" name="sender_must_fill_in" value="<?php esc_html_e( $form_messages->sender_must_fill_in);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">There is a field with input that is longer than the maximum allowed length</label>
                                    <input type="text"  class="form-control" name="max_allowed_length" value="<?php esc_html_e( $form_messages->max_allowed_length);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">There is a field with input that is shorter than the minimum allowed length</label>
                                    <input type="text"  class="form-control" name="min_allowed_length" value="<?php esc_html_e( $form_messages->min_allowed_length);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Date format that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="date_invalid" value="<?php esc_html_e( $form_messages->date_invalid);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Date is earlier than minimum limit</label>
                                    <input type="text"  class="form-control" name="date_min_limit" value="<?php esc_html_e( $form_messages->date_min_limit);?>">
                                </div>

                                <div class="form-group row">
                                    <label fclass="col-form-label">Date is later than maximum limit</label>
                                    <input type="text"  class="form-control" name="date_max_limit" value="<?php esc_html_e( $form_messages->date_max_limit);?>">
                                </div>-->

                                <div class="form-group row">
                                    <label class="col-form-label">Uploading a file fails for any reason</label>
                                    <input type="text"  class="form-control" name="upload_error" value="<?php esc_html_e( $form_messages->upload_error);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Uploaded file is not allowed for file type</label>
                                    <input type="text"  class="form-control" name="upload_file_type_error" value="<?php esc_html_e( $form_messages->upload_file_type_error);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Uploaded file is too large</label>
                                    <input type="text"  class="form-control" name="upload_file_too_large" value="<?php esc_html_e( $form_messages->upload_file_too_large);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Uploading a file fails for PHP error</label>
                                        <input type="text"  class="form-control" name="upload_php_error" value="<?php esc_html_e( $form_messages->upload_php_error);?>">
                                </div>
								<!--
                                <div class="form-group row">
                                    <label class="col-form-label">Number format that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="invalid_number" value="<?php esc_html_e( $form_messages->invalid_number);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Number is smaller than minimum limit</label>
                                        <input type="text"  class="form-control" name="number_min_limit" value="<?php esc_html_e( $form_messages->number_min_limit);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Number is larger than maximum limit</label>
                                        <input type="text"  class="form-control" name="number_max_limit" value="<?php esc_html_e( $form_messages->number_max_limit);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Sender doesn't enter the correct answer to the quiz</label>
                                        <input type="text"  class="form-control" name="incorrect_quiz" value="<?php esc_html_e( $form_messages->incorrect_quiz);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">Email address that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="email_invalid" value="<?php esc_html_e( $form_messages->email_invalid);?>">
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label">URL that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="url_invalid" value="<?php esc_html_e( $form_messages->url_invalid);?>">
                                </div>-->
                                <div class="form-group row">
                                    <label  class="col-form-label">Telephone number that the sender entered is invalid</label>
                                        <input type="text"  class="form-control" name="tel_invalid" value="<?php esc_html_e( $form_messages->tel_invalid);?>">
                                </div>
                        </div>

                        <?php  submit_button(); ?>
                         
                    </div>
                </form>
            </div>
        </div>
</div>
