<?php

use app\components\Helpers;
use yii\widgets\LinkPager;

	/* @var $models */
  /* @var $productLine */
  /* @var $pages */
?>
<div class="content" style="min-height: 960px;">
	<header class="main-header">
		<span class="text-bold" style="font-size: 50px;"><?=$productLine ? $productLine->linename : '-'?></span>
		<span class="pull-right" style="font-size: 50px;" id="clock"></span>
	</header>
	<!-- Main content -->
	<section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">
			<?php foreach($models as $model): ?>
				<div class="col-lg-6 col-xs-6">
					<!-- small box -->
					<div class="box bg-black-active">
						<table class="table text-center">
							<thead>
							<tr>
								<th colspan="3" style="border-bottom-color: black; padding-bottom: 0px;">
									<span class="text-bold" style="font-size: 50px;"><?=$model['part_no']?></span>
									<span class="text-yellow" style="font-size: 50px;"><?=$model['part_color']?></span>
								</th>
							</tr>
              <tr>
                <th colspan="3">
                  <span class="text-bold h2"><?=$model['part_name']?></span>
                </th>
              </tr>
							</thead>
							<tbody>
							<tr>
								<th><?=Yii::t('app', 'Target')?></th>
								<th><?=Yii::t('app', 'Actual')?></th>
								<th><?=Yii::t('app', 'Diff')?></th>
							</tr>
							<tr>
								<td><span class="text-bold" style="font-size: 50px;"><?=$model['target_qty']?></span></td>
								<td><span class="text-bold text-green" style="font-size: 50px;"><?=Helpers::formatRemoveDecimal($model['actual'])?></span></td>
								<td>
									<?php if($model['target_qty'] > $model['actual']): ?>
										<span class="text-bold text-red" style="font-size: 50px;"><?=Helpers::formatRemoveDecimal($model['target_qty'] - $model['actual'])?> </span>
									<?php else: ?>
										<span class="text-bold text-success" style="font-size: 50px;"><?=Helpers::formatRemoveDecimal($model['actual'] - $model['target_qty'])?> </span>
									<?php endif; ?>
								</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
				<!-- ./col -->
			<?php endforeach; ?>
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
