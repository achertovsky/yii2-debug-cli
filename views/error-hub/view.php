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

function tab($level)
{
    return "<span>".str_pad('', $level*4)."</span>";
}

function niceDisplay($data, $level = 0)
{
    if (is_string($data)) {
        return $data;
    }
    $result = '';
    end($data);
    $lastKey = key($data);
    foreach ($data as $key => $traceLine) {
        if (is_string($traceLine) || is_int($traceLine) || is_bool($traceLine)) {
            if ($traceLine == '') {
                $result .= '';
                continue;
            }
            $result .= tab($level == 1 ? 0 : $level).(is_int($key) ? '' : "$key: ").
                trim($traceLine).($key == $lastKey ? "" : "\n");
            continue;
        }
        $result .= tab($level++)."<b>$key:</b>\n";
        foreach ($traceLine as $key => $value) {
            $long = false;
            if (is_array($value) && !empty($value)) {
                $value = niceDisplay($value, $level+1);
                $long = true;
            } elseif (empty($value)) {
                $value = '';
            }
            $result .= trim(tab($level)."$key:".($long ? "\n" : " ")."$value")."\n";
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
