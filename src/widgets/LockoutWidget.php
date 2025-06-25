<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\widgets;

use Craft;
use craft\base\Widget;
use jalendport\lockout\Lockout;
use jalendport\lockout\assetbundles\lockoutwidget\LockoutWidgetAsset;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 *
 * @property mixed $bodyHtml
 */
class LockoutWidget extends Widget
{

	/**
	 * @return string
	 */
	public static function displayName(): string
    {
        return 'Lockout';
    }


	/**
	 * @return bool
	 */
	public static function isSelectable(): bool
	{
		$userIsAdmin = Craft::$app->user->getIsAdmin();
		$userAlreadyHasWidget = Craft::$app->getDashboard()->doesUserHaveWidget(static::class);

		return ($userIsAdmin && !$userAlreadyHasWidget);
	}


	/**
	 * @return bool
	 */
	protected static function allowMultipleInstances(): bool
	{
		return false;
	}


	/**
	 * @return string|null
	 */
    public static function icon(): ?string
    {
        return Craft::getAlias('@jalendport/lockout/assetbundles/lockoutwidget/dist/img/LockoutWidget-icon.svg');
    }


	/**
	 * @return string|null
	 * @throws InvalidConfigException
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 * @throws Exception
	 */
	public function getBodyHtml(): ?string
    {
        Craft::$app->getView()->registerAssetBundle(LockoutWidgetAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'lockout/widgets/lockout',
            [
				'localSettings' => Lockout::$plugin->getLocalSettings(),
            ]
        );
    }

}
