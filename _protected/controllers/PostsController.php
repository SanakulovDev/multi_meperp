<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\PostImages;
use app\models\Posts;
use app\models\PostsSearch;
use Yii;
use app\models\ReceptControl;
use app\models\ReceptControlSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * ReceptControlController implements the CRUD actions for ReceptControl model.
 */
class PostsController extends AppController
{
    /**
     * Lists all ReceptControl models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new ReceptControl model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      $model = new Posts();

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['view','id'=>$model->id]);
      }

      return $this->render('create', [
        'model' => $model,
      ]);
    }

    public function actionValidate($id = null) {
        $model = $id === null ? new ReceptControl() : ReceptControl::findOne($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    /**
     * Updates an existing ReceptControl model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
      $model = $this->findModel($id);

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['view','id'=>$id]);
      }

      return $this->render('update', [
        'model' => $model,
      ]);
    }

    /**
     * Deletes an existing ReceptControl model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!empty($model->getImages()))
        {
            foreach ($model->getImages() as $image)
            {
              if (file_exists(Yii::getAlias('@img').'/posts/'.$image->path))
              {
                unlink(Yii::getAlias('@img').'/posts/'.$image->path);
              }
              $image->delete();
            }
        }
        Yii::$app->session->setFlash('success',Yii::t('app','Success'));
        $model->delete();
        return $this->redirect('index');

    }


    /**
     * Displays a single Photos model.
     * @param integer $id
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
     * Finds the ReceptControl model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Posts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Posts::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionXls()
    {
        ini_set('memory_limit', '-1');
        $searchModel = new ReceptControlSearch();
        $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
        $xsl_file->send(Helpers::downloadFileName('receipt'));
    }



    public function actionUpload($id)
    {
      $model = $this->findModel($id);

      if (Yii::$app->request->post())
      {
        //var_dump($model->imagesCount());
        if (count($model->getImages()) >=3)
        {
          Yii::$app->session->setFlash('error',Yii::t('app','Images count in post must be 3'));
          return $this->redirect(['view','id'=>$id]);

        }
        //var_dump('sdscds');die();

        $model->image = UploadedFile::getInstance($model, 'image');

        if ($model->image != null && $model->upload())
        {
            $post_image = new PostImages();
            $post_image->path  = $model->image_name;
            $post_image->post_id = $model->id;
            $post_image->save();
            $this->redirect(['view','id'=>$id]);
        }
      }
      return $this->render('upload',['model'=>$model]);

    }

    public function actionDeleteImage($id)
    {
        $image = PostImages::findOne($id);
        $pos_id = $image->post_id;
        if ($image != null)
        {
            if (file_exists(Yii::getAlias('@img').'/posts/'.$image->path))
            {
                unlink(Yii::getAlias('@img').'/posts/'.$image->path);
            }
            $image->delete();
            return $this->redirect(['view','id'=>$pos_id]);
        }
        return $this->goBack();
    }

}
