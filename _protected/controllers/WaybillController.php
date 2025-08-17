<?php

namespace app\controllers;

use app\models\Factory;
use app\models\FgInvoice;
use app\models\FgInvoiceDetail;
use app\models\FgInvoiceWaybill;
use app\models\Part;
use app\models\ProductModel;
use app\models\Unit;
use app\models\User;
use app\models\Waybill;
use app\models\WaybillSearch;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * WaybillController implements the CRUD actions for Waybill model.
 */
class WaybillController extends AppController
{

  /**
   * Lists all Waybill models.
   *
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new WaybillSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $query = "SELECT way.waybill_id, way.fg_invoice_id, inv.invoice_no FROM `fg_invoice_waybill` way left join fg_invoice inv on way.fg_invoice_id = inv.id";
    $inv = Yii::$app->db->createCommand($query)->queryAll();
    $waybills = [];
    foreach ($inv as $row) {
      $waybills[] = $row['waybill_id'];
    }
    $invoices = [];
    $waybills = array_unique($waybills);
    foreach ($waybills as $wb) {
      $tmp = [];
      foreach ($inv as $in) {
        if($in['waybill_id'] == $wb){
          $tmp[] = $in['invoice_no'];
        }
      }
      $invoices[$wb] = $tmp;
    }

    return $this->render('index', compact('searchModel', 'dataProvider','invoices'));
  }

  /**
   * Displays a single Waybill model.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id)
  {
    $model = Waybill::find()->with([
      'fgInvoiceWaybills.fgInvoice.customer', 'factory', 'createdBy', 'updatedBy' => function ($query) {
        $query->from(['u2' => User::tableName()]);
      }
    ])->where(['id' => $id])->one();
    if ($model === null) {
      throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    $pivotData = [];
    foreach ($model->fgInvoiceWaybills as $pivot) {
      $model->invoices[] = $pivot->fg_invoice_id;
      $pivotData[] = $pivot->id;
    }
    // show fg invoice details
    $detailsTable = FgInvoiceDetail::tableName();
    $partsTable = Part::tableName();
    $unitsTable = Unit::tableName();
    $waybillsTable = Waybill::tableName();
    $modelsTable = ProductModel::tableName();
    $details = (new Query())->select(["$detailsTable.part_name", "$partsTable.part_color", "$unitsTable.unit_value", "$detailsTable.price", "SUM(qty) as total_qty"])
      ->from($detailsTable)
      ->leftJoin($partsTable, "$partsTable.part_no = $detailsTable.part_no")
      ->leftJoin($unitsTable, "$unitsTable.id = $detailsTable.unit_id")
      ->groupBy(["$detailsTable.part_name", "$partsTable.part_color", "$unitsTable.unit_value", "$detailsTable.price"])
      ->where(["$detailsTable.fg_invoice_id" => $model->invoices])
      ->all();
    //                            ->createCommand()->rawSql;
    //                            echo "<pre>"; print_r($details);echo "</pre>";
    //                            die;
    return $this->render('view', compact('model', 'details'));
  }

  /**
   * Creates a new Waybill model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new Waybill();
    $model->waybill_date = date('Y-m-d');

    if ($model->load(Yii::$app->request->post()) && $model->save()) {

      $post = Yii::$app->request->post();
      if (isset($post['Waybill']['invoices'])) {
        foreach ($post['Waybill']['invoices'] as $fginv) {
          $item = new FgInvoiceWaybill();
          $item->fg_invoice_id = $fginv;
          $item->waybill_id = $model->id;
          $item->save();
        }
      }
      return $this->redirect(['view', 'id' => $model->id]);
    }

    $factory_items = ArrayHelper::map(Factory::find()->all(), 'id', 'factoryinfo');
    
    return $this->render('create', compact('model', 'factory_items'));
  }

  /**
   * Updates an existing Waybill model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = self::findModel($id);
    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      $post = Yii::$app->request->post();
      FgInvoiceWaybill::deleteAll(['waybill_id' => $id]);
      if (isset($post['Waybill']['invoices'])) {
        foreach ($post['Waybill']['invoices'] as $fginv) {
          $item = new FgInvoiceWaybill();
          $item->fg_invoice_id = $fginv;
          $item->waybill_id = $model->id;
          $item->save();
        }
      }
      return $this->redirect(['view', 'id' => $model->id]);
    }
    $factories = Factory::find()->all();
    $factory_items = ArrayHelper::map($factories, 'id', 'factoryinfo');
    $model->invoices = [];
    $pivotData = [];
    foreach ($model->fgInvoiceWaybills as $pivot) {
      $model->invoices[] = $pivot->fg_invoice_id;
      $pivotData[] = $pivot->id;
    }
    $fgInvoices = FgInvoice::find()->leftJoin('fg_invoice_waybill', 'fg_invoice_waybill.fg_invoice_id=fg_invoice.id')->where(['factory_id' => $model->factory_id])->andWhere(['or', ['fg_invoice_waybill.id' => null], ['fg_invoice_waybill.id' => $pivotData]])->all();
    $items = [];
    $items = ArrayHelper::map($fgInvoices, 'id', 'invoice_no');
    return $this->render('update', compact('model', 'factory_items', 'items'));
  }

  /**
   * Deletes an existing Waybill model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    $this->findModel($id)->delete();
    return $this->redirect(['index']);
  }

  /**
   * Finds the Waybill model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return Waybill the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Waybill::find()->with(['fgInvoiceWaybills'])->where(['id' => $id])->one()) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionFactoryInfo($id,$isNewRecord)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;

    // next waybill no
    $currentYear = date('Y');
    $waybill = Waybill::find()->where(['factory_id' => $id, 'YEAR(waybill_date)' => $currentYear])->orderBy('waybill_no DESC')->one();
    $waybill_no = $waybill ? (int)$waybill->waybill_no + 1 : 1;

    // factory fg invoices
    $cond = ['factory_id' => $id];
    if($isNewRecord){
      $cond['fg_invoice_waybill.id'] = null;
    }
    $fgInvoices = FgInvoice::find()->leftJoin('fg_invoice_waybill', 'fg_invoice_waybill.fg_invoice_id=fg_invoice.id')->where($cond)->all();
    $items = [];
    foreach ($fgInvoices as $inv) {
      $items[] = ['id' => $inv->id, 'text' => $inv->invoice_no];
    }
    return compact('waybill_no', 'items');
  }
}
