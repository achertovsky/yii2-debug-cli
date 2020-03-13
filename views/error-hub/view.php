<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model achertovsky\debug\models\ErrorHub */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Error Hubs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

function niceDisplay($data)
{
    if (is_string($data)) {
        return $data;
    }
    $result = '';
    foreach ($data as $key => $traceLine) {
        if (is_string($traceLine)) {
            $result .= "$traceLine\n";
            continue;
        }
        $result .= "<b>$key:</b>\n";
        foreach ($traceLine as $key => $value) {
            if (is_array($value)) {
                $value = 'array('.implode(', ', $value).')';
            }
            $result .= "<span>    </span>$key: $value\n";
        }
    }
    return $result;
}
?>
<div class="error-hub-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Close', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure its closed?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Close this and similar ones', ['delete-by-trace', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure all similar issues closed?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'count',
            [
                'attribute' => 'text',
                'value' => function ($model) {
                    try {
                        $data = unserialize($model->text);
                        return niceDisplay($data);
                    } catch (\Exception $ex) {
                        return $model->text;
                    }
                }
            ],
            [
                'value' => $model->created_at,
                'label' => 'First case',
                'format' => 'datetime'
            ],
            [
                'value' => $model->updated_at,
                'label' => 'Last case',
                'format' => 'datetime'
            ],
            'category',
            [
                'attribute' => 'trace',
                'value' => function ($model) {
                    try {
                        $data = unserialize($model->trace);
                        if (empty($data)) {
                            return '';
                        }
                        return niceDisplay($data);
                    } catch (\Exception $ex) {
                        return "Error: Wrong format of trace\n";
                    }
                },
                'format' => 'raw'
            ]
        ],
    ]) ?>

</div>
