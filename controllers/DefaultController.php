<?php

namespace achertovsky\debug\controllers;

/**
 * Fixing issues
 * Extending functionality
 * 
 * @inheritdoc
 *
 * @author Alexander Chertovsky
 */
class DefaultController extends \yii\debug\controllers\DefaultController
{
    /**
     * @inheritdoc
     */
    public function loadData($tag, $maxRetry = 0)
    {
        // retry loading debug data because the debug data is logged in shutdown function
        // which may be delayed in some environment if xdebug is enabled.
        // See: https://github.com/yiisoft/yii2/issues/1504
        for ($retry = 0; $retry <= $maxRetry; ++$retry) {
            $manifest = $this->getManifest($retry > 0);
            if (isset($manifest[$tag])) {
                $dataFile = $this->module->dataPath . "/$tag.data";
                $data = unserialize(file_get_contents($dataFile));
                $exceptions = isset($data['exceptions']) ? $data['exceptions'] : [];
                foreach ($this->module->panels as $id => $panel) {
                    if (isset($data[$id])) {
                        $panel->tag = $tag;
                        if (is_array($data[$id])) {
                            $panel->load($data[$id]);
                        } else {
                            $panel->load(unserialize($data[$id]));
                        }
                    }
                    if (isset($exceptions[$id])) {
                        $panel->setError($exceptions[$id]);
                    }
                }
                $this->summary = $data['summary'];

                return;
            }
            sleep(1);
        }

        throw new NotFoundHttpException("Unable to find debug data tagged with '$tag'.");
    }
}
