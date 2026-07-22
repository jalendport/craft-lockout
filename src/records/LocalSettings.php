<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\records;

use craft\db\ActiveRecord;

/**
 * One row per environment, holding that environment's lockout flag.
 *
 * The flag lives in the database rather than project config on purpose: it
 * survives a `project-config/apply` mid-deploy, which is the whole reason to
 * reach for Lockout over `craft off`.
 *
 * @property string $environment
 * @property bool $enabled
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 1.0.0
 */
class LocalSettings extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public static function tableName(): string
    {
        return '{{%lockout}}';
    }
}
