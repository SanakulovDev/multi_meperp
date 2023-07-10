<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Part;
use app\models\ProductModel;
use app\models\Warehouse;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $data_weeklyProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $downloadFileNameJV */
/** @var TYPE_NAME $downloadFileNameOEM */
/** @var TYPE_NAME $btnOem_1_isActive */
/** @var TYPE_NAME $btnOem_2_isActive */
/** @var TYPE_NAME $btnJv_isActive */
/** @var TYPE_NAME $need_month */
/** @var TYPE_NAME $need_monthOEM */
$this->title = Yii::t('app', 'Production plan');
$calc_at = '';
$loading = '<img src="/themes/adminlte/img/loading.gif">';
$footerLists = [];
?>

<?php ob_start(); ?>
	.nav-tabs-custom > .nav-tabs > li.active{
		border:0px !important;
	}
	.nav-tabs{
		border-bottom:0px !important;
	}
	.nav-tabs-custom > .nav-tabs > li{
		border-top:0px !important;
		margin-bottom:0px !important;
		margin-right:0px !important;
	}
	.custom-text-normal{
		font-weight:normal !important;
	}
	.customer-type{
		color: #000;
		font-weight:bold;
		padding: 5px 10px!important;
		
	}
	.bg-warning{
		background-color: #f39c12!important;
	}
	th, td{
        border: 2px solid grey!important;
        text-align:center;
        padding: 5px 10px;
		text-wrap: nowrap;
    }
	thead {
		position: sticky;
		top: 0;
	}
	tfoot{
		position: sticky;
		bottom: 0;
	}
	.bg-primaries{
        background-color: #6abdff !important;
        border: 2px solid black; margin: 5px 10px;
		font-weight: bold;
    }
    .bg-lighties{
        border: 1px solid black; 
        margin: 5px 10px;
		background-color: #f2f2f2;
    } 
	.main-content{
        height: 70vh;
        overflow-x: auto;
    }
	.tbl-plan{
        width: 100%;
        border-collapse: collapse;
        max-height: 80vh;
        overflow: scroll;
    }

    .left-sticky{
        position: sticky;
        left: 0px;
    }
<?php $this->registerCss(ob_get_clean()); ?>

	<div class="req-index ">
		<div class="panel">
			<div class="panel-heading">
				<div class="row">
					
					<div class="col-sm-8">
						<h2 class="text-left" style="margin: 5px 0px 5px 10px;">
							<strong><?=$this->title?></strong>
						</h2>
					</div>
					<div class="col-sm-2"></div>
				</div>
			</div>
			<div class="panel-body" style="padding-top:20px !important;">
				<?php $form = ActiveForm::begin(['id' => 'prodPlanReport', 'class' => 'form-group form-group-sm', 'action' => ['production-fact'], 'method' => 'post',]); ?>
					<div class="row">
						<div style="clear: both;"></div>
						<div class="col-xs-6 col-sm-2">
							<?=DateTimePicker::widget(
							[
								'name' => 'need_month',
								'value' => $need_month,
								'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
								'layout' => '{picker}{input}{remove}',
								'removeButton' => ['position' => 'append'],
								'language' => Yii::$app->language,
								'pluginOptions' => [
								'autoclose' => true,
								'format' => 'yyyy-mm',
								'startView' => 'year',
								'minView' => 'year',
								'maxView' => 'year',
								],
								'options' => [
								'autocomplete' => 'off',
								'placeholder' => Yii::t('app', 'need_month'),
								'class' => ' form-control'
								]
							]
							);?>
						</div>
						<div class="col-xs-6 col-sm-2">
							<?
							
							$params = ['prompt' => 'Все','class' => 'form-control', 'id' => 'line'];
							echo Select2::widget([
							'theme' => Select2::THEME_DEFAULT,
							'model' => $model,
							'value' => 7,
							'attribute' => 'line',
							'data' => $lines,
							'options' => [$params],
							]);
							?>
						</div>
						<div class="col-xs-6 col-sm-2">
							<?
							$model->part_id = $part_id;
							$parts_list = ArrayHelper::map(Part::find()->all(), 'id', 'part_no');
							$parts_list = [0 => Yii::t('app', 'Part')] + $parts_list;
							$params = ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Part')];
							echo Select2::widget([
							'theme' => Select2::THEME_DEFAULT,
							'model' => $model,
							'value' => $part_id,
							'attribute' => 'part_id',
							'data' => $parts_list,
							'options' => [$params],
							]);
							?>
						</div>
						<div class="col-xs-6 col-sm-1">
							<button class="customer-type btn-secondary " data-id="1">Денъ</button>
						</div>
						<div class="col-xs-4 col-sm-1">
							<button class="customer-type btn-secondary" data-id="2">Смена</button>
						</div>
						<input type="hidden" name="report-type" class="report-type" value="1">
						<div class="col-xs-4 col-sm-2">
							<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary'])?>
						</div>

						<div class="col-xs-4 col-sm-2">
							<div class="form-group pull-right">
							<?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info', 'id' => 'download_xls_jv'])?>
							</div>
						</div>

					</div>
				<?php ActiveForm::end(); ?>

				<div class="row main-content">
					<table id="fix_table"  class=" tbl-plan">
						<thead>
							<tr>
								<th class="bg-primaries" rowspan="2"><?=Yii::t('app', 'Line')?></th>
								<th class="bg-primaries" rowspan="2"><?=Yii::t('app', 'Product no')?></th>
								<th class="bg-primaries" rowspan="2"><?= Yii::t('app', 'Calculation name')?></th>
								<th class="bg-primaries" rowspan="2"><?=Yii::t('app', 'Part color')?></th>
								<?php for ($i = 1; $i <= $month_end; $i++): ?>
									<?php 
									if($i < 10){
										$i = '0'.$i;
									}
									$bgClass = '';
									if($i == $todayDay){
										$bgClass = 'bg-warning';	
									}
									?>
								<th class="bg-primaries" colspan="<?= $type?>" class="<?= $bgClass?>" style="text-align:center"> <?=sprintf("%02d", $i)?> </th>
								<?php endfor;  ?>
								<th class="bg-primaries" rowspan="<?= $type?>"><?=Yii::t('app', 'Total')?></th>
							</tr>
						<?php if($type == 2):?>
							<tr>
								<? for ($i = 1; $i <= $month_end; $i++) { ?>
									<?php 
										if($i < 10){
											$i = '0'.$i;
										}
										$bgClass = '';
										if($i == $todayDay){
											$bgClass = 'bg-warning';	
										}
									?>
								<th class="<?= $bgClass?> bg-lighties" style="text-align:center">1</th>
								<th class="<?= $bgClass?> bg-lighties" style="text-align:center">2</th>
								<? } ?>
							</tr>
						<?php endif;?>
						</thead>
						<tbody>
							<?php foreach($data as $item):?>
								<tr>
									<td class="" ><?=$item['line']?></td>
									<td class="" ><?=$item['part_no']?></td>
									<td class="" style="text-align:left;"><?= substr($item['part_name'], 0, 20)?></td>
									<td class="" ><?=$item['part_color']?></td>
									<?php foreach($item['counts'] as $day => $count):?>
										<?php 
											$footerLists[$day] = $footerLists[$day] + $count;
											$day = str_replace('-1', '', $day);
											$day = str_replace('-2', '', $day);
											if($day < 10){
												$day = '0'.$day;
											}
											$bgClass = '';
											if($day == $todayDay){
												$bgClass = 'bg-warning';	
											}
										?>
											<td class="<?= $bgClass?>" data-day ="<?=$day?>" style="text-align:right;"><?=divideString($count, 3)?></td>
									<?php endforeach;?>
								</tr>
								
							<?php endforeach;?>	
						</tbody>
						<tfoot>
							<tr>

								<td class="bg-primaries" colspan="4">Итого</td>

								<?php foreach($footerLists as $count):?>
									<td class="bg-primaries"><?=divideString($count, 3)?></td>
								<?php endforeach;?>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>

		</div>
	</div>

	</div>


<?php 
ob_start();?>
	$(function(){
		$('.customer-type').on('click', function(e){
			e.preventDefault();
			var customer_type_id = $(this).data('id');
			$('.report-type').val(customer_type_id);
			$(this).css('background-color', '#31ff2a');
			$('.customer-type').each(function(){
				if($(this).data('id') != customer_type_id){
					$(this).css('background-color', '#fff');
				}
			});
		});
		$('.customer-type').each(function(){
			let id = $(this).data('id');
			if(id == <?=$type?>){
				$(this).css('background-color', '#31ff2a')
			}
		})
		$(".tbl-plan").tableFixer({'left' : 4, 'head': false, 'foot': false});

		// excel-export
		$('#download_xls_jv').on('click', function(){
			let tableId = $('.tbl-plan').attr('id');
			exportExcel(tableId);
		});

		function exportExcel(tableId){
			var excel = $JExcel.new("Calibri light 10");            
			excel.set( {sheet:0,value:"Sheet 1" } );
			
			var table = document.getElementById(tableId);
			var limit = table.rows.length;
			var cells = table.rows[0].cells.length;

			// alert(cells);

			var headers = [];

			for (var i = 0; i < cells; i++) {
				headers.push(table.rows[0].cells[i].innerHTML);
			}


		
			var formatHeader=excel.addStyle({
				border: "none,none,none,thin #333333",font: "Calibri 12 #000 B"}
			);                                                         

			for (var i=0;i< headers.length;i++){              // Loop headers
				excel.set(0,i,0,headers[i],formatHeader);    // Set CELL header text & header format
				excel.set(0,i,undefined,"auto");             // Set COLUMN width to auto 
			}
						
			for (var i=1; i < limit; i++){                                    // Generate 50 rows
				for(var j = 0; j < cells; j++){
					if(table.rows[i].cells[j] !== undefined)
					excel.set(0,j,i,table.rows[i].cells[j].innerHTML);                    // This column is a TEXT
				}
			}

			excel.generate("<?=$this->title?>-<?= date('Y-m-d H:i:s')?>.xlsx");    


			$(".tbl-plan").tableFixer({'left' : 3, 'head': false, 'foot': false});
		}
	});
<?php $this->registerJs(ob_get_clean(), yii\web\View::POS_END);?>
<?php $this->registerJsFile('/themes/excel/jquery-3.5.1.min.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myexcel.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/jszip.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/myscript.js', ['position' => \yii\web\View::POS_HEAD]); ?>
<?php $this->registerJsFile('/themes/excel/FileSaver.js', ['position' => \yii\web\View::POS_HEAD]); ?>
