<?php

namespace achertovsky\debug;

use Yii;
use yii\debug\LogTarget as CoreLogTarget;
use yii\debug\panels\RequestPanel;
use yii\helpers\FileHelper;
use yii\debug\panels\AssetPanel;
use yii\base\InvalidConfigException;
use yii\debug\panels\UserPanel;

class LogTarget extends CoreLogTarget
{
    /**
     * @inheritdoc
     */
    protected function collectSummary()
    {
        if (Yii::$app === null) {
            return '';
        }

        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        if (php_sapi_name() == 'cli') {
            $params = $request->params;
        }
        $summary = [
            'tag' => $this->tag,
            'url' => php_sapi_name() != 'cli' ? $request->getAbsoluteUrl() : implode(' ', $params),
            'ajax' => php_sapi_name() != 'cli' ? (int) $request->getIsAjax() : 0,
            'method' => php_sapi_name() != 'cli' ? $request->getMethod() : "CLI",
            'ip' => php_sapi_name() != 'cli' ? $request->getUserIP() : '127.0.0.1',
            'time' => time(),
            'statusCode' => php_sapi_name() != 'cli' ? $response->statusCode : 0,
            'sqlCount' => $this->getSqlTotalCount(),
        ];

        if (isset($this->module->panels['mail'])) {
            $summary['mailCount'] = count($this->module->panels['mail']->getMessagesFileName());
        }

        return $summary;
    }
    
    /**
     * @inheritdoc
     */
    public function export()
    {
        $path = $this->module->dataPath;
        FileHelper::createDirectory($path, $this->module->dirMode);

        $summary = $this->collectSummary();
        $dataFile = "$path/{$this->tag}.data";
        $data = [];
        $frontend = php_sapi_name() != 'cli';
        foreach ($this->module->panels as $id => $panel) {
            if ($frontend || (
                $panel::className() != RequestPanel::className() &&
                $panel::className() != AssetPanel::className() &&
                $panel::className() != UserPanel::className()
            )) {
                $data[$id] = $panel->save();
            }
        }
        $data['summary'] = $summary;
        file_put_contents($dataFile, serialize($data));
        if ($this->module->fileMode !== null) {
            @chmod($dataFile, $this->module->fileMode);
        }

        $indexFile = "$path/index.data";
        $this->updateIndexFile($indexFile, $summary);
    }
    
    /**
     * Updates index file with summary log data
     *
     * @param string $indexFile path to index file
     * @param array $summary summary log data
     * @throws \yii\base\InvalidConfigException
     */
    protected function updateIndexFile($indexFile, $summary)
    {
        touch($indexFile);
        if (($fp = @fopen($indexFile, 'r+')) === false) {
            throw new InvalidConfigException("Unable to open debug data index file: $indexFile");
        }
        @flock($fp, LOCK_EX);
        $manifest = '';
        while (($buffer = fgets($fp)) !== false) {
            $manifest .= $buffer;
        }
        if (!feof($fp) || empty($manifest)) {
            // error while reading index data, ignore and create new
            $manifest = [];
        } else {
            $manifest = unserialize($manifest);
        }

        $manifest[$this->tag] = $summary;
        $this->gc($manifest);

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, serialize($manifest));

        @flock($fp, LOCK_UN);
        @fclose($fp);

        if ($this->module->fileMode !== null) {
            @chmod($indexFile, $this->module->fileMode);
        }
    }
    
    /**
     * Added support of except
     * @inheritdoc
     */
    public function collect($messages, $final)
    {
        if (!empty($this->except)) {
            foreach ($messages as $key => $message) {
                foreach ($this->except as $except) {
                    if ($except !== '*') {
                        $prefix = rtrim($except, '*');
                    }
                    if (($message[2] == $except) || (strpos($message[2], $prefix) !== false)) {
                        unset($messages[$key]);
                    }
                }
            }
        }
        if (!empty($messages)) {
            $this->messages = array_merge($this->messages, $messages);
        }
        if ($final) {
            $this->export();
        }
    }
}
