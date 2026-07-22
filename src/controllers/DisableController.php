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
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 */
class DisableController extends Controller
{
    use WebControllerTrait;

    /**
     * Allows non-admin users to access the CP.
     *
     * @return Response|null
     * @throws BadRequestHttpException if the request isn't a POST accepting JSON
     * @throws ForbiddenHttpException if the user isn't an admin
     * @since 1.0.0
     */
    public function actionIndex(): ?Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        // false, not true: the toggle has to keep working when
        // `allowAdminChanges` is off — that's the scenario this plugin is for.
        $this->requireAdmin(false);

        return $this->runAndReturn(static function(): void {
            if (!Lockout::$plugin->lockoutService->disable()) {
                throw new ServerErrorHttpException(Craft::t('lockout', 'Couldn’t disable Lockout.'));
            }
        }, Craft::t('lockout', 'Lockout disabled.'));
    }
}
