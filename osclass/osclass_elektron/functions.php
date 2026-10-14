<?php
/*
 * Copyright 2014 Osclass
 * Copyright 2023 Osclass by OsclassPoint.com
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


define('ELEKTRON_THEME_VERSION', '110');
define('THEME_COMPATIBLE_WITH_OSCLASS_HOOKS', 830);     // Compatibility with new hooks up to version

require_once __DIR__ . '/inc/network.php';


// Style options. Keys map to the data attributes written on <html>.
function elektron_style_options() {
  return array(
    'palette' => array(
      'blue'      => __('Elektron Blue', 'osclass_elektron'),
      'neon'      => __('Neon Grid', 'osclass_elektron'),
      'synthwave' => __('Synthwave', 'osclass_elektron'),
      'matrix'    => __('Matrix', 'osclass_elektron'),
      'rust'      => __('Rust', 'osclass_elektron'),
      'iron'      => __('Iron', 'osclass_elektron'),
      'violet'    => __('Violet Night', 'osclass_elektron'),
    ),
    'scheme' => array(
      'dark'  => __('Dark', 'osclass_elektron'),
      'light' => __('Light', 'osclass_elektron'),
      'auto'  => __('Follow system', 'osclass_elektron'),
    ),
    'fx' => array(
      'off'    => __('Off', 'osclass_elektron'),
      'subtle' => __('Subtle', 'osclass_elektron'),
      'full'   => __('Full cyberpunk', 'osclass_elektron'),
    ),
    'hero' => array(
      'three'   => __('3D blockchain and escrow (WebGL)', 'osclass_elektron'),
      'network' => __('Animated network (lightweight)', 'osclass_elektron'),
      'grid'    => __('Perspective grid', 'osclass_elektron'),
      'minimal' => __('Minimal', 'osclass_elektron'),
    ),
    'glow' => array(
      'off'    => __('Off', 'osclass_elektron'),
      'subtle' => __('Subtle', 'osclass_elektron'),
      'medium' => __('Medium', 'osclass_elektron'),
      'strong' => __('Strong', 'osclass_elektron'),
    ),
    'lock' => array(
      'icon'  => __('Drawn icon (identical on every device)', 'osclass_elektron'),
      'emoji' => __('System emoji (Apple look on iPhone and Mac)', 'osclass_elektron'),
    ),
    'motion' => array(
      'always'  => __('Always animate, also on phones with reduced motion settings', 'osclass_elektron'),
      'respect' => __('Respect the reduced motion setting of the device', 'osclass_elektron'),
    ),
    'radius' => array(
      'round' => __('Rounded', 'osclass_elektron'),
      'sharp' => __('Sharp', 'osclass_elektron'),
    ),
    'header' => array(
      'glass' => __('Glass, sticky', 'osclass_elektron'),
      'solid' => __('Solid, static', 'osclass_elektron'),
    ),
  );
}

function elektron_style_defaults() {
  return array(
    'palette' => 'blue',
    'scheme'  => 'dark',
    'fx'      => 'subtle',
    'hero'    => 'three',
    'glow'    => 'medium',
    'lock'    => 'icon',
    'motion'  => 'always',
    'radius'  => 'round',
    'header'  => 'glass',
  );
}

function elektron_set_style_defaults($overwrite = true) {
  foreach(elektron_style_defaults() as $key => $value) {
    if($overwrite || (string)osc_get_preference($key, 'osclass_elektron') === '') {
      osc_set_preference($key, $value, 'osclass_elektron');
    }
  }
  if($overwrite) {
    osc_set_preference('hero_title', '', 'osclass_elektron');
    osc_set_preference('hero_text', '', 'osclass_elektron');
    osc_set_preference('logo_text', '', 'osclass_elektron');
  }
  if($overwrite || (string)osc_get_preference('logo_show_text', 'osclass_elektron') === '') {
    osc_set_preference('logo_show_text', '1', 'osclass_elektron');
  }
  if($overwrite || (string)osc_get_preference('network_show', 'osclass_elektron') === '') {
    osc_set_preference('network_show', '1', 'osclass_elektron');
  }
  if($overwrite || (string)osc_get_preference('network_ttl', 'osclass_elektron') === '') {
    osc_set_preference('network_ttl', '60', 'osclass_elektron');
  }
}

function elektron_style($key) {
  $options  = elektron_style_options();
  $defaults = elektron_style_defaults();
  $value    = (string)osc_get_preference($key, 'osclass_elektron');

  return isset($options[$key][$value]) ? $value : $defaults[$key];
}

function elektron_html_attributes() {
  $attrs = array(
    'data-palette'        => elektron_style('palette'),
    'data-default-scheme' => elektron_style('scheme'),
    'data-fx'             => elektron_style('fx'),
    'data-hero'           => elektron_style('hero'),
    'data-glow'           => elektron_style('glow'),
    'data-lock'           => elektron_style('lock'),
    'data-motion'         => elektron_style('motion'),
    'data-radius'         => elektron_style('radius'),
    'data-header'         => elektron_style('header'),
  );

  $out = '';
  foreach($attrs as $name => $value) {
    $out .= ' ' . $name . '="' . osc_esc_html($value) . '"';
  }
  return $out;
}

function elektron_hero_title() {
  $title = trim((string)osc_get_preference('hero_title', 'osclass_elektron'));
  return $title !== '' ? $title : __('Find it. Sell it. Locally.', 'osclass_elektron');
}

function elektron_hero_text() {
  $text = trim((string)osc_get_preference('hero_text', 'osclass_elektron'));
  return $text !== '' ? $text : __('Browse fresh listings from people near you or post your own in under a minute.', 'osclass_elektron');
}

function elektron_hero_stats() {
  $stats = array();
  if(function_exists('osc_total_active_items')) {
    $stats[] = array('value' => (int)osc_total_active_items(), 'label' => __('Active listings', 'osclass_elektron'));
  }
  if(function_exists('osc_total_active_items_today')) {
    $stats[] = array('value' => (int)osc_total_active_items_today(), 'label' => __('New today', 'osclass_elektron'));
  }
  if(osc_users_enabled() && function_exists('osc_total_users')) {
    $stats[] = array('value' => (int)osc_total_users('active'), 'label' => __('Members', 'osclass_elektron'));
  }
  return $stats;
}

// Get latest items for home page
function elektron_home_latest() {
  if(osc_is_home_page()) {
    osc_run_hook('home_premium');

    osc_reset_latest_items();

    if(osc_count_latest_items() > 0) {
      ?>
      <div class="home-latest">
        <div class="section-head">
          <h2><?php _e('Latest Listings', 'osclass_elektron') ; ?></h2>
          <a class="section-link" href="<?php echo osc_search_url(array('page' => 'search')); ?>"><?php _e('Browse all', 'osclass_elektron'); ?> <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php
          View::newInstance()->_exportVariableToView("listType", 'latestItems');
          View::newInstance()->_exportVariableToView("listClass", 'listing-grid');
          osc_current_web_theme_path('loop.php');
        ?>
      </div>

      <?php osc_run_hook('home_latest'); ?>
      <?php
    }
  }
}



if((string)osc_get_preference('keyword_placeholder', 'osclass_elektron')=="") {
  Params::setParam('keyword_placeholder', __('ie. PHP Programmer', 'osclass_elektron')) ;
}

function elektron_remove_styles() {
  osc_remove_style('font-open-sans');
  osc_remove_style('open-sans');
  osc_remove_style('fi_font-awesome');
  osc_remove_style('font-awesome44');
  osc_remove_style('font-awesome45');
  osc_remove_style('font-awesome47');
  osc_remove_style('font-awesome');
}

osc_add_hook('init', 'elektron_remove_styles');
osc_add_hook('header', 'elektron_remove_styles');

osc_enqueue_script('fancybox');
osc_enqueue_style('font-awesome-elektron', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css');
osc_enqueue_script('php-date');         // used for date/dateinterval custom fields

if(!OC_ADMIN) {
  osc_enqueue_style('fine-uploader-css', osc_assets_url('js/fineuploader/fineuploader.css'));
  osc_enqueue_style('elektron-fine-uploader-css', osc_current_web_theme_url('css/ajax-uploader.css'));
}

osc_enqueue_script('jquery-fineuploader');


// install options
if(!function_exists('elektron_theme_install')) {
  function elektron_theme_install() {
    osc_set_preference('logo', '', 'osclass_elektron');
    osc_set_preference('keyword_placeholder', Params::getParam('keyword_placeholder'), 'osclass_elektron');
    osc_set_preference('version', ELEKTRON_THEME_VERSION, 'osclass_elektron');
    osc_set_preference('footer_link', '1', 'osclass_elektron');
    osc_set_preference('donation', '0', 'osclass_elektron');
    osc_set_preference('defaultShowAs@all', 'list', 'osclass_elektron');
    osc_set_preference('defaultShowAs@search', 'list');
    osc_set_preference('defaultLocationShowAs', 'dropdown', 'osclass_elektron'); // dropdown / autocomplete
    osc_set_preference('rtl', '0', 'osclass_elektron');
    elektron_set_style_defaults();
    osc_reset_preferences();
  }
}


// update options
if(!function_exists('elektron_theme_update')) {
  function elektron_theme_update($current_version) {
    elektron_set_style_defaults(false);
    osc_set_preference('version', ELEKTRON_THEME_VERSION, 'osclass_elektron');
    osc_reset_preferences();
  }
}


if(!function_exists('check_install_elektron_theme')) {
  function check_install_elektron_theme() {
    $current_version = osc_get_preference('version', 'osclass_elektron');
    //check if current version is installed or need an update<
    if($current_version=='') {
      elektron_theme_install();
    } else if($current_version < ELEKTRON_THEME_VERSION){
      elektron_theme_update($current_version);
    }
  }
}

// RTL LANGUAGE SUPPORT
function elektron_is_rtl() {
  $current_lang = strtolower(osc_current_user_locale());

  if(in_array($current_lang, elektron_rtl_languages())) {
    return true;
  } else {
    return false;
  }
}


function elektron_rtl_languages() {
  $langs = array('ar_DZ','ar_BH','ar_EG','ar_IQ','ar_JO','ar_KW','ar_LY','ar_MA','ar_OM','ar_SA','ar_SY','fa_IR','ar_TN','ar_AE','ar_YE','ar_TD','ar_CO','ar_DJ','ar_ER','ar_MR','ar_SD');
  return array_map('strtolower', $langs);
}


if(!function_exists('elektron_add_body_class_construct')) {
  function elektron_add_body_class_construct($classes){
    $elektronBodyClass = elektronBodyClass::newInstance();
    $classes = array_merge($classes, $elektronBodyClass->get());
    return $classes;
  }
}

if(!function_exists('elektron_body_class')) {
  function elektron_body_class($echo = true){
    /**
    * Print body classes.
    *
    * @param string $echo Optional parameter.
    * @return print string with all body classes concatenated
    */
    osc_add_filter('elektron_bodyClass','elektron_add_body_class_construct');
    $classes = osc_apply_filter('elektron_bodyClass', array());
    if($echo && count($classes)){
      echo 'class="'.implode(' ',$classes).'"';
    } else {
      return $classes;
    }
  }
}

if(!function_exists('elektron_add_body_class')) {
  function elektron_add_body_class($class){
    /**
    * Add new body class to body class array.
    *
    * @param string $class required parameter.
    */
    $elektronBodyClass = elektronBodyClass::newInstance();
    $elektronBodyClass->add($class);
  }
}

if(!function_exists('elektron_nofollow_construct')) {
  /**
  * Hook for header, meta tags robots nofollos
  */
  function elektron_nofollow_construct() {
    echo '<meta name="robots" content="noindex, nofollow, noarchive" />' . PHP_EOL;
    echo '<meta name="googlebot" content="noindex, nofollow, noarchive" />' . PHP_EOL;

  }
}

if(!function_exists('elektron_follow_construct')) {
  /**
  * Hook for header, meta tags robots follow
  */
  function elektron_follow_construct() {
    echo '<meta name="robots" content="index, follow" />' . PHP_EOL;
    echo '<meta name="googlebot" content="index, follow" />' . PHP_EOL;
  }
}

/* logo: mark (uploaded logo or the bundled Elektron mark) plus optional text */
if(!function_exists('logo_header')) {
  function logo_header() {
    $text = elektron_logo_text();
    $upload = elektron_logo_url();
    $src = $upload !== false ? $upload : osc_current_web_theme_url('images/logo.png');
    $class = $upload !== false ? 'with-img' : 'only-text';
    $showText = osc_get_preference('logo_show_text', 'osclass_elektron') !== '0';

    $html = '<a href="' . osc_base_url() . '" class="logo-link ' . $class . '">';
    $html .= '<img class="' . ($upload !== false ? 'logo-img' : 'logo-mark') . '" alt="' . ($showText ? '' : osc_esc_html($text)) . '" src="' . osc_esc_html($src) . '">';
    if($showText) {
      $html .= '<span class="logo-text">' . osc_esc_html($text) . '</span>';
    }
    return $html . '</a>';
  }
}

/* logo text from the theme settings, the site title when empty */
if(!function_exists('elektron_logo_text')) {
  function elektron_logo_text() {
    $text = trim((string)osc_get_preference('logo_text', 'osclass_elektron'));
    return $text !== '' ? $text : osc_page_title();
  }
}

/* uploaded logo url, false when none */
if(!function_exists('elektron_logo_url')) {
  function elektron_logo_url() {
    $logo = osc_get_preference('logo','osclass_elektron');

    if($logo != '' && file_exists(osc_uploads_path() . $logo)) {
      return osc_uploads_url() . $logo;
    }

    return false;
  }
}


if(!function_exists('elektron_draw_item')) {
  function elektron_draw_item($class = false,$admin = false, $premium = false) {
    $filename = 'loop-single';
    if($premium){
      $filename .='-premium';
    }
    require WebThemes::newInstance()->getCurrentThemePath().$filename.'.php';
  }
}


if(!function_exists('elektron_show_as')){
  function elektron_show_as(){

    $p_sShowAs  = Params::getParam('sShowAs');
    $aValidShowAsValues = array('list', 'gallery');
    if(!in_array($p_sShowAs, $aValidShowAsValues)) {
      $p_sShowAs = elektron_default_show_as();
    }

    return $p_sShowAs;
  }
}


if(!function_exists('elektron_default_direction')){
  function elektron_default_direction(){
    $locale = osc_get_current_user_locale();
    if(isset($locale['b_rtl']) && $locale['b_rtl'] == 1) {
      return 1;
    } else {
      return getPreference('rtl','osclass_elektron');
    }
  }
}


if(!function_exists('elektron_default_show_as')){
  function elektron_default_show_as(){
    return getPreference('defaultShowAs@all','osclass_elektron');
  }
}


if(!function_exists('elektron_default_location_show_as')){
  function elektron_default_location_show_as(){
    return osc_get_preference('defaultLocationShowAs','osclass_elektron');
  }
}


if(!function_exists('elektron_search_number')) {
  /**
    *
    * @return array
    */
  function elektron_search_number() {
    $search_from = ((osc_search_page() * osc_default_results_per_page_at_search()) + 1);
    $search_to   = ((osc_search_page() + 1) * osc_default_results_per_page_at_search());
    if($search_to > osc_search_total_items()) {
      $search_to = osc_search_total_items();
    }

    return array(
      'from' => $search_from,
      'to'   => $search_to,
      'of'   => osc_search_total_items()
    );
  }
}


/*
 * Helpers used at view
 */
if(!function_exists('elektron_item_title')) {
  function elektron_item_title() {
    $title = osc_item_title();
    foreach(osc_get_locales() as $locale) {
      if(Session::newInstance()->_getForm('title') != "") {
        $title_ = Session::newInstance()->_getForm('title');
        if(@$title_[$locale['pk_c_code']] != ""){
          $title = $title_[$locale['pk_c_code']];
        }
      }
    }
    return $title;
  }
}


if(!function_exists('elektron_item_description')) {
  function elektron_item_description() {
    $description = osc_item_description();
    foreach(osc_get_locales() as $locale) {
      if(Session::newInstance()->_getForm('description') != "") {
        $description_ = Session::newInstance()->_getForm('description');
        if(@$description_[$locale['pk_c_code']] != ""){
          $description = $description_[$locale['pk_c_code']];
        }
      }
    }
    return $description;
  }
}


if(!function_exists('related_listings')) {
  function related_listings() {
    View::newInstance()->_exportVariableToView('items', array());

    $mSearch = new Search();
    $mSearch->addCategory(osc_item_category_id());
    $mSearch->addRegion(osc_item_region());
    $mSearch->addItemConditions(sprintf("%st_item.pk_i_id < %s ", DB_TABLE_PREFIX, osc_item_id()));
    $mSearch->limit('0', '3');

    $aItems    = $mSearch->doSearch();
    $iTotalItems = count($aItems);
    if($iTotalItems == 3) {
      View::newInstance()->_exportVariableToView('items', $aItems);
      return $iTotalItems;
    }
    unset($mSearch);

    $mSearch = new Search();
    $mSearch->addCategory(osc_item_category_id());
    $mSearch->addItemConditions(sprintf("%st_item.pk_i_id != %s ", DB_TABLE_PREFIX, osc_item_id()));
    $mSearch->limit('0', '3');

    $aItems = $mSearch->doSearch();
    $iTotalItems = count($aItems);
    if($iTotalItems > 0) {
      View::newInstance()->_exportVariableToView('items', $aItems);
      return $iTotalItems;
    }
    unset($mSearch);

    return 0;
  }
}

if(!function_exists('osc_is_contact_page')) {
  function osc_is_contact_page() {
    if(Rewrite::newInstance()->get_location() === 'contact') {
      return true;
    }

    return false;
  }
}

if(!function_exists('get_breadcrumb_lang')) {
  function get_breadcrumb_lang() {
    $lang = array();
    $lang['item_add']         = __('Publish a listing', 'osclass_elektron');
    $lang['item_edit']        = __('Edit your listing', 'osclass_elektron');
    $lang['item_send_friend']     = __('Send to a friend', 'osclass_elektron');
    $lang['item_contact']       = __('Contact publisher', 'osclass_elektron');
    $lang['search']         = __('Search results', 'osclass_elektron');
    $lang['search_pattern']     = __('Search results: %s', 'osclass_elektron');
    $lang['user_dashboard']     = __('Dashboard', 'osclass_elektron');
    $lang['user_dashboard_profile'] = __("%s's profile", 'osclass_elektron');
    $lang['user_account']       = __('Account', 'osclass_elektron');
    $lang['user_items']       = __('Listings', 'osclass_elektron');
    $lang['user_alerts']      = __('Alerts', 'osclass_elektron');
    $lang['user_profile']       = __('Update account', 'osclass_elektron');
    $lang['user_change_email']    = __('Change email', 'osclass_elektron');
    $lang['user_change_username']   = __('Change username', 'osclass_elektron');
    $lang['user_change_password']   = __('Change password', 'osclass_elektron');
    $lang['login']          = __('Login', 'osclass_elektron');
    $lang['login_recover']      = __('Recover password', 'osclass_elektron');
    $lang['login_forgot']       = __('Change password', 'osclass_elektron');
    $lang['register']         = __('Create a new account', 'osclass_elektron');
    $lang['contact']        = __('Contact', 'osclass_elektron');
    return $lang;
  }
}

if(!function_exists('user_dashboard_redirect')) {
  function user_dashboard_redirect() {
    $page   = Params::getParam('page');
    $action = Params::getParam('action');
    if($page=='user' && $action=='dashboard') {
      if(ob_get_length()>0) {
        ob_end_flush();
      }
      header("Location: ".osc_user_list_items_url(), TRUE,301);
    }
  }
  osc_add_hook('init', 'user_dashboard_redirect');
}

if(!function_exists('get_user_menu')) {
  function get_user_menu() {
    $options   = array();
    $options[] = array(
      'name' => __('Public Profile'),
       'url' => osc_user_public_profile_url(),
       'class' => 'opt_publicprofile'
    );
    $options[] = array(
      'name'  => __('Listings', 'osclass_elektron'),
      'url'   => osc_user_list_items_url(),
      'class' => 'opt_items'
    );
    if(function_exists('osc_alerts_enabled') && osc_alerts_enabled()) {
      $options[] = array(
        'name' => __('Alerts', 'osclass_elektron'),
        'url' => osc_user_alerts_url(),
        'class' => 'opt_alerts'
      );
    }
    $options[] = array(
      'name'  => __('Account', 'osclass_elektron'),
      'url'   => osc_user_profile_url(),
      'class' => 'opt_account'
    );
    $options[] = array(
      'name'  => __('Change email', 'osclass_elektron'),
      'url'   => osc_change_user_email_url(),
      'class' => 'opt_change_email'
    );
    $options[] = array(
      'name'  => __('Change username', 'osclass_elektron'),
      'url'   => osc_change_user_username_url(),
      'class' => 'opt_change_username'
    );
    $options[] = array(
      'name'  => __('Change password', 'osclass_elektron'),
      'url'   => osc_change_user_password_url(),
      'class' => 'opt_change_password'
    );
    $options[] = array(
      'name'  => __('Delete account', 'osclass_elektron'),
      'url'   => '#',
      'class' => 'opt_delete_account'
    );

    return $options;
  }
}

if(!function_exists('delete_user_js')) {
  function delete_user_js() {
    $location = Rewrite::newInstance()->get_location();
    $section  = Rewrite::newInstance()->get_section();
    if(($location === 'user' && in_array($section, array('dashboard', 'profile', 'alerts', 'change_email', 'change_username',  'change_password', 'items'))) || (Params::getParam('page') ==='custom' && Params::getParam('in_user_menu')==true)) {
      osc_enqueue_script('delete-user-js');
    }
  }
  osc_add_hook('header', 'delete_user_js', 1);
}

if(!function_exists('user_info_js')) {
  function user_info_js() {
    $location = Rewrite::newInstance()->get_location();
    $section  = Rewrite::newInstance()->get_section();

    if($location === 'user' && in_array($section, array('dashboard', 'profile', 'alerts', 'change_email', 'change_username',  'change_password', 'items'))) {
      $user = User::newInstance()->findByPrimaryKey(Session::newInstance()->_get('userId'));
      View::newInstance()->_exportVariableToView('user', $user);
      ?>
<script type="text/javascript">
elektron.user = {};
elektron.user.id = '<?php echo osc_user_id(); ?>';
elektron.user.secret = '<?php echo osc_user_field("s_secret"); ?>';
</script>
    <?php }
  }
  osc_add_hook('header', 'user_info_js');
}

function theme_elektron_actions_admin() {
  //if(OC_ADMIN)
  if(Params::getParam('file') == 'oc-content/themes/osclass_elektron/admin/settings.php') {
    if(Params::getParam('donation') == 'successful') {
      osc_set_preference('donation', '1', 'osclass_elektron');
      osc_reset_preferences();
    }
  }

  switch(Params::getParam('action_specific')) {
    case('settings'):
      $footerLink  = Params::getParam('footer_link');

      osc_set_preference('keyword_placeholder', Params::getParam('keyword_placeholder'), 'osclass_elektron');
      osc_set_preference('footer_link', ($footerLink ? '1' : '0'), 'osclass_elektron');
      osc_set_preference('defaultShowAs@all', Params::getParam('defaultShowAs@all'), 'osclass_elektron');
      osc_set_preference('defaultShowAs@search', Params::getParam('defaultShowAs@all'));

      osc_set_preference('defaultLocationShowAs', Params::getParam('defaultLocationShowAs'), 'osclass_elektron');

      osc_set_preference('header-728x90',     trim(Params::getParam('header-728x90', false, false, false)),          'osclass_elektron');
      osc_set_preference('homepage-728x90',     trim(Params::getParam('homepage-728x90', false, false, false)),        'osclass_elektron');
      osc_set_preference('sidebar-300x250',     trim(Params::getParam('sidebar-300x250', false, false, false)),        'osclass_elektron');
      osc_set_preference('search-results-top-728x90',   trim(Params::getParam('search-results-top-728x90', false, false, false)),      'osclass_elektron');
      osc_set_preference('search-results-middle-728x90',  trim(Params::getParam('search-results-middle-728x90', false, false, false)),     'osclass_elektron');

      osc_set_preference('rtl', (Params::getParam('rtl') ? '1' : '0'), 'osclass_elektron');

      $options = elektron_style_options();
      foreach(elektron_style_defaults() as $key => $default) {
        $value = (string)Params::getParam('style_' . $key);
        osc_set_preference($key, isset($options[$key][$value]) ? $value : $default, 'osclass_elektron');
      }
      osc_set_preference('network_show', Params::getParam('network_show') ? '1' : '0', 'osclass_elektron');
      osc_set_preference('network_sources', implode("\n", elektron_network_parse_sources((string)Params::getParam('network_sources', false, false))), 'osclass_elektron');
      osc_set_preference('network_ttl', (string)min(600, max(30, (int)Params::getParam('network_ttl'))), 'osclass_elektron');
      osc_set_preference('logo_text', mb_substr(trim(strip_tags((string)Params::getParam('logo_text'))), 0, 60), 'osclass_elektron');
      osc_set_preference('logo_show_text', Params::getParam('logo_show_text') ? '1' : '0', 'osclass_elektron');
      osc_set_preference('hero_title', trim(strip_tags((string)Params::getParam('hero_title'))), 'osclass_elektron');
      osc_set_preference('hero_text', trim(strip_tags((string)Params::getParam('hero_text'))), 'osclass_elektron');

      osc_add_flash_ok_message(__('Settings have been updated', 'osclass_elektron'), 'admin');
      osc_redirect_to(osc_admin_render_theme_url('oc-content/themes/osclass_elektron/admin/settings.php'));
    break;
    case('upload_logo'):
      $package = Params::getFiles('logo');
      if($package['error'] == UPLOAD_ERR_OK) {
        $img = ImageResizer::fromFile($package['tmp_name']);
        $ext = $img->getExt();
        $logo_name   = 'elektron_logo';
        $logo_name  .= '.'.$ext;
        $path = osc_uploads_path() . $logo_name ;
        $img->saveToFile($path);

        osc_set_preference('logo', $logo_name, 'osclass_elektron');

        osc_add_flash_ok_message(__('The logo image has been uploaded', 'osclass_elektron'), 'admin');
      } else {
        osc_add_flash_error_message(__('An error occurred. Please try again', 'osclass_elektron'), 'admin');
      }
      osc_redirect_to(osc_admin_render_theme_url('oc-content/themes/osclass_elektron/admin/header.php'));
    break;
    case('remove'):
      $logo = osc_get_preference('logo','osclass_elektron');
      $path = osc_uploads_path() . $logo ;
      if(file_exists($path)) {
        @unlink($path);
        osc_delete_preference('logo','osclass_elektron');
        osc_reset_preferences();
        osc_add_flash_ok_message(__('The logo image has been removed', 'osclass_elektron'), 'admin');
      } else {
        osc_add_flash_error_message(__("Image not found", 'osclass_elektron'), 'admin');
      }
      osc_redirect_to(osc_admin_render_theme_url('oc-content/themes/osclass_elektron/admin/header.php'));
    break;
  }
}

function elektron_redirect_user_dashboard()
{
  if((Rewrite::newInstance()->get_location() === 'user') && (Rewrite::newInstance()->get_section() === 'dashboard')) {
    header('Location: ' .osc_user_list_items_url());
    exit;
  }
}

function elektron_delete() {
  Preference::newInstance()->delete(array('s_section' => 'osclass_elektron'));
}

osc_add_hook('init', 'elektron_redirect_user_dashboard', 2);
osc_add_hook('init_admin', 'theme_elektron_actions_admin');
osc_add_hook('theme_delete_osclass_elektron', 'elektron_delete');

function elektron_admin_menu_links() {
  osc_admin_menu_appearance(__('Header logo', 'osclass_elektron'), osc_admin_render_theme_url('oc-content/themes/osclass_elektron/admin/header.php'), 'header_elektron');
  osc_admin_menu_appearance(__('Theme settings', 'osclass_elektron'), osc_admin_render_theme_url('oc-content/themes/osclass_elektron/admin/settings.php'), 'settings_elektron');
}

osc_add_hook('init_admin', 'elektron_admin_menu_links');


//TRIGGER FUNCTIONS
check_install_elektron_theme();

if(osc_is_home_page() || osc_is_search_page()){
  elektron_add_body_class('has-searchbox');
}


function elektron_sidebar_category_search($catId = null)
{
  $aCategories = array();
  if($catId==null || $catId <= 0) {
    $aCategories[] = Category::newInstance()->findRootCategoriesEnabled();
  } else {
    // if parent category, only show parent categories
    $aCategories = Category::newInstance()->toRootTree($catId);
    end($aCategories);
    $cat = current($aCategories);
    // if is parent of some category
    $childCategories = Category::newInstance()->findSubcategoriesEnabled($cat['pk_i_id']);
    if(count($childCategories) > 0) {
      $aCategories[] = $childCategories;
    }
  }

  if(count($aCategories) == 0) {
    return "";
  }

  elektron_print_sidebar_category_search($aCategories, $catId);
}

function elektron_print_sidebar_category_search($aCategories, $current_category = null, $i = 0)
{
  $class = '';
  if(!isset($aCategories[$i])) {
    return null;
  }

  if($i===0) {
    $class = 'class="category"';
  }

  $c   = $aCategories[$i];
  $i++;
  if(!isset($c['pk_i_id'])) {
    echo '<ul '.$class.'>';
    if($i==1) {
      echo '<li><a href="'.osc_esc_html(osc_update_search_url(array('sCategory'=>null, 'iPage'=>null))).'">'.__('All categories', 'osclass_elektron')."</a></li>";
    }
    foreach($c as $key => $value) {
  ?>
      <li>
        <a id="cat_<?php echo osc_esc_html($value['pk_i_id']);?>" href="<?php echo osc_esc_html(osc_update_search_url(array('sCategory'=> $value['pk_i_id'], 'iPage'=>null))); ?>">
        <?php if(isset($current_category) && $current_category == $value['pk_i_id']){ echo '<strong>'.$value['s_name'].'</strong>'; }
        else{ echo $value['s_name']; } ?>
        </a>

      </li>
  <?php
    }
    if($i==1) {
    echo "</ul>";
    } else {
    echo "</ul>";
    }
  } else {
  ?>
  <ul <?php echo $class;?>>
    <?php if($i==1) { ?>
    <li><a href="<?php echo osc_esc_html(osc_update_search_url(array('sCategory'=>null, 'iPage'=>null))); ?>"><?php _e('All categories', 'osclass_elektron'); ?></a></li>
    <?php } ?>
      <li>
        <a id="cat_<?php echo osc_esc_html($c['pk_i_id']);?>" href="<?php echo osc_esc_html(osc_update_search_url(array('sCategory'=> $c['pk_i_id'], 'iPage'=>null))); ?>">
        <?php if(isset($current_category) && $current_category == $c['pk_i_id']){ echo '<strong>'.$c['s_name'].'</strong>'; }
            else{ echo $c['s_name']; } ?>
        </a>
        <?php elektron_print_sidebar_category_search($aCategories, $current_category, $i); ?>
      </li>
    <?php if($i==1) { ?>
    <?php } ?>
  </ul>
<?php
  }
}

/**

CLASSES

*/
class elektronBodyClass
{
  /**
  * Custom Class for add, remove or get body classes.
  *
  * @param string $instance used for singleton.
  * @param array $class.
  */
  private static $instance;
  private $class;

  private function __construct()
  {
    $this->class = array();
  }

  public static function newInstance()
  {
    if(!self::$instance instanceof self)
    {
      self::$instance = new self;
    }
    return self::$instance;
  }

  public function add($class)
  {
    $this->class[] = $class;
  }
  public function get()
  {
    return $this->class;
  }
}


function osc_theme_check_compatibility_branch() {
  $osclass_version = (int)str_replace('.', '', OSCLASS_VERSION);
  $osclass_author = (!defined('OSCLASS_AUTHOR') ? 'NONE' : strtoupper(OSCLASS_AUTHOR));

  if($osclass_version >= 420 && $osclass_author <> 'OSCLASSPOINT') {
    osc_add_flash_error_message('Theme is not compatible with your osclass version or branch! You cannot use this theme as it would generate errors on your installation. Download and install supported osclass version: <a href="https://osclass-classifieds.com/download">https://osclass-classifieds.com/download</a>');
  }
}

osc_add_hook('header', 'osc_theme_check_compatibility_branch', 1);


// Search ads
if(!function_exists('search_ads_listing_top_fn')) {
  function search_ads_listing_top_fn() {
    if(osc_get_preference('search-results-top-728x90', 'osclass_elektron')!='') {
      echo '<div class="clear"></div>' . PHP_EOL;
      echo '<div class="ads_728">' . PHP_EOL;
      echo osc_get_preference('search-results-top-728x90', 'osclass_elektron');
      echo '</div>' . PHP_EOL;
    }
  }
}
//osc_add_hook('search_ads_listing_top', 'search_ads_listing_top_fn');

if(!function_exists('search_ads_listing_medium_fn')) {
  function search_ads_listing_medium_fn() {
    if(osc_get_preference('search-results-middle-728x90', 'osclass_elektron')!='') {
      echo '<div class="clear"></div>' . PHP_EOL;
      echo '<div class="ads_728">' . PHP_EOL;
      echo osc_get_preference('search-results-middle-728x90', 'osclass_elektron');
      echo '</div>' . PHP_EOL;
    }
  }
}
osc_add_hook('search_ads_listing_medium', 'search_ads_listing_medium_fn');

