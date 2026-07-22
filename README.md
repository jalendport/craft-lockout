<p align="center"><img src="src/icon.svg" alt="Lockout" width="80" height="80"></p>

<h1 align="center">Lockout</h1>

Lockout is a Craft CMS plugin that temporarily restricts control panel access during deployments, content freezes, and structural changes. Its database-backed state survives project config application, while permissions and per-environment configuration determine who can enter and how locked-out requests respond.

## Features

- **Dedicated utility** — see whether Lockout is enabled, the active environment, and whether the state comes from the database or a config override, then toggle it from one place.
- **Permission-based access** — grant users permission to manage Lockout or bypass it while everyone else remains locked out; administrators always retain access.
- **Deployment-safe state** — the database-backed toggle survives `project-config/apply`, making it suitable for deployment scripts and content freezes.
- **Environment overrides** — force Lockout on or off from `config/lockout.php`; an active override disables the utility toggle and clearly identifies its source.
- **Custom lock screen** — set the headline and message, return either a 403 or 503 response, and optionally render your own template.
- **CLI ready** — enable, disable, or inspect Lockout from deployment scripts, with status output that includes the effective state and its source.

## Installation

### Requirements

This plugin requires **Craft CMS 5.0.0 or later** and **PHP 8.2 or later**.

> Using Craft 4? Use the [2.x](https://github.com/jalendport/craft-lockout/tree/craft4) line, which is the Craft 4 release of this plugin.

### Plugin Store

Log into your control panel and click on 'Plugin Store'. Search for 'Lockout'.

### Composer

1. Open your terminal and go to your Craft project:

```bash
cd /path/to/project
```

2. Then tell Composer to load the plugin:

```bash
composer require jalendport/craft-lockout
```

3. In the Control Panel, go to Settings → Plugins and click the "Install" button for Lockout.

## Usage

### Managing Lockout

Open Utilities → Lockout to see the effective state, active environment, and state source. Users with the **Manage Lockout** permission can enable or disable Lockout there; administrators have this permission implicitly.

When `enabled` is set in `config/lockout.php`, the config value takes precedence over the database state. The utility identifies the override and disables its toggle until the override is removed.

### Choosing Who Can Enter

Lockout registers two permissions that can be assigned to users and user groups:

| Permission | Allows |
| --- | --- |
| **Manage Lockout** (`lockout:manage`) | View the utility and enable or disable Lockout. |
| **Bypass Lockout** (`lockout:bypass`) | Continue using the control panel while Lockout is enabled. |

Administrators always bypass Lockout. Give **Bypass Lockout** to any user groups that should retain control panel access during a lockout; all other users are shown the configured lock screen.

### CLI

Lockout provides three console commands for deployment scripts:

```bash
./craft lockout/lockout/enable
./craft lockout/lockout/disable
./craft lockout/lockout/status
```

The status command reports whether Lockout is enabled and whether the effective state comes from the database or a config override. It exits with `0` when locked and `1` when unlocked, so scripts can branch on the result.

The enabled state is stored separately from project config, so it survives `project-config/apply` during a deployment. This avoids the state-reset behavior that can affect [`craft off`](https://github.com/craftcms/cms/issues/9855) when project config is reapplied.

## Configuration

### Settings

The presentation settings are available from Lockout's plugin settings in the control panel:

| Setting | Config key | Description |
| --- | --- | --- |
| Headline | `headline` | The heading shown to locked-out users. |
| Message | `message` | The supporting message shown below the headline. |
| Status code | `statusCode` | The HTTP status returned by the lock screen: `403` (Forbidden) or `503` (Service Unavailable). A 503 response also includes a `Retry-After` header when Craft's [`retryDuration`](https://craftcms.com/docs/5.x/reference/config/general.html#retryduration) setting is configured. |
| Custom template | `template` | An optional site template to render instead of Lockout's stock error page. |

### Overriding Plugin Settings

Create a [config file](https://craftcms.com/docs/5.x/configure.html#config-files) named `lockout.php` in your project's `config` folder to override Lockout's settings. The file is fully [multi-environment](https://craftcms.com/docs/5.x/configure.html#multi-environment-configs) aware, so each environment can have its own state and presentation. A commented template you can copy is included at [`src/config.php`](src/config.php).

```php
<?php

return [
    'enabled' => true, // true, false, or null to use the database state
    'headline' => 'Maintenance in progress',
    'message' => 'We’ll be back shortly.',
    'statusCode' => 503,
    'template' => 'errors/maintenance',
];
```

Values declared in the config file override the corresponding control panel settings. For `enabled`, `true` forces Lockout on, `false` forces it off, and `null` uses the database-backed state managed by the utility or CLI.

## Support

Found a bug or need help? Open an [issue](https://github.com/jalendport/craft-lockout/issues).

<hr>

<p align="center">Made by <a href="https://jalendport.com">Jalen Davenport</a></p>
<p align="center">Lock icon by <a href="https://thenounproject.com/term/lock/680647/">Kiran Joseph</a> from the <a href="https://thenounproject.com">Noun Project</a></p>
