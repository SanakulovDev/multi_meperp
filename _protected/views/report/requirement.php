<?php
	use app\components\Helpers;
use app\models\Part;
use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\ReqSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Part requirement');
	$this->params['breadcrumbs'][] = $this->title;
	$period_weekly = app\components\Helpers::getPeriodWeek6Month();
	$period_daily = [];
	foreach (app\components\Helpers::getPeriodFull() as $pdate) {
		if($pdate > date('Y-m-t', strtotime('+6 month'))) break;
		$period_daily[] = $pdate;
	}
  
  $loading = '<img src="/themes/adminlte/img/loading.gif">';
	$calc_at = null;
	
  
?>

<?= $this->render('../common/_loading'); ?>


<div class="req-index">


	<div class="panel">
		<div class="panel-heading">
                <img style="height:28px;" src="/img/mep1.jpg" title="<?php echo Yii::$app->params['comp_name'] ?>" class="pull-left"/>
				<h3 class="pull-left" style="margin: 5px 0px -5px 10px;">
				<?=Yii::t('app', 'Part requirement')?>
				<span id="calc_at" style="font-size: 14px;color: #a29393;"><?=$loading?></span>
			</h3>
			<p class="pull-right" style="margin: 0px">
				<?=Html::a(Yii::t('app', 'btn-download'), ['download-weekly-requirement'], ['class' => 'btn btn-info btn-sm', 'id' => 'btnDownload'])?>
			</p>
			<div style="clear: both;"></div>
		</div>
		<div class="panel-body">


			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true" id="tabBtn_1">
							<h4 style="margin: 5px 0px -5px 0px;"><b>ЕЖЕНЕДЕЛЬНО</b></h4></a></li>
					<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false" id="tabBtn_2">
							<h4 style="margin: 5px 0px -5px 0px;"><b>ПОВСЕДНЕВНАЯ</b></h4></a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table class="table table-req" id="fix_table_w">
							<thead>
							<tr class="tr_head">
								<th style="width: 30px;" class="text-center">№</th>
								<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part')?></th>
								<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part color')?></th>
								<th><?=mb_strtoupper(Yii::t('app', 'Part name'))?></th>
								<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Type')?></th>
								<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Average usage')?></th>
								<? foreach($period_weekly as $col => $per){ ?>
									<th style="width: 90px;" class="text-center"><?=((strlen(trim($per['plandate'])) > 7)) ? date("d.m", strtotime($per['from'])).'<br>-<br>'.date("d.m", strtotime($per['to'])) : date("m.Y", strtotime($per['plandate']))?></th>
								<? } ?>
							</tr>
							</thead>
							<tbody>
							<? $i = 0; ?>
							<? foreach($data_weekly as $row){
								$i++; ?>
								<tr <?=($i%2 == 0) ? 'class="tr_odd"' : ''?>>
									<td class="text-center"><?=$i?></td>
									<td class="text-center"><?=$row['part_no']?></td>
									<td class="text-center" title="<?=$row['remark']?>"><?=$row['part_color']?></td>
									<td style="max-width: 150px;" class="td-nowrap"><?=mb_strtoupper($row['part_name'])?></td>
									<td class="text-center"><?=$row['csourse']?></td>
									<td class="text-right"><?=round(Part::findOne($row['part_id'])->averageUsage)?></td>
									<? foreach($period_weekly as $col => $per){ ?>
										<td class="text-right  <?=($row['col'.($col + 1)] == 0) ? 'qty-zero' : 'qty-bold'?>">
											<?=Helpers::formatRemoveDecimal($row['col'.($col + 1)])?>
										</td>
									<? } ?>

								</tr>
								<? $calc_at = date("d.m.Y H:i", strtotime($row['calc_at'])); ?>
							<? } ?>
							</tbody>
						</table>
					</div>
					<?
						$arr = [[], []];
						$nextWeek = false;
						$indexMonth = '';
						$month = [];
					?>
					<!-- /.tab-pane -->
					<div class="tab-pane" id="tab_2">
						<table class="table table-req" id="fix_table_d">
							<thead>
							<tr class="tr_head">
								<th style="width: 30px;" class="text-center">№</th>
								<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part')?></th>
								<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Part color')?></th>
								<th><?=mb_strtoupper(Yii::t('app', 'Part name'))?></th>
								<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Type')?></th>
								<th style="width: 100px;" class="text-center"><?=Yii::t('app', 'Average usage')?></th>
								<th style="width: 100px;" class="text-center">1 нед</th>
								<th style="width: 100px;" class="text-center">след нед</th>
								<th style="width: 100px;" class="text-center">1месяц</th>
								
								<? foreach($period_daily as $col => $pdate){ ?>
									<th style="width: 60px;" class="text-center"><?=date("d.m", strtotime($pdate));?></th>

									<? 
										if (count($arr[1]) == 0 and !$nextWeek)  {
											if (date('w', strtotime($pdate)) == 0) {
												$nextWeek = true;
											}
											array_push($arr[0], $col);
										} else if ($nextWeek and count($arr[1]) <= 6) {
											array_push($arr[1], $col);
										}
										if (!$indexMonth) {
											$indexMonth = date("m", strtotime($pdate));
											array_push($month, $col);
										} else if ($indexMonth == date("m", strtotime($pdate))) {
											array_push($month, $col);
										}
									?>

								<? } ?>
							</tr>
							</thead>
							<tbody>
							<? $i = 0; ?>
							<? foreach($data_daily as $row){
								$i++; ?>
								<tr <?=($i%2 == 0) ? 'class="tr_odd"' : ''?>>
									<td class="text-center"><?=$i?></td>
									<td class="text-center"><?=$row['part_no']?></td>
									<td class="text-center" title="<?=$row['remark']?>"><?=$row['part_color']?></td>
									<td style="max-width: 150px;" class="td-nowrap"><?=mb_strtoupper($row['part_name'])?></td>
									<td class="text-center"><?=$row['csourse']?></td>
									<td class="text-right"><?=round(Part::findOne($row['part_id'])->averageUsage)?></td>
									<?
										$c_week = 0;
										foreach($arr[0] as $col){
											$c_week = $c_week + Helpers::formatRemoveDecimal($row['col'.($col + 1)]);
										}
										$next_week = 0;
										foreach($arr[1] as $col){
											$next_week = $next_week + Helpers::formatRemoveDecimal($row['col'.($col + 1)]);
										}
										$month_total = 0;
										foreach($month as $col){
											$month_total = $month_total + Helpers::formatRemoveDecimal($row['col'.($col + 1)]);
										}
									?>

									<td><? echo $c_week ;  ?></td>
									<td><? echo $next_week ;  ?></td>
									<td><? echo $month_total ;  ?></td>
									
									<? foreach($period_daily as $col => $pdate){ ?>
										<td class="text-right  <?=($row['col'.($col + 1)] == 0) ? 'qty-zero' : 'qty-bold'?>">
											<?=Helpers::formatRemoveDecimal($row['col'.($col + 1)])?>
										</td>
									<? } ?>

								</tr>
								<? $calc_at = date("d.m.Y H:i", strtotime($row['calc_at'])); ?>
							<? } ?>
							</tbody>
						</table>
					</div>

				</div>
				<!-- /.tab-content -->
			</div>


		</div>
	</div>

</div>


<?
	$routeDaily = yii\helpers\Url::toRoute(['download-daily-requirement']);
	$routeWeekly = yii\helpers\Url::toRoute(['download-weekly-requirement']);
	$script1 = <<< JS
	
	$('#fix_table_w').tableFixer({'left' : 4});
	changeHeightW();
	$(window).resize(function(){
		changeHeightW();
	});
	function changeHeightW(){
		window_h = $(window).height();
		table_h = window_h - 200;
		// console.log(window_h+"-"+table_h);		
		$('#tab_1').height(table_h+'px');
	}
        
        
        $('#fix_table_d').tableFixer({'left' : 4});
	changeHeightD();
	$(window).resize(function(){
		changeHeightD();
	});
	function changeHeightD(){
		window_h = $(window).height();
		table_h = window_h - 200;
		// console.log(window_h+"-"+table_h);		
		$('#tab_2').height(table_h+'px');
	}
        
        $('#calc_at').html('($calc_at)');
        
        
        $('#tabBtn_1').on('click', function () {
            $('#btnDownload').attr('href','$routeWeekly')
        })
        
        $('#tabBtn_2').on('click', function () {
            $('#btnDownload').attr('href','$routeDaily')
				})
				
				$('#loading').hide();

	
JS;
	$this->registerJs($script1);
?>
