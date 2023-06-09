<?php
/**
 * Lockout plugin for Craft CMS 4.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\assetbundles\lockoutwidget;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 */
class LockoutWidgetAsset extends AssetBundle
{

	public function init(): void
	{
        $this->sourcePath = '@jalendport/lockout/assetbundles/lockoutwidget/dist';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/LockoutWidget.js',
        ];

        $this->css = [
            'css/LockoutWidget.css',
        ];

        parent::init();
    }

}
