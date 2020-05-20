<?php

namespace achertovsky\debug\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use achertovsky\debug\Module;
use yii\web\NotFoundHttpException;
use yii\base\InvalidConfigException;
use achertovsky\debug\models\ErrorHub;
use achertovsky\debug\models\ErrorHubSearch;

/**
 * ErrorHubController implements the CRUD actions for ErrorHub model.
 */
class ErrorHubController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $layout = 'main';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-by-trace' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ErrorHub models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ErrorHubSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ErrorHub model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing ErrorHub model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing ErrorHub models group, defined by ErrorHub::trace.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteByTrace($id)
    {
        $model = $this->findModel($id);

        ErrorHub::deleteAll(
            [
                'trace' => $model->trace,
            ]
        );

        return $this->redirect(['index']);
    }

    /**
     * Finds the ErrorHub model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ErrorHub the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ErrorHub::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Remove depends on days
     *
     * @param integer $days
     * @return void
     */
    public function actionDays($days)
    {
        ErrorHub::deleteAll(
            [
                'and',
                ['<', 'created_at', time()-$days*24*3600]
            ]
        );
    }

    /**
     * Remove depends on days
     *
     * @param integer $days
     * @return void
     */
    public function actionText($text)
    {
        ErrorHub::deleteAll(
            [
                'and',
                ['like', 'text', $text]
            ]
        );
    }
}
