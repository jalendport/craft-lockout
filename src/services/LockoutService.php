<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\services;

use Craft;
use craft\base\Component;
use craft\helpers\Json;
use craft\web\Application as WebApplication;
use craft\web\Request as WebRequest;
use jalendport\lockout\Lockout;
use jalendport\lockout\records\LocalSettings;
use yii\base\Action;
use yii\web\ForbiddenHttpException;

/**
 * Resolves and mutates the lockout state.
 *
 * State lives in two places: a per-environment database flag (what the utility
 * and the CLI write to) and an optional `enabled` setting in
 * `config/lockout.php`. A non-null config value always wins — see
 * {@see getIsEnabled()}.
 *
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 1.0.0
 */
class LockoutService extends Component
{
    // Const Properties
    // =========================================================================

    /**
     * @var string[] Actions that stay reachable while Lockout is enabled.
     *
     * Without these, a locked-out user can't reach `users/logout` — they're
     * stranded in the CP until they clear their session cookie by hand.
     *
     * This mirrors the auth-related entries of `UsersController::$allowAnonymous`,
     * minus `save-user` (public registration, not auth) and `impersonate-with-token`,
     * which stay locked.
     * @since 3.0.0
     */
    public const AUTH_ACTIONS = [
        'users/auth-form',
        'users/get-user-for-login',
        'users/login',
        'users/login-modal',
        'users/login-with-passkey',
        'users/logout',
        'users/send-password-reset-email',
        'users/session-info',
        'users/set-password',
        'users/verify-email',
    ];

    /**
     * Lets a user stay in the control panel while Lockout is enabled.
     *
     * Admins always bypass, permission or not.
     *
     * The permission handles live here rather than on a controller: the manage
     * permission is enforced by two web controllers, the utility, and the CLI,
     * so no single controller owns it — this service does.
     *
     * @since 3.0.0
     */
    public const PERMISSION_BYPASS = 'lockout:bypass';

    /**
     * Lets a user toggle Lockout from the utility, the controllers, or the CLI.
     *
     * @since 3.0.0
     */
    public const PERMISSION_MANAGE = 'lockout:manage';

    /**
     * The state is pinned by the `enabled` setting in `config/lockout.php`.
     *
     * @since 3.0.0
     */
    public const SOURCE_CONFIG = 'config';

    /**
     * The state comes from the environment's database flag.
     *
     * @since 3.0.0
     */
    public const SOURCE_DB = 'db';

    // Private Properties
    // =========================================================================

    /**
     * @var LocalSettings|null The memoized record for the current environment.
     * @see getLocalSettings()
     * @since 3.0.0
     */
    private ?LocalSettings $_localSettings = null;

    // Public Methods
    // =========================================================================

    /**
     * Throws if the current user is locked out.
     *
     * @return bool `true` when the user bypasses Lockout, `false` when it's off
     * @throws ForbiddenHttpException if the user is locked out
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public function check(): bool
    {
        /** @var WebApplication $app */
        $app = Craft::$app;

        // checkPermission() already lets admins through; the explicit check
        // keeps that guarantee if the permission is ever revoked or renamed.
        if ($app->getUser()->getIsAdmin() || $app->getUser()->checkPermission(self::PERMISSION_BYPASS)) {
            return true;
        }

        if (!$this->getIsEnabled()) {
            return false;
        }

        $message = Lockout::$plugin->getSettings()->message
            ?: Craft::t('lockout', 'Access to the control panel is temporarily restricted.');

        throw new ForbiddenHttpException($message);
    }

    /**
     * Blocks the given CP action if the current user is locked out.
     *
     * Runs off `Controller::EVENT_BEFORE_ACTION` rather than the plugin's
     * `init()`, so the route is already resolved and the auth actions above
     * can be let through.
     *
     * @param Action $action the action about to run
     * @throws ForbiddenHttpException if the user is locked out
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function checkAction(Action $action): void
    {
        $request = Craft::$app->getRequest();

        if (!$request instanceof WebRequest || !$request->getIsCpRequest()) {
            return;
        }

        if (in_array($action->getUniqueId(), self::AUTH_ACTIONS, true)) {
            return;
        }

        /** @var WebApplication $app */
        $app = Craft::$app;

        // Nobody's signed in yet — the login screen has to stay reachable.
        if (!$app->getUser()->getIdentity()) {
            return;
        }

        $this->check();
    }

    /**
     * Clears the lockout flag for the current environment.
     *
     * @return bool whether the flag was saved
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public function disable(): bool
    {
        return $this->_saveEnabled(false);
    }

    /**
     * Sets the lockout flag for the current environment.
     *
     * @return bool whether the flag was saved
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public function enable(): bool
    {
        return $this->_saveEnabled(true);
    }

    /**
     * Returns the state pinned by `config/lockout.php`, or `null` when the
     * config file leaves the decision to the database flag.
     *
     * @return bool|null the pinned state
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function getConfigOverride(): ?bool
    {
        if (!in_array('enabled', Lockout::$plugin->getConfigOverrides(), true)) {
            return null;
        }

        return Lockout::$plugin->getSettings()->enabled;
    }

    /**
     * Returns whether Lockout is currently enabled, config override included.
     *
     * @return bool whether the CP is locked
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function getIsEnabled(): bool
    {
        return $this->getConfigOverride() ?? (bool)$this->getLocalSettings()->enabled;
    }

    /**
     * Returns the current environment's record, creating an unsaved one when
     * the environment hasn't been locked before.
     *
     * Memoized: the state is read on every CP request, and once per request is
     * enough.
     *
     * @return LocalSettings the environment's record
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function getLocalSettings(): LocalSettings
    {
        if ($this->_localSettings !== null) {
            return $this->_localSettings;
        }

        $environment = (string)Craft::$app->getConfig()->env;

        $record = LocalSettings::findOne(['environment' => $environment]) ?? new LocalSettings();
        $record->environment = $environment;

        return $this->_localSettings = $record;
    }

    /**
     * Returns where the current state came from, so the utility and the CLI can
     * explain themselves.
     *
     * @return string one of the `SOURCE_` constants
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function getSource(): string
    {
        return $this->getConfigOverride() !== null ? self::SOURCE_CONFIG : self::SOURCE_DB;
    }

    // Private Methods
    // =========================================================================

    /**
     * Writes the environment's lockout flag.
     *
     * @param bool $enabled the state to store
     * @return bool whether the record saved
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    private function _saveEnabled(bool $enabled): bool
    {
        $record = $this->getLocalSettings();
        $record->enabled = $enabled;

        if (!$record->save()) {
            $errors = Json::encode($record->getErrors());
            Lockout::error("Couldn’t save the Lockout state: {$errors}");

            return false;
        }

        return true;
    }
}
