<?php

namespace app\controllers;

use app\components\Uploader;
use app\components\Utils;
use app\models\BlogAttraction;
use app\models\BlogCountry;
use Yii;
use app\models\BlogCity;
use app\models\BlogCitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * BlogCityController implements the CRUD actions for BlogCity model.
 */
class BlogCityController extends Controller
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
     * Lists all BlogCity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BlogCitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BlogCity model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $id = Utils::decrypt($id);
        $attractions = BlogAttraction::find()->where(['city_id'=>$id])->all();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'attractions'=>$attractions
        ]);
    }

    /**
     * Creates a new BlogCity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BlogCity();

        if ($model->load(Yii::$app->request->post())) {
            $model = $this->uploadFiles($model, Yii::$app->request->post());
            $model->save();

            $requestData = Yii::$app->request->post();
            $attractionsData = $requestData['attraction'];

            $name = array_filter($attractionsData['name']);
            if(count(array_unique($name)) != count($name)) {
                Yii::$app->session->setFlash('error', 'Failed. Duplicate top attraction name.');

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $countryId = $requestData['BlogCity']['country_id'];
            $country = BlogCountry::find()->select(['id'])->where(['id'=>$countryId])->one();

            if(isset($attractionsData) && count($attractionsData)) {
                foreach ($attractionsData['name'] as $key=>$value) {
                    $attraction = new BlogAttraction();
                    $attraction->lat = (string)$attractionsData['lat'][$key];
                    $attraction->long = (string)$attractionsData['lng'][$key];
                    if(!empty($_FILES['attraction']['name']['fileinputs'][$key])) {
                        $attraction->img_src = $this->processCityImage($_FILES['attraction']['name']['fileinputs'][$key], $_FILES["attraction"]["tmp_name"]['fileinputs'][$key]);
                    } else {
                        $attraction->img_src = null;
                    }
                    $attraction->name = $value;
                    $attraction->country_id = $country->id;
                    $attraction->city_id = $model->id;

                    $attraction->save();
                }
            }

            return $this->redirect(['view', 'id' => Utils::encrypt($model->id)]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BlogCity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $id = Utils::decrypt($id);
        $model = $this->findModel($id);
        $mapData = BlogAttraction::find()->where(['city_id'=>$id])->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            $existingAttractions = BlogAttraction::find()->select('name')->where(['city_id' => $id])->asArray()->column();
            $requestAttractions = Yii::$app->request->post('attraction');
            $name = array_filter($requestAttractions['name']);

            if(count(array_unique($name)) != count($name)) {
                Yii::$app->session->setFlash('error', 'Failed. Duplicate top attraction name');

                return $this->render('update', [
                    'model' => $model,
                    'mapData' => $mapData
                ]);
            }

            // remove attractions
            $removableAttractions = array_diff($existingAttractions, $name);
            if(count($removableAttractions)) {
                foreach ($removableAttractions as $key=>$attraction)
                {
                    BlogAttraction::deleteAll(['name'=> $attraction, 'city_id' => $id]);
                }
            }

            $requestData = Yii::$app->request->post();
            $countryId = $requestData['BlogCity']['country_id'];

            $country = BlogCountry::find()->select(['id'])->where(['id'=>$countryId])->one();

            //add attraction via add more
            $addableAttractions = array_diff($name, $existingAttractions);


            if(count($addableAttractions)) {
                foreach ($addableAttractions as $key=>$value) {
                    $attraction = new BlogAttraction();
                    $attraction->lat = (string)$requestAttractions['lat'][$key];
                    $attraction->long = (string)$requestAttractions['lng'][$key];
                    $attraction->name = $value;
                    if(!empty($_FILES['attraction']['name']['fileinputs'][$key])) {
                        $attraction->img_src = $this->processCityImage($_FILES['attraction']['name']['fileinputs'][$key], $_FILES["attraction"]["tmp_name"]['fileinputs'][$key]);
                    } else {
                        $attraction->img_src = null;
                    }
                    $attraction->country_id = $country->id;
                    $attraction->city_id = $model->id;

                    $attraction->save();
                }
            }

            //update existing image if user change it
            $updateImage = array_diff($name, $addableAttractions);
            if(count($updateImage)) {
                foreach ($updateImage as $key=>$item) {
                    $existingImage = BlogAttraction::find()->select('img_src')->where(['city_id' => $id, 'name' => $item])->asArray()->column();
                    if(isset($existingImage)) {
                        if (!empty($_FILES['attraction']['name']['fileinputs'][$key]) && (array_values($existingImage) != $_FILES['attraction']['name']['fileinputs'][$key])) {
                            $imagePath = $this->processCityImage($_FILES['attraction']['name']['fileinputs'][$key], $_FILES["attraction"]["tmp_name"]['fileinputs'][$key]);
                            BlogAttraction::updateAll(['img_src' => $imagePath], ['name' => $item, 'city_id' => $id]);
                        }
                    }
                }
            }

            // update country images
            $model = $this->uploadFiles($model, Yii::$app->request->post());
            $model->save();

            return $this->redirect(['view', 'id' => Utils::encrypt($model->id)]);
        }

        return $this->render('update', [
            'model' => $model,
            'mapData' => $mapData
        ]);
    }

    public static function processCityImage($orgFile, $tempFile)
    {
        $filename = strtotime(date('Y-m-d H:i:s', time())) . '.' .pathinfo($orgFile, PATHINFO_EXTENSION);
        $imageUploadPath = 'uploads/attraction_images/';
        Utils::checkDir($imageUploadPath);
        move_uploaded_file($tempFile, $imageUploadPath . $filename);

        return Uploader::uploadCDN($filename, $imageUploadPath, '');
    }

    /**
     * Deletes an existing BlogCity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $id = Utils::decrypt($id);
        $transaction = Yii::$app->db->beginTransaction();
        $blogCity = $this->findModel($id);

        try {
            $blogCity->delete();

            if(!empty($blogCity->img_src)) {
                Uploader::deleteCDN(basename($blogCity->img_src));
            }
            $blogAttractionImages = BlogAttraction::find()->select(['img_src'])->where(['city_id' => $id])->column();

            if(count($blogAttractionImages)) {
                foreach ($blogAttractionImages as $blogAttractionImage) {
                    Uploader::deleteCDN(basename($blogAttractionImage));
                }
            }

            BlogAttraction::deleteAll(['city_id' => $id]);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the BlogCity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BlogCity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlogCity::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionUploadFile()
    {
        $uploadedFile = UploadedFile::getInstanceByName('file');
        echo Uploader::processBlogImage($uploadedFile, 'uploads/tinymce-files/', true);
    }

    private function uploadFiles($model, $request)
    {
        if ($imgSrc = UploadedFile::getInstance($model, 'upload_image')) {
            $model->img_src = Uploader::processBlogImage($imgSrc, 'uploads/city_images/', true);
        } else if(empty($request['BlogCity']['existing_upload_image'])) {
            $model->img_src = null;
        }

        return $model;
    }
}
