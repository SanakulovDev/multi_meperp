<div class="row ">
	<div class="col-lg-4">
		<span class="text-bold"><?=Yii::t('app', 'Document number')?>:</span>
		<span><?=$model->gtd_no?></span>
	</div>
	<div class="col-lg-3">
		<span class="text-bold"><?=Yii::t('app', 'Date')?>:</span>
		<span><?=$model->gtd_dt?></span>
	</div>
	<div class="col-lg-3">
		<span class="text-bold"><?=Yii::t('app', 'Post number')?>:</span>
		<span><?=$model->post_no?></span>
	</div>
</div>
