<?php
/**
 * Lockout plugin for Craft CMS 3.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\records;

use craft\db\ActiveRecord;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 *
 * @property  string $environment
 * @property  bool $enabled
 */
class LocalSettings extends ActiveRecord
{
	
	/**
	 * @return string
	 */
	public static function tableName(): string
	{
		return '{{%lockout}}';
	}
	
}
