<?php
	use yii\helpers\Html;
	use yii\web\YiiAsset;
	use yii\widgets\DetailView;

	/* @var $this yii\web\View */
	/* @var $model app\models\Contract */
	$this->title = $model->contract_no;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Supplier'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	YiiAsset::register($this);
?>
<div class="contract-view">

	<p>
		<? if (Yii::$app->user->can('contract-update')) { ?>
			<?=Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm'])?>
		<?}?>
		<? if (Yii::$app->user->can('contract-delete')) { ?>
		<?=
			Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger btn-sm',
				'data' => [
					'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			])
		?>
		<?}?>
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
								                   'attribute' => 'supplier_id',
								                   'value' => $model->supplier->name,
							                   ],
							                   'contract_no',
							                   'contract_date',
							                   'expiry_date',
							                   [
								                   'attribute' => 'buyer_id',
								                   'value' => $model->buyer->fullname,
							                   ],
							                   [
								                   'attribute' => 'payment_term_id',
								                   'value' => $model->paymentTerm->name,
							                   ],
							                   'contract_amount',
							                   [
								                   'attribute' => 'contract_subject_id',
								                   'value' => $model->contractSubject->name,
							                   ],
							                   [
								                   'attribute' => 'currency_id',
								                   'value' => $model->currency->name,
							                   ],
							                   [
								                   'attribute' => 'contract_source_id',
								                   'value' => $model->contractSource->name,
							                   ],
							                   [
								                   'attribute' => 'status',
								                   'value' => $model->statusText,
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
						<th style="text-align: left"><?=Yii::t('app', 'Part name')?></th>
            <th style="text-align: left"><?=Yii::t('app', 'Part Color')?></th>
						<th style="text-align: right"><?=Yii::t('app', 'Price')?></th>
						<th><?=Yii::t('app', 'Unit')?></th>
					</tr>
					<?php
						$i = 0;
						foreach($model->contractDetails as $item){
							?>
							<tr class="tr_item <?=($i%2 == 0) ? 'odd' : ''?>">

								<th><?=++$i?></th>
								<td style="text-align: left"><?=$item->part->part_name?></td>
								<td style="text-align: left"><?=$item->part->part_color?></td>
                <td style="text-align: right"><?=app\components\Helpers::formatRemoveDecimal($item->price)?></td>
								<td><?=$item->part->unit->unit_value?></td>
							</tr>
							<?php
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
