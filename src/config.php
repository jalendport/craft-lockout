<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

/**
 * Lockout config.php
 *
 * This file exists only as a template for the Lockout settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'lockout.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [

    // This message will be displayed when non-admin users try to login while Lockout is enabled.
    'message' => '',

];
