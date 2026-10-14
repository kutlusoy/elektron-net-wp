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
if(!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.'); ?>
<?php if(!OC_ADMIN ) exit('User access is not allowed.'); ?>

<h2 class="render-title <?php echo (osc_get_preference('footer_link', 'osclass_elektron') ? '' : 'separate-top'); ?>"><?php _e('Theme settings', 'osclass_elektron'); ?></h2>
<form action="<?php echo osc_admin_render_theme_url('oc-content/themes/osclass_elektron/admin/settings.php'); ?>" method="post" class="nocsrf">
    <input type="hidden" name="action_specific" value="settings" />
    <h2 class="render-title"><?php _e('Appearance', 'osclass_elektron'); ?></h2>
    <fieldset>
        <div class="form-horizontal">
            <?php
            $style_labels = array(
              'palette' => __('Default color theme', 'osclass_elektron'),
              'scheme'  => __('Default color scheme', 'osclass_elektron'),
              'fx'      => __('Effects', 'osclass_elektron'),
              'hero'    => __('Home hero background', 'osclass_elektron'),
              'glow'    => __('Glow of the 3D hero animation', 'osclass_elektron'),
              'lock'    => __('Padlock style in the 3D hero', 'osclass_elektron'),
              'motion'  => __('Hero animation', 'osclass_elektron'),
              'radius'  => __('Corner style', 'osclass_elektron'),
              'header'  => __('Header', 'osclass_elektron'),
            );
            foreach(elektron_style_options() as $key => $choices) { ?>
            <div class="form-row">
                <div class="form-label"><?php echo $style_labels[$key]; ?></div>
                <div class="form-controls">
                    <select name="style_<?php echo $key; ?>">
                        <?php foreach($choices as $value => $label) { ?>
                        <option value="<?php echo osc_esc_html($value); ?>" <?php if(elektron_style($key) === $value) { echo 'selected="selected"'; } ?>><?php echo $label; ?></option>
                        <?php } ?>
                    </select>
                    <?php if($key === 'palette') { ?><span class="help-box"><?php _e('Visitors can switch the color theme, the scheme and the effects with the palette button in the header. Their choice is remembered in their browser.', 'osclass_elektron'); ?></span><?php } ?>
                </div>
            </div>
            <?php } ?>
            <div class="form-row">
                <div class="form-label"><?php _e('Logo text', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <input type="text" class="xlarge" name="logo_text" maxlength="60" value="<?php echo osc_esc_html(osc_get_preference('logo_text', 'osclass_elektron')); ?>" placeholder="<?php echo osc_esc_html(osc_page_title()); ?>">
                    <span class="help-box"><?php _e('Shown next to the logo in the header and footer. Empty uses the site title. The logo image is uploaded under Appearance > Header logo, without an upload the Elektron mark is used.', 'osclass_elektron'); ?></span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Show logo text', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <div class="form-label-checkbox"><label><input type="checkbox" name="logo_show_text" value="1" <?php echo (osc_get_preference('logo_show_text', 'osclass_elektron') !== '0' ? 'checked="checked"' : ''); ?>> <?php _e('Show the text next to the logo image. Turn it off if your logo already contains the name.', 'osclass_elektron'); ?></label></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Hero title', 'osclass_elektron'); ?></div>
                <div class="form-controls"><input type="text" class="xlarge" name="hero_title" value="<?php echo osc_esc_html(osc_get_preference('hero_title', 'osclass_elektron')); ?>" placeholder="<?php echo osc_esc_html(elektron_hero_title()); ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Hero text', 'osclass_elektron'); ?></div>
                <div class="form-controls"><input type="text" class="xlarge" name="hero_text" value="<?php echo osc_esc_html(osc_get_preference('hero_text', 'osclass_elektron')); ?>" placeholder="<?php echo osc_esc_html(elektron_hero_text()); ?>"></div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <div class="form-horizontal">
            <div class="form-row">
                <div class="form-label"><?php _e('Search placeholder', 'osclass_elektron'); ?></div>
                <div class="form-controls"><input type="text" class="xlarge" name="keyword_placeholder" value="<?php echo osc_esc_html( osc_get_preference('keyword_placeholder', 'osclass_elektron') ); ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Show lists as:', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <select name="defaultShowAs@all">
                        <option value="gallery" <?php if(elektron_default_show_as() == 'gallery'){ echo 'selected="selected"' ; } ?>><?php _e('Gallery','osclass_elektron'); ?></option>
                        <option value="list" <?php if(elektron_default_show_as() == 'list'){ echo 'selected="selected"' ; } ?>><?php _e('List','osclass_elektron'); ?></option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Footer link', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <div class="form-label-checkbox"><input type="checkbox" name="footer_link" value="1" <?php echo (osc_get_preference('footer_link', 'osclass_elektron') ? 'checked' : ''); ?> > <?php _e('I want to help Osclass by linking to <a href="https://osclass-classifieds.com/" target="_blank">osclass</a> from my site with the following text:', 'osclass_elektron'); ?></div>
                    <span class="help-box"><?php _e('This website is proudly using the <a title="Osclass web" href="https://osclass-classifieds.com/">classifieds scripts</a> software <strong>Osclass</strong>', 'osclass_elektron'); ?></span>
                </div>
            </div>
        </div>
    </fieldset>


<?php if(1==2) { ?>
    <h2 class="render-title"><?php _e('Right-To-Left (RTL)', 'osclass_elektron'); ?></h2>
    <fieldset>
        <div class="form-horizontal">
            <div class="form-row">
                <div class="form-label"><?php _e('Enable Right-To-Left', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <div class="form-label-checkbox">
                        <label><input type="checkbox" <?php echo ( (osc_get_preference('rtl', 'osclass_elektron') != '0') ? 'checked="checked"' : ''); ?> name="rtl" value="1" /> <?php _e('enable/disable', 'osclass_elektron'); ?></label>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
<?php } ?>

    <h2 class="render-title"><?php _e('Network status bar', 'osclass_elektron'); ?></h2>
    <fieldset>
        <div class="form-horizontal">
            <div class="form-row">
                <div class="form-label"><?php _e('Status bar', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <div class="form-label-checkbox"><label><input type="checkbox" name="network_show" value="1" <?php echo elektron_network_enabled() ? 'checked="checked"' : ''; ?>> <?php _e('Show live block height, hash rate, fee, mempool and retarget progress above the header', 'osclass_elektron'); ?></label></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('API sources', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <textarea style="height: 115px; width: 500px;" name="network_sources" spellcheck="false"><?php echo osc_esc_html(elektron_network_sources_text()); ?></textarea>
                    <span class="help-box"><?php _e('One explorer API address per line, HTTPS only, at most 8. The sources are tried from top to bottom and the first one that answers is used. The default is the Elektron Net explorer. An empty list also falls back to it.', 'osclass_elektron'); ?></span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Cache in seconds', 'osclass_elektron'); ?></div>
                <div class="form-controls"><input type="number" min="30" max="600" name="network_ttl" value="<?php echo (int)elektron_network_ttl(); ?>"></div>
            </div>
        </div>
    </fieldset>

    <h2 class="render-title"><?php _e('Location input', 'osclass_elektron'); ?></h2>
    <fieldset>
        <div class="form-horizontal">
            <div class="form-row">
                <div class="form-label"><?php _e('Show location input as:', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <select name="defaultLocationShowAs">
                        <option value="dropdown" <?php if(elektron_default_location_show_as() == 'dropdown'){ echo 'selected="selected"' ; } ?>><?php _e('Dropdown','osclass_elektron'); ?></option>
                        <option value="autocomplete" <?php if(elektron_default_location_show_as() == 'autocomplete'){ echo 'selected="selected"' ; } ?>><?php _e('Autocomplete','osclass_elektron'); ?></option>
                    </select>
                </div>
            </div>
        </div>
    </fieldset>

    <h2 class="render-title"><?php _e('Ads management', 'osclass_elektron'); ?></h2>
    <div class="form-row">
        <div class="form-label"></div>
        <div class="form-controls">
            <p><?php _e('In this section you can configure your site to display ads and start generating revenue.', 'osclass_elektron'); ?><br/><?php _e('If you are using an online advertising platform, such as Google Adsense, copy and paste here the provided code for ads.', 'osclass_elektron'); ?></p>
        </div>
    </div>
    <fieldset>
        <div class="form-horizontal">
            <div class="form-row">
                <div class="form-label"><?php _e('Header 728x90', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <textarea style="height: 115px; width: 500px;"name="header-728x90"><?php echo osc_esc_html( osc_get_preference('header-728x90', 'osclass_elektron') ); ?></textarea>
                    <br/><br/>
                    <div class="help-box"><?php _e('This ad will be shown at the top of your website, next to the site title and above the search results. Note that the size of the ad has to be 728x90 pixels.', 'osclass_elektron'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Homepage 728x90', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <textarea style="height: 115px; width: 500px;" name="homepage-728x90"><?php echo osc_esc_html( osc_get_preference('homepage-728x90', 'osclass_elektron') ); ?></textarea>
                    <br/><br/>
                    <div class="help-box"><?php _e('This ad will be shown on the main site of your website. It will appear both at the top and bottom of your site homepage. Note that the size of the ad has to be 728x90 pixels.', 'osclass_elektron'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Search results 728x90 (top of the page)', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <textarea style="height: 115px; width: 500px;" name="search-results-top-728x90"><?php echo osc_esc_html( osc_get_preference('search-results-top-728x90', 'osclass_elektron') ); ?></textarea>
                    <br/><br/>
                    <div class="help-box"><?php _e('This ad will be shown on top of the search results of your site. Note that the size of the ad has to be 728x90 pixels.', 'osclass_elektron'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Search results 728x90 (middle of the page)', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <textarea style="height: 115px; width: 500px;" name="search-results-middle-728x90"><?php echo osc_esc_html( osc_get_preference('search-results-middle-728x90', 'osclass_elektron') ); ?></textarea>
                    <br/><br/>
                    <div class="help-box"><?php _e('This ad will be shown among the search results of your site. Note that the size of the ad has to be 728x90 pixels.', 'osclass_elektron'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Sidebar 300x250', 'osclass_elektron'); ?></div>
                <div class="form-controls">
                    <textarea style="height: 115px; width: 500px;" name="sidebar-300x250"><?php echo osc_esc_html( osc_get_preference('sidebar-300x250', 'osclass_elektron') ); ?></textarea>
                    <br/><br/>
                    <div class="help-box"><?php _e('This ad will be shown at the right sidebar of your website, on the product detail page. Note that the size of the ad has to be 300x350 pixels.', 'osclass_elektron'); ?></div>
                </div>
            </div>
            <div class="form-actions">
                <input type="submit" value="<?php _e('Save changes', 'osclass_elektron'); ?>" class="btn btn-submit">
            </div>
        </div>
    </fieldset>
</form>
