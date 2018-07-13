<?php

namespace achertovsky\debug\panels;

use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * Contains overrides for correct displaying
 */
class DbPanel extends \yii\debug\panels\DbPanel
{
    /**
     * Returns all profile logs of the current request for this panel. It includes categories such as:
     * 'yii\db\Command::query', 'yii\db\Command::execute'.
     * @return array
     */
    public function getProfileLogs()
    {
        $target = $this->module->logTarget;
        return $target->filterMessages(
            $target->messages,
            Logger::LEVEL_PROFILE,
            ArrayHelper::merge(
                $this->module->dbProfileLogs,
                ['yii\db\Command::query', 'yii\db\Command::execute']
            )
        );
    }
}
