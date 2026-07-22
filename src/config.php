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

    // The lock screen's headline. Leave empty for the default,
    // "Control panel locked".
    'headline' => '',

    // The lock screen's message. Leave empty for the default,
    // "Access to the control panel is temporarily restricted."
    'message' => '',

    // The HTTP status locked-out requests are answered with: 403 (the default)
    // or 503. A 503 also sends a Retry-After header, built from Craft's own
    // retryDuration setting.
    'statusCode' => 403,

    // A site template to render in place of the stock lock screen. It's handed
    // `headline` and `message` variables. Leave null for the stock screen.
    'template' => null,
];
