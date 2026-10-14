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
  $js_lang = array(
    'delete' => __('Delete', 'osclasselektron'),
    'cancel' => __('Cancel', 'osclasselektron')
  );

  osc_enqueue_script('jquery');

  if(osc_is_publish_page() || osc_is_edit_page() || osc_is_search_page()) {
    osc_enqueue_script('jquery-ui');
  }

  osc_register_script('global-theme-js', osc_current_web_theme_js_url('global.js?v=' . ELEKTRON_THEME_VERSION), 'jquery');
  osc_register_script('delete-user-js', osc_current_web_theme_js_url('delete_user.js'), 'jquery-ui');
  osc_enqueue_script('global-theme-js');

  if(osc_is_home_page() && in_array(elektron_style('hero'), array('three', 'network'), true)) {
    if(elektron_style('hero') === 'three') {
      osc_register_script('elektron-hero-three-js', osc_current_web_theme_js_url('hero-three.min.js?v=' . ELEKTRON_THEME_VERSION));
      osc_enqueue_script('elektron-hero-three-js');
    }
    osc_register_script('elektron-hero-js', osc_current_web_theme_js_url('hero.js?v=' . ELEKTRON_THEME_VERSION));
    osc_enqueue_script('elektron-hero-js');
  }

  if(osc_is_publish_page() || osc_is_edit_page()) {
    osc_enqueue_script('tabber');
  }

  if(!defined('JQUERY_VERSION') || JQUERY_VERSION == '1') {
    $jquery_version = '1';
  } else {
    $jquery_version = JQUERY_VERSION;
  }
?>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

<title><?php echo meta_title() ; ?></title>
<meta name="title" content="<?php echo osc_esc_html(meta_title()); ?>" />
<?php if(meta_description() != '') { ?><meta name="description" content="<?php echo osc_esc_html(meta_description()); ?>" /><?php } ?>
<?php if(meta_keywords() != '') { ?><meta name="keywords" content="<?php echo osc_esc_html(meta_keywords()); ?>" /><?php } ?>
<?php if(osc_get_canonical() != '') { ?><link rel="canonical" href="<?php echo osc_get_canonical(); ?>"/><?php } ?>
<?php if(osc_get_prev_url() != '') { ?><link rel="prev" href="<?php echo osc_get_prev_url(); ?>" /><?php } ?>
<?php if(osc_get_next_url() != '') { ?><link rel="next" href="<?php echo osc_get_next_url(); ?>" /><?php } ?>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="color-scheme" content="dark light" />
<!-- favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo osc_current_web_theme_url('favicon/apple-touch-icon.png'); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo osc_current_web_theme_url('favicon/favicon-32x32.png'); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo osc_current_web_theme_url('favicon/favicon-16x16.png'); ?>">
<!-- /favicon -->

<script>
(function(){var r=document.documentElement,p=['blue','neon','synthwave','matrix','rust','iron','violet','red','frost','terminal'],f=['off','subtle','full'];try{var s=localStorage.getItem('elektron-scheme');if(s==='light'||s==='dark'){r.setAttribute('data-scheme',s);}var a=localStorage.getItem('elektron-palette');if(p.indexOf(a)>-1){r.setAttribute('data-palette',a);}var x=localStorage.getItem('elektron-fx');if(f.indexOf(x)>-1){r.setAttribute('data-fx',x);}}catch(e){}}());
</script>
<script type="text/javascript">
  var elektron = window.elektron || {};
  elektron.base_url = '<?php echo osc_base_url(true); ?>';
  elektron.langs = <?php echo json_encode($js_lang); ?>;
  elektron.fancybox_prev = '<?php echo osc_esc_js( __('Previous image','osclasselektron')) ?>';
  elektron.fancybox_next = '<?php echo osc_esc_js( __('Next image','osclasselektron')) ?>';
  elektron.fancybox_closeBtn = '<?php echo osc_esc_js( __('Close','osclasselektron')) ?>';
  var jqueryVersion = '<?php echo $jquery_version; ?>';
</script>
<?php
osc_enqueue_style('style', osc_current_web_theme_url('css/style.css?v=' . ELEKTRON_THEME_VERSION));

if(function_exists('elektron_escrow_config')) {
  osc_enqueue_style('elektron-escrow', osc_current_web_theme_url('css/escrow.css?v=' . ELEKTRON_THEME_VERSION));
}

if(osc_is_publish_page() || osc_is_edit_page() || osc_is_search_page()) {
  if($jquery_version == '1') {
    osc_enqueue_style('jquery-ui', osc_assets_url('css/jquery-ui/jquery-ui.css'));
  } else {
    osc_enqueue_style('jquery-ui', osc_assets_url('js/jquery3/jquery-ui/jquery-ui.min.css'));
  }
}
?>
<?php osc_run_hook('header');
