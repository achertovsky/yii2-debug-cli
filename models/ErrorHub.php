<?php

namespace achertovsky\debug\models;

use Yii;
use achertovsky\debug\Module;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "error_hub".
 *
 * @property string $id
 * @property string|null $text
 * @property string|null $trace
 * @property string|null $category
 * @property string|null $issue_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $count
 */
class ErrorHub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'error_hub';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get(Yii::$app->getModule('debug')->errorHubDb);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'default', 'value' => uniqid()],
            [['id'], 'required'],
            [['text', 'trace'], 'string'],
            [['created_at', 'updated_at', 'count'], 'integer'],
            [['id', 'category', 'issue_id'], 'string', 'max' => 255],
            [['issue_id'], 'unique'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
            'trace' => 'Trace',
            'category' => 'Category',
            'issue_id' => 'Issue ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'count' => 'Count',
        ];
    }
}
