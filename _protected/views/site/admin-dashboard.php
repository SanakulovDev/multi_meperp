<?php
use app\models\ProductionMonitor;
use yii\helpers\Url;
?>
<?php
  $this->title = Yii::t('app', 'Admin dashboard');
?>
<h2 class="text-center text-bold" style="color: #2378ab; margin-top: 8px;margin-bottom: -5px;"><?=Yii::$app->name?></h2>
<h4 class="text-center" style="color: #2378ab;"><?=Yii::$app->params['comp_name']?></h4>
<div class="row">
  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-yellow">
      <div class="inner">
        <h3><?=$data['users_cnt']?></h3>

        <p>Active users</p>
      </div>
      <div class="icon">
        <i class="ion ion-android-contacts"></i>
      </div>
      <a href="<?=Url::toRoute('user/index')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-aqua">
      <div class="inner">
        <h3><?=$data['docs_cnt']?></h3>

        <p>Documents</p>
      </div>
      <div class="icon">
        <i class="ion ion-document-text"></i>
      </div>
      <a href="<?=Url::toRoute('document/index')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-green">
      <div class="inner">
       <h3><?=$data['whs_cnt']?></h3>

        <p>Locations</p>
      </div>
      <div class="icon">
        <i class="ion ion-location"></i>
      </div>
      <a href="<?=Url::toRoute('warehouse/index')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->

  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-blue-active">
      <div class="inner">
        <h3><?=$data['parts_cnt']?></h3>

        <p>Active parts</p>
      </div>
      <div class="icon">
        <i class="ion ion-clipboard"></i>
      </div>
      <a href="<?=Url::toRoute('part/index')?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
</div>





<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">Created documents</h3>


  </div>
  <div class="box-body">
    <div class="row">
      <div class="col-lg-12">

        <div class="chart">
          <canvas id="lineChartDocs" style="height: 250px; width: 510px;" width="510" height="250"></canvas>
        </div>
      </div>
      <!-- /.box-body -->
    </div>

  </div>
</div>


<?php

  $users_data = $data['users_data'];
  $docs_data = $data['docs_data'];
  $whs_data = $data['whs_data'];
  $parts_data = $data['parts_data'];
  $visitors_data = $data['visitors_data'];
  $docs_line_data = $data['docs_line_data'];



  $script1 = <<< JS

  // -------------
  // - PIE CHART -
  // -------------
  // Get context with jQuery - using jQuery's .get() method.
  var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
  var pieChart       = new Chart(pieChartCanvas);
  
  var PieData        = $users_data;
  
  var pieOptions     = {
    // Boolean - Whether we should show a stroke on each segment
    segmentShowStroke    : true,
    // String - The colour of each segment stroke
    segmentStrokeColor   : '#fff',
    // Number - The width of each segment stroke
    segmentStrokeWidth   : 1,
    // Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    // Number - Amount of animation steps
    animationSteps       : 100,
    // String - Animation easing effect
    animationEasing      : 'easeOutBounce',
    // Boolean - Whether we animate the rotation of the Doughnut
    animateRotate        : true,
    // Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale         : false,
    // Boolean - whether to make the chart responsive to window resizing
    responsive           : true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio  : false,
    // String - A legend template
    legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
    // String - A tooltip template
    tooltipTemplate      : '<%=value %> <%=label%>'
  };
  // Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
    
    
    
  var pieChartCanvas2 = $('#pieChart2').get(0).getContext('2d');
  var pieChart2       = new Chart(pieChartCanvas2);
  var PieData2        = $docs_data;
    
    pieChart2.Doughnut(PieData2, pieOptions);
    
    
  var pieChartCanvas3 = $('#pieChart3').get(0).getContext('2d');
  var pieChart3       = new Chart(pieChartCanvas3);
  var PieData3        = $whs_data;
    
    pieChart3.Doughnut(PieData3, pieOptions);
    
    
  var pieChartCanvas4 = $('#pieChart4').get(0).getContext('2d');
  var pieChart4       = new Chart(pieChartCanvas4);
  var PieData4        = $parts_data;
    
    pieChart4.Doughnut(PieData4, pieOptions);
    
    
    
  // -----------------
  // - END PIE CHART -
  // -----------------
    
    
    //-------------
    //- LINE CHART Docs -
    //--------------
    
      var docsData        = $docs_line_data;
      var lineChartDocsData = {
      labels  : docsData.titles,
      datasets: [
        {
          label               : 'Documents',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : docsData.values
        }
      ]
    }
    
     var lineChartDocsOptions = {
      //Boolean - If we should show the scale at all
      showScale               : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : false,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - Whether the line is curved between points
      bezierCurve             : true,
      //Number - Tension of the bezier curve between points
      bezierCurveTension      : 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot                : false,
      //Number - Radius of each point dot in pixels
      pointDotRadius          : 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth     : 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius : 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke           : true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth      : 2,
      //Boolean - Whether to fill the dataset with a color
      datasetFill             : true,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio     : true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive              : true,
    
      showTooltips: false,
      onAnimationComplete: function () {

          var ctx = this.chart.ctx;
          ctx.font = this.scale.font;
          ctx.fillStyle = this.scale.textColor
          ctx.textAlign = "center";
          ctx.textBaseline = "bottom";

          this.datasets.forEach(function (dataset) {
              dataset.points.forEach(function (points) {
                  ctx.fillText(points.value, points.x, points.y - 10);
              });
          })
      }
    
    }

    
    var lineChartDocsCanvas          = $('#lineChartDocs').get(0).getContext('2d')
    var lineChartDocs                = new Chart(lineChartDocsCanvas)
    // lineChartDocsOptions.datasetFill = false
    lineChartDocs.Line(lineChartDocsData, lineChartDocsOptions)
    
 
    //-------------
    //- LINE CHART Visits -
    //--------------
    
      var visitorsData        = $visitors_data;
      var lineChartVisitsData = {
      labels  : visitorsData.titles,
      datasets: [
        {
          label               : 'Visitors',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : visitorsData.values,
        }
      ]
    }
    
     var lineChartVisitsOptions = {
      //Boolean - If we should show the scale at all
      showScale               : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : false,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - Whether the line is curved between points
      bezierCurve             : true,
      //Number - Tension of the bezier curve between points
      bezierCurveTension      : 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot                : false,
      //Number - Radius of each point dot in pixels
      pointDotRadius          : 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth     : 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius : 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke           : true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth      : 2,
      //Boolean - Whether to fill the dataset with a color
      datasetFill             : true,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio     : true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive              : true,
    
      showTooltips: false,
      onAnimationComplete: function () {

          var ctx = this.chart.ctx;
          ctx.font = this.scale.font;
          ctx.fillStyle = this.scale.textColor
          ctx.textAlign = "center";
          ctx.textBaseline = "bottom";

          this.datasets.forEach(function (dataset) {
              dataset.points.forEach(function (points) {
                  ctx.fillText(points.value, points.x, points.y - 10);
              });
          })
      }
    
    
    }

    
    var lineChartVisitsCanvas          = $('#lineChartVisits').get(0).getContext('2d')
    var lineChartVisits                = new Chart(lineChartVisitsCanvas)
    lineChartVisitsOptions.datasetFill = false
    lineChartVisits.Line(lineChartVisitsData, lineChartVisitsOptions)


        
JS;
  $this->registerJs($script1);

?>
