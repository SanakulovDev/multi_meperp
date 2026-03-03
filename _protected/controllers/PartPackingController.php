<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Pack;
use app\models\PackLevel;
use app\models\Part;
use app\models\PartPacking;
use app\models\PartPackingSearch;
use app\models\Sequence;
use app\models\Supplier;
use app\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PartPackingController implements the CRUD actions for PartPacking model.
 */
class PartPackingController extends AppController {

  /**
   * Lists all PartPacking models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new PartPackingSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', array_merge([
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ], self::loadDictionaries()));
  }

  /**
   * Displays a single PartPacking model.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id) {
    $model = $this->findModel($id);
    $packLevels = PackLevel::find()->with([
      'inPack', 'createdBy',
      'pack' => function($query) {
        $query->from(['mainPack' => Pack::tableName()]);
      },
      'updatedBy' => function($query) {
        $query->from(['u2' => User::tableName()]);
      }
    ])->where(['pack_id' => $model->pack_id])->all();

    return $this->render('view', compact('packLevels', 'model'));
  }

  /**
   * Finds the PartPacking model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int $id
   *
   * @return PartPacking the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = PartPacking::find()->where(['part_packing.id' => $id])->joinWith([
        'part', 'pack', 'supplier', 'createdBy',
        'updatedBy' => function($query) {
          $query->from(['u2' => User::tableName()]);
        }
      ])->one()) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  /**
   * Creates a new PartPacking model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate() {
    $model = new PartPacking();
    if(Yii::$app->getRequest()->isAjax) {
      if($model->load(Yii::$app->request->post())) {
        if($model->save()) {
          $data['status'] = 1;
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $data;
      } else {
        return $this->renderAjax('_form', array_merge([
          'model' => $model,
        ], self::loadDictionaries()));
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  private function loadDictionaries() {
    $parts = ArrayHelper::map(Part::find()->all(), 'id', 'partinfo');
    $suppliers = ArrayHelper::map(Supplier::find()->all(), 'id', 'name');
    $packs = ArrayHelper::map(Pack::find()->all(), 'id', 'code');

    return compact('parts', 'suppliers', 'packs');
  }

  /**
   * Updates an existing PartPacking model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
    if(Yii::$app->getRequest()->isAjax) {
      if($model->load(Yii::$app->request->post())) {
        if($model->save()) {
          $data['status'] = 1;
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $data;
      } else {
        return $this->renderAjax('_form', array_merge([
          'model' => $model,
        ], self::loadDictionaries()));
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  /**
   * Deletes an existing PartPacking model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
    $model = PartPacking::find()->where(['id' => $id])->one();
    if($model && $model->delete()) {
      return [
        "status" => 1
      ];
    }

    return [
      "status" => 0
    ];
  }

  public function actionXls() {
    ini_set('memory_limit', '-1');
    $searchModel = new PartPackingSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send(Helpers::downloadFileName('part-pack'));
    die;
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new PartPacking() : PartPacking::findOne($id);
    if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;

      return ActiveForm::validate($model);
    }
  }

  public function actionPrint($id) {
    $model = $this->findModel($id);
    $copies = (isset($_POST['copy'])) ? $_POST['copy'] : 1;
    $qty = (isset($_POST['qty'])) ? $_POST['qty'] : $model->pack_qty;
    $part = Part::find()->where(['id' => $model->part_id])->one();
    $unit = $part->unit->unit_value;
    $transaction = Yii::$app->db->beginTransaction();
    try {
      $modelSequence = Sequence::find()->where(['code' => Sequence::TYPE_SUPPLY])->one();
      if(empty($modelSequence)) {
        Yii::$app->db->createCommand(
          "INSERT INTO sequence(code, last_seq, description) VALUES('supply', 0, 'label for supply');"
        )->execute();
        $modelSequence = Sequence::find()->where(['code' => Sequence::TYPE_SUPPLY])->one();
      }
      $lastSeq = $modelSequence->last_seq;
      if($lastSeq + $copies > 999999) {
        $editedLastSeq = $copies;
      } else {
        $editedLastSeq = $lastSeq + $copies;
      }
      $modelSequence->last_seq = $editedLastSeq;
      if($modelSequence->save()) {
        $transaction->commit();
      }
      $transaction->rollback();
    }
    catch(Exception $e) {
      $transaction->rollback();
      Yii::$app->session->setFlash('error', $e);
    }

    return $this->render('print', [
      'model' => $model,
      'copies' => $copies ?? 0,
      'qty' => $qty,
      'unit' => $unit,
      'editedLastSeq' => $editedLastSeq ?? 0
    ]);
  }

}
