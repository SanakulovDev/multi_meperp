<?php
use app\models\LineStop;
use app\models\LineStopReason;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LineStopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $types */
$this->title = Yii::t("app", "Line stop");
$this->params["breadcrumbs"][] = $this->title;
$canUpdate = Yii::$app->user->can("line-stop-update");
$canDelete = Yii::$app->user->can("line-stop-delete");
$canAccept = Yii::$app->user->can("line-stop-accept");
$canReject = Yii::$app->user->can("line-stop-reject");
$reasons = ArrayHelper::map(LineStopReason::find()->all(), "id", "name");
?>
<div class="pack-level-index">
	<p class="pull-right">
    <?php if (Yii::$app->user->can("line-stop-create")) {
      echo Html::a(
        Yii::t("app", "btn-create"),
        ["create", "id" => 1, "planned" => "0"],
        [
          "class" => "btn btn-success btn-sm form-modal",
          "data-intro" => Yii::t("intro", "add-new-record"),
        ]
      );
    } ?>
	</p>
  <?php Pjax::begin(['id' => 'pjaxGrid']); ?>

  <?= $this->render("_search", ["model" => $searchModel]) ?>

  <?= GridView::widget([
    "dataProvider" => $dataProvider,
    "filterModel" => $searchModel,
    "summary" => Yii::t("app", "Showing {begin}-{end} of {totalCount} item."),
    "options" => ["style" => "overflow:auto;clear:both"],
    "emptyText" => Yii::t("app", "No results found."),
    "tableOptions" => [
      "class" => "sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0",
      "data-intro" => Yii::t("intro", "data-table"),
    ],
    "filterRowOptions" => ["data-intro" => Yii::t("intro", "filter")],
    "pager" => [
      "class" => "\yii\widgets\LinkPager",
      "options" => [
        "class" => "pagination",
        "data-intro" => Yii::t("intro", "pagination"),
      ],
    ],
    'rowOptions'=>function ($model) {
      if($model->status === LineStop::STATUS_REJECTED){
        return ['style' => 'text-decoration: line-through;'];
      }
      if($model->status === LineStop::STATUS_PENDING){
        return ['class' => 'text-danger'];
      }
    },
    "columns" => [
      ["class" => "yii\grid\SerialColumn"],
      [
        "class" => "yii\grid\ActionColumn",
        "template" => "{accept} {reject} {update} {delete} ",
        "header" => '<i class="fa fa-fw fa-gears"></i>',
        "headerOptions" => ["style" => "min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;"],
        "contentOptions" => ["style" => "min-width:50px;text-align:center;vertical-align:middle;"],
        "buttons" => [
          "update" => function ($url, $model) use ($canUpdate) {
            if (!$canUpdate) {
              return false;
            }
            return Html::a('<span  class="glyphicon glyphicon-pencil"></span>', false, [
              "class" => "modalButtonUpdate",
              "value" => $url,
              "title" => Yii::t("app", "Update"),
            ]);
          },
          "delete" => function ($url, $model) use ($canDelete) {
            if (!$canDelete) {
              return false;
            }
            return Html::a('<span class="glyphicon glyphicon-trash"></span>', false, [
              "class" => "modalButtonDelete",
              "data-href" => $url,
              "data-grid" => "pjaxGrid",
              "title" => Yii::t("app", "Delete"),
            ]);
          },
          "accept" => function ($url, $model) use ($canAccept) {
            if (
              !(
                $canAccept &&
                $model->status === LineStop::STATUS_PENDING &&
                Yii::$app->user->can($model->lineStopReason->auth_item_name)
              )
            ) {
              return false;
            }
            return Html::a('<span class="glyphicon glyphicon-ok"></span>', false, [
              "class" => "modalButtonUpdate",
              "value" => $url,
              "title" => Yii::t("app", "Accept"),
            ]);
          },
          "reject" => function ($url, $model) use ($canReject) {
            if (
              !(
                $canReject &&
                $model->status === LineStop::STATUS_PENDING &&
                Yii::$app->user->can($model->lineStopReason->auth_item_name)
              )
            ) {
              return false;
            }
            return Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
              "title" => Yii::t("app", "Reject"),
            ]);
          },
        ],
      ],
      [
        "attribute" => "warehouse",
        "content" => function ($model) {
          return $model->partProductionMonitor->productionMonitor->warehouse->name;
        },
      ],
      [
        "attribute" => "line_stop_reason_id",
        "filter" => Html::activeDropDownList($searchModel, "line_stop_reason_id", $reasons, [
          "class" => "form-control select2",
          "prompt" => "...",
        ]),
        "content" => function ($model) {
          return $model->lineStopReason ? $model->lineStopReason->name : "";
        },
      ],
      "start_time",
      "end_time",
      "elapsed_minutes",
      "bypass",
      "remark",
      "fix_list",
    ],
  ]) ?>
  <?php Pjax::end(); ?>
</div>
