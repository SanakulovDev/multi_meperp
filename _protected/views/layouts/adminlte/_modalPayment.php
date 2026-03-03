<?php
  
  $past_payments = Yii::$app->view->params['past_payments'];
  $past_payments_count = count(Yii::$app->view->params['past_payments']);
  
?>
<?if(Yii::$app->user->can('past-payment-alert')){?>
  <div class="modal modal-primary fade"  id="modal-payment">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"><i class="glyphicon glyphicon-alert"></i> <?=Yii::t('app', 'Warning')?></h4>
        </div>
        <div class="modal-body">
          <p>
            <?=Yii::t('app', 'You have {cnt} ',['cnt' => $past_payments_count])?>
            <a class="link-modal" href="<?=yii\helpers\Url::toRoute(['payment-control/index','PaymentControlSearch[dummy_order]' => 1])?>"><b><?=Yii::t('app', 'passed payments')?></b></a>
            <?=Yii::t('app', '. Please correct these data.')?>
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline pull-right" data-dismiss="modal"><?=Yii::t('app', 'Close')?></button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<?}?>






