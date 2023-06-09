<?php
/**
 * Lockout plugin for Craft CMS 4.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\models;

use craft\base\Model;

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 */
class Settings extends Model
{

	public string $message = '';

	/**
	 * @return array
	 */
	public function rules(): array
    {
        return [
        	['message', 'string']
        ];
    }

}
