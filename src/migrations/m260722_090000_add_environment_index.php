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

use craft\db\Migration;
use craft\db\Query;
use craft\helpers\Db;
use jalendport\lockout\records\LocalSettings;

/**
 * Adds the unique index on `environment`.
 *
 * Installs from 2.x and 3.0.0-beta.1 could accumulate more than one row per
 * environment, in which case which row decided the lockout state came down to
 * insertion order. This collapses the duplicates and stops new ones.
 *
 * @author Jalen Davenport <hello@jalendport.com>
 * @since 3.0.0
 */
class m260722_090000_add_environment_index extends Migration
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function safeDown(): bool
    {
        $index = Db::findIndex(LocalSettings::tableName(), ['environment'], true, $this->db);

        if ($index !== null) {
            $this->dropIndex($index, LocalSettings::tableName());
        }

        return true;
    }

    /**
     * @inheritdoc
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    public function safeUp(): bool
    {
        $this->_dropDuplicateRows();

        if (Db::findIndex(LocalSettings::tableName(), ['environment'], true, $this->db) === null) {
            $this->createIndex(null, LocalSettings::tableName(), ['environment'], true);
        }

        return true;
    }

    // Private Methods
    // =========================================================================

    /**
     * Keeps the most recently updated row per environment and deletes the rest,
     * so the unique index can be created.
     *
     * @author Jalen Davenport <hello@jalendport.com>
     * @since 3.0.0
     */
    private function _dropDuplicateRows(): void
    {
        $rows = (new Query())
            ->select(['id', 'environment'])
            ->from(LocalSettings::tableName())
            ->orderBy(['dateUpdated' => SORT_ASC, 'id' => SORT_ASC])
            ->all($this->db);

        $keepIds = [];
        $dropIds = [];

        foreach ($rows as $row) {
            $environment = (string)$row['environment'];

            if (isset($keepIds[$environment])) {
                $dropIds[] = $keepIds[$environment];
            }

            $keepIds[$environment] = $row['id'];
        }

        if ($dropIds !== []) {
            $this->delete(LocalSettings::tableName(), ['id' => $dropIds]);
        }
    }
}
