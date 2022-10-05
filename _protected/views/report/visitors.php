  <?php
	use kartik\datetime\DateTimePicker;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	$this->title = Yii::t('app', 'Visitors statistics');
	$this->params['breadcrumbs'][] = Yii::t('app', 'Report');
	$this->params['breadcrumbs'][] = $this->title;
?>


<div>

	<?php $form = ActiveForm::begin(); ?>
	<div class="panel panel-default">
		<div class="panel-body">

			<div class="row">

				<div class="col-md-3">
					<?=$form->field($model, 'from1')->widget(DateTimePicker::classname(), [
						'language' => 'ru',
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd hh:ii'
						],
						'options' => [
							'autocomplete' => 'off'
						]
					])?>
				</div>
				<div class="col-md-3">
					<?=$form->field($model, 'to1')->widget(DateTimePicker::classname(), [
						'language' => 'ru',
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd hh:ii'
						],
						'options' => [
							'autocomplete' => 'off'
						]
					])?>
				</div>
				<div class="col-md-3">
					<div class="form-group" style="padding-top: 25px;">
						<?=Html::submitButton(Yii::t('app', 'btn-show'), ['name' => 'submitShow', 'class' => 'btn btn-primary  '])?>
					</div>
				</div>
			</div>


			<div class="row">
				<div class="col-lg-6">
					<div id="chart1"></div>
				</div>
				<div class="col-lg-6">
					<div id="chart2"></div>
				</div>
			</div>

			<div class="row">

				<div class="col-lg-4">
					<div id="chart3"></div>
				</div>
			</div>

			<div class="row">

				<div class="col-md-3">
					<?=$form->field($model, 'from2')->widget(DateTimePicker::classname(), [
						'language' => 'ru',
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd hh:ii'
						],
						'options' => [
							'autocomplete' => 'off'
						]
					])?>
				</div>
				<div class="col-md-3">
					<?=$form->field($model, 'to2')->widget(DateTimePicker::classname(), [
						'language' => 'ru',
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd hh:ii'
						],
						'options' => [
							'autocomplete' => 'off'
						]
					])?>
				</div>
				<div class="col-md-3">
					<div class="form-group" style="padding-top: 25px;">

						<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary  '])?>

					</div>
				</div>
			</div>

			<div class="row">

				<div class="col-lg-12">
					<div id="chart6"></div>
				</div>

			</div>

		</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>

<?php
	$export_status = (Yii::$app->params['user_device_type'] == 'mobile') ? 'false' : 'true';
	$script = <<< JS

Highcharts.chart('chart1', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Страницы'
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: 0,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Посещаемость'
        }
    },
    legend: {
        enabled: false
    },
     tooltip: {
        pointFormat: '<b>{point.y}</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.y} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Посещаемость',
        data: $data[chart1],
        colorByPoint: true,
        dataLabels: {
            enabled: true,
            rotation: 0,
            color: '#000000',
            align: 'right',
            format: '{point.y}', // one decimal
            y: 00, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }],
    credits: {enabled : false},
    exporting: { enabled: $export_status }
});
        
Highcharts.chart('chart2', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Пользователи'
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: 0,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Посещаемость'
        }
    },
    legend: {
        enabled: false
    },
     tooltip: {
        pointFormat: '<b>{point.y}</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.y} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Посещаемость',
        data: $data[chart2],
        colorByPoint: true,
        dataLabels: {
            enabled: true,
            rotation: 0,
            color: '#000000',
            align: 'right',
            format: '{point.y}', // one decimal
            y: 00, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }],
    credits: {enabled : false},
    exporting: { enabled: $export_status }
});     
        
Highcharts.chart('chart3', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Ролы'
    },
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.y}'
            }
        }
    },

    tooltip: {
        headerFormat: '<b>{point.key}</b><br>',
        pointFormat: '{point.y}'
    },

    "series": [
        {
            "name": "Ролы",
            "colorByPoint": true,
            "data": $data[chart3]
        }
    ],
    credits: {enabled : false},
    exporting: { enabled: $export_status }
});    

   
Highcharts.chart('chart6', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Ежедневный посещаемость'
    },
    xAxis: {
        categories: $data[chart6_categories]
    },
    yAxis: {
        title: {
            text: 'Количество'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            }
        }
    },
    series: $data[chart6_series],
    credits: {enabled : false},
    exporting: { enabled: $export_status }
});        
        
      
        
JS;
	$this->registerJs($script);
?>
    
