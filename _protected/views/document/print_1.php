<?php
	use app\assets\AdminLteAsset;
	use yii\helpers\Html;
	use yii\web\JqueryAsset;

	/* @var $this yii\web\View */
	/* @var $model app\models\Document */
	$this->title = $model->docnum.' ('.$model->docdate.')';
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-view">

	<p class="pull-right">
		<?=Html::button(Yii::t('app', 'btn-pdf'), ['class' => 'btn btn-primary btn-sm  pull-right', 'id' => 'btnPdf', 'style' => 'margin-left:15px;'])?>
		<?=Html::button(Yii::t('app', 'btn-print'), ['class' => 'btn btn-info btn-sm  pull-right', 'id' => 'btnPrint'])?>
	</p>

	<div id="printarea" style="clear:both">
		<div class="panel">
			<div class="panel-body">
				<p class="text-right"><b><?=Yii::t('app', 'Document date')?>:</b> <?=date("d.m.Y", strtotime($model->docdate))?> г.
				</p>
				<p class="text-center"><b><?=Yii::t('app', 'Document number')?>:</b> <span id="docnum"><?=$model->docnum?></span>
				</p>


				<p><b><?=Yii::t('app', 'Warehouse A')?>:</b> <?=$model->fromWarehouse->name?></p>
				<p><b><?=Yii::t('app', 'Warehouse B')?>:</b> <?=$model->toWarehouse->name?></p>
				<? if(!empty($model->supplier_id)){ ?>
					<p><b><?=Yii::t('app', 'Supplier')?>:</b> <?=$model->supplier->name?></p>
				<? } ?>
				<p><b><?=Yii::t('app', 'Comment')?>:</b> <?=$model->comment?></p>
				<table class="table table-bordered table-responsive table-document">
					<tr>
						<th class="text-center" style="width: 30px">№</th>
						<th style="width: 140px"><?=Yii::t('app', 'Detail')?></th>
						<th><?=Yii::t('app', 'Part name')?></th>
						<th class="text-right" style="width: 60px"><?=Yii::t('app', 'Q-ty')?></th>
						<th class="text-center" style="width: 60px"><?=Yii::t('app', 'Unit')?></th>
					</tr>
					<? $i = 0;
						foreach($model->documentDetails as $detail){ ?>
							<tr>
								<td class="text-center"><?=++$i?></td>
								<td><?=$detail->part->partinfo?></td>
								<td><?=$detail->part->part_name?></td>
								<td class="text-right"><?=number_format($detail->qty, 2, '.', ' ')?></td>
								<td class="text-center"><?=$detail->part->unit->unit_value?></td>
							</tr>

						<? } ?>

				</table>
				<div>
					<div class="pull-left">
						Сдал: ____________________ <?=$model->fromWarehouse->users[0]->fullname ?? null?>
					</div>
					<div class="pull-right">
						Принял: ____________________ <?=$model->toWarehouse->users[0]->fullname ?? null?>
					</div>
				</div>
				<p class="text-right" style="clear: both; font-size: 12px; margin-top: 55px;font-style: italic">
					<b><?=Yii::t('app', 'Printed at')?>:</b> <?=date("d.m.Y H:i")?></p>
			</div>
		</div>
	</div>

	<p>
		<?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
	</p>

</div>

<?
	$this->registerCssFile("@themes/css/print.css", [
		'depends' => [
			AdminLteAsset::className()
		]
	]);
	$this->registerJsFile("@themes/js/html2pdf.bundle.min.js", [
		'depends' => [
			JqueryAsset::className()
		]
	]);
?>

