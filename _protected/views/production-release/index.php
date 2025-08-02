<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\url;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductionReleaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Production Order Release');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="production-release-index">

    <div class="pull-right">

        <p style="margin:0; padding: 0;">
            <?= Html::a(Yii::t('app', 'btn-create'), ['create'], [
                'class' => 'btn btn-success btn-sm form-modal mr-lg-5',
                'data-intro' => Yii::t('intro', 'add-new-record')
              ]) ?>
        </p>
    </div>

    <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{view}{delete}{history}',
                'header' => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                    'update' => function($url, $model)  {
                        
                        $url = Url::toRoute(['production-release/update', 'id' => $model->id]);
                        return Html::a(
                            '<span  class="glyphicon glyphicon-pencil"></span>',
                            false,
                            [
                                'class' => 'modalButtonUpdate',
                                'value' => $url,
                                'title' => Yii::t('app', 'Update')
                            ]
                        );
                    },
                    'history' => function($url, $model)  {
                        
                        $url = Url::toRoute(['production-release/history', 'id' => $model->id]);
                        return Html::a(
                            '<span  class="glyphicon glyphicon-time"></span>',
                            false,
                            [
                                'class' => 'modalButtonHistory',
                                'value' => $url,
                                'title' => Yii::t('app', 'History'),
                                'data-id' => $model->id,
                                'data-status' => $model->status,
                            ]
                        );
                    },
                    
                ],
            ],

            [
                'attribute'=> 'part_id',
                'value' => function($model) {
                    return substr($model->part->part_no.' '.$model->part->part_name, 0, 45);
                },
                'filter' => $parts,
                'filterInputOptions' => [
                    'class' => 'form-control select2',
                    'prompt' => '---',
                    'data-intro' => Yii::t('intro', 'production-release-part_id')
                ],
            ],
            [
                'attribute'=> 'line',
                'value' => function($model) use($lines) {
                    return $lines[$model->line];
                },
                'filter' =>$lines,
                'filterInputOptions' => [
                    'class' => 'form-control select2',
                    'prompt' => '---',
                    'data-intro' => Yii::t('intro', 'production-release-line')
                ],    
            ],
            'pr_order_number',
            'target_date',
            [
                    'attribute'=> 'shift',
                    'value' => function($model) use($shifts) {
                        return $shifts[$model->shift];
                    },
                    'filter' => $shifts,
                    'filterInputOptions' => [
                        'class' => 'form-control select2',
                        'prompt' => '---',
                        'data-intro' => Yii::t('intro', 'production-release-shift')
                    ],
            ],
            'time',
            'quantity',

        ],
    ]); ?>

    <?php Pjax::end(); ?>
    <!-- Modal -->
    <?php
        Modal::begin([
            'id' => 'modal-release',
            'header' => '<h4 class="modal-title">'.Yii::t('app', 'History').'</h4>',
            'size' => 'modal-md',
            'footer' => '
              <a href="javascript:void(0)" class="btn btn-success close-release" >Закрыть Релезов</a>
              ',
        ]);
        echo "<div id='modalContent'></div>";
        Modal::end();
    ?>
</div>
<?php  ob_start();?>
$(function(){
    $('.modalButtonHistory').click(function(){
        $('#modal-release').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
        let status = $(this).attr('data-status');
        if(status == 0){
            $('#modal-release').find('.close-release').hide();
        }
        else{
            $('#modal-release').find('.close-release').show();
        }
        $('#modal-release').find('.close-release').attr('data-id', $(this).attr('data-id'));
    });

    $('body').on('click', '.close-release', function(){
        var id = $(this).attr('data-id');
        $.ajax({
            url: '/production-release/close-release',
            type: 'GET',
            data: {id: id},
            success: function(data){
                $('#modal-release').modal('hide');
                $.pjax.reload({container: '#pjaxGrid'});
            }
        });
    })

})




<?php $this->registerJs(ob_get_clean()); ?>