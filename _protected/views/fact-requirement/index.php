<?php
use app\components\Helpers;
use app\models\Part;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $materialRequirements array */
/* @var $periods array */

$this->title = Yii::t('app', 'Fact Requirement');
$this->params['breadcrumbs'][] = $this->title;

$loading = '<img src="/themes/adminlte/img/loading.gif">';
$calc_at = date('Y-m-d H:i:s');
?>


<div class="req-index">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
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
                        <table id="table_weekly" class="table table-bordered table-striped">
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
                                <?php foreach ($materialRequirements as $req): 
                                    // Filter: skip if all weekly values are 0 when filter is active
                                    if (isset($filter) && $filter) {
                                        $hasNonZero = false;
                                        foreach ($weeks as $week) {
                                            $qty = isset($req['weekly'][$week]) ? $req['weekly'][$week]['quantity'] : 0;
                                            if ($qty > 0) {
                                                $hasNonZero = true;
                                                break;
                                            }
                                        }
                                        if (!$hasNonZero) continue;
                                    }
                                ?>
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
                        <table id="table_monthly" class="table table-bordered table-striped">
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
                                <?php foreach ($materialRequirements as $req): 
                                    // Filter: skip if all monthly values are 0 when filter is active
                                    if (isset($filter) && $filter) {
                                        $hasNonZero = false;
                                        foreach ($months as $month) {
                                            $qty = isset($req['monthly'][$month]) ? $req['monthly'][$month]['quantity'] : 0;
                                            if ($qty > 0) {
                                                $hasNonZero = true;
                                                break;
                                            }
                                        }
                                        if (!$hasNonZero) continue;

                                    }
                                ?>
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
                        <table id="table_yearly" class="table table-bordered table-striped">
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
                                <?php foreach ($materialRequirements as $req): 
                                    // Filter: skip if all yearly values are 0 when filter is active
                                    if (isset($filter) && $filter) {
                                        $hasNonZero = false;
                                        foreach ($years as $year) {
                                            $qty = isset($req['yearly'][$year]) ? $req['yearly'][$year]['quantity'] : 0;
                                            if ($qty > 0) {
                                                $hasNonZero = true;
                                                break;
                                            }
                                        }
                                        if (!$hasNonZero) continue;
                                    }
                                ?>
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
// Register xlsx library
$this->registerJsFile('@themes/js/xlsx.full.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$filterValue = (isset($filter) && $filter) ? 1 : 0;
$startDateJs = json_encode($startDate);
$loadingText = json_encode($loading . ' Юкланмоқда...');
$script = <<< JS
$(document).ready(function() {
    $('#btnExcelDownload').on('click', function(e){
        e.preventDefault();
        
        // Check if XLSX library is loaded
        if (typeof XLSX === 'undefined') {
            alert('Excel kutubxonasi юкланмади. Илтимос, саҳифани қайта юкланг.');
            return false;
        }
        
        // Get active tab
        var activeTab = $('.nav-tabs li.active a').attr('href');
        var period = 'weekly'; // default
        var tableId = 'table_weekly';
        
        // Determine period based on active tab
        if (activeTab === '#tab_monthly') {
            period = 'monthly';
            tableId = 'table_monthly';
        } else if (activeTab === '#tab_yearly') {
            period = 'yearly';
            tableId = 'table_yearly';
        } else if (activeTab === '#tab_detail') {
            // For detail tab, default to weekly or show message
            if (!confirm('Батафсил талаблар учун Excel юклаб олиш учун ҳафталик талаблар танланди. Давом этишни истайсизми?')) {
                return false;
            }
            period = 'weekly';
            tableId = 'table_weekly';
        } else {
            period = 'weekly';
            tableId = 'table_weekly';
        }
        
        // Show loading indicator
        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html({$loadingText});
        
        try {
            // Get the table element
            var table = document.getElementById(tableId);
            
            if (!table) {
                alert('Жадвал топилмади. Илтимос, саҳифани қайта юкланг.');
                btn.prop('disabled', false).html(originalText);
                return false;
            }
            
            // Create workbook from table
            var wb = XLSX.utils.table_to_book(table, {
                sheet: period,
                raw: false
            });
            
            // Generate filename
            var fileName = 'material-requirements-' + period + '-' + {$startDateJs};
            if ({$filterValue}) {
                fileName += '-filtered';
            }
            fileName += '.xlsx';
            
            // Download the file
            XLSX.writeFile(wb, fileName);
            
            // Restore button after a short delay
            setTimeout(function() {
                btn.prop('disabled', false).html(originalText);
            }, 1000);
            
        } catch (error) {
            console.error('Excel export xatosi:', error);
            alert('Excel файлни юклаб олишда хатолик юз берди: ' + error.message);
            btn.prop('disabled', false).html(originalText);
        }
    });
});
JS;
$this->registerJs($script);
?>