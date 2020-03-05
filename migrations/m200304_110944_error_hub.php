<?php

use yii\di\Instance;
use yii\db\Migration;
use yii\db\Connection;
use achertovsky\debug\Module;
use yii\base\InvalidConfigException;

/**
 * Class m200304_110944_error_hub
 */
class m200304_110944_error_hub extends Migration
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        if (!Yii::$app->hasModule('debug') || !(Yii::$app->getModule('debug') instanceof Module)) {
            throw new InvalidConfigException("No debud module launched or no module at all");
        }
        parent::init();
        $this->db = Instance::ensure(Yii::$app->getModule('debug')->errorHubDb, Connection::className());
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable(
            'error_hub',
            [
                'id' => $this->string()->notNull(),
                'text' => $this->text(),
                'trace' => $this->text(),
                'category' => $this->string(),
                'issue_id' => $this->string(),
                'count' => $this->integer(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
            ]
        );
        $this->addPrimaryKey("pd_id", '{{%error_hub}}', 'id');
        $this->createIndex('i_issue_id', '{{%error_hub}}', 'issue_id', true);
        $this->createIndex('i_count', '{{%error_hub}}', 'count');
    }

    public function down()
    {
        $this->dropIndex('i_issue_id', '{{%error_hub}}');
        $this->dropTable('error_hub');
    }
}
