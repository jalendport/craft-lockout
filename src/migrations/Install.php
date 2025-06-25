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

/**
 * @author    Jalen Davenport
 * @package   Lockout
 * @since     1.0.0
 */
class Install extends Migration
{

    /**
     * @return bool
     */
    public function safeUp(): bool
    {
        if ($this->createTables()) {
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        return true;
    }


    /**
     * @return bool
     */
    public function safeDown(): bool
    {
        $this->removeTables();

        return true;
    }


    /**
     * @return bool
     */
    protected function createTables(): bool
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%lockout}}');
        if ($tableSchema === null) {
            $tablesCreated = true;

            $this->createTable(
                '{{%lockout}}',
                [
                  	'id' => $this->primaryKey(),
					'environment' => $this->string()->notNull()->defaultValue(''),
                    'enabled' => $this->boolean()->notNull()->defaultValue(false),
					'dateCreated' => $this->dateTime()->notNull(),
					'dateUpdated' => $this->dateTime()->notNull(),
					'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;
    }


    /**
     * @return void
     */
    protected function removeTables(): void
	{
        $this->dropTableIfExists('{{%lockout}}');
    }
}
