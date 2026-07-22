<?php
/**
 * Lockout plugin for Craft CMS 5.x
 *
 * Temporarily lock certain users out of the control panel.
 *
 * @link      https://jalendport.com
 * @copyright Copyright (c) 2019 Jalen Davenport
 */

namespace jalendport\lockout\migrations;

use Craft;
use craft\db\Migration;
use jalendport\lockout\records\LocalSettings;

/**
 * Installation migration.
 *
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 1.0.0
 */
class Install extends Migration
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists(LocalSettings::tableName());

        return true;
    }

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 1.0.0
     */
    public function safeUp(): bool
    {
        if (Craft::$app->getDb()->tableExists(LocalSettings::tableName())) {
            return true;
        }

        $this->createTable(LocalSettings::tableName(), [
            'id' => $this->primaryKey(),
            'environment' => $this->string()->notNull()->defaultValue(''),
            'enabled' => $this->boolean()->notNull()->defaultValue(false),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        // One row per environment — the state is looked up by environment, and
        // duplicates would make which row wins a coin toss.
        $this->createIndex(null, LocalSettings::tableName(), ['environment'], true);

        return true;
    }
}
