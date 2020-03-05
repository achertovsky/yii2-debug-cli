<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel achertovsky\debug\models\ErrorHubSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Errors Hub';
$this->params['breadcrumbs'][] = $this->title;
achertovsky\debug\OverrideAsset::register($this);
?>
<div class="error-hub-index">
    <div class="container main-container">
        <div class="row">
            <h1><?= Html::encode($this->title) ?></h1>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'count',
                    'text:ntext',
                    'category',
                    [
                        'value' => 'updated_at',
                        'label' => 'Last case',
                        'format' => 'datetime'
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}'
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
