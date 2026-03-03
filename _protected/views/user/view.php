<?php
	use app\helpers\CssHelper;
	use app\models\Department;
	use yii\helpers\Html;
	use yii\widgets\DetailView;

	/* @var $this yii\web\View */
	/* @var $model app\models\User */
	$this->title = $model->username;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
	<div class="col-lg-7">
		<h1>
			<?=Html::encode($this->title)?>
			<div class="pull-right">
				<?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
				<?=Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm'])?>
				<?=Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], ['class' => 'btn btn-danger btn-sm',
				                                                                         'data' => [
					                                                                         'confirm' => Yii::t('app', 'Are you sure you want to delete this user?'),
					                                                                         'method' => 'post',
				                                                                         ],
				])?>
			</div>
		</h1>

		<?=DetailView::widget([
			                      'model' => $model,
			                      'attributes' => [
				                      'id',
				                      'username',
				                      'email:email',
				                      'tabno',
				                      [
					                      'attribute' => 'status',
					                      'value' => '<span class="'.CssHelper::userStatusCss($model->status).'">
                                '.$model->getStatusName($model->status).'
                            </span>',
					                      'format' => 'raw'
				                      ],
				                      [
					                      'attribute' => 'item_name',
					                      'value' => '<span class="'.CssHelper::roleCss($model->getRoleName()).'">
                                '.$model->getRoleName().'
                            </span>',
					                      'format' => 'raw'
				                      ],
				                      'account_suffix',
				                      [
					                      'attribute' => 'warehouse_ids',
					                      'value' => implode('<br>', $model->warehouseNames),
					                      'format' => 'raw'
				                      ],
				                      [
					                      'attribute' => 'created_at',
					                      'value' => (!empty($model->created_at)) ? date("d.m.Y", $model->created_at) : ""
				                      ],
				                      [
					                      'attribute' => 'updated_at',
					                      'value' => (!empty($model->updated_at)) ? date("d.m.Y", $model->updated_at) : ""
				                      ],
			                      ],
		                      ])?>

	</div>
</div>
