<?php defined('ABSPATH') || exit; ?>

<form id="wpap-send-activation-link-form" class="wpap-auth-form" method="post">
   <div class="wpap-fields">
       <?php wp_nonce_field('activation_email_nonce', 'activation_email_nonce'); ?>

       <label class="wpap-field-wrap">
           <span class="wpap-field-label">
               <?php esc_attr_e('Enter your email', 'arvand-panel'); ?>
           </span>

           <input type="email" name="email" />
       </label>

       <footer>
           <button class="wpap-btn-1" type="submit" name="send_email">
               <span class="wpap-btn-text"><?php esc_attr_e('Send activation link', 'arvand-panel'); ?></span>
               <div class="wpap-loading"></div>
           </button>
       </footer>
   </div>
</form>