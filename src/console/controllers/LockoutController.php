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

use Craft;
use craft\console\Controller;
use jalendport\base\controllers\ConsoleControllerTrait;
use jalendport\lockout\Lockout;
use jalendport\lockout\services\LockoutService;
use yii\base\Exception;
use yii\console\ExitCode;

/**
 * Allows you to temporarily lock certain users out of the control panel.
 *
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 1.0.0
 */
class LockoutController extends Controller
{
    use ConsoleControllerTrait;

    // Const Properties
    // =========================================================================

    /**
     * The exit code `status` returns when the control panel is open. It's not a
     * failure — it's the other half of the answer, so deploy scripts can branch
     * on it directly.
     *
     * @since 3.0.0
     */
    public const EXIT_CODE_UNLOCKED = 1;

    // Public Methods
    // =========================================================================

    /**
     * Allow non-admin users to access the CP.
     *
     * @return int the exit code
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public function actionDisable(): int
    {
        return $this->runAndExit(function(): void {
            if (!Lockout::$plugin->lockoutService->disable()) {
                throw new Exception('Couldn’t disable Lockout.');
            }

            $this->writeSuccess("Lockout disabled for {$this->_environment()}.");
            $this->_warnIfOverridden();
        });
    }

    /**
     * Restrict non-admin users from accessing the CP.
     *
     * @return int the exit code
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public function actionEnable(): int
    {
        return $this->runAndExit(function(): void {
            if (!Lockout::$plugin->lockoutService->enable()) {
                throw new Exception('Couldn’t enable Lockout.');
            }

            $this->writeSuccess("Lockout enabled for {$this->_environment()}.");
            $this->_warnIfOverridden();
        });
    }

    /**
     * Report whether the CP is locked.
     *
     * Exits `0` when it is and `1` when it isn't, so a deploy script can branch
     * on the command itself.
     *
     * @return int the exit code
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function actionStatus(): int
    {
        $service = Lockout::$plugin->lockoutService;
        $source = $service->getSource() === LockoutService::SOURCE_CONFIG
            ? 'config/lockout.php'
            : 'the database';

        if (!$service->getIsEnabled()) {
            $this->writeLine("Lockout is disabled for {$this->_environment()}, per {$source}.");

            return self::EXIT_CODE_UNLOCKED;
        }

        $this->writeSuccess("Lockout is enabled for {$this->_environment()}, per {$source}.");

        return ExitCode::OK;
    }

    // Private Methods
    // =========================================================================

    /**
     * Returns the environment being toggled, for the output.
     *
     * @return string the environment name
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    private function _environment(): string
    {
        return (string)Craft::$app->getConfig()->env ?: 'this unnamed environment';
    }

    /**
     * Points out that the flag just written isn't the one in charge.
     *
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    private function _warnIfOverridden(): void
    {
        $override = Lockout::$plugin->lockoutService->getConfigOverride();

        if ($override === null) {
            return;
        }

        $state = $override ? 'enabled' : 'disabled';

        $this->writeLine("Note: the enabled setting in config/lockout.php is pinning Lockout {$state}, so this change has no effect until it's removed.");
    }
}
