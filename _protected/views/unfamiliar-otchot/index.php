<?php

use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UnfamiliarOtchotSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Unfamiliar Otchots');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unfamiliar-otchot-index">

    <div class="row d-flex align-items-center justify-content-between">
        <div class="col-md-9"></div>
        <div class="col-md-3">
            <?= Html::a(Yii::t('app', 'btn-create'), ['create'], [
                // 'class' => 'btn btn-success',
                'class' => 'btn btn-success btn-sm form-modal mr-lg-5',
                ]) ?>
                <?=Html::button(Yii::t('app', 'btn-delete-all'),
              [
                'class' => 'btn btn-danger btn-sm modalButtonDelete mr-lg-5',
                'data-intro' => Yii::t('intro', 'delete-all-records'),
                'data-grid' => 'pjaxGrid',
                'data-status' => 1,
                'data-href' => Url::toRoute(['delete-all'])
              ]
            )?>
            <button id="export-excel" class="btn btn-sm btn-info"><?= Yii::t('app', "Export Excel")?></button>
        </div>
        
    </div>

    <p>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '№',
                'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
                'contentOptions' => ['style' => 'width: auto;text-align: center;']
              ],
              [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'header' => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                  'update' => function($url, $model) {
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
                  'delete' => function($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                      false,
                      [
                        'class' => 'modalButtonDelete',
                        'data-href' => $url,
                        'data-grid' => 'pjaxGrid',
                        'title' => Yii::t('app', 'Delete')
                      ]);
                  },
                ],
              ],


              [
                'attribute' => 'part_id',
                'value' => function($model) {
                    if ($part = $model->part) {
                        return $part->part_no . ' - ' . $part->part_name . ' (' . $part->part_color . ')';
                    }
                    return null; // Agar $model->part mavjud bo'lmasa, null qaytariladi
                },
                'format' => 'raw',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'part_id',
                    'data' => $list, // Bu yerda `$list` ni Select2 uchun foydalanamiz
                    'options' => [
                        'placeholder' => 'Select part...',
                        'class' => 'select2',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
            ],
            'quantity',
            'location',
            'status',
            [
                'attribute' => 'expected_arrival_date',
                'value' => function($model){
                    // format dd..mm.YYYY
                    return Yii::$app->formatter->asDate($model->expected_arrival_date,'d.M.Y');
                },
                'filter' => DateTimePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'expected_arrival_date',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'todayHighlight' => true,
                        'format' => 'yyyy-mm-dd', // Sana formati
                        'minView' => 2, // Kun darajasiga qadar tanlash
                        'startView' => 2, // Kalendar oynasini kun darajasida ochish
                    ],
                ]),
            ],
            'remark',
            [
                'attribute' => 'user_id',
                'value' => function($model){
                    if($model->user){
                        return $model->user->fullname;
                    }
                }
            ],
            'created_at',
            'updated_at',

            
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?php $this->registerJsFile("https://code.jquery.com/jquery-3.6.0.min.js");?>
<?php $this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js");?>

<?php ob_start();?>

$(document).ready(function() {
    $('#export-excel').on('click', function() {
        // Asl jadvalni tanlash
        var table = document.querySelector('table');
        
        // Yangi jadval yaratish
        var tempTable = document.createElement('table');
        
        // `thead` qismini tanlash va yangi jadvalga qo'shish
        var tHead = table.querySelector('thead');
        var newThead = document.createElement('thead');
        
        // Faqatgina ma'lumotga ega bo'lgan ustunlarni yig'ish
        if (tHead && tHead.rows.length > 0) {
            var headerRow = document.createElement('tr');
            for (var i = 0; i < tHead.rows[0].cells.length; i++) {
                var cell = tHead.rows[0].cells[i];
                if (cell.innerText.trim() !== "") { // Bo'sh bo'lmagan ustunlarni yig'ish
                    var newCell = cell.cloneNode(true);
                    headerRow.appendChild(newCell);
                }
            }
            newThead.appendChild(headerRow);
        }
        tempTable.appendChild(newThead);

        // `tbody` qismini tanlash va yangi jadvalga qo'shish
        var tBody = table.querySelector('tbody');
        var newTbody = document.createElement('tbody');

        // `tbody` qismidagi har bir qatorni tekshirish va bo'sh ustunlarni tashlab ketish
        if (tBody && tBody.rows.length > 0) {
            for (var r = 0; r < tBody.rows.length; r++) {
                var oldRow = tBody.rows[r];
                var newRow = document.createElement('tr');
                for (var i = 0; i < oldRow.cells.length; i++) {
                    var cell = oldRow.cells[i];
                    if (tHead.rows[0].cells[i].innerText.trim() !== "") { // Bo'sh bo'lmagan ustunlarni klonlash
                        var newCell = cell.cloneNode(true);
                        newRow.appendChild(newCell);
                    }
                }
                newTbody.appendChild(newRow);
            }
        }
        tempTable.appendChild(newTbody);

        // Excel faylga ma'lumotlarni yozish
        var workbook = XLSX.utils.table_to_book(tempTable, { sheet: "Sheet1" });
        var worksheet = workbook.Sheets["Sheet1"];

        // Sarlavhalarga (header) fon rangi berish va qalin qilish
        var range = XLSX.utils.decode_range(worksheet['!ref']);
        for (var C = range.s.c; C <= range.e.c; ++C) {
            var cell_address = XLSX.utils.encode_cell({r: 0, c: C});
            if (worksheet[cell_address]) {
                worksheet[cell_address].s = {
                    font: {
                        bold: true
                    },
                    fill: {
                        patternType: "solid",
                        fgColor: { rgb: "FFFF00" } // Sariq fon rangi (yashil uchun: `00FF00`)
                    }
                };
            }
        }

        // Ustun kengliklarini sozlash
        worksheet['!cols'] = [];
        for (var C = range.s.c; C <= range.e.c; ++C) {
            worksheet['!cols'].push({ width: 20 }); // Har bir ustunning kengligini 20 qilamiz
        }

        // `created_at` va `updated_at` formatini to'g'irlash
        for (var R = range.s.r + 1; R <= range.e.r; ++R) {
            var createdAtAddress = XLSX.utils.encode_cell({r: R, c: 9}); // Created At ustuni
            var updatedAtAddress = XLSX.utils.encode_cell({r: R, c: 10}); // Updated At ustuni

            if (worksheet[createdAtAddress]) {
                worksheet[createdAtAddress].z = 'yyyy-mm-dd hh:mm:ss';
            }
            if (worksheet[updatedAtAddress]) {
                worksheet[updatedAtAddress].z = 'yyyy-mm-dd hh:mm:ss';
            }
        }

        // Excel faylni yaratish va yuklab olish
        XLSX.writeFile(workbook, 'Bloknot_export_<?= date('Y-m-d H:i:s', time())?>.xlsx');
    });
});


<?php $this->registerJs(ob_get_clean());?>