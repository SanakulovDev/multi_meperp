<?php

use app\components\Helpers;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Document */

$this->title = $model->docnum . ' (' . $model->docdate . ')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//echo "<pre>";
//var_dump($model->isLocal);
//echo "</pre>";
//die;
?>
<div class="document-view">


	<p>
		<? if (empty($model->serial_number)) { ?>
			<? if ($model->document_type_id == 2) { ?>
				<? if ($model->status == 0) { ?>
					<? if (Yii::$app->user->can('document-update') and in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds)) { ?>
						<?= Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
					<? } ?>

					<? if (Yii::$app->user->can('document-delete')  and in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds)) { ?>
						<?=
							Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
								'class' => 'btn btn-danger btn-sm',
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							])
						?>
					<? } ?>


				<? } ?>


				<? if ($model->isLocal) { ?>
					<? if (Yii::$app->user->can('document-update-local') or (Yii::$app->user->identity->roleName == 'mrp' and in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))) { ?>
						<?= Html::a(Yii::t('app', 'btn-update'), ['update-local', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-delete-local') or (Yii::$app->user->identity->roleName == 'mrp' and in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))) { ?>
						<?=
							Html::a(Yii::t('app', 'btn-delete'), ['delete-local', 'id' => $model->id], [
								'class' => 'btn btn-danger btn-sm',
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							])
						?>
					<? } ?>

				<? } ?>


				<? if ($model->isLocalIssue) { ?>
					<? if (Yii::$app->user->can('document-update-local-issue') or (Yii::$app->user->identity->roleName == 'mrp' and in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))) { ?>

						<?= Html::a(Yii::t('app', 'btn-update'), ['update-local-issue', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-delete-local-issue') or (Yii::$app->user->identity->roleName == 'mrp' and in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))) { ?>
						<?=
							Html::a(Yii::t('app', 'btn-delete'), ['delete-local-issue', 'id' => $model->id], [
								'class' => 'btn btn-danger btn-sm',
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							])
						?>
					<? } ?>
				<? } ?>


				<? if ($model->isLocalkd) { ?>
					<? if (Yii::$app->user->can('document-update-local-kd') or (Yii::$app->user->identity->roleName == 'mrp' and in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))) { ?>
						<?= Html::a(Yii::t('app', 'btn-update'), ['update-local-kd', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
					<? } ?>
					<? if (Yii::$app->user->can('document-delete-local-kd') or (Yii::$app->user->identity->roleName == 'mrp' and in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))) { ?>
						<?=
							Html::a(Yii::t('app', 'btn-delete'), ['delete-local-kd', 'id' => $model->id], [
								'class' => 'btn btn-danger btn-sm',
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							])
						?>
					<? } ?>
				<? } ?>


			<? } elseif ($model->document_type_id == 3) { ?>
				<? if (Yii::$app->user->can('document-update-act') or (Yii::$app->user->identity->roleName == 'mrp' and Yii::$app->user->identity->act_access == 1)) { ?>

					<?= Html::a(Yii::t('app', 'btn-update-act'), ['update-act', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
				<? } ?>
				<? if (Yii::$app->user->can('document-delete-act') or (Yii::$app->user->identity->roleName == 'mrp' and Yii::$app->user->identity->act_access == 1)) { ?>
					<?=
						Html::a(Yii::t('app', 'btn-delete-act'), ['delete-act', 'id' => $model->id], [
							'class' => 'btn btn-danger btn-sm',
							'data' => [
								'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
								'method' => 'post',
							],
						])
					?>
				<? } ?>

			<? } ?>

			<? if (in_array($model->document_type_id, [1, 2]) and !$model->isLocal and !$model->isLocalIssue and !$model->isLocalkd) { ?>
				<? if (Yii::$app->user->can('document-confirm') or (Yii::$app->user->identity->roleName == 'mrp' and in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))) { ?>
					<? if ($model->to_warehouse_id != Yii::$app->params['logxWhId'] or Yii::$app->params['logxWhId'] == Yii::$app->params['kdWhId']) { ?>
						<?
						if ($model->status == 0) {
							$btn = 'btn-confirm-1';
							$color = 'success';
						} else {
							$btn = 'btn-confirm-0';
							$color = 'danger';
						}
						?>

						<?=
							Html::a(Yii::t('app', $btn), ['confirm', 'id' => $model->id], [
								'class' => 'btn btn-' . $color . ' btn-sm',
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure?'),
									'method' => 'post',
								],
							])
						?>

					<? } ?>
				<? } ?>
			<? } ?>
		<? } ?>

		<? if (Yii::$app->user->can('document-print') or (Yii::$app->user->identity->roleName == 'mrp' and in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))) { ?>

			<?= Html::a(Yii::t('app', 'btn-print'), ['print', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>

		<? } ?>

		<span class="pull-right btn-<?= ($model->status == 1) ? 'primary' : 'warning' ?> btn-sm">
			<?= $model->statusName ?>
		</span>

	</p>


	<div class="row">
		<? if ($model->document_type_id == 2 or $model->document_type_id == 1) { ?>
			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?= Yii::t('app', 'Warehouse A') ?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= $model->fromWarehouse->name ?></label>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?= Yii::t('app', 'Warehouse B') ?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= $model->toWarehouse->name ?></label>
				</div>
			</div>
			<? if ($model->isIntransit or $model->isLocalkd) { ?>
				<div class="col-lg-3">
					<div class="form-group">
						<label class="control-label"><?= Yii::t('app', 'Supplier') ?></label>
						<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= $model->supplier->name ?? null ?></label>
					</div>
				</div>
			<? } ?>
		<? } elseif ($model->document_type_id == 3) { ?>
			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?= Yii::t('app', 'Adjustment') ?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= $model->adjName ?></label>
				</div>
			</div>

			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?= Yii::t('app', 'Warehouse') ?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= $model->adjWhName ?></label>
				</div>
			</div>
		<? } ?>

	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<label class="control-label"><?= Yii::t('app', 'Serial number') ?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= $model->serial_number ?></label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<label class="control-label"><?= Yii::t('app', 'Comment') ?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= $model->comment ?></label>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-lg-8">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<p style="margin-bottom: 0px;">
						<?= Yii::t('app', 'Details') ?>
					</p>
				</div>


				<table class="table document-view-table" id="detailTable">
					<tr>
						<th>№</th>
						<th style="text-align: left"><?= Yii::t('app', 'Detail') ?></th>
						<th style="text-align: left"><?= Yii::t('app', 'Part name') ?></th>
						<th style="text-align: right"><?= Yii::t('app', 'Quantity') ?></th>
						<th><?= Yii::t('app', 'Unit') ?></th>
					</tr>
					<?
					$i = 0;
					$total = 0;
					foreach ($model->documentDetails as $item) {
						//if($key==0)continue;
					?>
						<tr class="tr_item <?= ($i % 2 == 0) ? 'odd' : '' ?>">

							<th><?= ++$i ?></th>
							<td style="text-align: left"><?= $item->part->partinfo ?></td>
							<td style="text-align: left"><?= $item->part->part_name ?></td>
							<td style="text-align: right"><?= Helpers::formatRemoveDecimal($item->qty) ?></td>
							<td><?= $item->part->unit->unit_value ?></td>
						</tr>
					<?
						$total += $item->qty;
					}
					?>

					<tr class="tr_item">
						<td style="text-align: right;" colspan="4"><?= Yii::t('app', 'Total') ?>: <b><?= $total ?></b></td>
						<td></td>
					</tr>

				</table>
			</div>
		</div>
		<!-- <div class="col-lg-4">
			
				
			<? //=$this->render('_qrcode', [
			//	'model' => $model
			//])
			?>
				
			
		</div> -->
	</div>

	<div class="row">
		<div class="col-lg-3">
			<div class="form-group">
				<label class="control-label"><?= Yii::t('app', 'Created by') ?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= $model->createdBy->fullname ?></label>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="form-group">
				<label class="control-label"><?= Yii::t('app', 'Created at') ?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= date("d.m.Y H:i", $model->created_at) ?></label>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="form-group">
				<label class="control-label"><?= Yii::t('app', 'Updated by') ?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= $model->updatedBy->fullname ?? null ?></label>
			</div>
		</div>

		<div class="col-lg-3">
			<div class="form-group">
				<label class="control-label"><?= Yii::t('app', 'Updated at') ?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?= (!empty($model->updated_at)) ? date("d.m.Y H:i", $model->updated_at) : '' ?></label>
			</div>
		</div>
	</div>

	<p>
		<?= Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
	</p>


</div>