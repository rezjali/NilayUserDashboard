<?php defined('ABSPATH') || exit; ?>

<form id="wpap-sms-send-form" class="wpap-auth-form" method="post" style="<?php echo $opt_display; ?>">
   <div class="wpap-fields">
       <?php wp_nonce_field('sms_register_login_send_nonce', 'sms_register_login_send_nonce'); ?>

       <?php do_action('wpap_top_sms_register_login_send_form'); ?>

       <label class="wpap-field-wrap">
           <span class="wpap-field-label">
               <?php esc_html_e('Phone Number', 'arvand-panel'); ?>
           </span>

           <input type="text" name="phone" />
       </label>

       <?php do_action('wpap_bottom_sms_register_login_send_form'); ?>

       <footer>
           <button class="wpap-btn-1" type="submit">
               <span class="wpap-btn-text"><?php esc_html_e('Send Code', 'arvand-panel'); ?></span>
               <div class="wpap-loading"></div>
           </button>
       </footer>
   </div>
</form>