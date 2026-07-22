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
use craft\web\Application as WebApplication;
use craft\web\Request as WebRequest;
use jalendport\lockout\Lockout;
use jalendport\lockout\models\Settings;
use yii\base\Action;
use yii\web\ForbiddenHttpException;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 */
class LockoutService extends Component
{
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
     * Blocks the given CP action if the current user is locked out.
     *
     * Runs off `Controller::EVENT_BEFORE_ACTION` rather than the plugin's
     * `init()`, so the route is already resolved and the auth actions above
     * can be let through.
     *
     * @param Action $action the action about to run
     * @throws ForbiddenHttpException if the user is locked out
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
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function check(): bool
    {
        /** @var WebApplication $app */
        $app = Craft::$app;

        if ($app->getUser()->getIsAdmin()) {
            return true;
        }

        $localSettings = Lockout::$plugin->getLocalSettings();
        /** @var Settings $settings */
        $settings = Lockout::$plugin->getSettings();

        $message = $settings->message ?: Craft::t('lockout', 'Access to the control panel is temporarily restricted.');

        if ($localSettings->enabled) {
            throw new ForbiddenHttpException($message);
        }

        return false;
    }


    /**
     * @return bool
     */
    public function enable(): bool
    {
        $lockoutRecord = Lockout::$plugin->getLocalSettings();
        $lockoutRecord->enabled = true;
        return $lockoutRecord->save();
    }


    /**
     * @return bool
     */
    public function disable(): bool
    {
        $lockoutRecord = Lockout::$plugin->getLocalSettings();
        $lockoutRecord->enabled = false;
        return $lockoutRecord->save();
    }
}
