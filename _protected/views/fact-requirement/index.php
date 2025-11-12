<?php
use app\components\Helpers;
use app\models\Part;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $materialRequirements array */
/* @var $periods array */

$this->title = Yii::t('app', 'Мавод талаблари ҳисоботи');
$this->params['breadcrumbs'][] = $this->title;

$loading = '<img src="/themes/adminlte/img/loading.gif">';
$calc_at = date('Y-m-d H:i:s');
?>


<div class="req-index">
    <div class="panel">
        <div class="panel-heading">
            <img style="height:28px;" src="/img/mep1.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left"/>
            <h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
                <?=Yii::t('app', 'Мавод талаблари ҳисоботи')?>
                <span id="calc_at" style="font-size: 14px;color: #a29393;"><?=$loading?></span>
            </h3>
            <div class="pull-right" style="margin: 0px">
                <form method="get" style="display: inline-block; margin-right: 10px;">
                    <label style="margin-right: 5px;"><?= Yii::t('app', 'Бошланиш санаси') ?>:</label>
                    <input type="date" name="start_date" value="<?= $startDate ?>" onchange="this.form.submit()" class="form-control" style="display: inline-block; width: auto;">
                </form>
                
                <!-- Single Excel download button -->
                <?php if (class_exists('codemix\excelexport\ExcelFile')): ?>
                    <a id="btnExcelDownload" href="#" class="btn btn-info btn-sm" style="display: inline-block; margin-right: 10px;">
                        <?= Yii::t('app', 'Excel юклаб олиш') ?>
                    </a>
                <?php endif; ?>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="">
            <a href="<?= \yii\helpers\Url::to(['fact-requirement/index', 'filter' => 1, 'start_date' => $startDate])?>" class="btn btn-success">
                <?= Yii::t('app', 'Филтр') ?> (<?= Yii::t('app', '0 қийматлиларни яшириш') ?>)
            </a>
            <a href="<?= \yii\helpers\Url::to(['fact-requirement/index', 'start_date' => $startDate])?>" class="btn btn-danger">
                <?= Yii::t('app', 'Филтрни тозалаш') ?>
            </a>
            <?php if (isset($filter) && $filter): ?>
                <span class="label label-info"><?= Yii::t('app', 'Филтр фаол: фақат нол эмас қийматлар') ?></span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="panel-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_weekly" data-toggle="tab" aria-expanded="true">
                        <h4 style="margin: 5px 0px -5px 0px; background-color: #f5f5f5;">
                            <b><?= Yii::t('app', 'Ҳафталик талаблар') ?></b>
                        </h4>
                    </a>
                </li>
                <li>
                    <a href="#tab_monthly" data-toggle="tab" aria-expanded="false">
                        <h4 style="margin: 5px 0px -5px 0px; background-color: #f5f5f5;">
                            <b><?= Yii::t('app', 'Ойлик талаблар') ?></b>
                        </h4>
                    </a>
                </li>
                <li>
                    <a href="#tab_yearly" data-toggle="tab" aria-expanded="false">
                        <h4 style="margin: 5px 0px -5px 0px; background-color: #f5f5f5;">
                            <b><?= Yii::t('app', 'Йиллик талаблар') ?></b>
                        </h4>
                    </a>
                </li>
                <li>
                    <a href="#tab_detail" data-toggle="tab" aria-expanded="false">
                        <h4 style="margin: 5px 0px -5px 0px; background-color: #f5f5f5;">
                            <b><?= Yii::t('app', 'Батафсил талаблар') ?></b>
                        </h4>
                    </a>
                </li>
            </ul>
            
            <div class="tab-content">
                <!-- Weekly Requirements Tab -->
                <div class="tab-pane active" id="tab_weekly" >
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?= Yii::t('app', 'Детал рақами') ?></th>
                                    <th><?= Yii::t('app', 'Детал номи') ?></th>
                                    <th><?= Yii::t('app', 'Ҳисоб бирлиги') ?></th>
                                    <?php 
                                    // Get unique weeks
                                    $weeks = isset($periods['weekly']) ? $periods['weekly'] : [];
                                    ksort($weeks);
                                    foreach ($weeks as $week): ?>
                                        <th style="min-width: 120px; font-size: 11px;"><?= $week ?></th>
                                    <?php endforeach; ?>
                                    <th><?= Yii::t('app', 'Жами') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materialRequirements as $req): ?>
                                    <tr>
                                        <td><?= Html::encode($req['part_no']) ?></td>
                                        <td><?= Html::encode($req['part_name']) ?></td>
                                        <td><?= Html::encode($req['unit']) ?></td>
                                        <?php foreach ($weeks as $week): ?>
                                            <td><?= isset($req['weekly'][$week]) ? number_format($req['weekly'][$week]['quantity'], 2) : '0.00' ?></td>
                                        <?php endforeach; ?>
                                        <td><strong><?= number_format($req['total_required'], 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Monthly Requirements Tab -->
                <div class="tab-pane" id="tab_monthly" >
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?= Yii::t('app', 'Детал рақами') ?></th>
                                    <th><?= Yii::t('app', 'Детал номи') ?></th>
                                    <th><?= Yii::t('app', 'Ҳисоб бирлиги') ?></th>
                                    <?php 
                                    // Get unique months
                                    $months = isset($periods['monthly']) ? $periods['monthly'] : [];
                                    ksort($months);
                                    foreach ($months as $month): ?>
                                        <th style="min-width: 120px; font-size: 11px;"><?= $month ?></th>
                                    <?php endforeach; ?>
                                    <th><?= Yii::t('app', 'Жами') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materialRequirements as $req): ?>
                                    <tr>
                                        <td><?= Html::encode($req['part_no']) ?></td>
                                        <td><?= Html::encode($req['part_name']) ?></td>
                                        <td><?= Html::encode($req['unit']) ?></td>
                                        <?php foreach ($months as $month): ?>
                                            <td><?= isset($req['monthly'][$month]) ? number_format($req['monthly'][$month]['quantity'], 2) : '0.00' ?></td>
                                        <?php endforeach; ?>
                                        <td><strong><?= number_format($req['total_required'], 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Yearly Requirements Tab -->
                <div class="tab-pane" id="tab_yearly" >
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?= Yii::t('app', 'Детал рақами') ?></th>
                                    <th><?= Yii::t('app', 'Детал номи') ?></th>
                                    <th><?= Yii::t('app', 'Ҳисоб бирлиги') ?></th>
                                    <?php 
                                    // Get unique years
                                    $years = isset($periods['yearly']) ? $periods['yearly'] : [];
                                    ksort($years);
                                    foreach ($years as $year): ?>
                                        <th style="min-width: 120px; font-size: 11px;"><?= $year ?></th>
                                    <?php endforeach; ?>
                                    <th><?= Yii::t('app', 'Жами') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materialRequirements as $req): ?>
                                    <tr>
                                        <td><?= Html::encode($req['part_no']) ?></td>
                                        <td><?= Html::encode($req['part_name']) ?></td>
                                        <td><?= Html::encode($req['unit']) ?></td>
                                        <?php foreach ($years as $year): ?>
                                            <td><?= isset($req['yearly'][$year]) ? number_format($req['yearly'][$year]['quantity'], 2) : '0.00' ?></td>
                                        <?php endforeach; ?>
                                        <td><strong><?= number_format($req['total_required'], 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Detailed Requirements Tab -->
                <div class="tab-pane" id="tab_detail">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?= Yii::t('app', 'Детал рақами') ?></th>
                                    <th><?= Yii::t('app', 'Детал номи') ?></th>
                                    <th><?= Yii::t('app', 'Ҳисоб бирлиги') ?></th>
                                    <th><?= Yii::t('app', 'Жами талаб') ?></th>
                                    <th><?= Yii::t('app', 'Батафсил') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materialRequirements as $req): ?>
                                    <tr>
                                        <td><?= Html::encode($req['part_no']) ?></td>
                                        <td><?= Html::encode($req['part_name']) ?></td>
                                        <td><?= Html::encode($req['unit']) ?></td>
                                        <td><strong><?= number_format($req['total_required'], 2) ?></strong></td>
                                        <td>
                                            <?php if (!empty($req['specifications'])): ?>
                                                <strong><?= Yii::t('app', 'Қўлланилган спецификациялар') ?>:</strong><br>
                                                <?php foreach ($req['specifications'] as $specKey => $specData): ?>
                                                    <small><?= Html::encode($specKey) ?>: <?= number_format($specData['total_usage'], 2) ?> <?= Html::encode($req['unit']) ?></small><br>
                                                <?php endforeach; ?>
                                                <hr style="margin: 5px 0;">
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($req['weekly'])): ?>
                                                <strong><?= Yii::t('app', 'Ҳафталик') ?>:</strong>
                                                <?php foreach ($req['weekly'] as $week => $data): ?>
                                                    <small><?= $week ?>: <?= number_format($data['quantity'], 2) ?></small>; 
                                                <?php endforeach; ?>
                                                <br>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($req['monthly'])): ?>
                                                <strong><?= Yii::t('app', 'Ойлик') ?>:</strong>
                                                <?php foreach ($req['monthly'] as $month => $data): ?>
                                                    <small><?= $month ?>: <?= number_format($data['quantity'], 2) ?></small>; 
                                                <?php endforeach; ?>
                                                <br>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($req['yearly'])): ?>
                                                <strong><?= Yii::t('app', 'Йиллик') ?>:</strong>
                                                <?php foreach ($req['yearly'] as $year => $data): ?>
                                                    <small><?= $year ?>: <?= number_format($data['quantity'], 2) ?></small>; 
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$script = <<< JS
$('#calc_at').html('($calc_at)');

$('#btnExcelDownload').on('click', function(e){
    e.preventDefault();
    var activeId = $('.nav-tabs li.active a').attr('href');
    var period = 'weekly';
    if (activeId === '#tab_monthly') period = 'monthly';
    if (activeId === '#tab_yearly') period = 'yearly';
    var url = "<?= Url::to(['fact-requirement/download-excel']) ?>";
    var params = {
        start_date: "<?= $startDate ?>",
        filter: <?= isset($filter) && $filter ? 1 : 0 ?>,
        period: period
    };
    var q = $.param(params);
    window.location.href = url + (url.indexOf('?') === -1 ? '?' : '&') + q;
});
JS;
$this->registerJs($script);
?>