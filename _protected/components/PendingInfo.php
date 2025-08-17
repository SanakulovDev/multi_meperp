<?php
  namespace app\components;

use app\models\ContainerInvoice;
use app\models\PaymentControl;
use app\models\VehicleCoverageInput;
use yii\base\Component;
  use Yii;

  class PendingInfo extends Component{
    
    public function init(){

      Yii::$app->view->params['pending_docs'] = null;
      Yii::$app->view->params['pending_docs_count'] = 0;
      Yii::$app->view->params['past_eta_data'] = null;
      Yii::$app->view->params['past_eta_count'] = 0;
      Yii::$app->view->params['past_eta_data_v'] = null;
      Yii::$app->view->params['past_eta_count_v'] = 0;

      Yii::$app->view->params['past_payments'] = [];

      if(Yii::$app->user->can('document-pending-alert')){

        $pending_docs_query = \app\models\Document::find()->with(['toWarehouse', 'fromWarehouse'])->where(['status' => 0]);

        if(in_array(Yii::$app->user->identity->rolename, ['mrp','mrpc'])){
          $pending_docs_query->andWhere(['to_warehouse_id' => Yii::$app->user->identity->warehouseIds,]);
        }

        Yii::$app->view->params['pending_docs'] = $pending_docs_query->all();
        Yii::$app->view->params['pending_docs_count'] = count(Yii::$app->view->params['pending_docs']);
      }
      
      // etadate
      if(Yii::$app->user->can('container-invoice-etadate-note') or Yii::$app->user->can('container-invoice-etadate-alert')){
        
        $pastEtaDates = ContainerInvoice::find()->with(['invoice','container'])->where([
          'and',
          ['not', ['shipped_at' => null]],
          ['arrived_at' => null],
          ['<','app_arr_at', date('Y-m-d')]
        ])->all();

        Yii::$app->view->params['past_eta_data'] = $pastEtaDates;
        Yii::$app->view->params['past_eta_count'] = count(Yii::$app->view->params['past_eta_data']);

      }

      // etadate vehicle
      if(Yii::$app->user->can('vehicle-coverage-input-etadate-note') or Yii::$app->user->can('vehicle-coverage-input-etadate-alert')){
        
        $pastEtaDatesV = VehicleCoverageInput::getExpiredData();

        Yii::$app->view->params['past_eta_data_v'] = $pastEtaDatesV;
        Yii::$app->view->params['past_eta_count_v'] = count(Yii::$app->view->params['past_eta_data_v']);

      }

      // past payment
      if(Yii::$app->user->can('past-payment-note') or Yii::$app->user->can('past-payment-alert')){
        $pastPayments = PaymentControl::getPastPayments();
        Yii::$app->view->params['past_payments'] = $pastPayments;
      }

      
    }

  }
  