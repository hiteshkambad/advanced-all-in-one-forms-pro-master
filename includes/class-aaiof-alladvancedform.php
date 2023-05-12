<?php
namespace AdvancedAllInOneForms;
if (!defined( 'ABSPATH')) exit;
if (!class_exists( 'AAIOF_Alladvancedform')){
    class AAIOF_Alladvancedform {
        protected static $instance;
        public static function aaiof_init(){
            is_null( self::$instance ) AND self::$instance = new self;
            return self::$instance;
        }
        public static function aaiof_on_activation(){
            global $wpdb;
            if ( ! current_user_can( 'activate_plugins' ) )
                return;
            $plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field($_REQUEST['plugin']) : '';
            
            check_admin_referer( "activate-plugin_{$plugin}" );
            
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE `{$wpdb->base_prefix}advanced_all_form_entry` (
            ID int(11) AUTO_INCREMENT NOT NULL,
            vcf_id bigint(20) UNSIGNED NOT NULL,
            data_id bigint(20) UNSIGNED NOT NULL,
            name varchar(250) NOT NULL,
            value text NOT NULL,
            created_at datetime NOT NULL DEFAULT current_timestamp(),
            modified_at datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY  (ID)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        public static function aaiof_on_deactivation(){
            if ( ! current_user_can( 'activate_plugins' ) )
                return;
            $plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field($_REQUEST['plugin']) : '';
            check_admin_referer( "deactivate-plugin_{$plugin}" );
        }
        public static function aaiof_on_uninstall(){        
        }
        public function __construct(){
            add_action('wp_enqueue_scripts', array($this, 'aaiof_frontend_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'aaiof_backend_scripts'));
            add_action( 'init', array($this, 'aaiof_my_custom_post_type') );
            add_shortcode('advanced-form', array($this, 'aaiof_advanced_form_front_view'));

            /* Ajax Hooks */
            add_action('wp_ajax_aaiof_select_field', array($this,'aaiof_select_field'));
            add_action( 'save_post', array($this,'aaiof_get_before_update_postdata'));
            add_action( 'admin_menu', array($this,'aaiof_vcf_options_page'));
            add_action( 'edit_form_after_title', array($this, 'aaiof_contact_form_tab_content'));

            add_action('wp_ajax_aaiof_recaptcha_generate',array($this, 'aaiof_recaptcha_generate'));
            add_action('wp_ajax_aaiof_advance_setting_form',array($this, 'aaiof_advance_setting_form'));
            add_action('wp_ajax_aaiof_vcfform_insert_data',array($this, 'aaiof_vcfform_insert_data'));
            add_action('wp_ajax_nopriv_aaiof_vcfform_insert_data',array($this, 'aaiof_vcfform_insert_data'));

            add_action('wp_ajax_aaiof_recaptcha_reset',array($this, 'aaiof_recaptcha_reset'));
            add_action('wp_ajax_aaiof_delete_list_view',array($this, 'aaiof_delete_list_view'));
            add_action('wp_ajax_aaiof_advance_setting_reset',array($this, 'aaiof_advance_setting_reset'));
            add_filter( 'gettext', array($this, 'aaiof_change_publish_button'), 10, 2 );

            /** Start Woocommerce Hooks **/
            add_action( 'woocommerce_before_cart',array($this, 'aaiof_action_woocommerce_after_cart_table'), 10, 0 );
            add_action( 'woocommerce_after_single_product_summary',array($this, 'aaiof_woocommerce_after_add_to_cart_button'), 10, 0 );
            add_action( 'woocommerce_after_add_to_cart_form',array($this, 'aaiof_woocommerce_add_pinquiry_button'), 10, 0 );		
            add_shortcode('advanced_product_enquiry',array($this,'aaiof_wc_add_pinquiry_button_shortcode'));
            add_action( 'woocommerce_after_cart_table',array($this, 'aaiof_woocommerce_cart_pinquiry_button'), 10, 0 );		
            add_shortcode('advanced_cart_enquiry',array($this,'aaiof_wc_cart_pinquiry_button_shortcode'));
            /** End Woocommerce Hooks **/
        }
        public function aaiof_backend_scripts(){
            wp_register_style( 'backend', AAIOF_ADVANCE_FORM_URL . 'assets/css/backend.css' );
            wp_enqueue_style( 'backend' );
            wp_register_style( 'font-awesome', AAIOF_ADVANCE_FORM_URL . 'assets/css/font-awesome.min.css' );
            wp_enqueue_style( 'font-awesome' );
            wp_register_style( 'dataTable', AAIOF_ADVANCE_FORM_URL . 'assets/css/jquery.dataTables.min.css' );
            wp_enqueue_style( 'dataTable' );
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script("jquery-ui-tabs");
            wp_enqueue_script( 'jquery-validate', AAIOF_ADVANCE_FORM_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'dataTables', AAIOF_ADVANCE_FORM_URL . 'assets/js/jquery.dataTables.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'backend', AAIOF_ADVANCE_FORM_URL . 'assets/js/backend.js', array( 'jquery' ), true, true);
            wp_localize_script( 'backend', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }
        public function aaiof_frontend_scripts(){
            wp_register_style( 'frontend', AAIOF_ADVANCE_FORM_URL . 'assets/css/frontend.css' );
            wp_enqueue_style( 'frontend' );
            wp_register_style( 'timepicker', AAIOF_ADVANCE_FORM_URL . 'assets/css/jquery.timepicker.min.css' );
            wp_enqueue_style( 'timepicker' );
            wp_register_style( 'jquery-ui', AAIOF_ADVANCE_FORM_URL . 'assets/css/jquery-ui.css' );
            wp_enqueue_style( 'jquery-ui' );
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'jquery-validate', AAIOF_ADVANCE_FORM_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'frontend', AAIOF_ADVANCE_FORM_URL . 'assets/js/frontend.js', array( 'jquery' ) );
            wp_enqueue_script( 'additional', AAIOF_ADVANCE_FORM_URL . 'assets/js/additional-methods.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'timepicker', AAIOF_ADVANCE_FORM_URL . 'assets/js/jquery.timepicker.min.js', array( 'jquery' ) );
            wp_localize_script( 'frontend', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }    
        public function aaiof_my_custom_post_type(){
            $supports = array('title');
            $args = array(
            'supports' => $supports,
            'public' => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'rewrite' => false,
            'labels' => array(
                'name' => __( 'All In One Forms' ),
                'singular_name' => __( 'All In One Form' ),
                'add_new_item' => __( 'Add New Form' ),
                'add_new' => __( 'Add New Form' ),
                'edit_item' => __( 'Edit Form' ),
            ),
            'has_archive' => false,
            );
            register_post_type( 'advanced_form', $args );
        }
        public function aaiof_change_publish_button( $translation, $text ){
            if ( 'advanced_form' == get_post_type() && $text == 'Update'){
                return 'Save';
            }
            return $translation;
        }
        public function aaiof_vcf_options_page(){
            remove_submenu_page('edit.php?post_type=advanced_form','post-new.php?post_type=advanced_form');
            add_submenu_page( 'edit.php?post_type=advanced_form', 'Recaptcha', 'Recaptcha', 'manage_options', 'cf-recaptcha-page', array($this,'aaiof_advanced_form_submenu_captcha_page' ));
            add_submenu_page( 'edit.php?post_type=advanced_form', 'Overview', 'Overview', 'manage_options', 'cf-overview-page', array($this,'aaiof_advanced_form_submenu_overview_page' ));        
            if ( class_exists( 'WooCommerce' ) ){
                add_submenu_page( 'edit.php?post_type=advanced_form', 'Advance Setting', 'Advance Setting', 'manage_options', 'cf-advance-page', array($this,'aaiof_advanced_form_submenu_advance_page' ));
            }
        }
        public function aaiof_recaptcha_generate(){
            $params = array();
            parse_str(AAIOF_sanitize($_POST['formData']), $params);
            $sitekey = $params['sitekey'];
            $secretkey = $params['secret'];

            update_option('gcaptcha_sitekey', $sitekey );
            update_option('gcaptcha_secret', $secretkey );
        }
        public function aaiof_advance_setting_form(){
            $params = array();
            parse_str(AAIOF_sanitize($_POST['formData']), $params);

            $show_enquiry_detail_page = sanitize_text_field($params['show_enquiry_detail_page']);
            $pdetail_form_id = sanitize_text_field($params['pdetail_form_id']);
            $show_enquiry_cart_page = sanitize_text_field($params['show_enquiry_cart_page']);
            $cart_form_id = sanitize_text_field($params['cart_form_id']);
            $enquiry_modal_title = sanitize_text_field($params['enquiry_modal_title']);
            $add_btn_title_enquiry = sanitize_text_field($params['add_btn_title_enquiry']);
            $add_enquiry_btn_details = sanitize_text_field($params['add_enquiry_btn_details']);
            $add_enquiry_btn_cart = sanitize_text_field($params['add_enquiry_btn_cart']);

            update_option('show_enquiry_detail_page', $show_enquiry_detail_page );
            update_option('pdetail_form_id', $pdetail_form_id );
            update_option('show_enquiry_cart_page', $show_enquiry_cart_page );
            update_option('cart_form_id', $cart_form_id );
            update_option('enquiry_modal_title', $enquiry_modal_title );
            update_option('add_btn_title_enquiry', $add_btn_title_enquiry );
            update_option('add_enquiry_btn_details', $add_enquiry_btn_details );
            update_option('add_enquiry_btn_cart', $add_enquiry_btn_cart );
        }
        public function aaiof_recaptcha_reset(){
            update_option('gcaptcha_sitekey', '');
            update_option('gcaptcha_secret', '');
        }
        public function aaiof_delete_list_view(){
            $id = sanitize_text_field($_POST['data_id']);
            global $wpdb;
            $table = $wpdb->prefix . "advanced_all_form_entry";
            $results = $wpdb->delete( $table, array( 'data_id' => $id ) );
            _e($results); die();
        }
        public function aaiof_advance_setting_reset(){
            update_option('show_enquiry_detail_page','');
            update_option('pdetail_form_id', '');
            update_option('show_enquiry_cart_page', '');
            update_option('cart_form_id', '');
        }
        public function aaiof_advanced_form_submenu_captcha_page(){
            if ( ! class_exists( 'AAIOF_Recaptchaform' ) ){
                require_once('class-aaiof-recaptchaform.php');
            }
            $captcha = new \AdvancedAllInOneForms\AAIOF_Recaptchaform();
            if(method_exists(AAIOF_Recaptchaform::class, 'aaiof_recaptcha_form_details')){  
                $captcha->aaiof_recaptcha_form_details();
            }            
        }
        public function aaiof_advanced_form_submenu_overview_page(){
            if ( ! class_exists( 'AAIOF_Overview' ) ){
                require_once('class-aaiof-overview.php');
            }
            $overviw = new \AdvancedAllInOneForms\AAIOF_Overview();
            if(method_exists(AAIOF_Overview::class, 'aaiof_wp_list_tables')){  
                $overviw->aaiof_wp_list_tables();
            }            
        }
        public function aaiof_advanced_form_submenu_advance_page(){
            if ( ! class_exists( 'AAIOF_Advanceform' ) ){
                require_once('class-aaiof-advanceform.php');
            }
            $advance = new \AdvancedAllInOneForms\AAIOF_Advanceform();            
            if(method_exists(AAIOF_Advanceform::class, 'aaiof_advance_form_details')){  
                $advance->aaiof_advance_form_details();
            }
        }    
        public function aaiof_select_field(){
            if ( ! class_exists( 'AAIOF_Customfields' ) ){
                require_once('class-aaiof-customfields.php');
            }
            $field = sanitize_text_field($_POST['field']);
            $fieldsobj = new \AdvancedAllInOneForms\AAIOF_Customfields();
            $rand = time();
            if(method_exists(AAIOF_Customfields::class, $field)){  
                $fieldsobj->$field($field,$rand);
            }            
            echo '[=] The '.ucfirst($field).' Field [ '.$rand.' ] is added.';
            die();  
        }
        public function aaiof_get_before_update_postdata( $post_id ){
            if(isset($_POST['input'])){
                //echo '<pre>';
                //print_r($_POST);exit;

                foreach(AAIOF_sanitize($_POST['input']) as $key=>$value){
                    $data['type'] = $value;
                    $data['label'] = sanitize_text_field($_POST['label'][$key]);
                    $data['name'] = sanitize_text_field($_POST['name'][$key]);
                    $data['placeholder'] = sanitize_text_field($_POST['placeholder'][$key]);
                    $data['required'] = sanitize_text_field($_POST['required'][$key]);
                    $data['id'] = sanitize_text_field($_POST['id'][$key]);
                    $data['class'] = sanitize_text_field($_POST['class'][$key]);
                    $data['max'] = sanitize_text_field($_POST['max'][$key]);
                    $data['min'] = sanitize_text_field($_POST['min'][$key]);
                    $data['filesize'] = sanitize_text_field($_POST['filesize'][$key]);
                    $data['extension'] = sanitize_text_field($_POST['extension'][$key]);
                    $data['rows'] = sanitize_text_field($_POST['rows'][$key]);
                    $data['columns'] = sanitize_text_field($_POST['columns'][$key]);
                    $data['option'] = AAIOF_sanitize($_POST['option'][$key]);
                    $data['option_val'] = AAIOF_sanitize($_POST['option_val'][$key]);
                    $data['rw-cls'] = sanitize_text_field($_POST['rw-cls'][$key]);
                    $data['raws'] = sanitize_text_field($_POST['raws'][$key]);
                    $data['rawed'] = sanitize_text_field($_POST['rawed'][$key]);
                    $data['cl-cls'] = sanitize_text_field($_POST['cl-cls'][$key]);
                    $data['col-data'] = sanitize_text_field($_POST['col-data'][$key]);
                    $data['col-data-num'] = sanitize_text_field($_POST['col-data-num'][$key]);
                    $data['cond'] = sanitize_text_field($_POST['cond'][$key]);
                    $data['condfield'] = sanitize_text_field($_POST['condfield'][$key]);
                    $data['condmatch'] = sanitize_text_field($_POST['condmatch'][$key]);
                    $data['condvalue'] = sanitize_text_field($_POST['condvalue'][$key]);
                    if(!empty($data['type'])){
                        $field[$key] = $data;
                    }
                }
            }
            $field_data = serialize($field);

            $vcf_mail = AAIOF_sanitize($_POST['vcf7-mail']);
            $mail['recipient'] = sanitize_email($vcf_mail['recipient']);
            $mail['sender'] = sanitize_email($vcf_mail['sender']);
            $mail['subject'] = sanitize_text_field($vcf_mail['subject']);
            $mail['attachments'] = wp_kses_post($vcf_mail['attachments']);
            $mail['additional_headers'] = wp_kses_post($vcf_mail['additional_headers']);

            $vcf_mail2 = AAIOF_sanitize($_POST['vcf7-mail-2']);
            $mail2['active'] = sanitize_text_field($vcf_mail2['active']);
            $mail2['recipient'] = wp_kses_post($vcf_mail2['recipient']);
            $mail2['sender'] = sanitize_email($vcf_mail2['sender']);
            $mail2['subject'] = sanitize_text_field($vcf_mail2['subject']);
            $mail2['attachments'] = wp_kses_post($vcf_mail2['attachments']);
            $mail2['additional_headers'] = wp_kses_post($vcf_mail2['additional_headers']);
            $mail_data = serialize($mail);
            
            $vcf_mail21 = serialize($mail2);
            update_post_meta( $post_id, 'vcf_fields_data', $field_data);
            update_post_meta( $post_id, 'vcf_mail_data2', $vcf_mail21);
            update_post_meta( $post_id, 'vcf_body_data', wp_kses_post($vcf_mail['body']));
            update_post_meta( $post_id, 'vcf_mail_body2', wp_kses_post($vcf_mail2['body']));
            update_post_meta( $post_id, 'vcf_mail_data', $mail_data);

            $message = AAIOF_sanitize($_POST['vcf7-message']);
            $message = serialize($message);
            update_post_meta( $post_id, 'vcf_success_sms', $message);
        }
        public function aaiof_contact_form_tab_content($post){
            if ( ! class_exists( 'AAIOF_Tabsform' ) ){
                require_once('class-aaiof-tabsform.php');
            }
            $tabs = new \AdvancedAllInOneForms\AAIOF_Tabsform();
            if(method_exists(AAIOF_Tabsform::class, 'aaiof_contact_form_tab_content')){  
                $tabs->aaiof_contact_form_tab_content($post);
            }            
        }
        public function aaiof_advanced_form_front_view($attr){
            if ( ! class_exists( 'AAIOF_Frontform' ) ){
                require_once('class-aaiof-frontform.php');
            }
            $front = new \AdvancedAllInOneForms\AAIOF_Frontform();
            if(method_exists(AAIOF_Frontform::class, 'aaiof_front_design_view')){  
                $front->aaiof_front_design_view($attr);
            }
        }
        public function aaiof_vcfform_insert_data(){
            global $wpdb;
            $table_name2 = $wpdb->prefix . "advanced_all_form_entry";
            $params = array();
            parse_str(AAIOF_sanitize($_POST['fields']), $params);
            $vcfid = sanitize_text_field($_POST['vcf_id']);
            if(!empty($_FILES['file'])){
                $file = $_FILES['file'];
                $filesize = sanitize_text_field($_POST['filesize']);
                $extension = sanitize_text_field($_POST['extension']);
                $file_img = $this->aaiof_image_upload($file,$filesize,$extension);
                
                if($file_img['success']){
                    $file_img1 = $file_img['success'];
                }
                else{
                    _e(json_encode($file_img['error']));
                    die();
                }
                $params['file'] = $file_img1;
            }            
            if ( ! class_exists( 'AAIOF_Mailform' ) ){
                require_once('class-aaiof-mailform.php');
            }
            $front = new \AdvancedAllInOneForms\AAIOF_Mailform();
            if(method_exists(AAIOF_Mailform::class, 'aaiof_mail_sent_format')){  
                $result = @$front->aaiof_mail_sent_format($params,$vcfid);                
                if($result == 1){                    
                    $last_id = $wpdb->get_var( 'SELECT data_id FROM ' . $table_name2 .' ORDER BY data_id DESC LIMIT 1');
                    $data_id = $last_id+1;
                    foreach($params as $key=>$value){
                        if($key == 'g-recaptcha-response'){                    
                        }
                        else{
                            if(is_array($value)){
                                $value = implode( ", ", $value);
                            }
                            $result_check = $wpdb->insert($table_name2, array('vcf_id'=>$vcfid, 'data_id'=>$data_id, 'name' => $key, 'value' => $value) );
                        }
                    }
                    _e($result_check);
                }else{
                    _e('Mail could not send.');                    
                }
            }
            die();
        }
        public function aaiof_image_upload($file,$filesize,$extension){
            if(isset($file)){
                $errors= array();
                $file_name = explode(".", $file["name"]);
                $newfilename = round(microtime(true)) . '.' . end($file_name);
                $file_size = $file['size'];
                $file_tmp = $file['tmp_name'];
                $file_type = $file['type'];
                $file_ext = strtolower(end(explode('.',$file['name'])));
                $extension = str_replace(".","",$extension);
                $extensions = explode(',', $extension);          
                if(in_array($file_ext,$extensions) === false && $extensions != '' && $extensions[0] != 'undefined'){
                $errors['error'] = "Only files with a following extensions are allowed : ".$extension;
                }
                $file_size = round($file_size / 1024,4);
            
                if($file_size > $filesize){
                $errors['error'] = 'File size must be smaller than '.$filesize.'KB';
                }
                
                if(empty($errors)==true){
                    $upload = wp_upload_dir();
                    $upload_dir = $upload['basedir'];
                    $upload_dir = $upload_dir . '/contact_form';
                    if (!is_dir($upload_dir)){
                        mkdir($upload_dir,0777);                    
                    }else{
                        chmod($upload_dir,0777);
                    }

                    if (!function_exists('wp_handle_upload')){ 
                        require_once( ABSPATH . 'wp-admin/includes/file.php' ); 
                    }
                    $uploadedfile = $file;
                    
                    $upload_overrides = array( 'test_form' => false );
                    add_filter('upload_dir', 'aaiof_upload_dir');
                    $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
                    remove_filter('upload_dir', 'aaiof_upload_dir');                
                    if(isset($movefile['url'])){
                        $errors['success'] = basename($movefile['url']);
                    }else{
                        $errors['success'] = basename($uploadedfile['name']);
                    }
                }
                return $errors;
            }
        }
        public function aaiof_action_woocommerce_after_cart_table(){
            $show_enquiry_cart_page = get_option('show_enquiry_cart_page');
            $enquiry_modal_title = get_option('enquiry_modal_title');

            if($show_enquiry_cart_page == 1)
            {
                $cart_form_id = get_option('cart_form_id');

                _e('<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">'.$enquiry_modal_title.'</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">');
                        _e(do_shortcode('[advanced-form id='.$cart_form_id.']'));
                        _e('</div>
                        </div>
                    </div>
                    </div>');
            }
        }
        public function aaiof_woocommerce_after_add_to_cart_button(){        
            $show_enquiry_detail_page = get_option('show_enquiry_detail_page');
            $enquiry_modal_title = get_option('enquiry_modal_title');
            if($show_enquiry_detail_page == 1){
                $pdetail_form_id = get_option('pdetail_form_id');
                $pdetail_form_title = get_option('pdetail_form_title');

                _e('<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">'.$enquiry_modal_title.'</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">');
                        _e(do_shortcode('[advanced-form id='.$pdetail_form_id.']'));
                        _e('</div>
                        </div>
                    </div>
                    </div>');
            }
        }
        public function aaiof_woocommerce_add_pinquiry_button(){
            $show_enquiry_detail_page = get_option('show_enquiry_detail_page');
            $add_btn_title_enquiry = get_option('add_btn_title_enquiry');
            $add_enquiry_btn_details = get_option('add_enquiry_btn_details');
            if($show_enquiry_detail_page == 1 && $add_enquiry_btn_details == 1){
                _e('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">'.$add_btn_title_enquiry.'</button>');
            }
        }
        public function aaiof_woocommerce_cart_pinquiry_button(){
            $show_enquiry_cart_page = get_option('show_enquiry_cart_page');
            $add_btn_title_enquiry = get_option('add_btn_title_enquiry');
            $add_enquiry_btn_cart = get_option('add_enquiry_btn_cart');
            if($show_enquiry_cart_page == 1 && $add_enquiry_btn_cart == 1){
                _e('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">'.$add_btn_title_enquiry.'</button>');
            }
        }	
        public function aaiof_wc_cart_pinquiry_button_shortcode(){
            $show_enquiry_cart_page = get_option('show_enquiry_cart_page');
            $add_btn_title_enquiry = get_option('add_btn_title_enquiry');
            if($show_enquiry_cart_page == 1){
                _e('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">'.$add_btn_title_enquiry.'</button>');
            }
        }	
        public function aaiof_wc_add_pinquiry_button_shortcode(){
            $show_enquiry_detail_page = get_option('show_enquiry_detail_page');
            $add_btn_title_enquiry = get_option('add_btn_title_enquiry');
            if($show_enquiry_detail_page == 1){
                _e('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">'.$add_btn_title_enquiry.'</button>');
            }
        }
    }    
    if (!function_exists( 'aaiof_upload_dir')){
        function aaiof_upload_dir($upload) {
            $upload['subdir'] = '/contact_form';      
            $upload['path']   = $upload['basedir'] . $upload['subdir'];    
            $upload['url']    = $upload['baseurl'] . $upload['subdir'];      
            return $upload;      
        }
    }
}