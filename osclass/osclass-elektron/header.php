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
<?php
$palettes = elektron_style_options()['palette'];
$swatches = array(
  'blue'      => array('#2da4fa', '#22d3ee'),
  'neon'      => array('#00f0ff', '#ff2a6d'),
  'synthwave' => array('#ff3cac', '#ffb347'),
  'matrix'    => array('#00ff88', '#b6ff00'),
  'rust'      => array('#ff7a1a', '#e0b04a'),
  'iron'      => array('#9db7cf', '#ffb020'),
  'violet'    => array('#bd93f9', '#ff79c6'),
);
?>
<!DOCTYPE html>
<html dir="<?php echo elektron_default_direction()=='0' ? 'ltr': 'rtl'; ?>" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>"<?php echo elektron_html_attributes(); ?>>
  <head>
    <?php osc_current_web_theme_path('head.php') ; ?>
  </head>
<body <?php elektron_body_class(); ?>>
<a class="skip-link" href="#content"><?php _e('Skip to content', 'osclass-elektron'); ?></a>
<?php $hud = elektron_network_items(); ?>
<?php if(!empty($hud)) { ?>
  <div class="hud-bar" role="region" aria-label="<?php echo osc_esc_html(__('Network status', 'osclass-elektron')); ?>">
    <ul class="hud-list">
      <?php $explorer = elektron_network_site_url(); ?>
      <li class="hud-live"><?php if($explorer !== '') { ?><a href="<?php echo osc_esc_html($explorer); ?>" rel="noopener" target="_blank"><?php _e('Live', 'osclass-elektron'); ?></a><?php } else { _e('Live', 'osclass-elektron'); } ?></li>
      <?php foreach($hud as $item) { ?>
        <li><?php echo osc_esc_html($item[0]); ?> <b><?php echo osc_esc_html($item[1]); ?></b></li>
      <?php } ?>
    </ul>
  </div>
<?php } ?>
<header class="site-header">
  <?php osc_run_hook('header_top'); ?>
  <div class="wrapper">
    <div class="header-bar">
      <div id="logo">
        <?php echo logo_header(); ?>
      </div>

      <nav class="nav" id="site-nav" aria-label="<?php echo osc_esc_html(__('Main menu', 'osclass-elektron')); ?>">
        <?php osc_run_hook('header_links'); ?>
        <a href="<?php echo osc_base_url(); ?>" class="nav-home"><?php _e('Home', 'osclass-elektron'); ?></a>
        <a href="<?php echo osc_search_url(array('page' => 'search')); ?>"><?php _e('Browse', 'osclass-elektron'); ?></a>
        <a href="<?php echo osc_contact_url(); ?>"><?php _e('Contact', 'osclass-elektron'); ?></a>

        <?php if(osc_users_enabled()) { ?>
          <?php if(osc_is_web_user_logged_in()) { ?>
            <a href="<?php echo osc_user_dashboard_url(); ?>" class="my-account">
              <span class="avatar"><?php echo osc_esc_html(mb_strtoupper(mb_substr((string)osc_logged_user_name(), 0, 1))); ?><img src="<?php echo osc_user_profile_img_url(osc_logged_user_id()); ?>" alt="" width="28" height="28" onerror="this.remove()"/></span>
              <span><?php echo osc_logged_user_name(); ?></span>
            </a>
            <a href="<?php echo osc_user_logout_url(); ?>" class="logout"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> <span><?php _e('Logout', 'osclass-elektron'); ?></span></a>
          <?php } else { ?>
            <a id="login_open" href="<?php echo osc_user_login_url(); ?>"><?php _e('Login', 'osclass-elektron') ; ?></a>

            <?php if(osc_user_registration_enabled()) { ?>
              <a href="<?php echo osc_register_account_url() ; ?>"><?php _e('Register', 'osclass-elektron'); ?></a>
            <?php } ?>
          <?php } ?>
        <?php } ?>

        <?php if(osc_users_enabled() || ( !osc_users_enabled() && !osc_reg_user_post() )) { ?>
          <a class="publish btn btn-primary" href="<?php echo osc_item_post_url_in_category() ; ?>"><i class="fas fa-plus" aria-hidden="true"></i> <span><?php _e('Publish Ad', 'osclass-elektron');?></span></a>
        <?php } ?>
      </nav>

      <div class="header-tools">
        <details class="style-menu">
          <summary class="tool-btn" aria-label="<?php echo osc_esc_html(__('Appearance', 'osclass-elektron')); ?>" title="<?php echo osc_esc_html(__('Appearance', 'osclass-elektron')); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.8-.9 1.5-1.9-.3-1 .4-2.1 1.5-2.1H17a4 4 0 0 0 4-4c0-5-4-10-9-10z"/><circle cx="7.5" cy="11" r="1" fill="currentColor"/><circle cx="11" cy="7" r="1" fill="currentColor"/><circle cx="16" cy="8" r="1" fill="currentColor"/></svg></summary>
          <div class="style-panel">
            <p class="style-title"><?php _e('Color theme', 'osclass-elektron'); ?></p>
            <div class="palette-list">
              <?php foreach($palettes as $key => $label) { ?>
                <button type="button" class="palette-option" data-palette="<?php echo $key; ?>" aria-pressed="false">
                  <span class="swatch" style="background:linear-gradient(135deg,<?php echo $swatches[$key][0]; ?> 50%,<?php echo $swatches[$key][1]; ?> 50%)"></span>
                  <?php echo $label; ?>
                </button>
              <?php } ?>
            </div>
            <button type="button" class="fx-toggle" data-label="<?php echo osc_esc_html(__('Effects: %s', 'osclass-elektron')); ?>" data-labels="<?php echo osc_esc_html(json_encode(elektron_style_options()['fx'])); ?>"></button>
          </div>
        </details>

        <button type="button" class="tool-btn scheme-toggle" aria-pressed="false" aria-label="<?php echo osc_esc_html(__('Toggle light and dark mode', 'osclass-elektron')); ?>" title="<?php echo osc_esc_html(__('Toggle light and dark mode', 'osclass-elektron')); ?>">
          <svg class="icon-dark" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z"/></svg><svg class="icon-light" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        </button>

        <button type="button" class="tool-btn menu-toggle" aria-expanded="false" aria-controls="site-nav" aria-label="<?php echo osc_esc_html(__('Menu', 'osclass-elektron')); ?>">
          <span class="bars" aria-hidden="true"><span></span><span></span><span></span></span>
        </button>
      </div>
    </div>
  </div>
  <?php osc_run_hook('header_bottom'); ?>
</header>

<?php osc_run_hook('header_after'); ?>

<?php if(osc_get_preference('header-728x90', 'osclass-elektron') <> '') { ?>
  <section class="header-ad">
    <div class="wrapper">
      <div class="ads_header"><?php echo osc_get_preference('header-728x90', 'osclass-elektron'); ?></div>
    </div>
  </section>
<?php } ?>

<?php if(osc_is_home_page() ) { ?>
  <?php osc_run_hook('home_search_pre'); ?>
  <?php osc_current_web_theme_path('hero.php'); ?>
  <?php osc_run_hook('home_search_after'); ?>
<?php } ?>

<main>
<?php osc_show_widgets('header'); ?>
  <?php $breadcrumb = osc_breadcrumb('>', false, get_breadcrumb_lang()); ?>

  <?php if($breadcrumb !== '') { ?>
    <div class="wrapper wrapper-flash">
      <div class="breadcrumb">
        <?php echo $breadcrumb; ?>
      </div>
    </div>
  <?php } ?>

  <div class="wrapper wrapper-flash flash2"><?php osc_show_flash_message(); ?></div>

  <?php osc_run_hook('before-content'); ?>

  <div class="wrapper" id="content">
    <?php osc_run_hook('before-main'); ?>
    <div id="main">
      <?php osc_run_hook('inside-main'); ?>
