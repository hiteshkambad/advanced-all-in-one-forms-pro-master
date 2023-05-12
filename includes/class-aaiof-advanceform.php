<?php
namespace AdvancedAllInOneForms;
if (!defined( 'ABSPATH')) exit;
if (!class_exists( 'AAIOF_Advanceform')){
    class AAIOF_Advanceform {
        public function aaiof_advance_form_details(){
            $show_enquiry_detail_page = get_option('show_enquiry_detail_page');
            $pdetail_form_id = get_option('pdetail_form_id');
            $show_enquiry_cart_page = get_option('show_enquiry_cart_page');
            $cart_form_id = get_option('cart_form_id');
            $enquiry_modal_title = get_option('enquiry_modal_title');
            $add_btn_title_enquiry = get_option('add_btn_title_enquiry');
            $add_enquiry_btn_details = get_option('add_enquiry_btn_details');
            $add_enquiry_btn_cart = get_option('add_enquiry_btn_cart');

            _e('<div class="captcha_details wrap" id="captcha-integration">
                    <h1 class="wp-heading-inline">Integration Advance Product Setting</h1>
                    <div class="" id="advance_product_setting">
                        <div class="inside">
                            <form id="advance_form_setting" method="post" action="">
                                <table class="form-table">
                                    <tbody>
                    
                                        <tr>
                                            <th><h5>Product Detail Page Setting : </h5></th>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                                <label for="show_enquiry_detail_page">Show product enquiry form in product detail page</label>
                                            </th>
                                            <td>
                                                <input type="checkbox" name="show_enquiry_detail_page" value="1" id="show_enquiry_detail_page" ' . (($show_enquiry_detail_page==1) ? "checked" : ""). '/>
                                            </td>
                                        </tr>
                                        <tr class="advanced_select_penquiry_form">
                                            <th scope="row">
                                                <label for="pdetail_form_id">Select  enquiry form for product detail page</label>
                                            </th>
                                            <td>
                                                <select id="pdetail_form_id" name="pdetail_form_id" class="form-control">');

                                                global $post;
                                                $args = array( 'post_type' => sanitize_text_field($_GET['post_type']),'post_status' => 'publish');
                                                $myposts = get_posts( $args );
                                                _e('<option value="">Select Form</option>');
                                                foreach ( $myposts as $post ){
                                                    _e('<option value="'.get_the_ID().'" '.(($pdetail_form_id==get_the_ID())?'selected':"").'>'.get_the_title().'</option>');
                                                }
                                                wp_reset_postdata();
                                                    
                                                _e('</select>											
                                            </td>
                                        </tr>
                                        <tr class="advanced_product_form_shortcode">
                                            <th scope="row">
                                                <label>Add enquiry button after add to cart button</label>
                                            </th>
                                            <td>
                                                <input type="checkbox" name="add_enquiry_btn_details" value="1" id="add_enquiry_btn_details" ' . (($add_enquiry_btn_details==1) ? "checked" : ""). '/>
                                            </td>
                                        </tr>
                                        <tr class="advanced_product_form_shortcode">
                                            <th colspan="2" class="or-details"><strong>OR</strong></th>
                                        </tr>
                                        <tr class="advanced_product_form_shortcode">
                                            <th scope="row">
                                                <label>Shortcode for product enquiry</label>
                                            </th>
                                            <td>
                                                [advanced_product_enquiry]
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><h5>Product Cart Page Setting :</h5> </th>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                                <label for="show_enquiry_cart_page">Show product enquiry form in cart page</label>
                                            </th>
                                            <td>
                                                <input type="checkbox" name="show_enquiry_cart_page" value="1" id="show_enquiry_cart_page" ' . (($show_enquiry_cart_page==1) ? "checked" : ""). '/>
                                            </td>
                                        </tr>
                                        <tr class="advanced_select_cenquiry_form">
                                            <th scope="row">
                                                <label for="cart_form_id">Select enquiry form for cart page</label>
                                            </th>
                                            <td>
                                                <select id="cart_form_id" name="cart_form_id" class="form-control">');

                                                    global $post;
                                                    $args = array( 'post_type' => sanitize_text_field($_GET['post_type']),'post_status' => 'publish');
                                                    $myposts = get_posts( $args );
                                                    _e('<option value="">Select Form</option>');
                                                    foreach ( $myposts as $post ){
                                                        _e('<option value="'.get_the_ID().'" '.(($cart_form_id==get_the_ID())?'selected':"").'>'.get_the_title().'</option>');
                                                    }
                                                    wp_reset_postdata();
                                                    
                                            _e('</select>
                                            </td>
                                        </tr>
                                        <tr class="advanced_cart_form_shortcode">
                                            <th scope="row">
                                                <label>Add enquiry button after cart table</label>
                                            </th>
                                            <td>
                                                <input type="checkbox" name="add_enquiry_btn_cart" value="1" id="add_enquiry_btn_cart" ' . (($add_enquiry_btn_cart==1) ? "checked" : ""). '/>
                                            </td>
                                        </tr>
                                        <tr class="advanced_cart_form_shortcode">
                                            <th colspan="2" class="or-details"><strong>OR</strong></th>
                                        </tr>
                                        <tr class="advanced_cart_form_shortcode">
                                            <th scope="row">
                                                <label>Shortcode for cart enquiry</label>
                                            </th>
                                            <td>
                                                [advanced_cart_enquiry]
                                            </td>
                                        </tr>
                                        
                                        <tr >
                                            <th><h5>Enquiry Button  Setting : </h5></th>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                                <label for="enquiry_modal_title">Add title for enquiry form</label>
                                            </th>
                                            <td>
                                                <input type="text" name="enquiry_modal_title" value="'.$enquiry_modal_title.'" id="enquiry_modal_title"/>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th scope="row">
                                                <label for="add_btn_title_enquiry">Add popup button title</label>
                                            </th>
                                            <td>
                                                <input type="text" name="add_btn_title_enquiry" value="'.$add_btn_title_enquiry.'" id="add_btn_title_enquiry"/>
                                            </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                <p class="submit">
                                    <input type="submit" id="submit" class="button button-primary" value="Save">
                                    <input type="button" id="reset" class="button button-primary" value="Reset" onclick="aaiof_advance_setting_reset()">
                                </p>
                                <span class="success_msg">Setting Saved.</span>
                            </form>
                        </div>
                    </div>
                </div>');
        }
    }
}