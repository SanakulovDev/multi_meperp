<?php
  
  $pending_docs = Yii::$app->view->params['pending_docs'];
  $pending_docs_count = Yii::$app->view->params['pending_docs_count'];
  
?>
  <div class="modal modal-danger fade"  id="modal-pending">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"><i class="glyphicon glyphicon-alert"></i> <?=Yii::t('app', 'Warning')?></h4>
        </div>
        <div class="modal-body">
          <p>
            <?=Yii::t('app', 'You have {cnt} ',['cnt' => $pending_docs_count])?>
            <a class="link-modal" href="<?=yii\helpers\Url::toRoute(['document/index', 'DocumentSearch[status]'=>0, 'DocumentSearch[to_warehouse_id]'=> implode(',', Yii::$app->user->identity->warehouseIds)])?>"><b><?=Yii::t('app', 'unconfirmed documents')?></b></a>
            <?=Yii::t('app', '. Please confirm these documents.')?>
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






