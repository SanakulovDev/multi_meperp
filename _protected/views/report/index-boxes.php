<?php
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $reports */
$this->title = Yii::t('app', 'Report lists');
$this->params['breadcrumbs'][] = $this->title;
?>
  

<div class="report-index">

  <div class="btn-group" style="width: 100%;">
    <a href="<?=Url::toRoute(['report/index-boxes'])?>" class="btn btn-default pull-right btn-default disabled" title="<?=Yii::t('app', 'Grid view')?>"><i class="fa fa-th"></i></a>
    <a href="<?=Url::toRoute(['report/index'])?>" class="btn btn-default pull-right" title="<?=Yii::t('app', 'List view')?>"><i class="fa fa-list"></i></a>
  </div>

  <div class="row" style="margin-top: 10px">

    <?
    foreach ($reports as $report) {
      ?>
      <? $style = explode(':', $report->style); ?>
      <div class="col-md-3 col-sm-6 col-xs-12">
        <a target="_blank" href="<?=Url::toRoute(['report/'.$report->action])?>" style="color: #000">
          <div class="info-box">
            <span class="info-box-icon bg-<?=$style[1] ?? ''?>"><i class="<?=$style[0] ?? ''?>"></i></span>

            <div class="info-box-content" style="color:#000000">
              <span class="info-box-number"><?=$report->titleLocalized?></span>
              <span class="info-box-text" title="<?=$report->descriptionLocalized?>" style="text-transform: none;"><?=$report->descriptionLocalized?></span>

            </div>
            <!-- /.info-box-content -->
          </div>
        </a>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->

    <? } ?>


  </div>
</div>
