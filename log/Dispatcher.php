<?php

namespace achertovsky\debug\log;

use achertovsky\debug\handlers\ErrorHubIssueHandler;
use yii\log\Dispatcher as LogDispatcher;
use yii\log\Logger;

/**
 * Created for gathering data of new /debug/errors page
 */
class Dispatcher extends LogDispatcher
{
    /**
     * Gatherer of errors only
     *
     * @inheritDoc
     */
    public function dispatch($messages, $final)
    {
        foreach ($messages as $message) {
            if ($message[1] == Logger::LEVEL_ERROR) {
                ErrorHubIssueHandler::collect($message);
            }
        }
        return parent::dispatch($messages, $final);
    }
}
