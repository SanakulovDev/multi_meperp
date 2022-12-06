<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Contract */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales contracts'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="contract-update" style="">

	<?=$this->render('_form', [
		'errorlist' => ($errorlist ?? null),
		'model' => ($model ?? null),
		'items' => ($items ?? null),
		'modelItems' => ($modelItems ?? null)
	])?>
	
</div>
<? if ($update) {?>
<div class="contract-detail-create">
	<div>
		<?php foreach ($status as $count_of_component): ?>
			<?=$this->render('_detail-form', [
				'customer' => $model->id,
				'model' => $detail,
				'index' => $count_of_component
			])?>
		<?php endforeach; ?>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<button type="button" onclick="onSave()" class="btn btn-success btn-sm">Сохранить</button>
	</div>
</div>
<?}?>

<?php
	$add_item = <<< JS
	function onSave () {
		const count = $model->status;
		for (let i = 1; i <= count; i++) {
			$('form#w' + i).submit();
		}
		setTimeout(() => {
        	if (!($("div").find('[aria-invalid=true]').length)) {
				window.location.href = "/sales-contract/index"
        	}
    	}, 2000);
	}
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>
