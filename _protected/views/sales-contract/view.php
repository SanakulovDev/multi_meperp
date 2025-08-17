<?php
	use yii\helpers\Html;
	use yii\web\YiiAsset;
	use yii\widgets\DetailView;

	/* @var $this yii\web\View */
	/* @var $model app\models\Contract */
	$this->title = $model->contract_no;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contracts'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	YiiAsset::register($this);
?>
<div class="contract-view">

	<p>
		<?=Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id, 'update' => 1], ['class' => 'btn btn-primary btn-sm'])?>
		<?=
			Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger btn-sm',
				'data' => [
					'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			])
		?>
	</p>
	<div class="row">
		<div class="col-lg-5">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<p style="margin-bottom: 0px;">
						<?=Yii::t('app', 'Contract data')?>
					</p>
				</div>
				<?=
					DetailView::widget([
						                   'model' => $model,
						                   'template' => '<tr><th style="width:250px">{label}</th><td>{value}</td></tr>',
						                   'attributes' => [
							                   'id',
							                   [
								                   'attribute' => 'customer_id',
								                   'value' => $model->customer->name,
							                   ],
							                   'contract_no',
							                   'contract_date',
							                   'expiry_date',
							                   [
								                   'attribute' => 'seller_id',
								                   'value' => $model->seller->fullname,
							                   ],
							                   [
								                   'attribute' => 'payment_term_id',
								                   'value' => $model->paymentTerm->name,
							                   ],
							                   [
								                   'attribute' => 'contract_amount',
								                   'value' => number_format($model->contract_amount, 2),
							                   ],
							                   [
								                   'attribute' => 'contract_subject_id',
								                   'value' => $model->contractSubject->name ?? null,
							                   ],
							                   [
								                   'attribute' => 'currency_id',
								                   'value' => $model->currency->code,
							                   ],
							                   [
								                   'attribute' => 'created_by',
								                   'value' => $model->createdBy->fullname,
							                   ],
							                   [
								                   'attribute' => 'created_at',
								                   'value' => $model->createdAtFormatted,
							                   ],
							                   [
								                   'attribute' => 'updated_by',
								                   'value' => $model->updatedBy->fullname ?? null,
							                   ],
							                   [
								                   'attribute' => 'updated_at',
								                   'value' => $model->updatedAtFormatted ?? null,
							                   ],
						                   ],
					                   ])
				?>
			</div>
		</div>
		<div class="col-lg-7">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<p style="margin-bottom: 0px;">
						<?=Yii::t('app', 'Contract details')?>
					</p>
				</div>


				<table class="table document-view-table" id="detailTable">
					<tr>
						<th>№</th>
						<th><?=Yii::t('app', 'Part name')?></th>
						<th style="text-align: left"><?=Yii::t('app', 'Part color')?></th>
						<th style="text-align: right"><?=Yii::t('app', 'Price')?></th>
						<th style="text-align: right"><?=Yii::t('app', 'VAT')?></th>
						<th style="text-align: right"><?=Yii::t('app', 'Excise')?></th>
						<th style="text-align: right"><?=Yii::t('app', 'Qty')?></th>
						<th><?=Yii::t('app', 'Unit')?></th>
					</tr>
					<?
						$i = 0;
						foreach($model->salesContractDetails as $item){
							?>
							<tr class="tr_item <?=($i%2 == 0) ? 'odd' : ''?>">
								<?php //vd($item);?>
								<th><?=++$i?></th>
								<td><?=$item->part->part_name?></td>
								<td style="text-align: left"><?=$item->part->part_color?></td>
								<td style="text-align: right"><?=number_format($item->price, 2);?></td>
								<td style="text-align: right"><?=number_format($item->vat, 2);?></td>
								<td style="text-align: right"><?=number_format($item->excise, 2);?></td>
								<td style="text-align: right"><?=number_format($item->qty, 2);?></td>
								<td><?=$item->part->unit->unit_value?></td>
							</tr>
							<?
						}
					?>

				</table>
			</div>
		</div>
	</div>
	<p>
		<?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
	</p>

</div>
