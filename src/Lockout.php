<?php
/**
 * Lockout plugin for Craft CMS 3.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout;

use jalendport\lockout\models\Settings;
use jalendport\lockout\records\LocalSettings;
use jalendport\lockout\services\LockoutService;
use jalendport\lockout\widgets\LockoutWidget;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\UrlHelper;
use craft\services\Dashboard;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use yii\base\Event;
use yii\web\HttpException;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 *
 * @property  LocalSettings $localSettings
 * @property  Settings $settings
 * @property  lockoutService $lockoutService
 */
class Lockout extends Plugin
{
	
	/**
	 * @var Lockout
	 */
    public static $plugin;
	
	
	/**
	 * @throws HttpException
	 */
	public function init()
    {
        parent::init();
        self::$plugin = $this;
        
        if (Craft::$app instanceof ConsoleApplication)
        {
            $this->controllerNamespace = 'jalendport\lockout\console\controllers';
        }
        
        // Register our widget
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            static function (RegisterComponentTypesEvent $event)
			{
                $event->types[] = LockoutWidget::class;
            }
        );
		
        // Save our local settings
		Event::on(
			__CLASS__,
			self::EVENT_BEFORE_SAVE_SETTINGS,
			static function()
			{
				$enabled = Craft::$app->request->getBodyParam('settings')['enabled'];
				if ($enabled) {
					self::$plugin->lockoutService->enable();
				} else {
					self::$plugin->lockoutService->disable();
				}
			}
		);
	
		// Redirect back to the plugin settings page
		Event::on(
			__CLASS__,
			self::EVENT_AFTER_SAVE_SETTINGS,
			static function () {
				Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/lockout'))->send();
			}
		);
  
		// Check all CP requests
		if (Craft::$app->user->getIdentity() && Craft::$app->getRequest()->isCpRequest)
		{
			$this->lockoutService->check();
		}
    }
	
	
	/**
	 * @return Model|Settings|null
	 */
	protected function createSettingsModel(): ?\craft\base\Model
    {
        return new Settings();
    }
	
	
	/**
	 * @return LocalSettings
	 */
	public function getLocalSettings(): LocalSettings
	{
		$lockoutRecord = LocalSettings::findOne([
			'environment' => Craft::$app->getConfig()->env
		]);
		
		if ($lockoutRecord === null) {
			$lockoutRecord = new LocalSettings();
		}
		
		$lockoutRecord->environment = Craft::$app->getConfig()->env;
		
		return $lockoutRecord;
	}
	
	
	/**
	 * @return string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	protected function settingsHtml(): string
    {
		$settings = $this->getSettings();
		$settings->validate();
	
		$localSettings = $this->getLocalSettings();
		$localSettings->validate();
	
		$overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));
    	
        return Craft::$app->view->renderTemplate(
            'lockout/settings',
            [
                'settings' => $settings,
				'localSettings' => $this->getLocalSettings(),
				'overrides' => array_keys($overrides),
            ]
        );
    }
    
}
