<?php
/**
 * Lockout config.php
 *
 * This file exists only as a template. Don't edit it directly — copy it to your
 * project's `config/` folder, rename it to `lockout.php`, and make your changes
 * there. Any setting you leave out falls back to the value configured on the
 * plugin's settings page.
 *
 * The file is fully multi-environment aware.
 *
 * @see https://craftcms.com/docs/5.x/configure.html#config-files
 */

return [
    // Whether the control panel is locked. Use null (or omit the setting
    // entirely) to leave the decision to the per-environment flag the Lockout
    // utility and the `lockout/lockout` commands write to. Setting true or
    // false pins the state — the utility's toggle is disabled while it does.
    //
    // Handy for committing a lockout to version control, or for driving one
    // from an env var: 'enabled' => App::env('LOCKOUT_ENABLED').
    'enabled' => null,

    // The message shown to locked-out users. Leave empty for the default,
    // "Access to the control panel is temporarily restricted."
    'message' => '',
];
