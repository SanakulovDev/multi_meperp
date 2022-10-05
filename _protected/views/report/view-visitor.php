<?php
	use yii\helpers\Html;
	use yii\widgets\DetailView;

	/* @var $this yii\web\View */
	/* @var $model app\models\Visitor */
	$this->title = $model->id;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Visitors'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-view">
	<p>
	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-back'), ['all-visitors'], ['class' => 'btn btn-default '])?>
	</div>
	</p>
	<?=DetailView::widget([
		                      'model' => $model,
		                      'template' => '<tr><th style="width:200px">{label}</th><td>{value}</td></tr>',
		                      'attributes' => [
			                      'id',
			                      [
				                      'attribute' => 'user_id',
				                      'value' => $model->user->fullname
			                      ],
			                      [
				                      'attribute' => 'page',
				                      'value' => $model->pageroute
			                      ],
			                      'user_ip',
//			                      'user_agent',
//			                      'user_browser',
//			                      'user_browser_version',
//			                      'user_platform',
//			                      'user_device_type',
			                      'visited_at',
		                      ],
	                      ])?>

</div>
