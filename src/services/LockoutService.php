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
use jalendport\lockout\Lockout;
use jalendport\lockout\models\Settings;
use yii\web\ForbiddenHttpException;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 */
class LockoutService extends Component
{

	/**
	 * @return bool
	 * @throws ForbiddenHttpException
	 */
	public function check(): bool
	{
		if (Craft::$app->user->getIsAdmin())
		{
			return true;
		}

		$localSettings = Lockout::$plugin->getLocalSettings();
		/** @var Settings $settings */
		$settings = Lockout::$plugin->getSettings();

		$message = $settings->message ?: 'Access to the control panel is temporarily restricted.';

		if ($localSettings->enabled)
		{
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
