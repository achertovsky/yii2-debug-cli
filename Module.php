<?php

namespace achertovsky\debug;

use Yii;
use yii\debug\Module as CoreModule;
use yii\helpers\ArrayHelper;

class Module extends CoreModule
{
    /**
     * Preffered error hub database component name
     */
    public $errorHubDb = 'db';

    /**
     * Controller mapping
     * @var array
     */
    public $controllerNamespace = 'achertovsky\debug\controllers';

    /**
     * @param array $dbProfileLogs
     * Allows to expand default classes that should be counted as db queries
     * In case of components override
     */
    public $dbProfileLogs = [];
    
    public $profilingPanelId = 'profiling';
    public $defaultPanel = 'profiling';
    public $historySize = 10000;
    public $dataPath = '@root/frontend/runtime/debug';
    
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        $logTarget = new $this->logTarget($this);
        $this->logTarget = Yii::$app->getLog()->targets['debug'] = $logTarget;
        Yii::setAlias('@ach-debug', '@vendor/achertovsky/yii2-debug-cli');

        /**
         * Parent bootstrap copy-pasted to avoid adding urlmanager rules in CLI
         */
        /* @var $app \yii\base\Application */
        $this->logTarget = $app->getLog()->targets['debug'] = new LogTarget($this);

        // delay attaching event handler to the view component after it is fully configured
        $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
            $app->getResponse()->on(Response::EVENT_AFTER_PREPARE, [$this, 'setDebugHeaders']);
        });
        $app->on(Application::EVENT_BEFORE_ACTION, function () use ($app) {
            $app->getView()->on(View::EVENT_END_BODY, [$this, 'renderToolbar']);
        });

        if (php_sapi_name() != 'cli') {
            $app->getUrlManager()->addRules([
                [
                    'class' => $this->urlRuleClass,
                    'route' => $this->id,
                    'pattern' => $this->id,
                    'suffix' => false
                ],
                [
                    'class' => $this->urlRuleClass,
                    'route' => $this->id . '/<controller>/<action>',
                    'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>',
                    'suffix' => false
                ]
            ], false);
        }
    }
    
    /**
     * Checks if current user is allowed to access the module
     * @return boolean if access is granted
     */
    protected function checkAccess($action = null)
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
            'db' => ['class' => 'achertovsky\debug\panels\DbPanel'],
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
