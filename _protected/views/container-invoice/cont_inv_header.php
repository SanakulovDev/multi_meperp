<?php
use app\components\Helpers;
use app\enums\CargoType;
?>
<div class="row" style="margin-top: 20px;" >
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Invoice no') ?>:</span>
			<span><?= $model->invoice->invoice_no ?></span>
		</p>
	</div>
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'CONT./TRUCK/AWBL') ?>:</span>
			<span><?= $model->container->container_no ?></span>
		</p>
	</div>
	<div class="col-lg-6">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Supplier') ?>:</span>
			<span><?= $model->invoice->supplier->name . "(" . $model->shipMode->name . ") - " . $model->deliveryTerm->name ?></span>
		</p>
	</div>
</div>
<div class="row">
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Net weight') ?> (kg):</span>
			<span><?= ($model->net_weight) ?  Helpers::numberFormatRemoveZero($model->net_weight) : "-" ?></span>
		</p>
	</div>
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Gross weight') ?> (kg):</span>
			<span><?= ($model->gross_weight) ? Helpers::numberFormatRemoveZero($model->gross_weight) : "-" ?></span>
		</p>
	</div>
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'CBM') ?> (m<sup>3</sup>):</span>
			<span><?= ($model->cbm) ? Helpers::numberFormatRemoveZero($model->cbm) : "-" ?></span>
		</p>
	</div>
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Cargo type') ?>:</span>
			<span><?= ($model->cargo_type) ? CargoType::name($model->cargo_type) : "-" ?></span>
		</p>
	</div>

</div>
<div class="row ">
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Shipped at') ?>:</span>
			<span title="<?= $model->shippedBy->username ?>"><?= $model->shipped_at ?></span>
		</p>
	</div>
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Need date') ?>:</span>
			<span><?= ($model->need_at) ? $model->need_at : "-" ?></span>
		</p>
	</div>
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Approximate arrival date') ?>:</span>
			<span><?= ($model->app_arr_at) ? $model->app_arr_at : "-" ?></span>
		</p>
	</div>
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Station date') ?>:</span>
			<span><?= ($model->station_date) ? $model->station_date : "-" ?></span>
		</p>
	</div>
</div>
<div class="row">
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Arrived at') ?>:</span>
			<span title="<?= (isset($model->arrived_by)) ? $model->arrivedBy->username : null ?>"><?= ($model->arrived_at) ? $model->arrived_at : "-" ?></span>
		</p>
	</div>
	<div class="col-lg-6">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Current location') ?>:</span>
			<span><?= $model->current_locate . "(" . $model->current_at . ")" ?></span>
		</p>
	</div>
</div>
<div class="row">
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Document number') ?>:</span>
			<span><?= ($model->document_id) ? $model->document->docnum : "-" ?></span>
		</p>
	</div>
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Received at') ?>:</span>
			<span title="<?= (isset($model->received_by)) ? $model->receivedBy->username : null ?>"><?= ($model->received_at) ? $model->received_at : "-" ?></span>
		</p>
	</div>
</div>
<div class="row">
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Regime') ?>:</span>
			<span><?= ($model->regime) ? $model->regime : "-" ?></span>
		</p>
	</div>
	<div class="col-lg-3">
		<p>
			<span class="text-bold"><?= Yii::t('app', 'Passed at') ?>:</span>
			<span><?= ($model->passed_at) ? $model->passed_at : "-" ?></span>
		</p>
	</div>
</div>
