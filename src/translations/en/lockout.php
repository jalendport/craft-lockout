<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

return [
    '403 (Forbidden)' => '403 (Forbidden)',
    '503 (Service Unavailable)' => '503 (Service Unavailable)',
    'A site template to render in place of the lock screen. It’s handed `headline` and `message` variables. Leave empty for the stock screen.' => 'A site template to render in place of the lock screen. It’s handed `headline` and `message` variables. Leave empty for the stock screen.',
    'Access to the control panel is temporarily restricted.' => 'Access to the control panel is temporarily restricted.',
    'Bypass Lockout' => 'Bypass Lockout',
    'Control panel locked' => 'Control panel locked',
    'Couldn’t disable Lockout.' => 'Couldn’t disable Lockout.',
    'Couldn’t enable Lockout.' => 'Couldn’t enable Lockout.',
    'Headline' => 'Headline',
    'Lock the control panel' => 'Lock the control panel',
    'Lockout' => 'Lockout',
    'Lockout disabled.' => 'Lockout disabled.',
    'Lockout enabled.' => 'Lockout enabled.',
    'Lockout is stored per environment. This one has no name; set CRAFT_ENVIRONMENT to tell your environments apart.' => 'Lockout is stored per environment. This one has no name; set CRAFT_ENVIRONMENT to tell your environments apart.',
    'Lockout is stored per environment. This toggle applies to {environment}.' => 'Lockout is stored per environment. This toggle applies to {environment}.',
    'Message' => 'Message',
    'Shown to users who are locked out of the control panel.' => 'Shown to users who are locked out of the control panel.',
    'Status Code' => 'Status Code',
    'Template' => 'Template',
    'The HTTP status locked-out requests are answered with. 503 also sends a `Retry-After` header, built from Craft’s Retry Duration setting.' => 'The HTTP status locked-out requests are answered with. 503 also sends a `Retry-After` header, built from Craft’s Retry Duration setting.',
    'The control panel is locked. Only admins and users with the “Bypass Lockout” permission can get in.' => 'The control panel is locked. Only admins and users with the “Bypass Lockout” permission can get in.',
    'The control panel is open to everyone who can normally sign in.' => 'The control panel is open to everyone who can normally sign in.',
    'The heading on the lock screen.' => 'The heading on the lock screen.',
    'This setting is being overridden by the {setting} setting in your {file} config file.' => 'This setting is being overridden by the {setting} setting in your {file} config file.',
    'Toggle Lockout' => 'Toggle Lockout',
    'You don’t have permission to change this.' => 'You don’t have permission to change this.',
];
