<?php
use app\components\Helpers;
use app\models\Part;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $materialRequirements array */
/* @var $periods array */

$this->title = Yii::t('app', 'Material Requirements Report');
$this->params['breadcrumbs'][] = $this->title;

$loading = '<img src="/themes/adminlte/img/loading.gif">';
$calc_at = date('Y-m-d H:i:s');
?>


<div class="req-index">
    <div class="panel">
        <div class="panel-heading">
            <img style="height:28px;" src="/img/mep1.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left"/>
            <h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
                <?=Yii::t('app', 'Material Requirements Report')?>
                <span id="calc_at" style="font-size: 14px;color: #a29393;"><?=$loading?></span>
            </h3>
            <div class="pull-right" style="margin: 0px">
                <form method="get" style="display: inline-block; margin-right: 10px;">
                    <label style="margin-right: 5px;"><?= Yii::t('app', 'Start Date') ?>:</label>
                    <input type="date" name="start_date" value="<?= $startDate ?>" onchange="this.form.submit()" class="form-control" style="display: inline-block; width: auto;">
                </form>
                
                <!-- Download dropdown -->
                <div class="btn-group" style="display: inline-block; margin-right: 10px;">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= Yii::t('app', 'Download') ?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><?=Html::a(Yii::t('app', 'Download CSV'), ['download', 'start_date' => $startDate, 'filter' => isset($filter) ? $filter : null])?></li>
                        
                        <?php if (class_exists('codemix\excelexport\ExcelFile')): ?>
                            <li><?=Html::a(Yii::t('app', 'Download Excel (All Periods)'), ['download-excel-codemix', 'start_date' => $startDate, 'filter' => isset($filter) ? $filter : null])?></li>
                            <li><?=Html::a(Yii::t('app', 'Download Weekly Excel'), ['download-weekly-requirement', 'start_date' => $startDate, 'filter' => isset($filter) ? $filter : null])?></li>
                        <?php endif; ?>
                        
                        <?php if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')): ?>
                            <li><?=Html::a(Yii::t('app', 'Download Excel (PhpSpreadsheet)'), ['download-excel', 'start_date' => $startDate, 'filter' => isset($filter) ? $filter : null])?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="">
            <a href="<?= \yii\helpers\Url::to(['fact-requirement/index', 'filter' => 1, 'start_date' => $startDate])?>" class="btn btn-success">
                <?= Yii::t('app', 'Filter') ?> (<?= Yii::t('app', 'Hide zero usage') ?>)
            </a>
            <a href="<?= \yii\helpers\Url::to(['fact-requirement/index', 'start_date' => $startDate])?>" class="btn btn-danger">
                <?= Yii::t('app', 'Clear Filter') ?>
            </a>
            <?php if (isset($filter) && $filter): ?>
                <span class="label label-info"><?= Yii::t('app', 'Filter active: showing only materials with usage > 0') ?></span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="panel-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_weekly" data-toggle="tab" aria-expanded="true">
                        <h4 style="margin: 5px 0px -5px 0px; background-color: #f5f5f5;">
                            <b><?= Yii::t('app', 'Weekly Requirements') ?></b>
                        </h4>
                    </a>
                </li>
                <li>
                    <a href="#tab_monthly" data-toggle="tab" aria-expanded="false">
                        <h4 style="margin: 5px 0px -5px 0px; background-color: #f5f5f5;">
                            <b><?= Yii::t('app', 'Monthly Requirements') ?></b>
                        </h4>
                    </a>
                </li>
                <li>
                    <a href="#tab_yearly" data-toggle="tab" aria-expanded="false">
                        <h4 style="margin: 5px 0px -5px 0px; background-color: #f5f5f5;">
                            <b><?= Yii::t('app', 'Yearly Requirements') ?></b>
                        </h4>
                    </a>
                </li>
                <li>
                    <a href="#tab_detail" data-toggle="tab" aria-expanded="false">
                        <h4 style="margin: 5px 0px -5px 0px; background-color: #f5f5f5;">
                            <b><?= Yii::t('app', 'Detailed Requirements') ?></b>
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
                                    <th><?= Yii::t('app', 'Part No') ?></th>
                                    <th><?= Yii::t('app', 'Part Name') ?></th>
                                    <th><?= Yii::t('app', 'Unit') ?></th>
                                    <?php 
                                    // Get unique weeks
                                    $weeks = isset($periods['weekly']) ? $periods['weekly'] : [];
                                    ksort($weeks);
                                    foreach ($weeks as $week): ?>
                                        <th style="min-width: 120px; font-size: 11px;"><?= $week ?></th>
                                    <?php endforeach; ?>
                                    <th><?= Yii::t('app', 'Total') ?></th>
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
                                    <th><?= Yii::t('app', 'Part No') ?></th>
                                    <th><?= Yii::t('app', 'Part Name') ?></th>
                                    <th><?= Yii::t('app', 'Unit') ?></th>
                                    <?php 
                                    // Get unique months
                                    $months = isset($periods['monthly']) ? $periods['monthly'] : [];
                                    ksort($months);
                                    foreach ($months as $month): ?>
                                        <th style="min-width: 120px; font-size: 11px;"><?= $month ?></th>
                                    <?php endforeach; ?>
                                    <th><?= Yii::t('app', 'Total') ?></th>
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
                                    <th><?= Yii::t('app', 'Part No') ?></th>
                                    <th><?= Yii::t('app', 'Part Name') ?></th>
                                    <th><?= Yii::t('app', 'Unit') ?></th>
                                    <?php 
                                    // Get unique years
                                    $years = isset($periods['yearly']) ? $periods['yearly'] : [];
                                    ksort($years);
                                    foreach ($years as $year): ?>
                                        <th style="min-width: 120px; font-size: 11px;"><?= $year ?></th>
                                    <?php endforeach; ?>
                                    <th><?= Yii::t('app', 'Total') ?></th>
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
                                    <th><?= Yii::t('app', 'Part No') ?></th>
                                    <th><?= Yii::t('app', 'Part Name') ?></th>
                                    <th><?= Yii::t('app', 'Unit') ?></th>
                                    <th><?= Yii::t('app', 'Total Required') ?></th>
                                    <th><?= Yii::t('app', 'Details') ?></th>
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
                                                <strong><?= Yii::t('app', 'Used in specifications') ?>:</strong><br>
                                                <?php foreach ($req['specifications'] as $specKey => $specData): ?>
                                                    <small><?= Html::encode($specKey) ?>: <?= number_format($specData['total_usage'], 2) ?> <?= Html::encode($req['unit']) ?></small><br>
                                                <?php endforeach; ?>
                                                <hr style="margin: 5px 0;">
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($req['weekly'])): ?>
                                                <strong><?= Yii::t('app', 'Weekly') ?>:</strong>
                                                <?php foreach ($req['weekly'] as $week => $data): ?>
                                                    <small><?= $week ?>: <?= number_format($data['quantity'], 2) ?></small>; 
                                                <?php endforeach; ?>
                                                <br>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($req['monthly'])): ?>
                                                <strong><?= Yii::t('app', 'Monthly') ?>:</strong>
                                                <?php foreach ($req['monthly'] as $month => $data): ?>
                                                    <small><?= $month ?>: <?= number_format($data['quantity'], 2) ?></small>; 
                                                <?php endforeach; ?>
                                                <br>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($req['yearly'])): ?>
                                                <strong><?= Yii::t('app', 'Yearly') ?>:</strong>
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



JS;
$this->registerJs($script);
?>