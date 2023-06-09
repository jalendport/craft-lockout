<?php
/**
 * Lockout plugin for Craft CMS 4.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\controllers;

use craft\web\Controller;
use jalendport\lockout\Lockout;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 */
class DisableController extends Controller
{

	/**
	 * @return bool
	 */
	public function actionIndex(): bool
	{
		return Lockout::$plugin->lockoutService->disable();
	}

}
