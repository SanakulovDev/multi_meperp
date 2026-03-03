<?php
	use app\components\Helpers;
	use yii\widgets\LinkPager;

	/* @var TYPE_NAME $models */
?>
<style>
	.table-bordered{
		font-size:30px;
	}
</style>
<div class="panel">
	<div class="panel-heading">

	</div>
</div>

<div class="content" style="min-height: 960px;">
	<header class="main-header">
		<span class="text-bold" style="font-size: 50px;"><?=$productLine ? $productLine->linename : '-'?></span>
		<span class="pull-right" style="font-size: 50px;" id="clock"></span>
	</header>
	<!-- Main content -->
	<section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row panel panel-primary">
			<table class="table table-bordered">
				<thead class="active">
				<tr>
					<th><?=Yii::t('app', 'Part')?></th>
					<th><?=Yii::t('app', 'Name')?></th>

					<th><?=Yii::t('app', 'Target')?></th>
					<th><?=Yii::t('app', 'Actual')?></th>
					<th><?=Yii::t('app', 'Diff')?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach($models as $model): ?>
					<tr class="<?=$model['diff'] > 0 ? 'danger' : 'success'?>">
						<th scope="row"><?=$model['part_no']?></th>
						<td><?=$model['part_name']?></td>
						<td><?=Helpers::numberFormatRemoveZero($model['target_qty'], 10,'.', " ", true, true);?></td>
						<td><?=Helpers::numberFormatRemoveZero($model['actual'], 10,'.', " ", true, true);?></td>
						<th><?=Helpers::numberFormatRemoveZero(abs($model['diff']), 10,'.', " ", true, true);?></th>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<!-- /.row -->
	</section>
	<!-- /.content -->
	<?=LinkPager::widget([
		                     'pagination' => $pages,
	                     ]);?>
</div>
<script>
	setInterval(function (){
		var ul = $('ul.pagination')
		if(ul.length){
			var li = $('ul.pagination > li.next')
			if(li.is('.next.disabled')){
				$('ul.pagination li:nth-child(2) a:first')[0].click()
			}else{
				li.find('a')[0].click()
			}
		}else{
			location.reload()
		}
	}, 60000)

	function startTime(){
		var today                                  = new Date()
		var h                                      = today.getHours()
		var m                                      = today.getMinutes()
		var s                                      = today.getSeconds()
		m                                          = checkTime(m)
		s                                          = checkTime(s)
		document.getElementById('clock').innerHTML =
			h + ':' + m + ':' + s
		var t                                      = setTimeout(startTime, 500)
	}

	function checkTime(i){
		if(i < 10){
			i = '0' + i
		}
		  // add zero in front of numbers < 10
		return i
	}

	startTime()
</script>
