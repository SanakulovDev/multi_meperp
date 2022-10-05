<?php
namespace app\controllers;

use app\models\Machine;
use app\models\Mold;
use app\models\MoldMachine;
use app\models\MoldPart;
use app\models\MoldSearch;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\db\Query;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * MoldController implements the CRUD actions for Mold model.
 */
class MoldController extends AppController {

  /**
   * Lists all Mold models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new MoldSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Creates a new Mold model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate() {
    $model = new Mold();
    $query = new Query();
    $t = 0;
    if($model->load(Yii::$app->request->post())) {
      foreach($_POST['items'] as $value) {
        $arr[$t] = $value['machine'];
        $t++;
      }
      $find_id = ($query)->select(['id'])->from('part')->where(["concat(part_no,' ',part_color)" => $arr])->all();
      $transaction = Yii::$app->db->beginTransaction();
      $model->created_by = Yii::$app->user->id;
      $model->created_at = time();
      if($model->save()) {
        $l = 0;
        foreach($_POST['items'] as $value) {
          $mold_part = new MoldPart();
          $mold_part->created_by = Yii::$app->user->id;
          $mold_part->created_at = time();
          if(!isset($value['machine']) || !isset($value['quantity'])) {
            $errorlist['Header'] = Yii::t('app', 'You must fill empty fields.');
            return $this->renderAjax('_form', array_merge([
              'errorlist' => $errorlist ?? null,
              'model' => $model,
            ]));
          } else {
            $mold_part->mold_id = $model->id;
            $mold_part->part_id = $find_id[$l]["id"];
            $mold_part->quantity = $value['quantity'];
            $l++;
            if($mold_part->save()) {
              //
            } else {
              print_r($mold_part->errors);
            }
          }
        }
        $transaction->commit();
        return $this->redirect(['index']);
      } else {
        $transaction->rollback();
      }
    }
    return $this->renderAjax('_form', array_merge([
      'model' => $model,
    ]));
  }

  /**
   * Updates an existing Mold model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
    $moldpart = MoldPart::find()->where(['mold_id' => $id])->all();
    $query = new Query;
    $arr = [];
    $arrr = [];
    $tt = 0;
    $ll = 0;
    $t = 0;
    foreach($moldpart as $value) {
      $arrr[$tt] = $value['part_id'];
      $tt++;
    }
    $find_code = ($query)->select(["concat(part_no,' ',part_color) as code"])->from('part')->where(["id" => $arrr])->all();
    foreach($moldpart as $value) {
      $value->part_id = $find_code[$ll]["code"];
      $ll++;
    }
    if($model->load(Yii::$app->request->post())) {
      foreach($_POST['items'] as $value) {
        $arr[$t] = $value['machine'];
        $t++;
      }
      $find_id = ($query)->select(['id'])->from('part')->where(["concat(part_no,' ',part_color)" => $arr])->all();
      $transaction = Yii::$app->db->beginTransaction();
      $query = new Query;
      if($model->save()) {
        $l = 0;
        MoldPart::deleteAll(['mold_id' => $id]);
        foreach($_POST['items'] as $value) {
          $mold_part = new MoldPart();
          $mold_part->created_by = Yii::$app->user->id;
          $mold_part->created_at = time();
          if(!isset($value['machine'])) {
            $errorlist['Header'] = Yii::t('app', 'You must fill empty field.');
            return $this->render('create', [
              'errorlist' => $errorlist ?? null,
              'model' => $model, 'moldpart' => $moldpart
            ]);
          } else {
            $mold_part->mold_id = $model->id;
            $mold_part->part_id = $find_id[$l]["id"];
            $mold_part->quantity = $value['quantity'];
            $l++;
            if($mold_part->save()) {
              //
            } else {
              print_r($mold_part->errors);
            }
          }
        }
        $transaction->commit();
        return $this->redirect(['index']);
      } else {
        $transaction->rollback();
      }
    }
    return $this->renderAjax('_form', array_merge([
      'model' => $model,
      'moldpart' => $moldpart
    ]));
    // return $this->render('update', [
    //     'model' => $model,
    // ]);
  }

  /**
   * Deletes an existing Mold model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
//  public function actionDelete($id) {
//      $moldModel = $this->findModel($id);
//      try {
//        MoldMachine::deleteAll(['mold_id' => $id]);
//        $moldModel->delete();
//      }
//      catch(\Exception $exception) {
//        echo "<pre>"; print_r($exception);echo "</pre>";
//        die;
//
//        throw new HttpException(500, $exception->getMessage());
//      }
//      return $this->redirect(['index']);
//  }

  public function actionDelete($id) {
    $moldModel = $this->findModel($id);
    $moldMachine = MoldMachine::find()->where(['mold_id' => $id])->all();
//    $machine = Machine::find()->where(['mold_id' => $id])->all();
    $transaction = Yii::$app->db->beginTransaction();
    $err=0;
    if(count($moldMachine) > 0){
      if(!MoldMachine::deleteAll(['mold_id' => $id])) {
        $err = 1;
        Yii::$app->session->setFlash('error', Yii::t('app', 'error_delete_fr_key - MoldMachine'));
      }
    }
//    if(count($machine) > 0){
//      $machine->mold_id = null;
//      if(!$machine->save()) {
//        $err = 1;
//        Yii::$app->session->setFlash('error', Yii::t('app', 'Set null to mold - Machine'));
//      }
//    }
    if($err == 0){
      if(!$moldModel->delete()){
        $transaction->rollback();
        Yii::$app->session->setFlash('error', Yii::t('app', 'error_delete_fr_key'));
        Yii::$app->session->setFlash('error', $moldModel->errors);
//        echo "<pre>"; print_r($moldModel->errors);echo "</pre>";
//        die;
      }
    }
    $transaction->commit();
    return $this->redirect(['index']);
  }

  public function actionPartDelete() {
    $id = $_POST['id'];
    if(Yii::$app->request->isAjax && Yii::$app->request->post()) {
      $moldpart = MoldPart::findOne($id);
      if($moldpart->delete()) {
        $data['status'] = 1;
      } else {
        $data['status'] = 0;
        $data['errors'] = $moldpart->getErrors();
      }
      Yii::$app->response->format = Response::FORMAT_JSON;
      return $data;
    }
  }

  /**
   * Finds the Mold model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return Mold the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = Mold::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new Mold() : Mold::findOne($id);
    if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ActiveForm::validate($model);
    }
  }

}
