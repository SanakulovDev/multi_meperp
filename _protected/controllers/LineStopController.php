<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\LineStop;
use app\models\LineStopReason;
use app\models\LineStopSearch;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * LineStopController implements the CRUD actions for LineStop model.
 */
class LineStopController extends AppController
{
  /**
   * Lists all LineStop models.
   *
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new LineStopSearch();
    $currShift = Helpers::getPeriod();
    $queryString = Yii::$app->request->queryParams;
    $formName = $searchModel->formName();
    $form = Yii::$app->request->get($formName);
//    if(!isset($form["production_date"]) || empty($form["production_date"])) {
//      if (!isset($form["start_time"], $form["start_time"])) {
//        $queryString[$formName]["start_time"] = $currShift["start_at"];
//        $queryString[$formName]["end_time"] = $currShift["end_at"];
//      } else {
//        if (empty($form["start_time"]) && empty($form["end_time"])) {
//          $queryString[$formName]["start_time"] = $currShift["start_at"];
//          $queryString[$formName]["end_time"] = $currShift["end_at"];
//        }
//      }
//    }

    if (!isset($form["type"])) {
      $queryString[$formName]["type"] = LineStopReason::TYPE_NOTPLANNED;
    } else {
      if (is_null($form["type"])) {
        $queryString[$formName]["type"] = LineStopReason::TYPE_NOTPLANNED;
      }
    }
    $dataProvider = $searchModel->search($queryString);
    if (!in_array(Yii::$app->user->identity->rolename, ["admin", "superadmin", "counter"])) {
      $dataProvider->query->andWhere(["line_stop_reason.auth_item_name" => Yii::$app->user->identity->rolename]);
    }

    return $this->render("index", [
      "searchModel" => $searchModel,
      "dataProvider" => $dataProvider,
    ]);
  }

  /**
   * Displays a single LineStop model.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id)
  {
    if (Yii::$app->getRequest()->isAjax) {
      return $this->renderAjax("_view", ["model" => $this->findModel($id)]);
    }

    return $this->render("view", [
      "model" => $this->findModel($id),
    ]);
  }

  /**
   * Finds the LineStop model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return LineStop the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id, $relations = [])
  {
    $query = LineStop::find();
    if($relations) $query->with($relations);
    $query->where(["id" => $id]);
    if (($model = $query->one()) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t("app", "The requested page does not exist."));
  }

  /**
   * Creates a new LineStop model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate($id)
  {
    $model = new LineStop();
    $model->part_production_monitor_id = $id;
    $model->status = LineStop::STATUS_PENDING;
    if (Yii::$app->getRequest()->isAjax) {
      $postData = Yii::$app->request->post();
      if ($model->load($postData)) {
        if ($model->save()) {
          $data["status"] = 1;
        } else {
          $data["status"] = 0;
          $data["errors"] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $data;
      }

      return $this->renderAjax("_form", compact("model"));
    }

    return $this->redirect(["index"]);
  }

  /**
   * Updates an existing LineStop model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);
    if (Yii::$app->getRequest()->isAjax) {
      if ($model->load(Yii::$app->request->post())) {
        if ($model->save()) {
          $data["status"] = 1;
        } else {
          $data["status"] = 0;
          $data["errors"] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $data;
      } else {
        $model->start_time = date("Y-m-d H:i", strtotime($model->start_time));
        $model->end_time = date("Y-m-d H:i", strtotime($model->end_time));
        return $this->renderAjax("_form", compact("model"));
      }
    } else {
      return $this->redirect(["index"]);
    }
  }

  /**
   * Deletes an existing LineStop model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
    $model = LineStop::find()
      ->where(["id" => $id])
      ->one();
    if ($model && $model->delete()) {
      return [
        "status" => 1,
      ];
    }

    return [
      "status" => 0,
    ];
  }

  public function actionValidate($id = null)
  {
    $model = $id === null ? new LineStop() : LineStop::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;

      return ActiveForm::validate($model);
    }
  }

  public function actionAccept($id)
  {
    $model = $this->findModel($id, ['lineStopReason']);
    $post = Yii::$app->request->post($model->formName());
    if ($post) {
      $model->accept();
      $model->fix_list = isset($post['fix_list']) ? implode(PHP_EOL, $post['fix_list']) : null;
      $model->save(false);
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ['status' => 1];
    }

    if(!$model->lineStopReason->fix_list || empty($model->lineStopReason->fix_list)) {
      $model->accept();
      $model->save(false);
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ['status' => 1];
    }

    $reasons = [];
    foreach (explode(PHP_EOL, $model->lineStopReason->fix_list) as $item) {
      $reasons[$item] = $item;
    }


    return $this->renderAjax("_accept", compact("model", "reasons"));
  }

  public function actionReject($id)
  {
    $model = $this->findModel($id, ['lineStopReason']);
    if (!Yii::$app->user->can($model->lineStopReason->auth_item_name)) {
      throw new ForbiddenHttpException(Yii::t("yii", "You are not allowed to perform this action."));
    }
    $model->reject();
    $model->save(false);

    return $this->redirect(["index"]);
  }
}
