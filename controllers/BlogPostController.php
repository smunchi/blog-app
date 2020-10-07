<?php

namespace app\controllers;

use app\components\Uploader;
use app\components\Utils;
use app\models\BlogPostTags;
use app\models\BlogTags;
use Yii;
use app\models\BlogPost;
use app\models\BlogPostSearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * BlogPostController implements the CRUD actions for Post model.
 */
class BlogPostController extends Controller
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
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BlogPostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Post model.
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
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BlogPost();

        if ($model->load(Yii::$app->request->post())) {
            $requestData = Yii::$app->request->post();

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model = $this->uploadFiles($model, $requestData);
                $model->save();

                if (!empty($requestData['BlogPost']['tags'])) {
                    $tags = explode(',', $requestData['BlogPost']['tags']);

                    if(count($tags)) {
                        foreach ($tags as $tag) {
                            $this->saveTag($tag, $model->id);
                        }
                    }
                }
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }

            return $this->redirect(['view', 'id' => Utils::encrypt($model->id)]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionSearchTags($query = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $tags = BlogTags::find()
            ->select('blog_tags.title')
            ->where(['like', 'blog_tags.title', $_GET['query'] . '%', false])
            ->column();

        return array_map(function($tag) {
            return [
                'name' => $tag
            ];
        }, $tags);
    }

    public function actionUploadFile()
    {
        $uploadedFile = UploadedFile::getInstanceByName('file');
        echo Uploader::processBlogImage($uploadedFile, 'uploads/tinymce-files/', true);
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $id = Utils::decrypt($id);
        $model = $this->findModel($id);
        $existingTags = $this->getExistingTags($id);
        $model->tags = implode(',', $existingTags);

        if ($model->load(Yii::$app->request->post())) {
            $requestData = Yii::$app->request->post();

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $requestTags = explode(',',$requestData['BlogPost']['tags']);
                $removableTags = array_diff($existingTags, $requestTags);
                $addableTags = array_diff($requestTags, $existingTags);

                if(count($removableTags)) {
                    $tagIds = BlogTags::find()->select('blog_tags.id')->where(['in', 'title', $removableTags])->column();
                    if (count($tagIds)) {
                        BlogPostTags::deleteAll(['in', 'tag_id', $tagIds]);
                    }
                }

                if(count($addableTags)) {
                    foreach ($addableTags as $tag) {
                        $this->saveTag($tag, $model->id);
                    }
                }

                $model = $this->uploadFiles($model, $requestData);
                $model->save();
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }

            return $this->redirect(['view', 'id' => Utils::encrypt($model->id)]);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    private function uploadFiles($model, $request)
    {
        if ($imgSrc = UploadedFile::getInstance($model, 'upload_image')) {
            $model->featured_image = Uploader::processBlogImage($imgSrc, 'uploads/featured_images/', true);
        } else if(empty($request['BlogPost']['existing_upload_image'])) {
            $model->featured_image = null;
        }

        return $model;
    }

    private function saveTag($tag, $postId)
    {
        $tagData = BlogTags::find()->where(['title' => $tag])->one();

        if (isset($tagData->id)) {
            $tagId = $tagData->id;
        } else {
            $blogTag = new BlogTags();
            $blogTag->title = $tag;
            $blogTag->save();
            $tagId = $blogTag->id;
        }

        $postTag = new BlogPostTags();
        $postTag->post_id = $postId;
        $postTag->tag_id = $tagId;
        $postTag->save();
    }

    private function getExistingTags($id)
    {
        return BlogPostTags::find()
            ->select('blog_tags.title')
            ->leftJoin('blog_posts', 'blog_posts.id = blog_post_tags.post_id')
            ->innerJoin('blog_tags', 'blog_tags.id = blog_post_tags.tag_id')
            ->where(['post_id'=>$id])
            ->column();
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $id = Utils::decrypt($id);
        $transaction = Yii::$app->db->beginTransaction();
        $blogPost = $this->findModel($id);

        try {
            $blogPost->delete();
            if(!empty($blogPost->featured_image)) {
              Uploader::deleteCDN(basename($blogPost->featured_image));
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BlogPost the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlogPost::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
