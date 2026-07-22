<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\utilities;

use Craft;
use craft\base\Utility;
use craft\web\Application as WebApplication;
use jalendport\lockout\Lockout as LockoutPlugin;
use jalendport\lockout\services\LockoutService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;

/**
 * The Lockout utility: the control panel's home for the lockout toggle.
 *
 * It replaces the dashboard widget 2.x shipped, which had no room to say which
 * environment it was toggling — the source of Lockout's most common support
 * question.
 *
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 3.0.0
 */
class Lockout extends Utility
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public static function contentHtml(): string
    {
        /** @var WebApplication $app */
        $app = Craft::$app;

        $service = LockoutPlugin::$plugin->lockoutService;

        return $app->getView()->renderTemplate('lockout/_utility', [
            'canManage' => $app->getUser()->checkPermission(LockoutService::PERMISSION_MANAGE),
            'enabled' => $service->getIsEnabled(),
            'environment' => (string)$app->getConfig()->env,
            'source' => $service->getSource(),
        ]);
    }

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public static function displayName(): string
    {
        return Craft::t('lockout', 'Lockout');
    }

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public static function icon(): ?string
    {
        return Craft::getAlias('@lockout/icon.svg') ?: null;
    }

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public static function id(): string
    {
        return 'lockout';
    }
}
