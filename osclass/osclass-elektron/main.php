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
// meta tag robots
osc_add_hook('header','elektron_follow_construct');
elektron_add_body_class('home');
?>

<?php osc_current_web_theme_path('header.php'); ?>

<?php osc_run_hook('home_top'); ?>

<?php osc_goto_first_category(); ?>
<?php if(osc_count_categories() > 0) { ?>
<section id="home-cats">
  <div class="section-head">
    <h2><?php _e('Browse by category', 'osclass-elektron'); ?></h2>
  </div>

  <div class="wrap">
    <?php while(osc_has_categories()) { ?>
      <a href="<?php echo osc_search_category_url(); ?>" class="cat-card">
        <span class="icon" <?php if(osc_category_color() <> '') { ?>style="color:<?php echo osc_esc_html(osc_category_color()); ?>;"<?php } ?>>
          <i class="<?php echo osc_esc_html(osc_category_icon() <> '' ? osc_category_icon() : 'far fa-share-square'); ?>" aria-hidden="true"></i>
        </span>
        <strong><?php echo osc_category_name(); ?></strong>
        <em><?php echo (int)osc_category_total_items(); ?></em>
      </a>
    <?php } ?>
  </div>
</section>
<?php } ?>

<?php elektron_home_latest(); ?>

<section class="how-it-works">
  <div class="section-head">
    <h2><?php _e('How it works', 'osclass-elektron'); ?></h2>
  </div>
  <ol class="steps">
    <li>
      <span class="step-no">01</span>
      <h3><?php _e('Post a listing', 'osclass-elektron'); ?></h3>
      <p><?php _e('Add photos, a price and a short description. It takes less than a minute.', 'osclass-elektron'); ?></p>
    </li>
    <li>
      <span class="step-no">02</span>
      <h3><?php _e('Get contacted', 'osclass-elektron'); ?></h3>
      <p><?php _e('Interested people reach you directly by message, phone or email.', 'osclass-elektron'); ?></p>
    </li>
    <li>
      <span class="step-no">03</span>
      <h3><?php _e('Close the deal', 'osclass-elektron'); ?></h3>
      <p><?php _e('Meet locally, agree on the price and hand over the item safely.', 'osclass-elektron'); ?></p>
    </li>
  </ol>
</section>

<?php
  $regions = RegionStats::newInstance()->listRegions('%%%%', '>', 'i_num_items DESC');
  if(!empty($regions)) {
?>
<section id="home-regs">
  <div class="section-head">
    <h2><?php _e('Popular locations', 'osclass-elektron'); ?></h2>
  </div>
  <div class="wrap">
    <?php foreach(array_slice($regions, 0, 20) as $r) { ?>
      <a class="chip" href="<?php echo osc_search_url(array('page' => 'search', 'sRegion' => $r['pk_i_id']));?>"><i class="fas fa-location-arrow" aria-hidden="true"></i> <span><?php echo osc_esc_html($r['s_name']); ?></span> <em><?php echo (int)$r['i_num_items']; ?></em></a>
    <?php } ?>
  </div>
</section>
<?php } ?>

<?php if(osc_get_preference('sidebar-300x250', 'osclass-elektron') != '') { ?>
  <div class="ads_300"><?php echo osc_get_preference('sidebar-300x250', 'osclass-elektron'); ?></div>
<?php } ?>

<?php if(osc_get_preference('homepage-728x90', 'osclass-elektron') != '') { ?>
  <div class="ads_728"><?php echo osc_get_preference('homepage-728x90', 'osclass-elektron'); ?></div>
<?php } ?>

<section class="cta-band">
  <div>
    <h2><?php _e('Have something to sell?', 'osclass-elektron'); ?></h2>
    <p><?php _e('Reach thousands of buyers. Publishing is free and takes a minute.', 'osclass-elektron'); ?></p>
  </div>
  <a class="btn btn-primary btn-lg" href="<?php echo osc_item_post_url_in_category(); ?>"><i class="fas fa-plus" aria-hidden="true"></i> <?php _e('Publish Ad', 'osclass-elektron'); ?></a>
</section>

<?php osc_run_hook('home_bottom'); ?>

<?php osc_current_web_theme_path('footer.php') ;
