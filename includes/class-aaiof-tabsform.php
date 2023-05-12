<?php
namespace AdvancedAllInOneForms;
if (!defined( 'ABSPATH')) exit;
if(!class_exists( 'AAIOF_Customfields')){
  require_once('class-aaiof-customfields.php');
}
if (!class_exists( 'AAIOF_Tabsform')){
    class AAIOF_Tabsform {
        public function aaiof_contact_form_tab_content($post){
            if($post->post_type == 'advanced_form'){
                _e('<div class="form_details">');
                if(!empty($_GET['post'])){
                    _e('<p>Copy this shortcode and paste it into your post, page, or text widget content:</p>
                    <span class="shortcode wp-ui-highlight"><input type="text" id="vcf-shortcode" readonly="readonly" class="large-text code" value="[advanced-form id='.sanitize_text_field($_GET['post']).']"></span><br><br>');
                }
                _e('<div id="vcf7_tabs">
                    <ul>
                    <li><a href="#tabs-1">Form</a></li>
                    <li><a href="#tabs-2">Mail</a></li>
                    <li><a href="#tabs-3">Message</a></li>
                    </ul>
                    <div id="tabs-1">');
                    $this->aaiof_get_tab1_html($post);
                    _e('</div>
                    <div id="tabs-2">');
                    $this->aaiof_get_tab2_html($post);
                    _e('</div>
                    <div id="tabs-3">');
                        $this->aaiof_get_tab3_html($post);
                    _e('</div>
                    </div>
                </div>');
            }
        }
        public function aaiof_get_mail_tags($post){
            $field = get_post_meta( $post->ID, 'vcf_fields_data', true);
            $get_fields = unserialize($field);
            _e('<h3 class="mail-info-title"><span>In the following fields, you can use these mail-tags :</span> ');
            foreach($get_fields as $key=>$value){
                if($value['type'] != 'recaptcha' && $value['type'] != 'submit'){
                    if($value['type'] == 'password' || $value['type'] == 'description'){
                    }else{
                        _e('['.$value['name'].']');
                    }
                }
            }
            _e('</h3>');
        }
        public function aaiof_get_tab1_html($post){
            _e('<div class="container">
                    <div class="form-group dropdown_fields">
                    <label for="field">Select Fields:</label>
                    <select class="form-control" id="fields">
                        <option value="text">Text</option>
                        <option value="email">Email</option>
                        <option value="password">Password</option>
                        <option value="phone">Phone</option>
                        <option value="url">URL</option>
                        <option value="date">Date</option>
                        <option value="time">Time</option>
                        <option value="textarea">Textarea</option>
                        <option value="file">File</option>
                        <option value="select">Select</option>
                        <option value="radio">Radio</option>
                        <option value="rating">Rating</option>
                        
                        <option value="checkbox">Checkboxes</option><option value="acceptance">Acceptance</option><option value="description">Description</option>');

                            if ( class_exists( 'WooCommerce' ) )
                            {
                            _e('<option value="product_title">Product Title</option>
                            <option value="product_url">Product Url</option>
                            <option value="product_price">Product Price</option>
                            <option value="product_qty">Product Quantity</option>');
                            }
                        _e('<option value="recaptcha">reCaptcha</option>
                        <option value="submit">Submit</option>
                    </select>
                    <button type="button" id="add_fields" class="btn btn-link"><i class="fa fa-plus"></i></button>
                    </div>
                    <div class="form-fields">
                    <div class="bs-example">
                        <div class="accordion" id="accordionExample">
                            <ul id="sortable1" class="connectedSortable">');

                                $field = get_post_meta( $post->ID, 'vcf_fields_data', true);
                                $get_fields = unserialize($field);
                                
                                $fieldsobj = new \AdvancedAllInOneForms\AAIOF_Customfields();
                                if(isset($get_fields))
                                {
                                    foreach($get_fields as $key=>$value)
                                    {
                                        $fields = $value['type'];
                                        if(method_exists(AAIOF_Customfields::class, $fields)){  
                                            $fieldsobj->$fields($value,$key);
                                        }                                         
                                    }
                                }
                                _e('</ul>');
                            _e('</div>
                    </div>
                    </div>
                </div>');
        }
        public function aaiof_get_tab2_html($post){
                $mail_field = get_post_meta( $post->ID, 'vcf_mail_data', true);
                $mail_body = get_post_meta( $post->ID, 'vcf_body_data', true);

                $vcf_mail2 = get_post_meta( $post->ID, 'vcf_mail_data2', true);
                $mail_body2 = get_post_meta( $post->ID, 'vcf_mail_body2', true);
                $get_mail = unserialize($mail_field);
                $vcf_mail2 = unserialize($vcf_mail2);
            
                $body_html = htmlspecialchars($mail_body);
                $body_html2 = htmlspecialchars($mail_body2);
                            
                _e('<table class="form-table">
                    <tbody>
                    <tr>');

                    $this->aaiof_get_mail_tags($post);
                    
                    _e('</tr>
                    <tr>
                        <th scope="row">
                            <label for="vcf7-mail-recipient">To</label>
                        </th>
                        <td>
                            <input type="text" id="vcf7-mail-recipient" name="vcf7-mail[recipient]" class="large-text code" size="70" value="'.$get_mail['recipient'].'">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="vcf7-mail-sender">From</label>
                        </th>
                        <td>
                            <input type="text" id="vcf7-mail-sender" name="vcf7-mail[sender]" class="large-text code" size="70" value="'.$get_mail['sender'].'">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="vcf7-mail-subject">Subject</label>
                        </th>
                        <td>
                            <input type="text" id="vcf7-mail-subject" name="vcf7-mail[subject]" class="large-text code" size="70" value="'.$get_mail['subject'].'">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="vcf7-mail-additional-headers">Additional Headers</label>
                        </th>
                        <td>
                            <textarea id="vcf7-mail-additional-headers" name="vcf7-mail[additional_headers]" cols="100" rows="4" class="large-text code">'.$get_mail['additional_headers'].'</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="vcf7-mail-body">Message Body</label>
                        </th>
                        <td>
                            <textarea id="vcf7-mail-body" name="vcf7-mail[body]" cols="100" rows="18" class="large-text code">'.$body_html.'</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="vcf7-mail-attachments">File Attachments</label>
                        </th>
                        <td>
                            <textarea id="vcf7-mail-attachments" name="vcf7-mail[attachments]" cols="100" rows="4" class="large-text code">'.$get_mail['attachments'].'</textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="contact-form-editor-box-mail" id="vcf-mail-2">
                <h2>Mail (2)</h2>

                <label for="vcf-mail-2">
                    <input type="checkbox" id="mail-2-active" name="vcf7-mail-2[active]" class="toggle-form-table" value="1" '.(($vcf_mail2['active']=='1')?'checked':"").'> Use Mail (2)</label>
                <fieldset class="">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="vcf7-mail-2-recipient">To</label>
                                </th>
                                <td>
                                    <input type="text" id="vcf7-mail-2-recipient" name="vcf7-mail-2[recipient]" class="large-text code" size="70" value="'.$vcf_mail2['recipient'].'" ">
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="vcf7-mail-2-sender">From</label>
                                </th>
                                <td>
                                    <input type="text" id="vcf7-mail-2-sender" name="vcf7-mail-2[sender]" class="large-text code" size="70" value="'.$vcf_mail2['sender'].'">
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="vcf7-mail-2-subject">Subject</label>
                                </th>
                                <td>
                                    <input type="text" id="vcf7-mail-2-subject" name="vcf7-mail-2[subject]" class="large-text code" size="70" value="'.$vcf_mail2['subject'].'">
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="vcf7-mail-2-additional-headers">Additional Headers</label>
                                </th>
                                <td>
                                    <textarea id="vcf7-mail-2-additional-headers" name="vcf7-mail-2[additional_headers]" cols="100" rows="4" class="large-text code">'.$vcf_mail2['additional_headers'].'</textarea>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="vcf7-mail-2-body">Message Body</label>
                                </th>
                                <td>
                                    <textarea id="vcf7-mail-2-body" name="vcf7-mail-2[body]" cols="100" rows="18" class="large-text code">'.$body_html2.'</textarea>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="vcf7-mail-2-attachments">File Attachments</label>
                                </th>
                                <td>
                                    <textarea id="vcf7-mail-2-attachments" name="vcf7-mail-2[attachments]" cols="100" rows="4" class="large-text code">'.$vcf_mail2['attachments'].'</textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
                </div>');
        }
        public function aaiof_get_tab3_html($post){
            $message = unserialize(get_post_meta( $post->ID, 'vcf_success_sms', true));
                _e('<table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="vcf7-mail-2-recipient">Success</label>
                        </th>
                        <td>
                            <input type="text" id="vcf7-success-message" name="vcf7-message[success]" class="large-text code" size="70" value="'.$message['success'].'" placeholder="Success Message">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="vcf7-mail-2-sender">Thankyou</label>
                        </th>
                        <td>
                            <input type="url" id="vcf7-thankyou-message" name="vcf7-message[thankyou]" class="large-text code" size="70" value="'.$message['thankyou'].'" placeholder="Thankyou Page URL">
                        </td>
                    </tr>
                </tbody>
                </table>');
        }
    }
}