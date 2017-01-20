<?php

namespace achertovsky\yii2-debug-cli;

use Yii;
use yii\debug\Module as CoreModule;

class Module extends CoreModule
{
    public $defaultPanel = 'profiling';
    public $historySize = 10000;
    public $dataPath = '@root/frontend/runtime/debug';
    
    public function bootstrap($app)
    {
        $logTarget = new $this->logTarget($this);
        parent::bootstrap($app);
        $this->logTarget = Yii::$app->getLog()->targets['debug'] = $logTarget;
    }
    
    /**
     * Checks if current user is allowed to access the module
     * @return boolean if access is granted
     */
    protected function checkAccess()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $ip = Yii::$app->getRequest()->getUserIP();
        foreach ($this->allowedIPs as $filter) {
            if ($filter === '*' ||
            $filter === $ip ||
            (($pos = strpos($filter, '*')) !== false &&
            !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        foreach ($this->allowedHosts as $hostname) {
            $filter = gethostbyname($hostname);
            if ($filter === $ip) {
                return true;
            }
        }
        return false;
    }
}
