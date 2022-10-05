<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Factory */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Factories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="factory-view">

	<p>
		<? if (Yii::$app->user->can('factory-update')) { ?>
			<?= Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
		<? } ?>
		<? if (Yii::$app->user->can('factory-delete')) { ?>
			<?= Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger btn-sm',
				'data' => [
					'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			]) ?>
		<? } ?>
	</p>

	<?= DetailView::widget(
		[
			'model' => $model,
			'attributes' => [
				'id',
				'name',
				'head',
				'chief_accountant',
				'address',
				'tin',
				'vat',
				'duns',
				'remark',
				[
					'label' => Yii::t('app', 'Status'),
					'attribute' => 'status',
					'format' => 'html',
					'value' => function ($model, $column) {
						$sts_value = $model->status;
						switch ($sts_value) {
							case 1:
								$class = 'success';
								$sts_name = "✔";
								$sts_title = Yii::t('app', 'Active');
								break;
							case 0:
								$class = 'danger';
								$sts_name = "✖";
								$sts_title = Yii::t('app', 'Inactive');
								break;
						}
						$html = Html::tag('span', Html::encode($sts_name), ['class' => 'label label-' . $class]);
						return $sts_value === null ? $column->grid->emptyCell : $html;
					},
				],
				[
					'attribute' => 'fg_warehouse_id',
					'value' => $model->fgWarehouse ? $model->fgWarehouse->name : null
				],
				[
					'attribute' => 'created_by',
					'value' => $model->createdBy->fullname,
				],
				[
					'attribute' => 'created_at',
					'value' => $model->createdAtFormatted
				],
				[
					'attribute' => 'updated_by',
					'value' => $model->updatedBy->fullname,
				],
				[
					'attribute' => 'updated_at',
					'value' => $model->updatedAtFormatted
				]
			],
		]
	) ?>

</div>