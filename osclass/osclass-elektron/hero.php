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
<?php $stats = elektron_hero_stats(); ?>
<section class="hero home-search" data-hero="<?php echo osc_esc_html(elektron_style('hero')); ?>">
  <?php if(elektron_style('hero') === 'network') { ?>
    <canvas class="hero-canvas" aria-hidden="true"></canvas>
  <?php } ?>
  <div class="hero-glow" aria-hidden="true"></div>
  <div class="wrapper hero-inner">
    <form action="<?php echo osc_base_url(true); ?>" method="get" class="search nocsrf box">
      <input type="hidden" name="page" value="search"/>

      <?php osc_run_hook('home_search_top'); ?>

      <p class="hero-eyebrow"><span class="dot" aria-hidden="true"></span> <?php echo osc_esc_html(osc_page_title()); ?></p>
      <h1 data-text="<?php echo osc_esc_html(elektron_hero_title()); ?>"><?php echo osc_esc_html(elektron_hero_title()); ?></h1>
      <p class="hero-lead"><?php echo osc_esc_html(elektron_hero_text()); ?></p>

      <div class="main-search">
        <div class="cell c1">
          <label for="query"><?php _e('Keyword', 'osclass-elektron'); ?></label>
          <input type="text" name="sPattern" id="query" class="input-text" value="" placeholder="<?php echo osc_esc_html(__(osc_get_preference('keyword_placeholder', 'osclass-elektron'), 'osclass-elektron')); ?>" />
        </div>

        <div class="cell c2">
          <label for="sCategory"><?php _e('Category', 'osclass-elektron'); ?></label>
          <?php osc_categories_select('sCategory', null, __('All categories', 'osclass-elektron')) ; ?>
        </div>

        <div class="cell c3">
          <button class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> <span><?php _e("Search", 'osclass-elektron');?></span></button>
        </div>
      </div>
      <div id="message-seach"></div>

      <?php osc_run_hook('home_search_bottom'); ?>

      <?php if(!empty($stats)) { ?>
        <ul class="hero-stats">
          <?php foreach($stats as $stat) { ?>
            <li><strong><?php echo number_format($stat['value']); ?></strong> <span><?php echo osc_esc_html($stat['label']); ?></span></li>
          <?php } ?>
        </ul>
      <?php } ?>
    </form>
  </div>
</section>
