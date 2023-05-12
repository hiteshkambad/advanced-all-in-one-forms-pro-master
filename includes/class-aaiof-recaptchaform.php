<?php
namespace AdvancedAllInOneForms;
if (!defined( 'ABSPATH')) exit;
if (!class_exists( 'AAIOF_Recaptchaform')){
    class AAIOF_Recaptchaform {
        public function aaiof_recaptcha_form_details(){
            $sitekey = get_option('gcaptcha_sitekey');
            $secretkey = get_option('gcaptcha_secret');
            _e('<div class="captcha_details wrap" id="captcha-integration">
                    <h1 class="wp-heading-inline">Integration reCAPTCHA</h1>
                    <div class="" id="recaptcha">
                        <div class="infobox">Whast is CAPTCHA? <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">google.com/recaptcha</a>
                        </div>
                        <div class="inside">
                            <form id="recaptcha_form" method="post" action="">
                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row">
                                                <label for="sitekey">Site Key</label>
                                            </th>
                                            <td>
                                                <input type="text" aria-required="true" value="'.$sitekey.'" id="sitekey" name="sitekey" class="regular-text code" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">
                                                <label for="secret">Secret Key</label>
                                            </th>
                                            <td>
                                                <input type="text" aria-required="true" value="'.$secretkey.'" id="secret" name="secret" class="regular-text code">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="submit">
                                    <input type="submit" id="submit" class="button button-primary" value="Save">
                                    <input type="button" id="reset" class="button button-primary" value="Reset" onclick="aaiof_recaptcha_reset()">
                                </p>
                                <span class="success_msg">Setting Saved.</span>
                            </form>
                        </div>
                    </div>
            </div>');
        }
    }
}