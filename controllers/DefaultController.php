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
    
    public function actionActionsSummary()
    {
        $result = [];
        $manifest = $this->getManifest();
        $panels = $this->module->panels;
        $panelId = $this->module->profilingPanelId;
        if (!isset($panels[$panelId])) {
            throw new \yii\web\NotFoundHttpException('No profiling panel found.');
        }
        $panel = $panels[$panelId];
        foreach ($manifest as $action) {
            $dataFile = $this->module->dataPath . "/{$action['tag']}.data";
            if (!file_exists($dataFile)) {
                continue;
            }
            $data = unserialize(file_get_contents($dataFile));
            if (is_array($data[$panelId])) {
                $panel->load($data[$panelId]);
            } else {
                $panel->load(unserialize($data[$panelId]));
            }
            if (!isset($result[$action['url']])) {
                $result[$action['url']]['totalCount'] = 0;
                $result[$action['url']]['totalTime'] = 0;
                $result[$action['url']]['totalMemory'] = 0;
            }
            $result[$action['url']] = [
                'totalTime' => $result[$action['url']]['totalTime']+$panel->data['time'],
                'totalMemory' => $result[$action['url']]['totalMemory']+$panel->data['time'],
                'totalCount' => ++$result[$action['url']]['totalCount'],
            ];
        }
        return $this->render(
            'actions-summary',
            [
                'results' => $result,
            ]
        );
    }
}
