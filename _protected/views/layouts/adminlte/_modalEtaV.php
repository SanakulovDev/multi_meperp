<?php
  
  $past_eta_data_v = Yii::$app->view->params['past_eta_data_v'];
  $past_eta_data_count_v = Yii::$app->view->params['past_eta_count_v'];
  
?>
<?if(Yii::$app->user->can('vehicle-coverage-input-etadate-alert')){?>
  <div class="modal modal-warning fade"  id="modal-eta-v">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"><i class="glyphicon glyphicon-alert"></i> <?=Yii::t('app', 'Warning')?></h4>
        </div>
        <div class="modal-body">
          <p>
            <?=Yii::t('app', 'You have {cnt} ',['cnt' => $past_eta_data_count_v])?>
            <a class="link-modal" href="<?=yii\helpers\Url::toRoute(['vehicle-coverage-input/index'])?>"><b><?=Yii::t('app', 'vehicle sets with incorrect ETA date')?></b></a>
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






