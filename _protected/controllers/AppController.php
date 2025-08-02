<?php
namespace app\controllers;

use app\models\Document;
use app\models\HistoryDocument;
use app\models\HistoryDocumentDetail;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * AppController extends Controller and implements the behaviors() method
 * where you can specify the access control ( AC filter + RBAC ) for your controllers and their actions.
 */
class AppController extends Controller {

  /**
   * Returns a list of behaviors that this component should behave as.
   * Here we use RBAC in combination with AccessControl filter.
   *
   * @return array
   */
  public function behaviors() {
    return [
       'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'delete' => ['post'],
          'confirm' => ['post'],
          'shop-disconfirm' => ['post'],
          'delete-shop-consumption' => ['post'],
        ],
      ], // verbs
    ]; // return
  } // behaviors

  public function beforeAction($action) {
    if (!in_array($action->id, [
      'validate',
      'validate-comment',
      'list-by-supplier',
      'list-by-contract-id',
      'shop',
      'line',
      'daily',
      'get-partname',
      'order-list-by-contract',
      'part-list-by-order',
      'order-list-by-contract-and-part',
      'wh-list-by-part',
      'fetch-by-part',
      'xlsx',
      'get-points-by-ship-mode',
      'out-containers-list-by-out-invoice',
      'get-parts-by-mark-and-color',
      'list-by-fg-invoice',
      'list-by-waybill'
    ])
    ) {
      if (!in_array($action->controller->id, ['report'])) {
        if (!Yii::$app->user->can($action->controller->id.'-'.$action->id))
          throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
      }
    }

    return parent::beforeAction($action);
  }

  public function writeToDocHistory($id, $action) {
    if (in_array($action, ['update-act', 'update-local', 'update-local-issue']))
      $action = 'update';
    if (in_array($action, ['delete-act', 'delete-local', 'delete-local-issue', 'delete-shop-consumption']))
      $action = 'delete';
    $historyDocument = new HistoryDocument();
    $document = Document::findOne($id);
    $historyDocument->his_action = $action;
    $historyDocument->his_user_id = Yii::$app->user->id;
    $historyDocument->his_date = date('Y-m-d H:i:s');
    $historyDocument->document_id = $document->id;
    $historyDocument->docnum = $document->docnum;
    $historyDocument->docdate = $document->docdate;
    $historyDocument->document_type_id = $document->document_type_id;
    $historyDocument->from_warehouse_id = $document->from_warehouse_id;
    $historyDocument->to_warehouse_id = $document->to_warehouse_id;
    $historyDocument->supplier = $document->supplier->name ?? null;
    $historyDocument->status = $document->status;
    $historyDocument->created_by = $document->created_by;
    $historyDocument->created_at = $document->created_at;
    $historyDocument->updated_by = $document->updated_by;
    $historyDocument->updated_at = $document->updated_at;
    $transaction = Yii::$app->db->beginTransaction();
    $error = false;
    if ($historyDocument->save()) {
      foreach ($document->documentDetails as $detail) {
        $historyDetail = new HistoryDocumentDetail();
        $historyDetail->history_document_id = $historyDocument->id;
        $historyDetail->document_id = $detail->document_id;
        $historyDetail->part_id = $detail->part_id;
        $historyDetail->veh = $detail->veh;
        $historyDetail->qty = $detail->qty;
        $historyDetail->price = $detail->price;
        $historyDetail->currency = $detail->currency;
        $historyDetail->remarks = $detail->remarks;
        if (!$historyDetail->save())
          $error = true;
      }
    } else {
      $error = true;
    }
    if (!$error) {
      $transaction->commit();
    } else {
      $transaction->rollBack();
    }

    return !$error;
  }

} // AppController
