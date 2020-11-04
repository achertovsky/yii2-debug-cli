<?php

use yii\di\Instance;
use yii\db\Migration;
use achertovsky\debug\Module;
use common\overrides\db\Connection;
use yii\base\InvalidConfigException;

/**
 * Class m201104_120452_error_hub_index
 */
class m201104_120452_error_hub_index extends Migration
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
        $this->db = Instance::ensure(Yii::$app->getModule('debug')->errorHubDb, Connection::class);
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->createIndex('i_trace', '{{%error_hub}}', 'trace(255)');
        } catch (\Exception $ex) {
            echo "Index may already exist\n";
        }
        try {
            $this->createIndex('i_created_at', '{{%error_hub}}', 'created_at');
        } catch (\Exception $ex) {
            echo "Index may already exist\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201104_120452_error_hub_index cannot be reverted.\n";
    }
}
