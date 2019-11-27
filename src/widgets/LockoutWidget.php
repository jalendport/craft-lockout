<?php
/**
 * Lockout plugin for Craft CMS 3.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\widgets;

use jalendport\lockout\Lockout;
use jalendport\lockout\assetbundles\lockoutwidget\LockoutWidgetAsset;

use Craft;
use craft\base\Widget;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
     * @return string|bool
     */
    public static function icon()
    {
        return Craft::getAlias('@jalendport/lockout/assetbundles/lockoutwidget/dist/img/LockoutWidget-icon.svg');
    }
	
	
	/**
	 * @return false|string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 * @throws InvalidConfigException
	 */
	public function getBodyHtml()
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
