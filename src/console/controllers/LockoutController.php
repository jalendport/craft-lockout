<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\console\controllers;

use jalendport\lockout\Lockout;
use yii\console\Controller;

/**
 * Allows you to temporarily lock certain users out of the control panel.
 *
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 */
class LockoutController extends Controller
{

	/**
	 * Restrict non-admin users from accessing the CP.
	 *
	 * @return bool
	 */
	public function actionEnable(): bool
	{
		return (int)Lockout::$plugin->lockoutService->enable();
	}


	/**
	 * Allow non-admin users to access the CP.
	 *
	 * @return bool
	 */
	public function actionDisable(): bool
	{
		return (int)Lockout::$plugin->lockoutService->disable();
	}

}
