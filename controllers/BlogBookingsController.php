<?php

namespace app\controllers;

use app\components\Uploader;
use app\components\Utils;
use Yii;
use app\models\BlogBookings;
use app\models\BlogBookingsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * BlogBookingsController implements the CRUD actions for BlogBookings model.
 */
class BlogBookingsController extends Controller
{
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
                ],
            ],
        ];
    }

    /**
     * Lists all BlogBookings models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BlogBookingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BlogBookings model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $id = Utils::decrypt($id);

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BlogBookings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BlogBookings();

        if ($model->load(Yii::$app->request->post())) {
            $requestData = Yii::$app->request->post();
            $model = $this->uploadFiles($model, $requestData);
            $model->save();

            return $this->redirect(['view', 'id' => Utils::encrypt($model->id)]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BlogBookings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $id = Utils::decrypt($id);
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $requestData = Yii::$app->request->post();
            $model = $this->uploadFiles($model, $requestData);
            $model->save();

            return $this->redirect(['view', 'id' => Utils::encrypt($model->id)]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BlogBookings model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $id = Utils::decrypt($id);

        $transaction = Yii::$app->db->beginTransaction();
        $blogBooking = $this->findModel($id);

        try {
            $blogBooking->delete();

            if(!empty($blogBooking->img_src)) {
                Uploader::deleteCDN(basename($blogBooking->img_src));
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }


        return $this->redirect(['index']);
    }

    /**
     * Finds the BlogBookings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BlogBookings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlogBookings::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    private function uploadFiles($model, $request)
    {
        if ($imgSrc = UploadedFile::getInstance($model, 'upload_image')) {
            $model->img_src = Uploader::processBlogImage($imgSrc, 'uploads/bookings_images/', true);
        } else if(empty($request['BlogBookings']['existing_upload_image'])) {
            $model->img_src = null;
        }

        return $model;
    }
}
