<?php

use \yii\helpers\Html;

/* @var $panel yii\debug\panels\RouterPanel */

?>
<div class="yii-debug-toolbar__block">
    <a href="<?= $panel->getUrl() ?>" title="Action: <?= Html::encode(isset($panel->data['action']) ? $panel->data['action'] : '')  ?>">Route <span
            class="yii-debug-toolbar__label"><?= Html::encode(isset($panel->data['route']) ? $panel->data['route'] : '') ?></span></a>
</div>
