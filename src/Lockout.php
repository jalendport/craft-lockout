<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout;

use Craft;
use craft\base\Model;
use craft\console\Application as ConsoleApplication;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Dashboard;
use craft\web\Application as WebApplication;
use craft\web\Controller;
use jalendport\base\Plugin;
use jalendport\lockout\models\Settings;
use jalendport\lockout\services\LockoutService;
use jalendport\lockout\widgets\LockoutWidget;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Lockout plugin.
 *
 * @property-read LockoutService $lockoutService
 * @method Settings getSettings()
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 1.0.0
 */
class Lockout extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Lockout The plugin instance.
     * @since 1.0.0
     */
    public static Lockout $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var bool Whether the plugin has a settings page in the control panel.
     * @since 1.0.0
     */
    public bool $hasCpSettings = true;

    /**
     * @var string The plugin's schema version.
     * @since 1.0.0
     */
    public string $schemaVersion = '3.0.0';

    // Public Methods
    // =========================================================================

    /**
     * Registers the plugin's components per the Craft 5 plugin spec.
     *
     * @return array<string, mixed>
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public static function config(): array
    {
        return [
            'components' => [
                'lockoutService' => LockoutService::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'jalendport\lockout\console\controllers';
        }

        $this->_registerWidgets();
        $this->_registerCpGuard();
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     * @throws InvalidConfigException if the settings model can't be created
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    /**
     * @inheritdoc
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    protected function settingsHtml(): ?string
    {
        $settings = $this->getSettings();
        $settings->validate();

        /** @var WebApplication $app */
        $app = Craft::$app;

        return $app->getView()->renderTemplate('lockout/settings', [
            'settings' => $settings,
            'overrides' => $this->getConfigOverrides(),
        ]);
    }

    // Private Methods
    // =========================================================================

    /**
     * Blocks locked-out users from the CP.
     *
     * This hangs off the controller's before-action event rather than `init()`:
     * throwing from `init()` fires on *every* CP request, logout included, so a
     * locked-out user could never sign out.
     *
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    private function _registerCpGuard(): void
    {
        Event::on(
            Controller::class,
            Controller::EVENT_BEFORE_ACTION,
            static function(ActionEvent $event): void {
                self::$plugin->lockoutService->checkAction($event->action);
            }
        );
    }

    /**
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    private function _registerWidgets(): void
    {
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            static function(RegisterComponentTypesEvent $event): void {
                $event->types[] = LockoutWidget::class;
            }
        );
    }
}
