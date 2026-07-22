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
use craft\web\Request as WebRequest;
use jalendport\base\Plugin;
use jalendport\lockout\models\Settings;
use jalendport\lockout\records\LocalSettings;
use jalendport\lockout\services\LockoutService;
use jalendport\lockout\widgets\LockoutWidget;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\base\Exception;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 *
 * @property  LocalSettings $localSettings
 * @property  Settings $settings
 * @property  LockoutService $lockoutService
 */
class Lockout extends Plugin
{
    /**
     * @var Lockout
     */
    public static $plugin;

    /**
     * @var bool Whether the plugin has a settings page in the control panel.
     */
    public bool $hasCpSettings = true;

    /**
     * @var string The plugin's schema version.
     */
    public string $schemaVersion = '1.0.0';


    /**
     * Registers the plugin's components per the Craft 5 plugin spec.
     *
     * @return array<string, mixed>
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
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'jalendport\lockout\console\controllers';
        }

        $this->_registerWidgets();
        $this->_registerSettingsHooks();
        $this->_registerCpGuard();
    }


    /**
     * @return LocalSettings
     */
    public function getLocalSettings(): LocalSettings
    {
        $lockoutRecord = LocalSettings::findOne([
            'environment' => Craft::$app->getConfig()->env,
        ]);

        if ($lockoutRecord === null) {
            $lockoutRecord = new LocalSettings();
        }

        $lockoutRecord->environment = Craft::$app->getConfig()->env;

        return $lockoutRecord;
    }


    /**
     * @return Model|null
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }


    /**
     * @return string|null
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    protected function settingsHtml(): ?string
    {
        $settings = $this->getSettings();
        $settings->validate();

        $localSettings = $this->getLocalSettings();
        $localSettings->validate();

        /** @var WebApplication $app */
        $app = Craft::$app;

        return $app->getView()->renderTemplate('lockout/settings', [
            'settings' => $settings,
            'localSettings' => $localSettings,
            'overrides' => $this->getConfigOverrides(),
        ]);
    }


    /**
     * Blocks locked-out users from the CP.
     *
     * This hangs off the controller's before-action event rather than `init()`:
     * throwing from `init()` fires on *every* CP request, logout included, so a
     * locked-out user could never sign out.
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
     * Mirrors the settings-page lightswitch into the environment-local record,
     * which is where the enabled flag actually lives.
     */
    private function _registerSettingsHooks(): void
    {
        Event::on(
            __CLASS__,
            self::EVENT_BEFORE_SAVE_SETTINGS,
            static function(): void {
                $request = Craft::$app->getRequest();

                // Programmatic saves have no body to read the lightswitch from;
                // leave the current state alone rather than disabling it.
                if (!$request instanceof WebRequest) {
                    return;
                }

                $settings = $request->getBodyParam('settings');

                if (!is_array($settings) || !array_key_exists('enabled', $settings)) {
                    return;
                }

                if ($settings['enabled']) {
                    self::$plugin->lockoutService->enable();
                } else {
                    self::$plugin->lockoutService->disable();
                }
            }
        );
    }


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
