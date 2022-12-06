<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Contract */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Supplier'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="contract-update">

	<?=$this->render('_form', [
		'errorlist' => ($errorlist ?? null),
		'model' => ($model ?? null),
		'items' => ($items ?? null),
		'isUpdating' => $count ? true : false,
		'count' => false,
		'modelItems' => ($modelItems ?? null)
	])?>

	<div>
		<?php foreach ($count as $count_of_component): ?>
			<?=$this->render('_form_detail', [
				'model_detail' => $model_detail,
				'id' => $model->id,
				'index' => $count_of_component
			])?>
		<?php endforeach; ?>
	</div>

	<? if (count($count)) { ?>
		<button type="button" onclick="onSave()" class="btn btn-success btn-sm">Далее</button>
		<button type="button" onclick="saveAndQuit()" class="btn btn-success btn-sm">Сохранить и выйти</button>
		
	<?}?>



</div>
<div id="multistepsform">
  <!-- progressbar -->
  <ul id="progressbar">
    <li class="active">Создание контракта</li>
    <li>Создание заказа</li>
    <li>Создание счёт фактуры</li>
  </ul>
</div>
<style>
#multistepsform {
	 width: 640px;
	 margin: 50px auto;
	 text-align: center;
	 position: relative;
}
 #multistepsform #progressbar {
	 margin-bottom: 30px;
	 overflow: hidden;
	 counter-reset: step;
}
 #multistepsform #progressbar li {
	 list-style-type: none;
	 color: #679b9b;
	 text-transform: uppercase;
	 font-size: 12px;
	 width: 33.33%;
	 float: left;
	 position: relative;
}
 #multistepsform #progressbar li:before {
	 content: counter(step);
	 counter-increment: step;
	 width: 45px;
	 line-height: 20px;
	 display: block;
	 font-size: 10px;
	 color: #fff;
	 background: #ff9a76;
	 border-radius: 3px;
	 margin: 0 auto 5px auto;
}
 #multistepsform #progressbar li:after {
	 content: "";
	 width: 100%;
	 height: 2px;
	 background: #ff9a76;
	 position: absolute;
	 left: -50%;
	 top: 9px;
	 z-index: -1;
}
 #multistepsform #progressbar li:first-child:after {
	 content: none;
}
 #multistepsform #progressbar li.active {
	 color: #00a65a;
}
 #multistepsform #progressbar li.active:before, #multistepsform #progressbar li.active:after {
	background-color: #00a65a;
	color: white;
}
</style>


<?php
	$add_item = <<< JS
	function onSave () {
		const count = $model->status;
		for (let i = 1; i <= count; i++) {
			$('form#w' + i).submit();
		}
		if ($model->id) {
			window.location.href = "/part-order/create?id=" + $model->id
		}
	}

	function saveAndQuit () {
		const count = $model->status;
		for (let i = 1; i <= count; i++) {
			$('form#w' + i).submit();
		}

		window.location.href = "/contract/index"
	}
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>
