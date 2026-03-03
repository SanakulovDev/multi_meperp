<?php
	use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\DeliveryPlanSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	/* @var $need_month app\controllers\DeliveryPlanController */
	$this->title = Yii::t('app', 'Pipeline material report(container)');
	$this->params['breadcrumbs'][] = $this->title;
?>
<p>
<div class="form-group pull-right">
	<?=Html::button(Yii::t('app', 'btn-download'), ['class' => 'btn btn-info btn-sm', 'id' => 'download-xls'])?>
</div>
</p>
<div class="row">
	<div class="col-md-12 col-lg-12">
		<div id="div_fix_table" class="col-md-12  col-lg-12">
			<table id="fix_table" class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
				<thead>
				<tr>
					<th class="txt_center"><?=Yii::t('app', 'Current location')?></th>
					<th class="txt_center"><?=Yii::t('app', 'Approximate arrival date')?></th>
					<th class="txt_center"><?=Yii::t('app', 'Containers count')?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach($data as $item): ?>
					<tr>
						<td class="midtext"><?=$item['current_locate']?></td>
						<td class="midtext"><?=$item['app_arr_at']?></td>
						<td class="midtext txt_center"> <?=$item['total']?> </td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php
	$script1 = <<< JS
	$('#fix_table').tableFixer({'left' : 2});
	changeHeight();
	$(window).resize(function(){
		changeHeight();
	});
	function changeHeight(){
		window_h = $(window).height();
		table_h = window_h - 270;
		// console.log(window_h+"-"+table_h);
		$('#div_fix_table').height(table_h+'px');
	}
	
	$('#download-xls').on('click', function (e) {		
		html_xls_export('fix_table', '$downloadFileName');
	});
JS;
	$this->registerJs($script1);
?>

