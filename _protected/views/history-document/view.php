<?php
	use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $model app\models\Document */
	$this->title = $model->docnum.' ('.$model->docdate.')';
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'History documents'), 'url' => Yii::$app->request->referrer];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-view">

	<p>
        <span class="btn-<?=($model->status == 1) ? 'primary' : 'warning'?> btn-sm">
            <?=$model->statusName?>
        </span>
	</p>


	<div class="panel panel-primary">

		<div class="panel-body">
			<div class="row">
				<div class="col-lg-3">
					<div class="form-group">
						<label class="control-label"><?=Yii::t('app', 'Action')?></label>
						<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->actionName?></label>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<label class="control-label"><?=Yii::t('app', 'User')?></label>
						<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->hisUser->fullname?></label>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<label class="control-label"><?=Yii::t('app', 'Date')?></label>
						<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=date("d.m.Y H:i", strtotime($model->his_date))?></label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<? if($model->document_type_id == 2){ ?>
			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Warehouse A')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->fromWarehouse->name?></label>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Warehouse B')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->toWarehouse->name?></label>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Supplier')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->supplier?></label>
				</div>
			</div>
		<? }elseif($model->document_type_id == 3){ ?>
			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Adjustment')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->adjName?></label>
				</div>
			</div>

			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Warehouse')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->adjWhName?></label>
				</div>
			</div>
		<? } ?>
	</div>


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<p style="margin-bottom: 0px;">
						<?=Yii::t('app', 'Details')?>
					</p>
				</div>


				<table class="table document-view-table" id="detailTable">
					<tr>
						<th>№</th>
						<th><?=Yii::t('app', 'Detail')?></th>
						<th><?=Yii::t('app', 'Part name')?></th>
						<th><?=Yii::t('app', 'Quantity')?></th>
						<th><?=Yii::t('app', 'Unit')?></th>
					</tr>
					<?
						$i = 1;
						foreach($model->historyDocumentDetails as $item){
							//if($key==0)continue;
							?>
							<tr class="tr_item <?=($i%2 == 0) ? 'odd' : ''?>">
								<th><?=++$i?></th>
								<td><?=$item->part->partinfo?></td>
								<td><?=$item->part->part_name?></td>
								<td><?=$item->qty?></td>
								<td><?=$item->part->unit->unit_value?></td>
							</tr>
							<?
						}
					?>

				</table>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-3">
			<div class="form-group">
				<label class="control-label"><?=Yii::t('app', 'Created by')?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->createdBy->fullname?></label>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="form-group">
				<label class="control-label"><?=Yii::t('app', 'Created at')?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=date("d.m.Y H:i", $model->created_at)?></label>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="form-group">
				<label class="control-label"><?=Yii::t('app', 'Updated by')?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->updatedBy->fullname ?? null?></label>
			</div>
		</div>

		<div class="col-lg-3">
			<div class="form-group">
				<label class="control-label"><?=Yii::t('app', 'Updated at')?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=(!empty($model->updated_at)) ? date("d.m.Y H:i", $model->updated_at) : ''?></label>
			</div>
		</div>
	</div>

	<p>
		<?=Html::a(Yii::t('app', 'btn-back'), Yii::$app->request->referrer, ['class' => 'btn btn-default btn-sm'])?>
	</p>


</div>
