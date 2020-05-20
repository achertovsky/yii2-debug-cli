<?php

use yii\helpers\Url;
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
            <div class='col-md-12'>
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
            <div class='clearfix'></div>
            <div class='col-md-4'>
                Bulk delete options
            </div>
            <div class='col-md-4'>
                <?=Html::button('Older than X days', ['id' => 'olderThanBth'])?>
            </div>
            <div class='col-md-4'>
                <?=Html::button('Error text contains ...', ['id' => 'textContainsBth'])?>
            </div>
            <div class='clearfix'></div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}'
                    ],
                    'count',
                    [
                        'value' => 'updated_at',
                        'label' => 'Last case',
                        'format' => 'datetime'
                    ],
                    [
                        'attribute' => 'text',
                        'value' => function ($model) {
                            try {
                                $result = unserialize($model->text);
                                if (is_string($result)) {
                                    return $result;
                                }
                                return $model->text;
                            } catch (\Exception $ex) {
                                return $model->text;
                            }
                        }
                    ],
                    
                ],
            ]); ?>
        </div>
    </div>
</div>
<script>
    var olderThanUrl = '<?=Url::toRoute('days')?>';
    var textContainsUrl = '<?=Url::toRoute('text')?>';
</script>
