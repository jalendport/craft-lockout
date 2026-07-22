<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\controllers;

use Craft;
use craft\web\Controller;
use jalendport\base\controllers\WebControllerTrait;
use jalendport\lockout\Lockout;
use jalendport\lockout\services\Lockout as LockoutService;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Turns Lockout off.
 *
 * Kept as its own controller so the 2.x `lockout/disable` route still works.
 *
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 1.0.0
 */
class DisableController extends Controller
{
    // Traits
    // =========================================================================

    use WebControllerTrait;

    // Public Methods
    // =========================================================================

    /**
     * Allows non-admin users to access the CP.
     *
     * @return Response|null the response
     * @throws BadRequestHttpException if the request isn't a POST accepting JSON
     * @throws ForbiddenHttpException if the user can't manage Lockout
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public function actionIndex(): ?Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();
        $this->requirePermission(LockoutService::PERMISSION_MANAGE);

        return $this->runAndReturn(static function(): void {
            if (!Lockout::$plugin->lockout->disable()) {
                throw new ServerErrorHttpException(Craft::t('lockout', 'Couldn’t disable Lockout.'));
            }
        }, Craft::t('lockout', 'Lockout disabled.'));
    }
}
