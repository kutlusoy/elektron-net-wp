<?php
/*
 * Copyright 2014 Osclass
 * Copyright 2026 Osclass by OsclassPoint.com
 *
 * Osclass maintained & developed by OsclassPoint.com
 * You may not use this file except in compliance with the License.
 * You may download copy of Osclass at
 *
 *     https://osclass-classifieds.com/download
 *
 * Do not edit or add to this file if you wish to upgrade Osclass to newer
 * versions in the future. Software is distributed on an "AS IS" basis, without
 * warranties or conditions of any kind, either express or implied. Do not remove
 * this NOTICE section as it contains license information and copyrights.
 */

?>
    </div><!-- /#main -->
    <?php osc_run_hook('after-main'); ?>
  </div><!-- /#content -->
</main>

<?php osc_show_widgets('footer');?>

<?php osc_run_hook('footer_pre'); ?>

<footer class="site-footer">
  <?php osc_run_hook('footer_top'); ?>
  <div class="wrapper">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo"><?php echo logo_header(); ?></div>
        <p><?php echo osc_esc_html(osc_page_description()); ?></p>
      </div>

      <div class="footer-col">
        <h4><?php _e('Explore', 'osclass-elektron'); ?></h4>
        <a href="<?php echo osc_search_url(array('page' => 'search')); ?>"><?php _e('Browse', 'osclass-elektron'); ?></a>
        <a href="<?php echo osc_item_post_url_in_category(); ?>"><?php _e('Publish Ad', 'osclass-elektron'); ?></a>
        <a href="<?php echo osc_contact_url(); ?>"><?php _e('Contact', 'osclass-elektron'); ?></a>
      </div>

      <?php osc_reset_static_pages(); ?>
      <?php if(osc_count_static_pages() > 0) { ?>
        <div class="footer-col">
          <h4><?php _e('Information', 'osclass-elektron'); ?></h4>
          <?php while(osc_has_static_pages() ) { ?>
            <a href="<?php echo osc_static_page_url(); ?>"><?php echo osc_static_page_title(); ?></a>
          <?php } ?>
        </div>
      <?php } ?>

      <?php if(osc_users_enabled()) { ?>
        <div class="footer-col">
          <h4><?php _e('Account', 'osclass-elektron'); ?></h4>
          <?php if(osc_is_web_user_logged_in()) { ?>
            <a href="<?php echo osc_user_dashboard_url(); ?>"><?php _e('My account', 'osclass-elektron'); ?></a>
            <a href="<?php echo osc_user_logout_url(); ?>"><?php _e('Logout', 'osclass-elektron'); ?></a>
          <?php } else { ?>
            <a href="<?php echo osc_user_login_url(); ?>"><?php _e('Login', 'osclass-elektron'); ?></a>
            <?php if(osc_user_registration_enabled()) { ?>
              <a href="<?php echo osc_register_account_url(); ?>"><?php _e('Register', 'osclass-elektron'); ?></a>
            <?php } ?>
          <?php } ?>
        </div>
      <?php } ?>
    </div>

    <?php if(osc_subdomain_enabled() && osc_subdomain_type() != 'language') { ?>
      <?php echo osc_subdomain_links($with_images = false, $with_counts = true, $with_toplink = true, $limit = 50, $min_item_count = 0); ?>
    <?php } ?>

    <?php osc_run_hook('footer_links'); ?>

    <div class="footer-bottom">
      <div class="copy">
        &copy; <?php echo date('Y'); ?> <?php echo osc_esc_html(osc_page_title()); ?>
        <?php if((!defined('MULTISITE') || MULTISITE==0) && osc_get_preference('footer_link', 'osclass-elektron') !== '0') {
          echo ' &middot; ' . sprintf(__('Powered by <a title="Osclass classifieds script" href="%s">Osclass</a>', 'osclass-elektron'), 'https://osclass-classifieds.com');
        } ?>
      </div>

      <?php if(osc_count_web_enabled_locales() > 1) { ?>
        <div class="language">
          <?php osc_goto_first_locale(); ?>
          <?php while(osc_has_web_enabled_locales() ) { ?>
            <a id="<?php echo osc_locale_code(); ?>" href="<?php echo osc_change_language_url ( osc_locale_code() ); ?>" class="<?php if(osc_locale_code() == osc_current_user_locale()) { ?>active<?php } ?>"><?php echo osc_locale_name(); ?></a>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>

  <?php osc_run_hook('footer'); ?>
</footer>

<?php osc_run_hook('footer_after'); ?>

<link href="<?php echo osc_assets_url('css/jquery-ui/jquery-ui.css'); ?>" rel="stylesheet" type="text/css" />

</body>
</html>
