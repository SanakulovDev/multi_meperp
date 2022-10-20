<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Contract */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Supplier'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-create">

	<?=$this->render('_form', [
		'errorlist' => ($errorlist ?? null),
		'model' => ($model ?? null),
		'items' => ($items ?? null),
		'isUpdating' => false,
		'modelItems' => ($modelItems ?? null),
		'isNewRecord' => ($isNewRecord ?? null)
	])?>

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
	 background: #679b9b;
	 border-radius: 3px;
	 margin: 0 auto 5px auto;
}
 #multistepsform #progressbar li:after {
	 content: "";
	 width: 100%;
	 height: 2px;
	 background: #679b9b;
	 position: absolute;
	 left: -50%;
	 top: 9px;
	 z-index: -1;
}
 #multistepsform #progressbar li:first-child:after {
	 content: none;
}
 #multistepsform #progressbar li.active {
	 color: #ff9a76;
}
 #multistepsform #progressbar li.active:before, #multistepsform #progressbar li.active:after {
	 background: #ff9a76;
	 color: white;
}
</style>

</div>
