<?php

namespace achertovsky\debug;

use Yii;
use yii\debug\Module as CoreModule;
use yii\helpers\ArrayHelper;

class Module extends CoreModule
{
    /**
     * Controller mapping
     * @var array
     */
    public $controllerMap = [
        'default' => 'achertovsky\debug\controllers\DefaultController',
    ];
    
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
    
    /**
     * @return array default set of panels
     */
    protected function corePanels()
    {
        $panels = [
            'config' => ['class' => 'yii\debug\panels\ConfigPanel'],
            'request' => ['class' => 'yii\debug\panels\RequestPanel'],
            'log' => ['class' => 'yii\debug\panels\LogPanel'],
            'profiling' => ['class' => 'yii\debug\panels\ProfilingPanel'],
            'db' => ['class' => 'yii\debug\panels\DbPanel'],
            'mail' => ['class' => 'yii\debug\panels\MailPanel'],
            'timeline' => ['class' => 'yii\debug\panels\TimelinePanel'],
        ];

        if (php_sapi_name() !== 'cli') {
            $components = Yii::$app->getComponents();
            if (isset($components['user']['identityClass'])) {
                $panels['user'] = ['class' => 'yii\debug\panels\UserPanel'];
            }
            $panels['router'] = ['class' => 'yii\debug\panels\RouterPanel'];
            $panels['assets'] = ['class' => 'yii\debug\panels\AssetPanel'];
        }

        return $panels;
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        /*
         * avoid execution of debug Module init
         * thats why copy-paste code from core module
         */
        if ($this->controllerNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->controllerNamespace = substr($class, 0, $pos) . '\\controllers';
            }
        }
        $this->dataPath = Yii::getAlias($this->dataPath);
        $this->initPanels();
    }
}
