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


    // meta tag robots
    osc_add_hook('header','elektron_nofollow_construct');

    elektron_add_body_class('register');
    osc_enqueue_script('jquery-validate');
    osc_current_web_theme_path('header.php') ;
?>
<div class="form-container form-horizontal form-container-box">
    <div class="header">
        <h1><?php _e('Register an account for free', 'osclasselektron'); ?></h1>
    </div>
    <div class="resp-wrapper">
        <form name="register" action="<?php echo osc_base_url(true); ?>" method="post" >
            <input type="hidden" name="page" value="register" />
            <input type="hidden" name="action" value="register_post" />

            <?php osc_run_hook('user_pre_register_form'); ?>

            <ul id="error_list"></ul>
            <div class="control-group">
                <label class="control-label" for="name"><?php _e('Name', 'osclasselektron'); ?></label>
                <div class="controls">
                    <?php UserForm::name_text(); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email"><?php _e('Email', 'osclasselektron'); ?></label>
                <div class="controls">
                    <?php UserForm::email_text(); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password"><?php _e('Password', 'osclasselektron'); ?></label>
                <div class="controls">
                    <?php UserForm::password_text(); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password-2"><?php _e('Repeat password', 'osclasselektron'); ?></label>
                <div class="controls">
                    <?php UserForm::check_password_text(); ?>
                    <p id="password-error" style="display:none;">
                        <?php _e('The passwords do not match', 'osclasselektron'); ?>
                    </p>
                </div>
            </div>

            <?php osc_run_hook('user_register_form'); ?>

            <div class="control-group"><?php osc_show_recaptcha('register'); ?></div>

            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary"><?php _e("Create account", 'osclasselektron'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php UserForm::js_validation(); ?>
<?php osc_current_web_theme_path('footer.php') ;
