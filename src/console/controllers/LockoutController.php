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

use jalendport\base\controllers\ConsoleControllerTrait;
use jalendport\lockout\Lockout;
use yii\base\Exception;
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
    use ConsoleControllerTrait;

    /**
     * Restrict non-admin users from accessing the CP.
     *
     * @return int the exit code
     * @since 1.0.0
     */
    public function actionEnable(): int
    {
        return $this->runAndExit(function(): void {
            if (!Lockout::$plugin->lockoutService->enable()) {
                throw new Exception('Couldn’t enable Lockout.');
            }

            $this->writeSuccess('Lockout enabled.');
        });
    }


    /**
     * Allow non-admin users to access the CP.
     *
     * @return int the exit code
     * @since 1.0.0
     */
    public function actionDisable(): int
    {
        return $this->runAndExit(function(): void {
            if (!Lockout::$plugin->lockoutService->disable()) {
                throw new Exception('Couldn’t disable Lockout.');
            }

            $this->writeSuccess('Lockout disabled.');
        });
    }
}
