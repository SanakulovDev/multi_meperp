<?php 
use yii\helpers\Html;


?>
<div class="production-release-history" >
  <div class="row">
      <div class="col-md-12">
      <?php
          echo Html::beginTag('table', ['class' => 'table table-striped table-bordered table-hover']);
          echo Html::beginTag('thead');
          echo Html::beginTag('tr');
          //id
          echo Html::tag('th', Yii::t('app', 'ID'));
          echo Html::tag('th', Yii::t('app', 'User'));
          echo Html::tag('th', Yii::t('app', 'Quantity'));
          echo Html::tag('th', Yii::t('app', 'Created at'));
          echo Html::endTag('tr');
          echo Html::endTag('thead');
          echo Html::beginTag('tbody');
          foreach ($history as $key => $value) {
              echo Html::beginTag('tr');
              echo Html::tag('td', $key+1);
              echo Html::tag('td', $value->user?$value->user->fullname:'----');
              echo Html::tag('td', $value->quantity*1);
              echo Html::tag('td', date('d.m.Y H:i:s',  strtotime($value->created_at)));
              echo Html::endTag('tr');
          }
          echo Html::endTag('tbody');
          echo Html::endTag('table');
      ?>
      </div>
    </div>
</div>