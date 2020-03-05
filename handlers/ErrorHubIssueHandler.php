<?php

namespace achertovsky\debug\handlers;

use Yii;
use yii\helpers\Json;
use yii\base\BaseObject;
use achertovsky\debug\Module;
use yii\base\InvalidConfigException;
use achertovsky\debug\models\ErrorHub;

/**
 * Contains rules to communicate with the ErrorHubModel
 */
class ErrorHubIssueHandler extends BaseObject
{
    /**
     * Formats message and writes into model
     *
     * @param array $message
     * Format:
     * [
     *     0 => *string, message*,
     *     1 => *integer, log level*
     *     2 => *string, log category*
     *     3 => *numeric, timestamp*
     *     4 => *array, trace*
     *     5 => *integer, memory usage*
     * ]
     * @return void
     */
    public static function collect($message)
    {
        if (!Yii::$app->hasModule('debug') || !(Yii::$app->getModule('debug') instanceof Module)) {
            throw new InvalidConfigException("No debud module launched or no module at all");
        }
        if (isset(Yii::$app->params['error_hub_issue'])) {
            return;
        }
        try {
            $message[0] = Json::encode($message[0]);
            $message[4] = Json::encode($message[4]);
            $issueId = md5($message[0].$message[4].$message[2]);
            $error = ErrorHub::findOne(['issue_id' => $issueId]);
            if (is_null($error)) {
                $error = new ErrorHub(
                    [
                        'text' => $message[0],
                        'trace' => $message[4],
                        'category' => $message[2],
                        'issue_id' => $issueId,
                        'count' => 1,
                        'created_at' => (int)$message[3],
                        'updated_at' => (int)$message[3],
                    ]
                );
            } else {
                $error->count++;
                $error->updated_at = (int)$message[3];
            }
            $error->save();
        } catch (\Exception $ex) {
            Yii::$app->params['error_hub_issue'] = 1;
            Yii::error($ex);
        }
    }
}
